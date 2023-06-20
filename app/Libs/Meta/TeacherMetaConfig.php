<?php

namespace App\Libs\Meta;

class TeacherMetaConfig extends MetaConfig
{
    const CLASS_ID = 'class_id';
    const MAJOR_ID = 'major_id';
    const LESSON_ID = 'lesson_id';

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
            self::CLASS_ID,
            self::MAJOR_ID,
            self::LESSON_ID
        ];
    }
}
