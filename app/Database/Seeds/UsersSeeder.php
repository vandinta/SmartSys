<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UsersSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'email' => 'youberr01@gmail.com',
                'password' => "21232f297a57a5a743894a0e4a801fc3",
                'username' => 'youberr01',
                'profile_picture' => "default.png",
                'role' => 'superadmin',
            ],
            [
                'email' => 'youberrr02@gmail.com',
                'password' => "21232f297a57a5a743894a0e4a801fc3",
                'username' => 'youberrr02',
                'profile_picture' => "default.png",
                'role' => 'admin',
            ],
            [
                'email' => 'huhfmalas@gmail.com',
                'password' => 'afb91ef692fd08c445e8cb1bab2ccf9c',
                'username' => 'huhfmalas',
                'profile_picture' => "default.png",
                'role' => 'petugas',
            ],
        ];
        $this->db->table('users')->insertBatch($data);
    }
}
