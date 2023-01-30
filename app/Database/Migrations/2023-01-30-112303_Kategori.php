<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Kategori extends Migration
{
    public function up()
	{
		$this->forge->addField([
			'id_kategori'          => [
				'type'           => 'INT',
				'constraint'     => 5,
				'unsigned'       => true,
				'auto_increment' => true
			],
			'nama_kategori'       => [
				'type'           => 'VARCHAR',
				'constraint'     => 255,
				'null'           => false,
			],
			'created_at datetime default current_timestamp',
			'updated_at datetime default current_timestamp on update current_timestamp',
		]);

		$this->forge->addKey('id_kategori', TRUE);
		$this->forge->createTable('tb_kategori', TRUE);
	}

	public function down()
	{
		$this->forge->dropTable('tb_kategori');
	}
}
