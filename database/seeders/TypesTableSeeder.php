<?php

namespace Database\Seeders;

use App\Models\Type;
use Illuminate\Database\Seeder;

class TypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Type::query()->firstOrCreate([
            'slug' => 'deposit'
        ]);

        Type::query()->firstOrCreate([
            'slug' => 'withdraw'
        ]);

        Type::query()->firstOrCreate([
            'slug' => 'transfer'
        ]);
    }
}
