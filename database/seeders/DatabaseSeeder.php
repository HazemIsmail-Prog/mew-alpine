<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        $this->call([
            ContractSeeder::class,
            StakeholderSeeder::class,
            // DocumentSeeder::class,
            // StepSeeder::class,
            // AttachmentSeeder::class,
            TagSeeder::class,
        ]);

        User::factory()->create([
            'name' => 'حازم',
            'username' => 'hazem',
            'stakeholder_id' => 1,
            'role' => 'superAdmin',
        ]);
        User::factory()->create([
            'name' => 'Mahmoud',
            'username' => 'mahmoud',
            'stakeholder_id' => 1,
            'role' => 'admin',
        ]);
        User::factory()->create([
            'name' => 'User',
            'username' => 'user',
            'stakeholder_id' => 1,
            'role' => 'user',
        ]);
        
    }
}
