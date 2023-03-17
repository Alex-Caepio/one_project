<?php

namespace App\Http\Requests;

use App\Models\EmailMessage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ConversationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return EmailMessage::query()
                ->whereConversationId($this->route('conversationId'))
                ->where(function (Builder $builder) {
                    return $builder
                        ->where('receiver_id', Auth::id())
                        ->orWhere('sender_id', Auth::id());
                })
                ->count('id') > 0;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'conversation_id' => [
                'required',
                'exists:email_messages,conversation_id'
            ]
        ];
    }

    public function all($keys = null)
    {
        return array_merge($this->request->all(), ['conversation_id' => $this->route('conversationId')]);
    }
}
