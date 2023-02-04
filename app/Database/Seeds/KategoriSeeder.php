<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class KategoriSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'nama_kategori' => 'kategori A',
            ],
            [
                'nama_kategori' => 'kategori B',
            ],
            [
                'nama_kategori' => 'kategori C',
            ],
            [
                'nama_kategori' => 'kategori D',
            ],
            [
                'nama_kategori' => 'kategori E',
            ],
            [
                'nama_kategori' => 'kategori F',
            ],
            [
                'nama_kategori' => 'kategori G',
            ],
            [
                'nama_kategori' => 'kategori H',
            ],
            [
                'nama_kategori' => 'kategori I',
            ],
            [
                'nama_kategori' => 'kategori J',
            ],
            [
                'nama_kategori' => 'kategori K',
            ],
            [
                'nama_kategori' => 'kategori L',
            ],
            [
                'nama_kategori' => 'kategori M',
            ],
            [
                'nama_kategori' => 'kategori N',
            ],
        ];
        $this->db->table('tb_kategori')->insertBatch($data);
    }
}
