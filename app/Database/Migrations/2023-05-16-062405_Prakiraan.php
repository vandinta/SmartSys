<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Prakiraan extends Migration
{
    public function up()
	{
		$this->forge->addField([
			'id_barang'          => [
				'type'           => 'INT',
				'constraint'     => 5,
				'unsigned'       => true,
				'null'			=> true,
			],
			'nama_prakiraan'       => [
				'type'           => 'VARCHAR',
				'constraint'     => 255,
				'null'           => false,
			],
			'created_at datetime default current_timestamp',
			'updated_at datetime default current_timestamp on update current_timestamp',
		]);

		$this->forge->addKey('id_prakiraan', TRUE);
		$this->forge->addForeignKey('id_barang', 'tb_barang', 'id_barang', 'CASCADE', 'SET NULL');
		$this->forge->createTable('tb_prakiraan', TRUE);
	}

	public function down()
	{
		$this->forge->dropTable('tb_prakiraan');
	}
}
