<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin=User::create([
            'first_name'    =>'Admin',
            'middle_name'   =>'Admin',
            'last_name'     =>'Admin',
            'username'      =>'admin',
            'email'         =>'admin@example.com',
            'phone_number'  =>'0748730956',
            'document_type'=>'NATIONAL_ID',
            'document_number'=>'12653764',
            'nationality'   =>'KENYAN',//Upload photo
            'password'      =>Hash::make('123456'),
            'status'        =>'ACTIVE',
            'is_phone_number_confirmed'=>true,
            'is_email_address_confirmed'=>true,
            'iprs_status'=>'SUCCESS'
        ]);
        $admin->assignRole("ADMIN");
    }
}
