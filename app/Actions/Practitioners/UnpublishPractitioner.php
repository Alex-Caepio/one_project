<?php


namespace App\Actions\Practitioners;

use App\Helpers\UserRightsHelper;
use App\Http\Requests\Auth\UnpublishPractitionerRequest;
use App\Models\User;


class UnpublishPractitioner {

    public function execute(User $user, UnpublishPractitionerRequest $request): void {
        $user->is_published = false;
        $user->save();
    }
}
