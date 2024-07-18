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
        $categories = Category::factory()->count(20)->make();

        Category::upsert(
            values: $categories->toArray(),
            uniqueBy: ['name'],
            update: ['description', 'parent_id']
        );
    }
}
