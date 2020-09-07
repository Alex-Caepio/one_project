<?php

namespace App\Http\Requests\Services;

use App\Models\Service;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreServiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $serviceId = $this->route('services');

        return Service::where('id', $serviceId)
            ->where('user_id', Auth::id())->exists();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title'=>'min:5|max:200',
            'description'=>'string|min:5',
            'is_published'=>'boolean',
            'introduction'=>'min:5',
            'url'=> 'url'
        ];
    }
}
