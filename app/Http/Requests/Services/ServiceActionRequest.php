<?php

namespace App\Http\Requests\Services;

use App\Http\Requests\Request;
use Illuminate\Support\Facades\Auth;

class ServiceActionRequest extends Request {
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        $service = $this->route('service');
        return $service->user_id === Auth::id();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'title'        => 'min:5|max:200',
            'description'  => 'string|min:5',
            'is_published' => 'boolean',
            'introduction' => 'min:5',
            'url'          => 'url'
        ];
    }
}
