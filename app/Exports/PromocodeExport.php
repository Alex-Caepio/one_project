<?php

namespace App\Exports;

use App\Filters\PromocodeFiltrator;
use App\Http\Requests\Request;
use App\Models\PromotionCode;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class PromocodeExport implements FromCollection, ShouldAutoSize,WithHeadings {

    private $request;

    public function __construct(Request $request) {
        $this->request = $request;
    }

    public function headings(): array {
        return [
            '#',
            'Code',
            'Uses Per Client',
            'Uses Per Code',
            'Promotion ID',
            'Created Date',
            'Updated Date',
            'Deleted Date',
            'Status',
        ];
    }


    public function collection(): Collection {
        $promotionQuery = PromotionCode::query();
        $promotionFilter = new PromocodeFiltrator();
        $promotionFilter->apply($promotionQuery, $this->request);
        return $promotionQuery->get();
    }

}
