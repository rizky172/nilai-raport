<?php

namespace App\Libs\Repository\Logger;

use Wevelope\Wevelope\Parser;

use App\Models\Category;

class TeacherLogger extends AbstractLogger
{
    public function setup()
    {
        parent::setup();

        $this->updateRemovedIndex(['ref_no']);

        $this->updateRenameIndexArray([
            'class_id' => 'Kelas',
            'major_id' => 'Jurusan',
            'semester_id' => 'Semester',
            'nis' => 'NIS',
            'name' => 'Nama',
            'email' => 'Email',
            'phone' => 'No. HP',
            'address' => 'Alamat'
        ]);

        $categoryList = Category::whereIn('group_by', ['class','major','semester'])->get();
        $mapper = $categoryList->map(function($x) {
            $value = $x['label'];
            return ['id' => $x['id'], 'value' => $value];
        });
        $parser = new Parser\IdToStringParser($mapper->toArray());
        $this->addParser('class_id', $parser);
        $this->addParser('major_id', $parser);
        $this->addParser('semester_id', $parser);
    }
}
