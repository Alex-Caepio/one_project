<?php


namespace App\Transformers;


use App\Models\PractitionerCommission;

class PractitionerCommissionTransformer extends Transformer
{
    protected $availableIncludes = [
        'user',
    ];

    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(PractitionerCommission $practitionerCommission)
    {
        return [
            'id'                 => $practitionerCommission->id,
            'practitioner_id'    => $practitionerCommission->practitioner_id,
            'rate'               => $practitionerCommission->rate,
            'commission_on_sale' => $practitionerCommission->commission_on_sale,
            'date_from'          => $practitionerCommission->date_from,
            'date_to'            => $practitionerCommission->date_to,
            'is_dateless'        => $practitionerCommission->is_dateless,
            'created_at'         => $practitionerCommission->created_at,
            'updated_at'         => $practitionerCommission->updated_at,
        ];
    }

    public function includeUser(PractitionerCommission $practitionerCommission)
    {
        return $this->itemOrNull($practitionerCommission->user, new UserTransformer());
    }
}
