<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\Request;
use App\Models\Service;
use Illuminate\Support\Facades\Auth;

class UpdateServiceRequest extends Request {
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return $this->admin->is_admin == true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        $isPublished = $this->getBoolFromRequest('is_published');
        $url = $this->get('url') ?? to_url($this->get('title'));
        $this->getInputSource()->set('url', $url);

        if ($isPublished === true) {
            return [
                'title'           => 'required|string|min:5|max:100',
                'description'     => 'nullable|string|min:5|max:1000',
                'is_published'    => 'bool',
                'introduction'    => 'required|string|min:5|max:500',
                'url'             => 'required|url|unique:services,url' . ($this->service ? ',' . $this->service->id : ''),
                'service_type_id' => 'required|exists:service_types,id',
                'image_url'       => 'nullable|url',
                'icon_url'        => 'nullable|url',
            ];
        }
        return [
            'title'           => 'string|min:5|max:100',
            'description'     => 'nullable|string|min:5|max:1000',
            'is_published'    => 'boolean',
            'introduction'    => 'string|min:5|max:500',
            'url'             => 'url',
            'service_type_id' => 'required|exists:service_types,id'
        ];
    }
}
