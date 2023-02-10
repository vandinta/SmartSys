<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PenjualanModel;
use App\Models\BarangModel;
use Firebase\JWT\JWT;

class PenjualanController extends BaseController
{
    private $session;
    protected $penjualanmodel;
    protected $barangmodel;
    protected $decoded;

    public function __construct()
    {
        helper(['cookie', 'rupiah']);

        if (!get_cookie("access_token")) {
            return redirect()->to("/");
        }

        $token = get_cookie("access_token");
        $this->decoded = JWT::decode($token, 'JWT_SECRET', ['HS256']);

        $this->session = \Config\Services::session();

        $this->penjualanmodel = new PenjualanModel();
        $this->barangmodel = new Barangmodel();
    }

    public function index()
    {
        if (!get_cookie("access_token")) {
            return redirect()->to("/");
        }

        $data = [
            "menu" => "datapenjualan",
            "submenu" => "",
            "title" => "Data Penjualan",
            "penjualan" => $this->penjualanmodel->findAll(),
            "validation" => \Config\Services::validation(),
        ];

        return view("cms/penjualan/v_penjualan", $data);
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
            "menu" => "datapenjualan",
            "submenu" => " ",
            "title" => "Tambah Data Barang",
            "barang" => $this->barangmodel->findAll(),
            "validation" => \Config\Services::validation()
        ];

