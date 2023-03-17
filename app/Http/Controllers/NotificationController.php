<?php

namespace App\Http\Controllers;

use App\Http\Requests\Request;
use App\Models\Notification;
use App\Transformers\NotificationTransformer;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    private NotificationTransformer $transformer;

    public function __construct(NotificationTransformer $transformer)
    {
        $this->transformer = $transformer;
    }

    public function index(Request $request): Response
    {
        $paginator = $this->getPractitionerNotifications(Auth::id(), $request->getIncludes(), $request->getLimit());

        return $this->buildResponseByNotifications($paginator, $request);
    }

    private function getPractitionerNotifications(
        int $userId,
        array $relations,
        ?int $limit = null
    ): LengthAwarePaginator {
        return $this->getNotifications('practitioner_id', $userId, $relations, $limit);
    }

    public function clientNotifications(Request $request): Response
    {
        $paginator = $this->getClientNotifications(Auth::id(), $request->getIncludes(), $request->getLimit());

        return $this->buildResponseByNotifications($paginator, $request);
    }

    private function getClientNotifications(int $userId, array $relations, ?int $limit = null): LengthAwarePaginator
    {
        return $this->getNotifications('client_id', $userId, $relations, $limit);
    }

    public function markAsRead(Notification $notification): Response
    {
        $notification->read_at = now();
        $notification->save();

        return response(null, 204);
    }

    private function getNotifications(
        string $keyColumn,
        int $userId,
        array $relations,
        ?int $limit = null
    ): LengthAwarePaginator {
        return Notification::query()
            ->where($keyColumn, $userId)
            ->where('receiver_id', $userId)
            ->whereNull('read_at')
            ->orderByDesc('id')
            ->with($relations)
            ->paginate($limit)
        ;
    }

    private function buildResponseByNotifications(LengthAwarePaginator $paginator, Request $request): Response
    {
        $notification = $paginator->getCollection();

        return response(fractal($notification, $this->transformer)->parseIncludes($request->getIncludes()))
            ->withPaginationHeaders($paginator)
        ;
    }
}
