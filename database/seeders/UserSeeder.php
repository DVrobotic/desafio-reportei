<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::factory()->count(5)->create();
        User::updateOrCreate([
            'name' => 'Admin',
            'email' => 'admin@admin.com.br',
            'type' => 1,
        ],[
            'name' => 'Admin',
            'email' => 'admin@admin.com.br',
            'email_verified_at' => now(),
            'dateBirth' => now(),
            'cpf' => "000.000.000-00",
            'password' => bcrypt('123456'),
            'type' => 1,
        ]);
    }
}
