<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PenjualanModel;
use App\Models\BarangModel;
use App\Models\CartModel;
use App\Models\OrderModel;
use Firebase\JWT\JWT;

class PenjualanController extends BaseController
{
    private $session;
    protected $penjualanmodel;
    protected $barangmodel;
    protected $cartmodel;
    protected $ordermodel;
    protected $decoded;

    public function __construct()
    {
        helper(['cookie', 'date', 'tgl_indo', 'rupiah']);

        if (!get_cookie("access_token")) {
            return redirect()->to("/");
        }

        $token = get_cookie("access_token");
        $this->decoded = JWT::decode($token, 'JWT_SECRET', ['HS256']);

        $this->session = \Config\Services::session();

        $this->penjualanmodel = new PenjualanModel();
        $this->barangmodel = new Barangmodel();
        $this->cartmodel = new Cartmodel();
        $this->ordermodel = new Ordermodel();
    }

    public function index()
    {
        if (!get_cookie("access_token")) {
            return redirect()->to("/");
        }

        $delete_all = $this->cartmodel->delete_all();

        $data = [
            "menu" => "datapenjualan",
            "submenu" => "",
            "title" => "Data Penjualan",
            "penjualan" => $this->penjualanmodel->findAll(),
            "validation" => \Config\Services::validation(),
        ];

        return view("cms/penjualan/v_penjualan", $data);
    }

    // public function pencarian_produk()
    // {
    //     var_dump('test');
    //     die;
    // 	$data = $this->barangmodel->pencarian_produk($_REQUEST['keyword']);
    // 	echo json_encode($data);
    // }

    public function create()
    {
        if (!get_cookie("access_token")) {
            return redirect()->to("/");
        }

        if ($this->decoded->role == "superadmin") {
            return redirect()->to("/");
        }

        $cart = $this->cartmodel->findAll();

        $total_harga_raw = 0;
        $total_harga_final = 0;

        if ($cart != null) {
            for ($i = 0; $i < count($cart); $i++) {
                $total_harga_raw += $cart[$i]['jumlah_harga'];
                $total_harga_final = $total_harga_raw;

                $harga['total_harga'] = $total_harga_final;
            }
        } else {
            $harga['total_harga'] = 0;
        }

        $data = [
            "menu" => "datapenjualan",
            "submenu" => " ",
            "title" => "Tambah Data Penjualan",
            "barang" => $this->barangmodel->findAll(),
            "cart" => $this->cartmodel->join('tb_barang', 'tb_barang.id_barang=cart.id_barang', 'left')->findAll(),
            "harga" => $harga,
            "validation" => \Config\Services::validation()
        ];

        return view("cms/penjualan/v_tambahdata", $data);
    }

    public function input_cart()
    {
        if (!get_cookie("access_token")) {
            return redirect()->to("/");
        }

        if ($this->decoded->role == "superadmin") {
            return redirect()->to("/");
        }

        $id = $this->request->getVar("id_barang");

        $get = $this->barangmodel->where('id_barang', $id)->first();

        $data = [
            "id_barang" => $this->request->getVar("id_barang"),
            "harga_beli_barang" => $get['harga_beli'],
            "harga_jual_barang" => $get['harga_jual'],
            "qty" => $this->request->getVar("qty"),
            "jumlah_harga" => $this->request->getVar("jumlah_harga_hide"),
        ];

        $this->cartmodel->save($data);
        return redirect()->to("/datapenjualan/tambah");
    }

    public function delete_cart($id = null)
    {
        $cek = $this->cartmodel->where('id_cart', $id)->first();

        if ($cek != null) {
            $this->cartmodel->delete($id);
        }
    }

