<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\DB;

use App\Models\Category;

class ConfigSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'key' => 'xendit_api_key',
                'value' =>  env('XENDIT_API_KEY'),
                'created_at' => new \DateTime,
                'updated_at' => new \DateTime
            ],
            [
                'key' => 'is_debug',
                'value' =>  1,
                'created_at' => new \DateTime,
                'updated_at' => new \DateTime
            ]
        ];

        DB::table('config')->insert($data);
    }
}