        return view("cms/penjualan/v_tambahdata", $data);
    }

    public function save()
    {
        if (!get_cookie("access_token")) {
            return redirect()->to("/");
        }

        if ($this->decoded->role == "superadmin") {
            return redirect()->to("/");
        }

        $rules = [
            'id_kategori' => 'required',
            'nama_barang' => 'required|max_length[255]',
            'stok_barang' => 'required|numeric',
            'harga_beli' => 'required|numeric',
            'harga_jual' => 'required|numeric',
        ];

        $messages = [
            "id_kategori" => [
                "required" => "{field} tidak boleh kosong",
            ],
            "nama_barang" => [
                "required" => "{field} tidak boleh kosong",
                "max_length" => "{field} maksimal 255 karakter",
            ],
            "stok_barang" => [
                "required" => "{field} tidak boleh kosong",
                "numeric" => "{field} harus berisi angka",
            ],
            "harga_beli" => [
                "required" => "{field} tidak boleh kosong",
                "numeric" => "{field} harus berisi angka",
            ],
            "harga_jual" => [
                "required" => "{field} tidak boleh kosong",
                "numeric" => "{field} harus berisi angka",
            ],
            "image_barang" => [
                'uploaded' => '{field} tidak boleh kosong',
                'mime_in' => '{field} Harus Berupa jpg, jpeg, png atau webp',
                'max_size' => 'Ukuran {field} Maksimal 4 MB'
            ],
        ];

        // if ($this->validate($rules, $messages)) {
        //     if ($this->validate($rules_image, $messages_image)) {
        //         $dataimagebarang = $this->request->getFile('image_barang');
        //         if ($dataimagebarang->isValid() && !$dataimagebarang->hasMoved()) {
        //             $imagebarangFileName = $dataimagebarang->getRandomName();
        //             $dataimagebarang->move('assets/image/barang/', $imagebarangFileName);
        //         }

        //         $data = [
        //             "id_kategori" => $this->request->getVar("id_kategori"),
        //             "nama_barang" => $this->request->getVar("nama_barang"),
        //             "stok_barang" => $this->request->getVar("stok_barang"),
        //             "harga_beli" => $this->request->getVar("harga_beli"),
        //             "harga_jual" => $this->request->getVar("harga_jual"),
        //             "image_barang" => $imagebarangFileName
        //         ];

        //         $this->penjualanmodel->save($data);
        //         session()->setFlashdata("berhasil_tambah", "Data Barang Berhasil Ditambahkan");
        //         return redirect()->to("/databarang");
        //     }
        // } else {
        //     $this->session->setFlashdata('gagal_tambah', 'Data anda tidak valid');
        //     $kesalahan = $this->validator;
        //     return redirect()
        //         ->to("/databarang/tambah")
        //         ->withInput()
        //         ->with("validation", $kesalahan);
        // }
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
            "menu" => "datapenjualan",
            "submenu" => " ",
            "title" => "Data Penjualan",
            "penjualan" => $this->penjualanmodel->where('id_penjualan', $id)->first(),
            "validation" => \Config\Services::validation()
        ];

        return view("cms/penjualan/v_editdata", $data);
    }

    public function update($id)
    {
        if (!get_cookie("access_token")) {
            return redirect()->to("/");
        }

        if ($this->decoded->role == "superadmin") {
            return redirect()->to("/");
        }

        $rules = [
            'id_kategori' => 'required',
            'nama_barang' => 'required|max_length[255]',
            'stok_barang' => 'required|numeric',
            'harga_beli' => 'required|numeric',
            'harga_jual' => 'required|numeric',
        ];

        $rules_image = [
            "image_barang" => "uploaded[image_barang]|is_image[image_barang]|mime_in[image_barang,image/jpg,image/jpeg,image/png]|max_size[image_barang,4000]",
        ];

        $messages = [
            "id_kategori" => [
                "required" => "{field} tidak boleh kosong",
            ],
            "nama_barang" => [
                "required" => "{field} tidak boleh kosong",
                "max_length" => "{field} maksimal 255 karakter",
            ],
            "stok_barang" => [
                "required" => "{field} tidak boleh kosong",
                "numeric" => "{field} harus berisi angka",
            ],
            "harga_beli" => [
                "required" => "{field} tidak boleh kosong",
                "numeric" => "{field} harus berisi angka",
            ],
            "harga_jual" => [
                "required" => "{field} tidak boleh kosong",
                "numeric" => "{field} harus berisi angka",
            ]
        ];

        $messages_image = [
            "image_barang" => [
                'uploaded' => '{field} tidak boleh kosong',
                'mime_in' => '{field} Harus Berupa jpg, jpeg, png atau webp',
                'max_size' => 'Ukuran {field} Maksimal 4 MB'
            ],
        ];

        $cek = $this->penjualanmodel->where('id_barang', $id)->first();

        if ($this->validate($rules, $messages)) {
            if ($this->validate($rules_image, $messages_image)) {
                $oldimagebarang = $cek['image_barang'];
                $dataimagebarang = $this->request->getFile('image_barang');
                if ($dataimagebarang->isValid() && !$dataimagebarang->hasMoved()) {
                    if (file_exists("assets/image/barang/" . $oldimagebarang)) {
                        unlink("assets/image/barang/" . $oldimagebarang);
                    }
                    $imagebarangFileName = $dataimagebarang->getRandomName();
                    $dataimagebarang->move('assets/image/barang/', $imagebarangFileName);
                } else {
                    $imagebarangFileName = $oldimagebarang['profile_picture'];
                }

                $data = [
                    "id_barang" => $id,
                    "id_kategori" => $this->request->getVar("id_kategori"),
                    "nama_barang" => $this->request->getVar("nama_barang"),
                    "stok_barang" => $this->request->getVar("stok_barang"),
                    "harga_beli" => $this->request->getVar("harga_beli"),
                    "harga_jual" => $this->request->getVar("harga_jual"),
                    "image_barang" => $imagebarangFileName
                ];

                $this->penjualanmodel->save($data);
                session()->setFlashdata("berhasil_diubah", "Data Barang Berhasil Ditubah");
                return redirect()->to("/databarang/ubah/" . "/" . $id);
            }

            $data = [
                "id_barang" => $id,
                "id_kategori" => $this->request->getVar("id_kategori"),
                "nama_barang" => $this->request->getVar("nama_barang"),
                "stok_barang" => $this->request->getVar("stok_barang"),
                "harga_beli" => $this->request->getVar("harga_beli"),
                "harga_jual" => $this->request->getVar("harga_jual"),
            ];

            $this->penjualanmodel->save($data);
            session()->setFlashdata("berhasil_diubah", "Data Barang Berhasil Ditubah");
            return redirect()->to("/databarang/ubah/" . "/" . $id);
        } else {
            $kesalahan = \Config\Services::validation();
            $this->session->setFlashdata('gagal_diubah', 'Data anda tidak valid');
            return redirect()
                ->to("/databarang/ubah/" . "/" . $id)
                ->with("validation", $kesalahan);
        }
    }

    public function delete($id = null)
    {
        $cek = $this->penjualanmodel->where('id_barang', $id)->first();

        if ($this->penjualanmodel->delete($id)) {
            return $this->response->setJSON([
                'error' => false,
                'message' => 'Data Barang ' . $cek['nama_barang'] . ' Berhasil Dihapus!'
            ]);
        }
    }
}
