<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Discipline\DisciplineCleanupRequest;
use App\Actions\Discipline\DisciplineSaveRelationsRequest;
use App\Http\Requests\Admin\DisciplineUpdateRequest;
use App\Models\Discipline;
use App\Http\Requests\Request;
use App\Http\Controllers\Controller;
use App\Traits\hasMediaItems;
use App\Transformers\DisciplineTransformer;
use App\Http\Requests\Admin\DisciplineStoreRequest;
use App\Http\Requests\Admin\DisciplinePublishRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DisciplineController extends Controller
{
    use hasMediaItems;
    public function index(Request $request)
    {
        $query = Discipline::query();

        if ($request->hasOrderBy()) {
            $order = $request->getOrderBy();
            $query->orderBy($order['column'], $order['direction']);
        }

        if ($request->hasSearch()) {
            $search = $request->search();

            $query->where(
                function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('introduction', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                }
            );
        }

        $isPublished = $request->getBoolFromRequest('is_published');
        if ($isPublished !== null) {
            $query->where('is_published', $isPublished);
        }

        $includes  = $request->getIncludes();
        $paginator = $query->with($includes)
            ->paginate($request->getLimit());

        $discipline = $paginator->getCollection();

        return response(fractal($discipline, new DisciplineTransformer())
            ->parseIncludes($includes)->toArray())
            ->withPaginationHeaders($paginator);
    }

    public function store(DisciplineStoreRequest $request)
    {
        $data = run_action(DisciplineCleanupRequest::class, $request);
        $discipline  = Discipline::create($data);

        run_action(DisciplineSaveRelationsRequest::class, $discipline, $request);

        return fractal($discipline, new DisciplineTransformer())
            ->parseIncludes($request->getIncludes())
            ->respond();
    }

    public function show(Discipline $discipline, Request $request)
    {
        return fractal($discipline, new DisciplineTransformer())->parseIncludes($request->getIncludes())
            ->toArray();
    }

    public function update(DisciplineUpdateRequest $request, Discipline $discipline)
    {

        $discipline->update(run_action(DisciplineCleanupRequest::class, $request));

        run_action(DisciplineSaveRelationsRequest::class, $discipline, $request);

        return fractal($discipline, new DisciplineTransformer())
            ->parseIncludes($request->getIncludes())
            ->respond();
    }

    public function unpublish(Discipline $discipline)
    {
        $discipline->forceFill([
            'is_published' => false,
        ]);
        $discipline->update();

        return response(null, 204);
    }

    public function publish(Discipline $discipline, DisciplinePublishRequest $request)
    {
        $discipline->forceFill([
            'is_published' => true,
        ]);
        $discipline->update();

        return response(null, 204);
    }

    public function destroy(Discipline $discipline)
    {
        DB::beginTransaction();
        $discipline->services()->detach();
        $discipline->articles()->detach();
        $discipline->featured_services()->detach();
        $discipline->featured_practitioners()->detach();
        $discipline->practitioners()->detach();
        $discipline->focus_areas()->detach();
        $discipline->related_disciplines()->detach();

        $discipline->media_images()->delete();
        $discipline->media_videos()->delete();
        $discipline->media_files()->delete();
        $discipline->delete();
        DB::commit();

        return response(null, 204);
    }
}
