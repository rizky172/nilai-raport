<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(CategorySeeder::class);
        // $this->call(ConfigSeeder::class);
        $this->createDummy();
        $this->call(UserSeeder::class);
    }

    public function createDummy()
    {
        $this->call(HakAksesGuruSeeder::class);
        $this->call(HakAksesSiswaSeeder::class);
    }
}
