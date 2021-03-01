<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Request;
use App\Models\CustomEmail;
use App\Transformers\CustomEmailTransformer;
use Illuminate\Support\Facades\Auth;

class CustomEmailController extends Controller {
    public function index(Request $request) {
        $paginator = CustomEmail::query()->paginate($request->getLimit());
        $customerEmail = $paginator->getCollection();
        return response(fractal($customerEmail,
                                new CustomEmailTransformer())
                            ->parseIncludes($request->getIncludes()))->withPaginationHeaders($paginator);
    }

    public function store(Request $request) {
        $admin = Auth::user();
        $customerEmail = new CustomEmail();
        $customerEmail->forceFill([
                                      'name'       => $request->get('name'),
                                      'user_type'  => $request->get('user_type'),
                                      'from_email' => $admin->email,
                                      'from_title' => $admin->first_name . " " . $admin->last_name,
                                      'subject'    => $request->get('subject'),
                                      'logo'       => $request->get('logo'),
                                      'text'       => $request->get('text'),
                                      'delay'      => $request->get('delay'),
                                  ]);
        $customerEmail->save();
    }

    public function show(Request $request, CustomEmail $customEmail) {
        return response(fractal($customEmail, new CustomEmailTransformer())->parseIncludes($request->getIncludes()));
    }

    public function update(Request $request, CustomEmail $customEmail) {
        $customEmail->update($request->all());
        return fractal($customEmail, new CustomEmailTransformer())->respond();
    }

    public function destroy(CustomEmail $customEmail) {
        $customEmail->delete();
        return response(null, 204);
    }
}
