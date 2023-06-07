<?php

namespace App\Models;

use CodeIgniter\Model;

class PrakiraanModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'tb_prakiraan';
    protected $primaryKey       = 'id_prakiraan';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['id_barang', 'nama_prakiraan', 'created_at', 'updated_at'];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
