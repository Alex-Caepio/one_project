<?php

namespace App\Http\Controllers;

use App\Http\Requests\Message\MessageMultipleRequest;
use App\Http\Requests\Message\MessageRequest;
use App\Http\Requests\Request;
use App\Mail\SendUserMail;
use App\Models\EmailMessage;
use App\Models\User;
use App\Transformers\EmailMessageTransformer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class MessageController extends Controller {
    public function index(Request $request) {
        $massage = EmailMessage::where('sender_id', Auth::id())->get();
        return fractal($massage, new EmailMessageTransformer())->parseIncludes($request->getIncludes())->toArray();

    }

    public function store(MessageRequest $request, User $user) {
        $message = $this->sendMessage($user, $request->get('text'));

        return fractal($message, new EmailMessageTransformer())->parseIncludes($request->getIncludes())->respond();
    }

    public function storeMultiple(MessageMultipleRequest $request) {
        $users =
            User::where('account_type', User::ACCOUNT_CLIENT)->whereIn('id', $request->getArrayFromRequest('users'))
                ->get();
        foreach ($users as $user) {
            $this->sendMessage($user, $request->get('text'));
        }
        return response('', 204);
    }

    private function sendMessage(User $receiver, string $message): EmailMessage {
        Mail::to($receiver->email)->send(new SendUserMail(Auth::user(), $receiver, $message));


        $emailMessage = new EmailMessage();
        $emailMessage->forceFill([
                                     'sender_id'   => Auth::id(),
                                     'receiver_id' => $receiver->id,
                                     'text'        => $message,
                                 ]);
        $emailMessage->save();
        return $emailMessage;
    }


    public function showByReceiver(User $user, Request $request) {
        $massage = EmailMessage::where('sender_id', Auth::id())->where('receiver_id', $user->id)->get();

        return fractal($massage, new EmailMessageTransformer())->parseIncludes($request->getIncludes())->respond();
    }
}
