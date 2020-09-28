<?php

namespace App\Http\Controllers;

use App\Http\Requests\Request;
use App\Mail\SendUserMail;
use App\Models\EmailMessage;
use App\Models\User;
use App\Transformers\EmailMessageTransformer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class MessageController extends Controller
{
    public function index(Request $request)
    {
        $massage = EmailMessage::where('sender_id', Auth::id())->get();

        return fractal($massage, new EmailMessageTransformer())
            ->parseIncludes($request->getIncludes())
            ->toArray();
    }

    public function store(Request $request, User $user)
    {
        $text = $request->get('text');
        Mail::to($user->email)->send(new SendUserMail($text));

        $message = new EmailMessage();
        $message->forceFill([
            'sender_id'   => Auth::id(),
            'receiver_id' => $user->id,
            'text'        => $text,
        ]);
        $message->save();

        return fractal($message, new EmailMessageTransformer())
            ->parseIncludes($request->getIncludes())
            ->respond();
    }

    public function showByReceiver(User $user, Request $request)
    {
        $massage = EmailMessage::where('sender_id', Auth::id())->where('receiver_id', $user->id)->get();

        return fractal($massage, new EmailMessageTransformer())
            ->parseIncludes($request->getIncludes())
            ->respond();
    }
}
