<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

use App\Libs\Repository\Category;
use App\WevelopeLibs\Csv;

use App\Models\Meta;
use App\Models\Category as CategoryModel;

class HakAksesSeeder extends Seeder
{
    public function run()
    {
        $csv = Storage::disk('database')->get('csv/hak_akses.csv');
        $lines = Csv::toArray($csv);

        // ignore first line
        array_shift($lines);

        $category = CategoryModel::whereIn('group_by', ['permission_group', 'permission'])->get();

        $permissionGroup = $category->filter(function($x) {
            return $x->group_by == 'permission_group';
        });

        $permission = $category->filter(function($x) {
            return $x->group_by == 'permission';
        });

        foreach($lines as $x) {
            $fkId = $permissionGroup->firstWhere('label', strtolower($x[0]))->id;
            $permissionId = $permission->firstWhere('name', $x[1])->id;
            Meta::create(
                [
                    'fk_id' => $fkId,
                    'table_name' => 'category',
                    'value' => $permissionId,
                    'key' => 'permission_id'
                ]
            );
        }
    }
}