    public function save()
    {
        if (!get_cookie("access_token")) {
            return redirect()->to("/");
        }

        if ($this->decoded->role == "superadmin") {
            return redirect()->to("/");
        }

        $cart = $this->cartmodel->findAll();

        if ($cart == null) {
            session()->setFlashdata("gagal_tambah", "Data List Barang Kosong");
            return redirect()->to("/datapenjualan/tambah");
        }

        $t = now('Asia/Jakarta');
        $time = date("Y-m-d H:i:s", $t);
        $tanggal = tgl_indonesia($time);

        $nama_penjualan = "Penjualan Pada Hari " . $tanggal;

        $user_id = $this->decoded->uid;

        $total_harga_raw = 0;
        $total_harga_final = 0;

        if ($cart != null) {
            for ($i = 0; $i < count($cart); $i++) {
                $total_harga_raw += $cart[$i]['jumlah_harga'];
                $total_harga_final = $total_harga_raw;

                $harga = $total_harga_final;
            }
        } else {
            $harga = 0;
        }

        $data_penjualan = [
            "user_id" => $user_id,
            "nama_penjualan" => $nama_penjualan,
            "total_harga" => $harga,
        ];

        $this->penjualanmodel->save($data_penjualan);
        $id_penjualan = $this->penjualanmodel->insertID;

        for ($i = 0; $i < count($cart); $i++) {
            $data_order = [
                "id_penjualan" => $id_penjualan,
                "id_barang" => $cart[$i]['id_barang'],
                "harga_beli_barang" => $cart[$i]['harga_beli_barang'],
                "harga_jual_barang" => $cart[$i]['harga_jual_barang'],
                "jumlah_barang" => $cart[$i]['qty'],
            ];

            $this->ordermodel->save($data_order);

            $cek_barang = $this->barangmodel->where('id_barang', $cart[$i]['id_barang'])->first();
            $cek_barang['stok_barang'] -= $cart[$i]['qty'];
            $data = [
                'stok_barang' => $cek_barang['stok_barang'],
            ];
            $this->barangmodel->update($cart[$i]['id_barang'], $data);
        }

        $delete_all = $this->cartmodel->delete_all();

        session()->setFlashdata("berhasil_tambah", "Data Penjualan Berhasil Ditambahkan");
        return redirect()->to("/datapenjualan");
    }

    public function edit($id)
    {
        if (!get_cookie("access_token")) {
            return redirect()->to("/");
        }

        if ($this->decoded->role == "superadmin") {
            return redirect()->to("/");
        }

        $t = now('Asia/Jakarta');
        $time = date("Y-m-d", $t);

        $data_penjualan = $this->penjualanmodel->where('id_penjualan', $id)->first();
        $wkt_penjualan = strtotime($data_penjualan["created_at"]);
        $waktu_penjualan = date("Y-m-d", $wkt_penjualan);

        if ($waktu_penjualan == $time) {
            $waktu = 1;
        }else{
            $waktu = 0;
        }

        $data = [
            "menu" => "datapenjualan",
            "submenu" => " ",
            "title" => "Detail Data Penjualan",
            "expired" => $waktu,
            "barang" => $this->barangmodel->findAll(),
            "penjualan" => $this->penjualanmodel->where('id_penjualan', $id)->join('users', 'users.user_id=tb_penjualan.user_id', 'left')->first(),
            "order" => $this->ordermodel->where('id_penjualan', $id)->join('tb_barang', 'tb_barang.id_barang=tb_order.id_barang', 'left')->findAll(),
            "validation" => \Config\Services::validation()
        ];

        return view("cms/penjualan/v_editdata", $data);
    }

