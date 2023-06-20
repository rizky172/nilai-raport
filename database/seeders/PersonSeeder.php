<?php

use Illuminate\Database\Seeder;

use App\WevelopeLibs\Csv;

use App\Category;

class PersonSeeder extends Seeder
{
    public function run()
    {
        // $category = Category::where('group_by', 'person')->get();

        $csv = Storage::disk('database')->get('csv/person.csv');
        $lines = Csv::toArray($csv);

        // ignore first line
        array_shift($lines);
        foreach ($lines as $x) {
            if (!empty($x)) {
                // $rows[] = [
                $rows = [
                    'id' => (int)$x[0],
                    'ref_no' => (int) $x[1],
                    'name' => $x[2],
                    'is_supplier' => $x[3],
                    'created_at' => new \DateTime,
                    'updated_at' => new \DateTime
                ];
                DB::table('person')->insert($rows);
            }
        }

        // DB::table('person')->insert($rows);
    }
}