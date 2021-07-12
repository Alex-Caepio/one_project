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
    public function authorize() {
        return $this->user()->id !== $this->user->id;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'text' => 'required|max:1000'
        ];
    }

    public function withValidator($validator) {

        $validator->after(function($validator) {
            // practitioner as sender
            if ($this->user()->isPractitioner()) {
                if (!$this->user()->is_published && !Booking::where('practitioner_id', $this->user()->id)->where('user_id', $this->user->id)->active()->exists()) {
                    $validator
                        ->errors()
                        ->add('text',
                              'You are not allowed to send message to the client not in your clients list');
                }
            } else {
                if (!$this->user->is_published && !Booking::where('practitioner_id', $this->user->id)->where('user_id', $this->user()->id)->active()->exists()) {
                    $validator
                        ->errors()
                        ->add('text',
                              'You are not allowed to send message to this practitioner');
                }
            }
        });
    }
}
