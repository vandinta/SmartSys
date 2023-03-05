<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class BarangSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'id_kategori' => 1,
                'nama_barang' => 'Barang A',
                'stok_barang' => 20,
                'harga_beli' => 18000,
                'harga_jual' => 20000,
                'image_barang' => 'testing.jpg',
            ],
            [
                'id_kategori' => 2,
                'nama_barang' => 'Barang B',
                'stok_barang' => 20,
                'harga_beli' => 19000,
                'harga_jual' => 21000,
                'image_barang' => 'testing.jpg',
            ],
            [
                'id_kategori' => 1,
                'nama_barang' => 'Barang C',
                'stok_barang' => 20,
                'harga_beli' => 20000,
                'harga_jual' => 22000,
                'image_barang' => 'testing.jpg',
            ],
            [
                'id_kategori' => 2,
                'nama_barang' => 'Barang D',
                'stok_barang' => 20,
                'harga_beli' => 21000,
                'harga_jual' => 23000,
                'image_barang' => 'testing.jpg',
            ],
            [
                'id_kategori' => 1,
                'nama_barang' => 'Barang E',
                'stok_barang' => 20,
                'harga_beli' => 22000,
                'harga_jual' => 24000,
                'image_barang' => 'testing.jpg',
            ],
            [
                'id_kategori' => 2,
                'nama_barang' => 'Barang F',
                'stok_barang' => 20,
                'harga_beli' => 23000,
                'harga_jual' => 25000,
                'image_barang' => 'testing.jpg',
            ],
        ];
        $this->db->table('tb_barang')->insertBatch($data);
    }
}
