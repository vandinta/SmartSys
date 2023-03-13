<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Users extends Migration
{
  public function up()
	{
		$this->forge->addField([
			'user_id'          => [
				'type'           => 'INT',
				'constraint'     => 5,
				'unsigned'       => true,
				'auto_increment' => true
			],
			'email'      => [
				'type'           => 'VARCHAR',
				'constraint'     => 100,
			],
			'password' => [
				'type'           => 'VARCHAR',
				'constraint'     => 255,
			],
			'username'       => [
				'type'           => 'VARCHAR',
				'constraint'     => 255,
				'null'           => true,
			],
			'profile_picture'      => [
				'type'           => 'VARCHAR',
				'constraint'     => 255,
				'null'           => true,
			],
			'role'      => [
				'type'          => 'ENUM("admin", "petugas", "superadmin")',
				'default' 		=> 'petugas',
				'null' 			=> false,
			],
			'activation_status'      => [
				'type'           => 'BOOL',
				'default' 		=> 0,
				'null' 			=> false,
			],
			'last_login'       => [
				'type'           => 'DATETIME',
				'null'           => true,
			],
			'created_at datetime default current_timestamp',
			'updated_at datetime default current_timestamp on update current_timestamp',
		]);

		$this->forge->addKey('user_id', TRUE);
		$this->forge->createTable('users', TRUE);
	}

	public function down()
	{
		$this->forge->dropTable('users');
	}
}
