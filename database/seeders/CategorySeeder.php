<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $food = Category::create([
            'name' => 'Food',
            'description' => 'Food Category',
        ]);

        $drink = Category::create([
            'name' => 'Drink',
            'description' => 'Drink Category',
        ]);
    }
}