    public function input_order()
    {
        if (!get_cookie("access_token")) {
            return redirect()->to("/");
        }

        if ($this->decoded->role == "superadmin") {
            return redirect()->to("/");
        }

        $id_penjualan = $this->request->getVar("id_penjualan");

        $t = now('Asia/Jakarta');
        $time = date("Y-m-d", $t);

        $data_penjualan = $this->penjualanmodel->where('id_penjualan', $id_penjualan)->first();
        $wkt_penjualan = strtotime($data_penjualan["created_at"]);
        $waktu_penjualan = date("Y-m-d", $wkt_penjualan);

        if ($waktu_penjualan != $time) {
            return redirect()->to("/datapenjualan/ubah/" . $id_penjualan);
        }

        $id_barang = $this->request->getVar("id_barang");

        $get = $this->barangmodel->where('id_barang', $id_barang)->first();

        $data = [
            "id_penjualan" => $id_penjualan,
            "id_barang" => $id_barang,
            "harga_beli_barang" => $get['harga_beli'],
            "harga_jual_barang" => $get['harga_jual'],
            "jumlah_barang" => $this->request->getVar("qty"),
            "jumlah_harga" => $this->request->getVar("jumlah_harga_barang"),
        ];

        $this->ordermodel->save($data);

        $cek_barang = $this->barangmodel->where('id_barang', $id_barang)->first();
        $cek_barang['stok_barang'] -= $data['jumlah_barang'];
        $data_barang = [
            'stok_barang' => $cek_barang['stok_barang'],
        ];
        $this->barangmodel->update($id_barang, $data_barang);

        $cek_penjualan = $this->penjualanmodel->where('id_penjualan', $id_penjualan)->first();
        $cek_penjualan['total_harga'] += $data['jumlah_harga'];
        $data_penjualan = [
            'total_harga' => $cek_penjualan['total_harga'],
        ];
        $this->penjualanmodel->update($id_penjualan, $data_penjualan);

        session()->setFlashdata("berhasil_tambah_order", "Data Barang Berhasil Ditambahkan");
        return redirect()->to("/datapenjualan/ubah/" . $id_penjualan);
    }

    public function update_penjualan($id)
    {
        if (!get_cookie("access_token")) {
            return redirect()->to("/");
        }

        if ($this->decoded->role == "superadmin") {
            return redirect()->to("/");
        }

        $t = now('Asia/Jakarta');
        $time = date("Y-m-d", $t);

        $data_penjualan = $this->penjualanmodel->where('id_penjualan', $id)->first();
        $wkt_penjualan = strtotime($data_penjualan["created_at"]);
        $waktu_penjualan = date("Y-m-d", $wkt_penjualan);

        if ($waktu_penjualan != $time) {
            return redirect()->to("/datapenjualan/ubah/" . $id);
        }

        $rules = [
            'nama_penjualan' => 'required|max_length[255]'
        ];

        $messages = [
            "nama_penjualan" => [
                "required" => "{field} tidak boleh kosong",
                "max_length" => "{field} maksimal 255 karakter",
            ],
        ];

        if ($this->validate($rules, $messages)) {
            $data = [
                "id_penjualan" => $id,
                "nama_penjualan" => $this->request->getVar("nama_penjualan"),
            ];

            $this->penjualanmodel->save($data);
            session()->setFlashdata("berhasil_diubah", "Data Penjualan Berhasil Ditubah");
            return redirect()->to("/datapenjualan/ubah/" . "/" . $id);
        } else {
            $kesalahan = \Config\Services::validation();
            $this->session->setFlashdata('gagal_diubah', 'Data anda tidak valid');
            return redirect()
                ->to("/databarang/ubah/" . "/" . $id)
                ->with("validation", $kesalahan);
        }
    }

