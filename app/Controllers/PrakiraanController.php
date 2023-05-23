<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\BarangModel;
use App\Models\OrderModel;
use App\Models\PrakiraanModel;
use App\Models\HasilPrakiraanModel;
use Firebase\JWT\JWT;

class PrakiraanController extends BaseController
{
    private $session;
    protected $barangmodel;
    protected $ordermodel;
    protected $prakiraanmodel;
    protected $hasilprakiraanmodel;
    protected $decoded;

    public function __construct()
    {
        helper(['cookie', 'form', 'bulan_indo']);

        if (!get_cookie("access_token")) {
            return redirect()->to("/");
        }

        $token = get_cookie("access_token");
        $this->decoded = JWT::decode($token, 'JWT_SECRET', ['HS256']);

        $this->session = \Config\Services::session();

        $this->barangmodel = new BarangModel();
        $this->ordermodel = new OrderModel();
        $this->prakiraanmodel = new PrakiraanModel();
        $this->hasilprakiraanmodel = new HasilPrakiraanModel();
    }

    public function index()
    {
        if (!get_cookie("access_token")) {
            return redirect()->to("/");
        }
        
        if ($this->decoded->role == "superadmin") {
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

    public function create()
    {
        if (!get_cookie("access_token")) {
            return redirect()->to("/");
        }

        if ($this->decoded->role == "superadmin") {
            return redirect()->to("/");
        }

        $data = [
            "menu" => "prakiraan",
            "submenu" => " ",
            "title" => "Tambah Prakiraan",
            "barang" => $this->barangmodel->findAll()
        ];

        return view("cms/prakiraan/v_tambahdata", $data);
    }

    public function model()
    {
        if (!get_cookie("access_token")) {
            return redirect()->to("/");
        }

        if ($this->decoded->role == "superadmin") {
            return redirect()->to("/");
        }

        $rules = [
            'id_barang' => 'required',
            'lim_akurasi' => 'required|numeric',
        ];

        $messages = [
            "id_barang" => [
                "required" => "Nama Barang tidak boleh kosong",
            ],
            "lim_akurasi" => [
                "required" => "Limit Nilai Akurasi Tidak Boleh Kosong",
                "numeric" => "Limit Nilai Akurasi harus berisi angka",
            ]
        ];

        if ($this->validate($rules, $messages)) {
            $id_barang = $this->request->getVar("id_barang");
            
        } else {
            $this->session->setFlashdata('gagal_tambah', 'Data anda tidak valid');
            return redirect()->to("/dataprakiraan/tambah");
        }
    }

    public function detail($id)
    {
        if (!get_cookie("access_token")) {
            return redirect()->to("/");
        }

        if ($this->decoded->role == "superadmin") {
            return redirect()->to("/");
        }

        $data_prakiraan = $this->prakiraanmodel->where('id_prakiraan', $id)->first();
        $data_bulan = $this->hasilprakiraanmodel->where('id_prakiraan', $id)->findAll();

        for ($i = 0; $i < count($data_bulan); $i++) {
            $cek[$i] = $this->ordermodel->where('id_barang', $data_prakiraan['id_barang'])->where('bulan', $data_bulan[$i]['bulan'])->findAll();
            if ($cek[$i] == null) {
                $penjualan[$i]['jumlah_barang'] = 0;
            } else {
                $penjualan[$i] = $this->ordermodel->selectSum('jumlah_barang')->where('id_barang', $data_prakiraan['id_barang'])->where('bulan', $data_bulan[$i]['bulan'])->first();
            }
        }

        $nilai = $this->hasilprakiraanmodel->where('id_prakiraan', $id)->findAll();

        $barang = $this->barangmodel->where('id_barang', $data_prakiraan['id_barang'])->first();

        $nilai_perhitungan = $barang['stok_barang'];
        for ($b = 0; $b < count($data_bulan); $b++) {
            $nilai_perhitungan -= $data_bulan[$b]['hasil_prakiraan'];
            if ($nilai_perhitungan < 0) {
                $nilai[$b]['status'] = 'kurang';
                $nilai[$b]['selisih'] = $nilai_perhitungan;
            } elseif ($nilai_perhitungan >= 0 && $nilai_perhitungan <= 30) {
                $nilai[$b]['status'] = 'cukup';
                $nilai[$b]['selisih'] = $nilai_perhitungan;
            } else {
                $nilai[$b]['status'] = 'aman';
                $nilai[$b]['selisih'] = $nilai_perhitungan;
            }
        }

        $data = [
            "menu" => "prakiraan",
            "submenu" => "",
            "title" => "Detail Prakiraan",
            "dataprakiraan" => $this->prakiraanmodel->join('tb_barang', 'tb_barang.id_barang=tb_prakiraan.id_barang')->where('tb_prakiraan.id_prakiraan', $id)->first(),
            "grafik" => $nilai,
            "penjualan" => $penjualan,
            // "status" => $nilai,
        ];

        return view("cms/prakiraan/v_detaildata", $data);
    }
}
