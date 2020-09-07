<?php


namespace App\Actions\Keyword;


use App\Http\Requests\Request;
use App\Models\Keyword;

class KeywordFilter
{
    public function execute(Request $request)
    {
        $keyword = Keyword::query();
        if ($request->filled('title')) {
            $title = $request->get('title');
            $keyword->where('title', 'like', "%$title%");
        }
        $keyword = $keyword->get();
        return $keyword;
    }
}
