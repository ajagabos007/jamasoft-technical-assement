<?php

namespace Database\Seeders;

use App\Models\Vote;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VoteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $votes = Vote::factory()->count(20)->make();

        Vote::upsert(
            values: $votes->toArray(),
            uniqueBy: ['user_id', 'votable_id', 'votable_type'],
            update: ['user_id']
        );
    }
}
