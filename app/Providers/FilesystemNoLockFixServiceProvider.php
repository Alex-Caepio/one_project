<?php

namespace App\Providers;

use App\Helpers\FilesystemFixNoLock;
use Illuminate\Filesystem\FilesystemServiceProvider;

class FilesystemNoLockFixServiceProvider extends FilesystemServiceProvider {

    /**
     * Register the native filesystem implementation.
     *
     * @return void
     */
    protected function registerNativeFilesystem() {
        $this->app->singleton('files', function() {
            return new FilesystemFixNoLock();
        });
    }


}
