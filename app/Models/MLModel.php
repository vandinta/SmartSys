<?php

namespace App\Models;

use CodeIgniter\Model;

class MLModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'tb_model';
    protected $primaryKey       = 'id_model';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['id_barang', 'nama_model', 'nilai_akurasi', 'created_at', 'updated_at'];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // function get_order($id_barang, $tanggal)
    // {
    //     $builder = $this->db->table('tb_order');
    //     $builder->where(['id_barang' => $id_barang]);
    //     $builder->group_by($tanggal);
    //     $builder->SUM('jumlah_barang');
    //     $query = $builder->get();
    //     return $query->getResultArray();
    // }

    // function get_order($id_barang, $tanggal)
    // {
    //     $this->db->select( 'NAME' );
    //     $this->db->select_sum( 'COUNTER' );
    //     $this->db->from('tb_order');
    //     $this->db->group_by( 'NAME' );
    //     $this->db->limit( 5 );
    //     $query = $this->db->get();
    //     return $query->getResultArray();
    // }
//     SELECT SUM(quantity) AS s, DATE_FORMAT(recdatetime, '%M') AS m
// FROM table_name
// GROUP BY DATE_FORMAT(recdatetime, '%Y-%m')
}
