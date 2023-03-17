<?php


namespace App\Traits;

use App\Http\Requests\Request;
use App\Models\Keyword;

trait KeywordCollection {

    /**
     * @param \App\Http\Requests\Request $request
     * @return array
     */
    private function collectKeywordModelsFromRequest($request): array {
        $ids = [];
        if ($request->filled('keywords') && is_array($request->get('keywords'))) {
            $keywords = array_unique($request->get('keywords'));
            foreach ($keywords as $keyword) {
                $keyword = Keyword::firstOrCreate(['title' => strtoupper($keyword)]);
                $ids[] = $keyword->id;
            }
        }
        return $ids;
    }

}
