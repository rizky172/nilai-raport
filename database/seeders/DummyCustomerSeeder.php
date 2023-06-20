<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory;

use App\Models\Person as Model;
use App\Models\Category;
use App\Libs\Repository\Customer;

class DummyCustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Factory::create();

        $category = Category::where('group_by', 'person')
                            ->where('name', 'customer')
                            ->first();

        $limit = 100;
        $data = [];
        for ($i = 0; $i < $limit; $i++) {

            $row = new Model;
            $row->person_category_id = $category->id;
            $row->name = $faker->name;
            $row->email = $faker->email;
            $row->notes = $faker->text;
            $row->due_date = $faker->date();

            $repo = new Customer($row);

            $repo->save();
        }
    }
}
