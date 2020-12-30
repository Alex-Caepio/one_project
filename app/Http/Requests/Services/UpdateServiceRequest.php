<?php


namespace App\Http\Requests\Services;


use App\Models\Service;
use App\Http\Requests\Request;
use Illuminate\Support\Facades\Auth;

class UpdateServiceRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::user()->isPractitioner();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $service = Service::find($this->service);
        $isPublished = $this->getBoolFromRequest('is_published');
        $url = $this->get('url') ?? to_url($this->get('title'));
        $this->getInputSource()->set('url', $url);


            return [
                'title' => 'required|string|min:5|max:100',
                'description' => 'string|min:5|max:1000',
                'is_published' => 'bool',
                'introduction' => 'required|string|min:5|max:500',
                'url' => 'required|url|unique:services,url' . $service ? ',' . $service->id : '',
                'service_type_id' => 'required|exists:service_types,id',
                'image_url' => 'url',
                'icon_url' => 'url',
            ];


    }


}
