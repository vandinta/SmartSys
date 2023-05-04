<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Model extends Migration
{
    public function up()
	{
		$this->forge->addField([
			'id_model'          => [
				'type'           => 'INT',
				'constraint'     => 5,
				'unsigned'       => true,
				'auto_increment' => true
			],
      'id_barang'          => [
				'type'           => 'INT',
				'constraint'     => 5,
				'unsigned'       => true,
				'null'			=> false,
			],
			'nama_model'       => [
				'type'           => 'VARCHAR',
				'constraint'     => 255,
				'null'           => false,
			],
			'nilai_akurasi'       => [
				'type'           => 'VARCHAR',
				'constraint'     => 255,
				'null'           => false,
			],
			'created_at datetime default current_timestamp',
			'updated_at datetime default current_timestamp on update current_timestamp',
		]);

		$this->forge->addKey('id_model', TRUE);
    $this->forge->addForeignKey('id_barang', 'tb_barang', 'id_barang', 'CASCADE', 'CASCADE');
		$this->forge->createTable('tb_model', TRUE);
	}

	public function down()
	{
		$this->forge->dropTable('tb_model');
	}
}
