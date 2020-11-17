<?php

namespace App\Http\Requests\Services;

use App\Models\Service;
use Illuminate\Foundation\Http\FormRequest;

class StoreServiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
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
            'url'=> 'url',
            'service_type_id' => 'required|exists:service_types,id'
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }
            $url       = $this->get('url') ?? to_url($this->get('name'));
            $fieldName = $this->get('url') ? 'url' : 'name';

            if (Service::where('url', $url)->exists()) {
                $validator->errors()->add(
                    $fieldName,
                    "The slug {$url} is not unique! Please, chose the different {$fieldName}."
                );
            }
        });
    }
}
