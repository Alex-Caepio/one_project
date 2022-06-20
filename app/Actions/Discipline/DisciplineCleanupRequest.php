<?php


namespace App\Actions\Discipline;


use App\Http\Requests\Request;
use App\Models\Discipline;

class DisciplineCleanupRequest
{
    public function execute(Request $request): array
    {
        $data = $request->all();
        $data['slug'] = $data['slug'] ?? to_url($data['name']);
        return $data;
    }
}
