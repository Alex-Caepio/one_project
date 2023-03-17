<?php


namespace App\Actions\Email;


use App\Http\Requests\Admin\CustomEmailSaveRequest;
use App\Models\CustomEmail;

class SaveEmailTemplate {

    public function execute(CustomEmailSaveRequest $request, ?CustomEmail $customEmail = null): CustomEmail {
        if ($customEmail === null) {
            $customEmail = new CustomEmail();
        }
        $customEmail->forceFill([
                                    'user_type'     => $request->get('user_type'),
                                    'from_email'    => $request->filled('from_email') ? $request->get('from_email') : config('mail.from.address'),
                                    'from_title'    => $request->filled('from_title') ? $request->get('from_title') : config('mail.from.name'),
                                    'subject'       => $request->get('subject'),
                                    'logo'          => $request->get('logo'),
                                    'logo_filename' => $request->get('logo_filename'),
                                    'text'          => $request->get('text'),
                                    'delay'         => (int)$request->get('delay'),
                                ]);
        $customEmail->save();
        return $customEmail;
    }
}
