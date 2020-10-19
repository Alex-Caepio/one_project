<?php

namespace App\Http\Requests\Disciplines;

use App\Models\Discipline;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreDisciplineRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
//        $serviceId = $this->route('services');
//
//        return Service::where('id', $serviceId)
//            ->where('user_id', Auth::id())->exists();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'         => 'string|min:5',
            'introduction' => 'min:5',
            'description'  => 'min:5',
            'url'          => 'url',
            'banner_url'   => 'min:5',
            'icon_url'     => 'url',
            'is_published' =>' boolean',
        ];
    }
}
