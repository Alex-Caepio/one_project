<?php

namespace App\Http\Requests\Services;

use App\Http\Requests\Request;
use App\Models\Service;
use Illuminate\Support\Facades\Auth;

class CopyServiceRequest extends Request {
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return !Auth::user()->isFullyRestricted();
    }

}
