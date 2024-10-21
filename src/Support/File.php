<?php

namespace FilippoToso\ResourcePermissions\Support;

use Carbon\Carbon;
use ErrorException;
use FilesystemIterator;

class File
{
    public static function remember($path, $ttl, $callback)
    {
        if (is_readable($path) && filemtime($path) >= Carbon::now()->subSeconds($ttl)->timestamp) {
            return include($path);
        }

        $content = $callback();

        static::save($path, $content);

        return $content;
    }

    public static function load($path, $default = [], $ttl = null)
    {
        if (!is_readable($path)) {
            return $default;
        }

        return include($path);
    }

    public static function save($path, $content)
    {
        @mkdir(dirname($path), 0755, true);

        file_put_contents($path, '<?php return ' . var_export($content, true) . ';');
    }

    /** @see Illuminate\Filesystem\Filesystem */
    public static function delete($paths)
    {
        $paths = is_array($paths) ? $paths : func_get_args();

        $success = true;

        foreach ($paths as $path) {
            try {
                if (@unlink($path)) {
                    clearstatcache(false, $path);
                } else {
                    $success = false;
                }
            } catch (ErrorException) {
                $success = false;
            }
        }

        return $success;
    }

    /** @see Illuminate\Filesystem\Filesystem */
    public static function deleteDirectory($directory, $preserve = false)
    {
        if (! is_dir($directory)) {
            return false;
        }

        $items = new FilesystemIterator($directory);

        foreach ($items as $item) {
            if ($item->isDir() && ! $item->isLink()) {
                static::deleteDirectory($item->getPathname());
            } else {
                static::delete($item->getPathname());
            }
        }

        unset($items);

        if (! $preserve) {
            @rmdir($directory);
        }

        return true;
    }
}
