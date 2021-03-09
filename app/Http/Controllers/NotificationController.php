<?php

namespace App\Http\Controllers;

use App\Http\Requests\Request;
use App\Models\Notification;
use App\Transformers\NotificationTransformer;
use Illuminate\Support\Facades\Auth;


class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $query = Notification::where('practitioner_id', Auth::id())
                        ->where('receiver_id', Auth::id())
                        ->with($request->getIncludes());

        $paginator = $query->paginate($request->getLimit());
        $notification  = $paginator->getCollection();

        return response(fractal($notification, new NotificationTransformer())
            ->parseIncludes($request->getIncludes()))
            ->withPaginationHeaders($paginator);
    }
}

