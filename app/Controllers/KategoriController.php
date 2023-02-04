<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\KategoriModel;
use Firebase\JWT\JWT;

class KategoriController extends BaseController
{
    private $session;
    protected $kategorimodel;
    protected $decoded;

    public function __construct()
    {
        helper('cookie');
        
        if (!get_cookie("access_token")) {
            return redirect()->to("/");
        }
        
        $token = get_cookie("access_token");
        $this->decoded = JWT::decode($token, 'JWT_SECRET', ['HS256']);
        
        $this->session = \Config\Services::session();

        $this->kategorimodel = new KategoriModel();
    }

    public function index()
    {
        if (!get_cookie("access_token")) {
            return redirect()->to("/");
        }

        $nilai = [
            "menu" => "masterdata",
            "submenu" => "datakategori",
            "title" => "Data Kategori",
            "kategori" => $this->kategorimodel->findAll()
        ];

        return view("cms/kategori/v_kategori", $nilai);
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
            "menu" => "masterdata",
            "submenu" => "datakategori",
            "title" => "Tambah Data Kategori",
            "validation" => \Config\Services::validation()
        ];

        return view("cms/kategori/v_tambahdata", $data);
    }

    public function save()
    {
        if (!get_cookie("access_token")) {
            return redirect()->to("/");
        }

        // $key = getenv('TOKEN_SECRET');
        // $token = get_cookie("access_token");
        // $decoded = JWT::decode($token, $key, ['HS256']);

        if ($this->decoded->role == "superadmin") {
            return redirect()->to("/");
        }

        // $rules = [
        //     "nama_kategori" => [
        //         "rules" => "required|max_length[2]",
        //         "errors" => [
        //             "required" => "{field} harus diisi.",
        //             "max_length" => "{field} maksimal 2 karakter.",
        //         ],
        //     ]
        // ];

        // if(!$validation){
        //     $kesalahan = \Config\Services::validation();
        //     dd($kesalahan);
        // }

        $rules = [
            'nama_kategori' => 'required|max_length[2]'
        ];

        $messages = [
            "nama_kategori" => [
                "required" => "{field} tidak boleh kosong",
                "max_length" => "{field} maksimal 2 karakter",
            ]
        ];

        // $data = [
        //     "nama_kategori" => $this->request->getVar("nama_kategori"),
        // ];

        if ($this->validate($rules, $messages)) {
            $data = [
                "nama_kategori" => $this->request->getVar("nama_kategori"),
            ];

            $this->kategorimodel->save($data);
            session()->setFlashdata("berhasil_tambah", "Data Kategori Berhasil Ditambahkan");
            return redirect()->to("/datakategori");
        }
        $this->session->setFlashdata('gagal_tambah', 'Data anda tidak valid');
        $kesalahan = $this->validator;
        return redirect()
            ->to("/datakategori/tambah")
            ->withInput()
            ->with("validation", $kesalahan);
    }

    public function edit($id)
    {
        if (!get_cookie("access_token")) {
            return redirect()->to("/");
        }

        if ($this->decoded->role == "superadmin") {
            return redirect()->to("/");
        }

        $data = [
            "menu" => "masterdata",
            "submenu" => "datakategori",
            "title" => "Ubah Data Kategori",
            "kategori" => $this->kategorimodel->where('id_kategori', $id)->first(),
            "validation" => \Config\Services::validation()
        ];

        return view("cms/kategori/v_editdata", $data);
    }

    public function update($id)
    {
        if (!get_cookie("access_token")) {
            return redirect()->to("/");
        }

        if ($this->decoded->role == "superadmin") {
            return redirect()->to("/");
        }

        $validation = $this->validate([
            "nama_kategori" => [
                "rules" => "required|max_length[2]",
                "errors" => [
                    "required" => "{field} harus diisi.",
                    "max_length" => "{field} maksimal 2 karakter.",
                ],
            ]
        ]);

        // $data = [
        //     "id_kategori" => $id,
        //     "nama_kategori" => $this->request->getVar("nama_kategori")
        // ];

        if ($validation) {
            $data = [
                "id_kategori" => $id,
                "nama_kategori" => $this->request->getVar("nama_kategori")
            ];

            $this->kategorimodel->save($data);
            session()->setFlashdata("berhasil_diubah", "Data Kategori Berhasil Ditubah");
            return redirect()->to("/datakategori/ubah/" . "/" . $id);
        } else {
            $kesalahan = \Config\Services::validation();
            $this->session->setFlashdata('gagal_diubah', 'Data anda tidak valid');
            return redirect()
                ->to("/datakategori/ubah/" . "/" . $id)
                ->withInput()
                ->with("validation", $kesalahan);
        }
    }

    public function delete($id = null)
    {
        $cek = $this->kategorimodel->where('id_kategori', $id)->first();

        if ($this->kategorimodel->delete($id)) {
            return $this->response->setJSON([
                'error' => false,
                'message' => 'Data Kategori ' . $cek['nama_kategori'] . ' Berhasil Dihapus!'
            ]);
        }
    }
}
