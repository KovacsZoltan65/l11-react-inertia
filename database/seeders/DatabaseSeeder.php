<?php

namespace Database\Seeders;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;


use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Kovács Zoltán',
            'email' => 'zoltan1_kovacs@msn.com',
            'password' => bcrypt('password'),
            'email_verified_at' => time(),
        ]);
        
        User::factory()->create([
            'name' => 'Völgyes Ildikó',
            'email' => 'volgyes_ildikos@msn.com',
            'password' => bcrypt('password'),
            'email_verified_at' => time(),
        ]);
        
        Project::factory()
                ->count(30)
                ->hasTasks(30)
                ->create();
    }
}
