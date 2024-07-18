<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if(is_null(User::where('email', 'user@example.com')->first()))
        {
            User::factory()->withPersonalTeam()->create([
                'name' => 'Test User',
                'email' => 'user@example.com',
            ]);
        }

        if(is_null($user = User::where('email', 'admin@example.com')->first()))
        {
            User::factory()->withPersonalTeam()->create([
                'name' => 'Test Admin',
                'email' => 'admin@example.com',
                'is_admin' => true,
            ]);
        }
        else {
            $user->update(['is_admin'=>true]);
        }
    }
}
