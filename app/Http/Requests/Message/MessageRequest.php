<?php

namespace App\Http\Requests\Message;

use App\Http\Requests\Request;
use App\Models\Booking;

class MessageRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()->id !== $this->user->id;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'text' => [
                'required',
                'max:1000'
            ],
            'conversation_id' => 'nullable'
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $booking = Booking::query()
                ->where('practitioner_id', $this->user()->id)
                ->where('user_id', $this->user->id)
                ->active()
                ->exists();
            // practitioner as sender
            if ($this->user()->isPractitioner()) {
                if (!$this->user()->is_published && !$booking) {
                    $validator
                        ->errors()
                        ->add(
                            'text',
                            'You are not allowed to send message to the client not in your clients list'
                        );
                }
            } else {
                if (!$this->user->is_published && !$booking) {
                    $validator
                        ->errors()
                        ->add(
                            'text',
                            'You are not allowed to send message to this practitioner'
                        );
                }
            }
        });
    }
}
