<?php
namespace App\Actions\Admin;

use app\Http\Requests\Admin\UserDestroyRequest;
use App\Models\User;

class DeleteUser {

    public function execute(User $user, UserDestroyRequest $request): void {
        $user->update(['termination_message' => $request->get('message')]);
        $user->delete();
    }
}
