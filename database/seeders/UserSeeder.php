<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserRole;
use App\Models\UserType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Exception;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            DB::beginTransaction();
            $user = User::create([
                'name' => 'Masum Billah',
                'email' => 'billah@gmail.com',
                'password' => bcrypt('12345678')
            ]);
            $user->roles()->create([
                'name' => UserRole::ROLE_USER,
            ]);
            $user->type()->create([
                'title' => UserType::USER_TYPE_TRIAL,
                'start_date' => now(),
                'end_date' => now()->addDays(7),
            ]);
            $user->userInformation()->create([
                'business_name' => 'Software Development',
                'business_type' => 'IT Solutions',
                'phone_number' => '+8801611525400',
                'country' => 'Bangladesh',
                'address' => 'Banasree, Dhaka 1212',
            ]);
            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();
        }
    }
}
