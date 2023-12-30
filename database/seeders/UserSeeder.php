<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Make User Admin
        $admin = User::create([
            'name' => 'Rizky Ibrahim',
            'username' => 'rizky98ibrahim',
            'email' => 'rizky98ibrahim@gmail.com',
            'password' => Hash::make('password'),
            'phone_number' => '085932990070',
            'address' => 'Jl. Primadana VII Blok C8 No.28 RT.06 RW.10, Kel. Jatisari, Kec. Jatiasih, Kota Bekasi, Jawa Barat 17426',
            'place_of_birth' => 'Jakarta',
            'date_of_birth' => '1998-06-06',
            'profile_picture' => 'https://avatars.githubusercontent.com/u/56132740?v=4',
            'religion' => 'islam',
            'gender' => 'laki-laki',
            'status' => 'active',
            'email_verified_at' => '2021-06-06 00:00:00',
        ]);

        $admin->assignRole('admin');

        // Make Users
        $user = User::create([
            'name' => 'user',
            'username' => 'user',
            'email' => 'user@localhost.com',
            'password' => Hash::make('user'),
            'phone_number' => '085932990071',
        ]);
        $user->assignRole('user');

        $writter = User::create([
            'name' => 'writter',
            'username' => 'writter',
            'email' => 'writter@localhost.com',
            'password' => Hash::make('writter'),
            'phone_number' => '085932990072',
        ]);
        $writter->assignRole('writter');
    }
}
