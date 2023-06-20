<?php

namespace App\Libs\Meta;

class StudentMetaConfig extends MetaConfig
{
    const CLASS_ID = 'class_id';

    public function __construct($list = [])
    {
        if(empty($list)) {
            $list = $this->generateList();
        }

        parent::__construct($list);
    }

    public static function getIgnoreKeys()
    {
        return [
            'media'
        ];
    }

    private function generateList()
    {
        return [
            self::CLASS_ID
        ];
    }
}
