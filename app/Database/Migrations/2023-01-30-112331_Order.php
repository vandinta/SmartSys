<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Order extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'id_order'          => [
				'type'           => 'INT',
				'constraint'     => 5,
				'unsigned'       => true,
				'auto_increment' => true
			],
			'id_penjualan'          => [
				'type'           => 'INT',
				'constraint'     => 5,
				'unsigned'       => true,
				'null'			=> false,
			],
			'id_barang'          => [
				'type'           => 'INT',
				'constraint'     => 5,
				'unsigned'       => true,
				'null'			=> true,
			],
			'jumlah_barang'       => [
				'type'           => 'VARCHAR',
				'constraint'     => 255,
				'null'           => false,
			],
			'harga_beli_barang'       => [
				'type'           => 'VARCHAR',
				'constraint'     => 255,
				'null'           => false,
			],
			'harga_jual_barang'       => [
				'type'           => 'VARCHAR',
				'constraint'     => 255,
				'null'           => false,
			],
			'bulan'       => [
				'type'           => 'DATE',
				'null'           => false,
			],
			'created_at datetime default current_timestamp',
			'updated_at datetime default current_timestamp on update current_timestamp',
		]);

		$this->forge->addKey('id_order', TRUE);
		$this->forge->addForeignKey('id_penjualan', 'tb_penjualan', 'id_penjualan', 'CASCADE', 'CASCADE');
		$this->forge->addForeignKey('id_barang', 'tb_barang', 'id_barang', 'CASCADE', 'SET NULL');
		$this->forge->createTable('tb_order', TRUE);
	}

	public function down()
	{
		$this->forge->dropTable('tb_order');
	}
}
