<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class HasilPrakiraan extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'id_hasil_prakiraan'          => [
				'type'           => 'INT',
				'constraint'     => 5,
				'unsigned'       => true,
				'auto_increment' => true
			],
			'id_prakiraan'          => [
				'type'           => 'INT',
				'constraint'     => 5,
				'unsigned'       => true,
				'null'			=> false,
			],
			'bulan'       => [
				'type'           => 'DATE',
				'null'           => false,
			],
			'hasil_prakiraan'       => [
				'type'           => 'VARCHAR',
				'constraint'     => 255,
				'null'           => false,
			],
			'created_at datetime default current_timestamp',
			'updated_at datetime default current_timestamp on update current_timestamp',
		]);

		$this->forge->addKey('id_hasil_prakiraan', TRUE);
		$this->forge->addForeignKey('id_prakiraan', 'tb_prakiraan', 'id_prakiraan', 'CASCADE', 'CASCADE');
		$this->forge->createTable('tb_hasil_prakiraan', TRUE);
	}

	public function down()
	{
		$this->forge->dropTable('tb_hasil_prakiraan');
	}
}
