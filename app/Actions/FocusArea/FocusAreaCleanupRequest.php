<?php


namespace App\Actions\FocusArea;


use App\Http\Requests\Request;
use App\Models\Discipline;

class FocusAreaCleanupRequest
{
    public function execute(Request $request): array
    {
        $data = $request->all();
        $data['slug'] = $data['slug'] ?? to_url($data['name']);
        return $data;
    }
}
