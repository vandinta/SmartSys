<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Penjualan extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'id_penjualan'          => [
				'type'           => 'INT',
				'constraint'     => 5,
				'unsigned'       => true,
				'auto_increment' => true
			],
			'user_id'          => [
				'type'           => 'INT',
				'constraint'     => 5,
				'unsigned'       => true,
				'null'			=> true,
			],
			'total_harga'       => [
				'type'           => 'VARCHAR',
				'constraint'     => 255,
				'null'           => false,
			],
			'created_at datetime default current_timestamp',
			'updated_at datetime default current_timestamp on update current_timestamp',
		]);

		$this->forge->addKey('id_penjualan', TRUE);
		$this->forge->addForeignKey('user_id', 'users', 'user_id', 'CASCADE', 'SET NULL');
		$this->forge->createTable('tb_penjualan', TRUE);
	}

	public function down()
	{
		$this->forge->dropTable('tb_penjualan');
	}
}
