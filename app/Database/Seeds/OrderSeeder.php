<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'id_penjualan' => 1,
                'id_barang' => 1,
                'jumlah_barang' => 2,
                'harga_beli_barang' => 18000,
                'harga_jual_barang' => 20000,
            ],
            [
                'id_penjualan' => 1,
                'id_barang' => 1,
                'jumlah_barang' => 2,
                'harga_beli_barang' => 18000,
                'harga_jual_barang' => 20000,
            ],
            [
                'id_penjualan' => 1,
                'id_barang' => 1,
                'jumlah_barang' => 2,
                'harga_beli_barang' => 18000,
                'harga_jual_barang' => 20000,
            ],
            [
                'id_penjualan' => 1,
                'id_barang' => 1,
                'jumlah_barang' => 2,
                'harga_beli_barang' => 18000,
                'harga_jual_barang' => 20000,
            ],
            [
                'id_penjualan' => 1,
                'id_barang' => 1,
                'jumlah_barang' => 2,
                'harga_beli_barang' => 18000,
                'harga_jual_barang' => 20000,
            ],
            [
                'id_penjualan' => 1,
                'id_barang' => 1,
                'jumlah_barang' => 2,
                'harga_beli_barang' => 18000,
                'harga_jual_barang' => 20000,
            ],
            [
                'id_penjualan' => 1,
                'id_barang' => 1,
                'jumlah_barang' => 2,
                'harga_beli_barang' => 18000,
                'harga_jual_barang' => 20000,
            ],
            [
                'id_penjualan' => 2,
                'id_barang' => 1,
                'jumlah_barang' => 2,
                'harga_beli_barang' => 18000,
                'harga_jual_barang' => 20000,
            ],
            [
                'id_penjualan' => 2,
                'id_barang' => 1,
                'jumlah_barang' => 2,
                'harga_beli_barang' => 18000,
                'harga_jual_barang' => 20000,
            ],
            [
                'id_penjualan' => 2,
                'id_barang' => 1,
                'jumlah_barang' => 2,
                'harga_beli_barang' => 18000,
                'harga_jual_barang' => 20000,
            ],
        ];
        $this->db->table('tb_order')->insertBatch($data);
    }
}
