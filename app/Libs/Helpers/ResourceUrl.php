<?php
namespace App\Libs\Helpers;

use Wevelope\Wevelope\Misc\ResourceUrl as ParentObject;

// Get full path of resource
class ResourceUrl extends ParentObject
{
    public function __construct($host, $path = [], $filename = null) {
        // If empty, get from ENV
        if(empty($host))
            $host = env('APP_URL');

        parent::__construct($host, $path, $filename);
    }

    public function isFileExists()
    {
        $ds = DIRECTORY_SEPARATOR;
        // Check if $x folder is exists
        $path = implode($ds, $this->getPath()) . $ds . $this->getFilename();

        return file_exists($path);
    }

    public static function tmp($filename)
    {
        $path = ['tmp'];
        $url = new self(null, $path, $filename);

        return $url->getUrl();
    }

    public static function item($filename)
    {
        $path = ['images'];
        $urlDefault = new self(null, $path, 'gift-card-default.png');

        $url = $urlDefault;
        if(!empty($filename) && $this->isFileExists()) {
            $path = ['uploads', 'item'];
            $url = new self(null, $path, $filename);
        }

        return $url->getUrl();

    }

    public static function logo($filename)
    {
        $path = ['uploads', 'logo'];
        $url = new self(null, $path, $filename);

        return $url->getUrl();
    }

    public static function config($filename)
    {
        $path = ['uploads', 'config'];
        $url = new self(null, $path, $filename);

        return $url->getUrl();
    }

    public static function popUp($filename)
    {
        $path = ['uploads', 'pop_up'];
        $url = new self(null, $path, $filename);

        return $url->getUrl();
    }

    public static function quotation($filename)
    {
        $path = ['uploads', 'quotation'];
        $url = new self(null, $path, $filename);

        return $url->getUrl();
    }
}
