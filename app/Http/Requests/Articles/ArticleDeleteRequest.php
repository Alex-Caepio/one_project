<?php
namespace App\Http\Requests\Articles;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\VarDumper\VarDumper;

class ArticleDeleteRequest extends FormRequest {
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        $model = $this->route('article');
        return $this->user()->isPractitioner() && $model->user_id === $this->user()->id;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //
        ];
    }


}
