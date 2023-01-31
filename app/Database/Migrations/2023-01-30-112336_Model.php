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
            'user_id'          => [
				'type'           => 'INT',
				'constraint'     => 5,
				'unsigned'       => true,
				'null'			=> false,
			],
            'id_penjualan'          => [
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
			'created_at datetime default current_timestamp',
			'updated_at datetime default current_timestamp on update current_timestamp',
		]);

		$this->forge->addKey('id_model', TRUE);
        $this->forge->addForeignKey('user_id', 'users', 'user_id', 'NO ACTION', 'NO ACTION');
        $this->forge->addForeignKey('id_penjualan', 'tb_penjualan', 'id_penjualan', 'NO ACTION', 'NO ACTION');
		$this->forge->createTable('tb_model', TRUE);
	}

	public function down()
	{
		$this->forge->dropTable('tb_model');
	}
}
