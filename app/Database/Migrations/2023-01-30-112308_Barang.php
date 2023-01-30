<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Barang extends Migration
{
    public function up()
	{
		$this->forge->addField([
			'id_barang'          => [
				'type'           => 'INT',
				'constraint'     => 5,
				'unsigned'       => true,
				'auto_increment' => true
			],
            'id_kategori'          => [
				'type'           => 'INT',
				'constraint'     => 5,
				'unsigned'       => true,
				'null'			=> false,
			],
			'nama_barang'       => [
				'type'           => 'VARCHAR',
				'constraint'     => 255,
				'null'           => false,
			],
			'stok_barang'       => [
				'type'           => 'VARCHAR',
				'constraint'     => 5,
				'null'           => false,
			],
            'harga_beli'       => [
				'type'           => 'VARCHAR',
				'constraint'     => 255,
				'null'           => false,
			],
            'harga_jual'       => [
				'type'           => 'VARCHAR',
				'constraint'     => 255,
				'null'           => false,
			],
			'image_barang'      => [
				'type'           => 'VARCHAR',
				'constraint'     => 255,
			],
			'created_at datetime default current_timestamp',
			'updated_at datetime default current_timestamp on update current_timestamp',
		]);

		$this->forge->addKey('id_barang', TRUE);
        $this->forge->addForeignKey('id_kategori', 'tb_kategori', 'id_kategori', 'NO ACTION', 'NO ACTION');
		$this->forge->createTable('tb_barang', TRUE);
	}

	public function down()
	{
		$this->forge->dropTable('tb_barang');
	}
}
