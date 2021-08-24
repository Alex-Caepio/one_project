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
        $data['section_2_textarea'] =
            isset($data['section_2_textarea']) && !empty($data['section_2_textarea']) ? strip_tags(
                $data['section_2_textarea']
            ) : null;
        $data['section_4_textarea'] =
            isset($data['section_4_textarea']) && !empty($data['section_4_textarea']) ? strip_tags(
                $data['section_4_textarea']
            ) : null;
        $data['section_6_textarea'] =
            isset($data['section_6_textarea']) && !empty($data['section_6_textarea']) ? strip_tags(
                $data['section_6_textarea']
            ) : null;
        $data['section_9_textarea'] =
            isset($data['section_9_textarea']) && !empty($data['section_9_textarea']) ? strip_tags(
                $data['section_9_textarea']
            ) : null;
        return $data;
    }
}
