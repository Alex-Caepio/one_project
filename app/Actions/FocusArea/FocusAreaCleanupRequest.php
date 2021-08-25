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
        $data['section_2_textarea'] =
            isset($data['section_2_textarea']) && !empty($data['section_2_textarea']) ? strip_tags(
                $data['section_2_textarea']
            ) : null;
        $data['section_5_textarea'] =
            isset($data['section_5_textarea']) && !empty($data['section_5_textarea']) ? strip_tags(
                $data['section_5_textarea']
            ) : null;
        $data['section_7_textarea'] =
            isset($data['section_7_textarea']) && !empty($data['section_7_textarea']) ? strip_tags(
                $data['section_7_textarea']
            ) : null;
        $data['section_10_textarea'] =
            isset($data['section_10_textarea']) && !empty($data['section_10_textarea']) ? strip_tags(
                $data['section_10_textarea']
            ) : null;
        return $data;
    }
}
