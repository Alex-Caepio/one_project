<?php

namespace App\Http\Controllers;

use App\Http\Requests\ConversationRequest;
use App\Http\Requests\Message\MessageMultipleRequest;
use App\Http\Requests\Message\MessageRequest;
use App\Http\Requests\Request;
use App\Http\Resources\ConversationResource;
use App\Mail\SendUserMail;
use App\Models\EmailMessage;
use App\Models\User;
use App\Transformers\EmailMessageTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class MessageController extends Controller
{
    public function index(Request $request)
    {
        $massage = EmailMessage::where('sender_id', Auth::id())->get();

        return fractal($massage, new EmailMessageTransformer())->parseIncludes($request->getIncludes())->toArray();
    }

    public function store(MessageRequest $request, User $user)
    {
        $message = $this->sendMessage($user, $request->get('text'), $request->get('conversation_id'));

        return fractal($message, new EmailMessageTransformer())->parseIncludes($request->getIncludes())->respond();
    }

    public function storeMultiple(MessageMultipleRequest $request)
    {
        $users =
            User::query()
                ->where('account_type', User::ACCOUNT_CLIENT)
                ->whereIn('id', $request->get('users'))
                ->get();
        foreach ($users as $user) {
            $this->sendMessage($user, $request->get('text'));
        }

        return response('', 204);
    }

    private function sendMessage(User $receiver, string $message, string $conversationId = null): EmailMessage
    {
        $message = nl2br($message);
        $conversationId = $conversationId ?? Str::random(16);
        Mail::to($receiver->email)->send(new SendUserMail(Auth::user(), $receiver, $message, $conversationId));

        $emailMessage = new EmailMessage();
        $emailMessage->forceFill([
            'sender_id' => Auth::id(),
            'receiver_id' => $receiver->id,
            'text' => $message,
            'conversation_id' => $conversationId
        ]);
        $emailMessage->save();

        return $emailMessage;
    }

    /**
     * @param ConversationRequest $request
     * @return AnonymousResourceCollection
     */
    public function getConversation(ConversationRequest $request): AnonymousResourceCollection
    {
        $validated = $request->validated();
        $userFields = "id,first_name,last_name,account_type,business_name";
        $messages = EmailMessage::query()
            ->with([
                'receiver:' . $userFields,
                'sender:' . $userFields
            ])
            ->whereConversationId($validated['conversation_id'])
            ->oldest()
            ->paginate(10);

        return ConversationResource::collection($messages);
    }

    public function showByReceiver(User $user, Request $request)
    {
        $massage = EmailMessage::where('sender_id', Auth::id())->where('receiver_id', $user->id)->get();

        return fractal($massage, new EmailMessageTransformer())->parseIncludes($request->getIncludes())->respond();
    }
}
