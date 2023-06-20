<?php
namespace App\Libs\Helpers;

use Wevelope\Wevelope\Misc\FullPath as ParentObject;

class FullPath extends ParentObject
{
    public function __construct($root = null, $list = [], $filename = null, $ds = DIRECTORY_SEPARATOR)
    {
        parent::__construct($root ?? public_path(), $list, $filename, $ds);
    }

    public static function tmp($filename = null)
    {
        $list = ['tmp'];
        $fullpath = new self(null, $list, $filename);

        return $fullpath->getPath();
    }

    public static function production($filename = null)
    {
        $list = ['uploads', 'production'];
        $fullpath = new self(null, $list, $filename);

        return $fullpath->getPath();
    }

    public static function activity($filename = null)
    {
        $list = ['uploads', 'production', 'activity'];
        $fullpath = new self(null, $list, $filename);

        return $fullpath->getPath();
    }

    public static function signature($filename = null)
    {
        $list = ['uploads', 'signature'];
        $fullpath = new self(null, $list, $filename);

        return $fullpath->getPath();
    }

    public static function item($filename = null)
    {
        $list = ['uploads', 'item'];
        $fullpath = new self(null, $list, $filename);

        return $fullpath->getPath();
    }

    public static function post($filename = null)
    {
        $list = ['uploads', 'post'];
        $fullpath = new self(null, $list, $filename);

        return $fullpath->getPath();
    }

    public static function person($filename = null)
    {
        $list = ['uploads', 'person'];
        $fullpath = new self(null, $list, $filename);

        return $fullpath->getPath();
    }

    public static function config($filename = null)
    {
        $list = ['uploads', 'config'];
        $fullpath = new self(null, $list, $filename);

        return $fullpath->getPath();
    }

    public static function popUp($filename = null)
    {
        $list = ['uploads', 'pop_up'];
        $fullpath = new self(null, $list, $filename);

        return $fullpath->getPath();
    }

    public static function quotation($filename = null)
    {
        $list = ['uploads', 'quotation'];
        $fullpath = new self(null, $list, $filename);

        return $fullpath->getPath();
    }
}