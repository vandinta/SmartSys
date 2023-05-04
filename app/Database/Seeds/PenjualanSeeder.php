<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PenjualanSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'user_id' => 1,
                'nama_penjualan' => 'penjualan 1',
                'total_harga' => 120000,
            ],
            [
                'user_id' => 1,
                'nama_penjualan' => 'penjualan 2',
                'total_harga' => 120000,
            ],
            [
                'user_id' => 1,
                'nama_penjualan' => 'penjualan 3',
                'total_harga' => 120000,
            ],
            [
                'user_id' => 1,
                'nama_penjualan' => 'penjualan 4',
                'total_harga' => 120000,
            ],
            [
                'user_id' => 1,
                'nama_penjualan' => 'penjualan 5',
                'total_harga' => 120000,
            ],
        ];
        $this->db->table('tb_penjualan')->insertBatch($data);
    }
}
