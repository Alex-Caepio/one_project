<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Email\SaveEmailTemplate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CustomEmailSaveRequest;
use App\Http\Requests\Request;
use App\Models\CustomEmail;
use App\Transformers\CustomEmailTransformer;
use Illuminate\Support\Facades\Auth;

class CustomEmailController extends Controller {
    public function index(Request $request) {
        $customEmailQuery = CustomEmail::query();
        if ($request->hasSearch()) {
            $customEmailQuery->where('name', 'LIKE', '%' . $request->get('search') . '%');
        }
        $paginator = $customEmailQuery->paginate($request->getLimit());
        $customerEmail = $paginator->getCollection();
        return response(fractal($customerEmail,
                                new CustomEmailTransformer())->parseIncludes($request->getIncludes()))->withPaginationHeaders($paginator);
    }

    public function store(CustomEmailSaveRequest $request) {
        $email = run_action(SaveEmailTemplate::class, $request);
        return fractal($email, new CustomEmailTransformer())->respond();
    }

    public function show(Request $request, CustomEmail $customEmail) {
        return response(fractal($customEmail, new CustomEmailTransformer())->parseIncludes($request->getIncludes()));
    }

    public function update(CustomEmailSaveRequest $request, CustomEmail $customEmail) {
        run_action(SaveEmailTemplate::class, $request, $customEmail);
        return fractal($customEmail, new CustomEmailTransformer())->respond();
    }

    public function destroy(CustomEmail $customEmail) {
        $customEmail->delete();
        return response(null, 204);
    }
}
