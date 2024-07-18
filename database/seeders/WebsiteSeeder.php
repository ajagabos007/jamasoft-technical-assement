<?php

namespace Database\Seeders;

use App\Models\Website;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WebsiteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $websites = Website::factory()->count(20)->make();

        Website::upsert(
            values: $websites->toArray(),
            uniqueBy: ['url'],
            update: ['name', 'description']
        );
    }
}