    public function update_order()
    {
        if (!get_cookie("access_token")) {
            return redirect()->to("/");
        }

        if ($this->decoded->role == "superadmin") {
            return redirect()->to("/");
        }

        $id_penjualan = $this->request->getVar("id_penjualan");

        $t = now('Asia/Jakarta');
        $time = date("Y-m-d", $t);

        $data_penjualan = $this->penjualanmodel->where('id_penjualan', $id_penjualan)->first();
        $wkt_penjualan = strtotime($data_penjualan["created_at"]);
        $waktu_penjualan = date("Y-m-d", $wkt_penjualan);

        if ($waktu_penjualan != $time) {
            return redirect()->to("/datapenjualan/ubah/" . $id_penjualan);
        }

        $id_barang = $this->request->getVar("id_barang");
        $id_order = $this->request->getVar("id_order");

        $cek = $this->ordermodel->where('id_order', $id_order)->first();

        $rules = [
            'qty' => 'required'
        ];

        $messages = [
            "qty" => [
                "required" => "{field} tidak boleh kosong",
            ],
        ];

        if ($this->validate($rules, $messages)) {
            $qty = $this->request->getVar("qty");
            $data = [
                "id_order" => $id_order,
                "jumlah_barang" => $qty,
            ];

            $this->ordermodel->save($data);

            if ($cek['jumlah_barang'] > $qty) {
                $selisih = $cek['jumlah_barang'] - $qty;

                $cek_barang = $this->barangmodel->where('id_barang', $id_barang)->first();
                $cek_barang['stok_barang'] = $cek_barang['stok_barang'] + $selisih;
                $data_barang = [
                    'stok_barang' => $cek_barang['stok_barang'],
                ];
                $this->barangmodel->update($id_barang, $data_barang);

                $cek_penjualan = $this->penjualanmodel->where('id_penjualan', $id_penjualan)->first();

                $selisih_harga = $cek['harga_jual_barang'] * $selisih;
                $harga = $cek_penjualan['total_harga'] - $selisih_harga;
                $data_penjualan = [
                    'total_harga' => $harga,
                ];
                $this->penjualanmodel->update($id_penjualan, $data_penjualan);
            } elseif ($cek['jumlah_barang'] < $qty) {
                $selisih = $qty - $cek['jumlah_barang'];

                $cek_barang = $this->barangmodel->where('id_barang', $id_barang)->first();
                $cek_barang['stok_barang'] -= $selisih;
                $data_barang = [
                    'stok_barang' => $cek_barang['stok_barang'],
                ];
                $this->barangmodel->update($id_barang, $data_barang);

                $cek_penjualan = $this->penjualanmodel->where('id_penjualan', $id_penjualan)->first();

                $selisih_harga = $cek['harga_jual_barang'] * $selisih;
                $harga = $cek_penjualan['total_harga'] + $selisih_harga;
                $data_penjualan = [
                    'total_harga' => $harga,
                ];
                $this->penjualanmodel->update($id_penjualan, $data_penjualan);
            }

            session()->setFlashdata("berhasil_diubah_order", "Data Penjualan Berhasil Ditubah");
            return redirect()->to("/datapenjualan/ubah/" . "/" . $id_penjualan);
        } else {
            $kesalahan = \Config\Services::validation();
            $this->session->setFlashdata('gagal_diubah', 'Data anda tidak valid');
            return redirect()
                ->to("/databarang/ubah/" . "/" . $id_penjualan)
                ->with("validation", $kesalahan);
        }
    }

    public function delete_order($id = null)
    {
        $cek = $this->ordermodel->where('id_order', $id)->join('tb_barang', 'tb_barang.id_barang=tb_order.id_barang', 'left')->first();

        $t = now('Asia/Jakarta');
        $time = date("Y-m-d", $t);

        $data_penjualan = $this->penjualanmodel->where('id_penjualan', $cek['id_penjualan'])->first();
        $wkt_penjualan = strtotime($data_penjualan["created_at"]);
        $waktu_penjualan = date("Y-m-d", $wkt_penjualan);

        if ($waktu_penjualan != $time) {
            return redirect()->to("/datapenjualan/ubah/" . $cek['id_penjualan']);
        }

        if ($cek != null) {
            $cek_barang = $this->barangmodel->where('id_barang', $cek['id_barang'])->first();
            $cek_barang['stok_barang'] = $cek_barang['stok_barang'] + $cek['jumlah_barang'];
            $data_barang = [
                'stok_barang' => $cek_barang['stok_barang'],
            ];
            $this->barangmodel->update($cek['id_barang'], $data_barang);

            $cek_penjualan = $this->penjualanmodel->where('id_penjualan', $cek['id_penjualan'])->first();

            $selisih_harga = $cek['harga_jual_barang'] * $cek['jumlah_barang'];
            $harga = $cek_penjualan['total_harga'] - $selisih_harga;
            $data_penjualan = [
                'total_harga' => $harga,
            ];
            $this->penjualanmodel->update($cek['id_penjualan'], $data_penjualan);
            if ($this->ordermodel->delete($id)) {
                return $this->response->setJSON([
                    'error' => false,
                    'message' => 'Data ' . $cek['nama_barang'] . ' Berhasil Dihapus!'
                ]);
            }
        }
    }

    public function delete($id = null)
    {
        $cek = $this->penjualanmodel->where('id_penjualan', $id)->first();

        if ($this->penjualanmodel->delete($id)) {
            return $this->response->setJSON([
                'error' => false,
                'message' => 'Data ' . $cek['nama_penjualan'] . ' Berhasil Dihapus!'
            ]);
        }
    }
}
