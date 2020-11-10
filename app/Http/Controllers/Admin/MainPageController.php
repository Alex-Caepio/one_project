<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\MainPageUpdateRequest;
use App\Http\Controllers\Controller;
use App\Models\MainPage;
use App\Transformers\MainPageTransformer;


class MainPageController extends Controller
{
    public function index()
    {
        return fractal(MainPage::first(), new MainPageTransformer())->respond();
    }

    public function update(MainPageUpdateRequest $request)
    {
        $mainPage = MainPage::first();
        $mainPage
            ? $mainPage->update($request->all())
            : $mainPage->create($request->all());


        if ($request->filled('featured_focus_areas')) {
            $mainPage->featured_focus_areas()->sync($request->get('featured_focus_areas'));
        }
        if ($request->filled('featured_disciplines')) {
            $mainPage->featured_disciplines()->sync($request->get('featured_disciplines'));
        }
        if ($request->filled('featured_practitioners')) {
            $mainPage->featured_practitioners()->sync($request->get('featured_practitioners'));
        }
        if ($request->filled('featured_services')) {
            $mainPage->featured_services()->sync($request->get('featured_services'));
        }

        return fractal($mainPage->first(), new MainPageTransformer())->respond();
    }
}
