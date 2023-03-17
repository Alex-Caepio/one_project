<?php

namespace App\Helpers;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Log;

class FilesystemFixNoLock extends Filesystem {

    public function get($path, $lock = false) {
        if ($this->isFile($path)) {
            return $lock ? $this->sharedGet($path) : file_get_contents($path);
        }

        throw new FileNotFoundException("File does not exist at path {$path}.");
    }

    /**
     * Write the contents of a file.
     *
     * @param  string  $path
     * @param  string  $contents
     * @param  bool  $lock
     * @return int|bool
     */
    public function put($path, $contents, $lock = false) {
        return file_put_contents($path, $contents, 0);
    }

}
