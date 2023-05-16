<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\BarangModel;
use App\Models\PrakiraanModel;
use App\Models\HasilPrakiraanModel;
use Firebase\JWT\JWT;

class PrakiraanController extends BaseController
{
    private $session;
    protected $barangmodel;
    protected $prakiraanmodel;
    protected $hasilprakiraanmodel;
    protected $decoded;

    public function __construct()
    {
        helper(['cookie', 'form']);

        if (!get_cookie("access_token")) {
            return redirect()->to("/");
        }

        $token = get_cookie("access_token");
        $this->decoded = JWT::decode($token, 'JWT_SECRET', ['HS256']);

        $this->session = \Config\Services::session();

        $this->barangmodel = new BarangModel();
        $this->prakiraanmodel = new PrakiraanModel();
        $this->hasilprakiraanmodel = new HasilPrakiraanModel();
    }

    public function index()
    {
        if (!get_cookie("access_token")) {
            return redirect()->to("/");
        }

        $nilai = [
            "menu" => "prakiraan",
            "submenu" => "",
            "title" => "Data Prakiraan",
            "prakiraan" => $this->prakiraanmodel->join('tb_barang', 'tb_barang.id_barang=tb_prakiraan.id_barang', 'left')->orderBy('tb_barang.created_at', 'DESC')->orderBy('tb_prakiraan.created_at', 'DESC')->findAll(),
        ];

        return view("cms/prakiraan/v_prakiraan", $nilai);
    }
}
