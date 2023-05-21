<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PenjualanModel;
use App\Models\BarangModel;
use App\Models\CartModel;
use App\Models\OrderModel;
use Firebase\JWT\JWT;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Libraries\Pdfgenerator;

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
        helper(['cookie', 'date', 'tgl_indo', 'rupiah', 'form']);

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

        $t = now('Asia/Jakarta');
        $time = date("Y-m-d", $t);

        $waktu_awal = $time . " 00:00:01";
        $waktu_akhir = $time . " 23:59:59";

        $delete_all = $this->cartmodel->delete_all();

        $data = [
            "menu" => "datapenjualan",
            "submenu" => "",
            "title" => "Data Penjualan",
            "penjualan" => $this->penjualanmodel->orderBy('created_at', 'DESC')->findAll(),
            "waktu_awal" => $waktu_awal,
            "waktu_akhir" => $waktu_akhir,
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

        $rules = [
            'id_barang' => 'required',
            'qty' => 'required|numeric'
        ];

        $messages = [
            "id_barang" => [
                "required" => "Nama Barang Tidak Boleh Kosong",
            ],
            "qty" => [
                "required" => "QTY Tidak Boleh Kosong",
                "numeric" => "QTY Harus Berisi Angka",
            ]
        ];

        if ($this->validate($rules, $messages)) {
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
        return redirect()->to("/datapenjualan/tambah")->withInput();
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

        $bulan_sekarang = date("Y-m", $t);
        $bulan = $bulan_sekarang . '-01';

        for ($i = 0; $i < count($cart); $i++) {
            $data_order = [
                "id_penjualan" => $id_penjualan,
                "id_barang" => $cart[$i]['id_barang'],
                "harga_beli_barang" => $cart[$i]['harga_beli_barang'],
                "harga_jual_barang" => $cart[$i]['harga_jual_barang'],
                "jumlah_barang" => $cart[$i]['qty'],
                "bulan" => $bulan,
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
        } else {
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

        $rules = [
            'id_barang' => 'required',
            'qty' => 'required|numeric'
        ];

        $messages = [
            "id_barang" => [
                "required" => "Nama Barang Tidak Boleh Kosong",
            ],
            "qty" => [
                "required" => "QTY Tidak Boleh Kosong",
                "numeric" => "QTY Harus Berisi Angka",
            ]
        ];

        if ($this->validate($rules, $messages)) {

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
        return redirect()->to("/datapenjualan/ubah/" . $id_penjualan)->withInput();
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
                "required" => "Nama Penjualan tidak boleh kosong",
                "max_length" => "Nama Penjualan maksimal 255 karakter",
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
            $this->session->setFlashdata('gagal_diubah', 'Data anda tidak valid');
            return redirect()
                ->to("/datapenjualan/ubah/" . "/" . $id)
                ->withInput();
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
                "required" => "QTY Tidak Boleh Kosong",
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
                ->to("/datapenjualan/ubah/" . "/" . $id_penjualan)
                ->withInput();
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
        } else {
            return $this->response->setJSON([
                'error' => true,
                'message' => 'Data ' . $cek['nama_penjualan'] . ' Gagal Dihapus!'
            ]);
        }
    }

    public function exportExcel()
    {
        if (!get_cookie("access_token")) {
            return redirect()->to("/");
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Tanggal Penjualan');
        $sheet->setCellValue('C1', 'Jam Penjualan');
        $sheet->setCellValue('D1', 'Nama Barang');
        $sheet->setCellValue('E1', 'Jumlah Barang');
        $sheet->setCellValue('F1', 'Harga Pembelian');
        $sheet->setCellValue('G1', 'Harga Penjualan');
        $sheet->setCellValue('H1', 'Jumlah');
        $sheet->setCellValue('I1', 'Total Pembelian');
        $kolom = 2;

        $penjualan = $this->penjualanmodel->findAll();
        for ($i = 0; $i < count($penjualan); $i++) {

            $id_penjualan[$i] = $penjualan[$i]['id_penjualan'];

            // waktu pembelian
            $wkt_penjualan[$i] = strtotime($penjualan[$i]["created_at"]);
            $tanggal_penjualan[$i] = date("d-m-Y", $wkt_penjualan[$i]);
            $jam_penjualan[$i] = date("H:i:s", $wkt_penjualan[$i]);

            $order[$i] = $this->ordermodel->where('id_penjualan', $id_penjualan[$i])->join('tb_barang', 'tb_barang.id_barang=tb_order.id_barang', 'left')->findAll();

            for ($q = 0; $q < count($order[$i]); $q++) {
                $id_order[$q] = $order[$i][$q]['id_order'];
                $order_list[$q] = $this->ordermodel->where('id_order', $id_order[$q])->join('tb_barang', 'tb_barang.id_barang=tb_order.id_barang', 'left')->first();

                // hitung jumlah
                $jumlah[$q] = $order_list[$q]["jumlah_barang"] * $order_list[$q]["harga_jual_barang"];

                $sheet->setCellValue('A' . $kolom, ($kolom - 1));
                $sheet->setCellValue('B' . $kolom, $tanggal_penjualan[$i]);
                $sheet->setCellValue('C' . $kolom, $jam_penjualan[$i]);
                $sheet->setCellValue('D' . $kolom, $order_list[$q]['nama_barang']);
                $sheet->setCellValue('E' . $kolom, $order_list[$q]['jumlah_barang']);
                $sheet->setCellValue('F' . $kolom, $order_list[$q]['harga_beli_barang']);
                $sheet->setCellValue('G' . $kolom, $order_list[$q]['harga_jual_barang']);
                $sheet->setCellValue('H' . $kolom, $jumlah[$q]);
                $sheet->setCellValue('I' . $kolom, $penjualan[$i]['total_harga']);
                $kolom++;
            }
        }

        $sheet->getStyle('A1:I1')->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle('A1:I1')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('4040ff');
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ]
            ]
        ];
        $sheet->getStyle('A1:I' . ($kolom - 1))->applyFromArray($styleArray);

        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setAutoSize(true);
        $sheet->getColumnDimension('E')->setAutoSize(true);
        $sheet->getColumnDimension('F')->setAutoSize(true);
        $sheet->getColumnDimension('G')->setAutoSize(true);
        $sheet->getColumnDimension('H')->setAutoSize(true);
        $sheet->getColumnDimension('I')->setAutoSize(true);

        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformat-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=data-penjualan.xlsx');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit();
    }

    public function exportCsv()
    {
        if (!get_cookie("access_token")) {
            return redirect()->to("/");
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Tanggal Penjualan');
        $sheet->setCellValue('C1', 'Nama Barang');
        $sheet->setCellValue('D1', 'Jumlah Barang');
        $sheet->setCellValue('E1', 'Harga Pembelian');
        $sheet->setCellValue('F1', 'Harga Penjualan');
        $sheet->setCellValue('G1', 'Jumlah');
        $sheet->setCellValue('H1', 'Total Pembelian');
        $kolom = 2;

        $penjualan = $this->penjualanmodel->findAll();
        for ($i = 0; $i < count($penjualan); $i++) {

            $id_penjualan[$i] = $penjualan[$i]['id_penjualan'];

            // waktu pembelian
            $wkt_penjualan[$i] = strtotime($penjualan[$i]["created_at"]);
            $waktu_penjualan[$i] = date("d-m-Y H:i:s", $wkt_penjualan[$i]);


            $order[$i] = $this->ordermodel->where('id_penjualan', $id_penjualan[$i])->join('tb_barang', 'tb_barang.id_barang=tb_order.id_barang', 'left')->findAll();

            for ($q = 0; $q < count($order[$i]); $q++) {
                $id_order[$q] = $order[$i][$q]['id_order'];
                $order_list[$q] = $this->ordermodel->where('id_order', $id_order[$q])->join('tb_barang', 'tb_barang.id_barang=tb_order.id_barang', 'left')->first();

                // hitung jumlah
                $jumlah[$q] = $order_list[$q]["jumlah_barang"] * $order_list[$q]["harga_jual_barang"];

                $sheet->setCellValue('A' . $kolom, ($kolom - 1));
                $sheet->setCellValue('B' . $kolom, $waktu_penjualan[$i]);
                $sheet->setCellValue('C' . $kolom, $order_list[$q]['nama_barang']);
                $sheet->setCellValue('D' . $kolom, $order_list[$q]['jumlah_barang']);
                $sheet->setCellValue('E' . $kolom, $order_list[$q]['harga_beli_barang']);
                $sheet->setCellValue('F' . $kolom, $order_list[$q]['harga_jual_barang']);
                $sheet->setCellValue('G' . $kolom, $jumlah[$q]);
                $sheet->setCellValue('H' . $kolom, $penjualan[$i]['total_harga']);
                $kolom++;
            }
        }

        $sheet->getStyle('A1:H1')->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle('A1:H1')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('4040ff');
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ]
            ]
        ];
        $sheet->getStyle('A1:H' . ($kolom - 1))->applyFromArray($styleArray);

        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setAutoSize(true);
        $sheet->getColumnDimension('E')->setAutoSize(true);
        $sheet->getColumnDimension('F')->setAutoSize(true);
        $sheet->getColumnDimension('G')->setAutoSize(true);
        $sheet->getColumnDimension('H')->setAutoSize(true);

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Csv($spreadsheet);
        header('Content-Type: application/vnd.openxmlformat-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=data-penjualan.csv');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit();
    }

    public function exportPdf()
    {
        if (!get_cookie("access_token")) {
            return redirect()->to("/");
        }

        $Pdfgenerator = new Pdfgenerator();

        $file_pdf = 'data-penjualan';
        $paper = 'A4';
        $orientation = "portrait";

        $data = [
            "title" => "Data Penjualan",
            "order" => $this->ordermodel->join('tb_penjualan', 'tb_penjualan.id_penjualan=tb_order.id_penjualan', 'left')->join('tb_barang', 'tb_barang.id_barang=tb_order.id_barang', 'left')->findAll()
        ];

        $html = view("cms/penjualan/v_pdf", $data);

        $output = $Pdfgenerator->generate($html, $file_pdf, $paper, $orientation);
        file_put_contents('data-penjualan.pdf', $output);
        exit();
    }

    public function import()
    {
        if (!get_cookie("access_token")) {
            return redirect()->to("/");
        }

        $file = $this->request->getFile('file_import');
        $extension = $file->getClientExtension();
        if ($extension == 'xlsx' || $extension == 'xls' || $extension == 'csv') {
            if ($extension == 'xlsx' || $extension == 'xls') {
                if ($extension == 'xlsx') {
                    $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
                } else {
                    $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
                }
                $spreadsheet = $reader->load($file);
                $penjualan = $spreadsheet->getActiveSheet()->toArray();
                foreach ($penjualan as $key => $value) {
                    if ($key == 0) {
                        continue;
                    }

                    $tanggal = $value[1];
                    $jam = $value[2];
                    $tanggal_jam = $tanggal . $jam;
                    $tanggal_penjualan = strtotime($tanggal_jam);
                    $waktu_penjualan = date("Y-m-d H:i:s", $tanggal_penjualan);
                    $bulan_sekarang = date("Y-m", $tanggal_penjualan);
                    $bulan = $bulan_sekarang . '-01';
                    $nama_penjualan = "Penjualan Pada Hari " . tgl_indonesia($waktu_penjualan);
                    $cekdatapenjualan = $this->penjualanmodel->where('nama_penjualan', $nama_penjualan)->first();
                    $datapenjualan = [
                        'user_id' => $this->decoded->uid,
                        'nama_penjualan' => $nama_penjualan,
                        'total_harga' => $value[8],
                        'created_at' => $waktu_penjualan
                    ];
                    if ($cekdatapenjualan == null) {
                        $this->penjualanmodel->insert($datapenjualan);
                    }
                    $ceknamapenjualan = $this->penjualanmodel->findAll();
                    for ($i = 0; $i < count($ceknamapenjualan); $i++) {
                        if ($ceknamapenjualan[$i]['nama_penjualan'] == $nama_penjualan) {
                            $primarypenjualan = $ceknamapenjualan[$i]['id_penjualan'];
                        }
                    }
                    $cekbarang = $this->barangmodel->findAll();
                    for ($q = 0; $q < count($cekbarang); $q++) {
                        if ($cekbarang[$q]['nama_barang'] == $value[3]) {
                            $barang = $cekbarang[$q]['id_barang'];
                        }
                    }
                    $cekpenjualanorder = $this->ordermodel->where('id_barang', $barang)->where('id_penjualan', $primarypenjualan)->first();
                    $data = [
                        'id_penjualan' => $primarypenjualan,
                        'id_barang' => $barang,
                        'jumlah_barang' => $value[4],
                        'harga_beli_barang' => $value[5],
                        'harga_jual_barang' => $value[6],
                        'bulan' => $bulan,
                        'created_at' => $waktu_penjualan
                    ];
                    if ($cekpenjualanorder == null) {
                        $this->ordermodel->insert($data);
                    }
                }
                $this->session->setFlashdata('berhasil_import', 'Data Berhasil Diimport!');
                return redirect()->to("/datapenjualan");
            } else {
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
                $spreadsheet = $reader->load($file);
                $penjualan = $spreadsheet->getActiveSheet()->toArray();
                foreach ($penjualan as $key => $value) {
                    if ($key == 0) {
                        continue;
                    }

                    if ($value[1] != 0) {
                        $tanggal = $value[1];
                        $jam = $value[2];
                        $tanggal_jam = $tanggal . $jam;
                        $tanggal_penjualan = strtotime($tanggal_jam);
                        $waktu_penjualan = date("Y-m-d H:i:s", $tanggal_penjualan);
                        $bulan_sekarang = date("Y-m", $tanggal_penjualan);
                        $bulan = $bulan_sekarang . '-01';
                        $nama_penjualan = "Penjualan Pada Hari " . tgl_indonesia($waktu_penjualan);
                        $cekdatapenjualan = $this->penjualanmodel->where('nama_penjualan', $nama_penjualan)->first();
                        $datapenjualan = [
                            'user_id' => $this->decoded->uid,
                            'nama_penjualan' => $nama_penjualan,
                            'total_harga' => $value[8],
                            'created_at' => $waktu_penjualan
                        ];
                        if ($cekdatapenjualan == null) {
                            $this->penjualanmodel->insert($datapenjualan);
                        }
                        $ceknamapenjualan = $this->penjualanmodel->findAll();
                        for ($i = 0; $i < count($ceknamapenjualan); $i++) {
                            if ($ceknamapenjualan[$i]['nama_penjualan'] == $nama_penjualan) {
                                $primarypenjualan = $ceknamapenjualan[$i]['id_penjualan'];
                            }
                        }
                        $cekbarang = $this->barangmodel->findAll();
                        for ($q = 0; $q < count($cekbarang); $q++) {
                            if ($cekbarang[$q]['nama_barang'] == $value[3]) {
                                $barang = $cekbarang[$q]['id_barang'];
                            }
                        }
                        $cekpenjualanorder = $this->ordermodel->where('id_barang', $barang)->where('id_penjualan', $primarypenjualan)->first();
                        $data = [
                            'id_penjualan' => $primarypenjualan,
                            'id_barang' => $barang,
                            'jumlah_barang' => $value[4],
                            'harga_beli_barang' => $value[5],
                            'harga_jual_barang' => $value[6],
                            'bulan' => $bulan,
                            'created_at' => $waktu_penjualan
                        ];
                        if ($cekpenjualanorder == null) {
                            $this->ordermodel->insert($data);
                        }
                    } else {
                        $rawpenjualan = $value[0];
                        $cleanpenjualan = explode(",", $rawpenjualan);
                        $tanggal = $cleanpenjualan[1];
                        $jam = $cleanpenjualan[2];
                        $tanggal_jam = $tanggal . $jam;
                        $tanggal_penjualan = strtotime($tanggal_jam);
                        $waktu_penjualan = date("Y-m-d H:i:s", $tanggal_penjualan);
                        $bulan_sekarang = date("Y-m", $tanggal_penjualan);
                        $bulan = $bulan_sekarang . '-01';
                        $nama_penjualan = "Penjualan Pada Hari " . tgl_indonesia($waktu_penjualan);
                        $cekdatapenjualan = $this->penjualanmodel->where('nama_penjualan', $nama_penjualan)->first();
                        $datapenjualan = [
                            'user_id' => $this->decoded->uid,
                            'nama_penjualan' => $nama_penjualan,
                            'total_harga' => $cleanpenjualan[8],
                            'created_at' => $waktu_penjualan
                        ];
                        if ($cekdatapenjualan == null) {
                            $this->penjualanmodel->insert($datapenjualan);
                        }
                        $ceknamapenjualan = $this->penjualanmodel->findAll();
                        for ($i = 0; $i < count($ceknamapenjualan); $i++) {
                            if ($ceknamapenjualan[$i]['nama_penjualan'] == $nama_penjualan) {
                                $primarypenjualan = $ceknamapenjualan[$i]['id_penjualan'];
                            }
                        }
                        $cekbarang = $this->barangmodel->findAll();
                        for ($q = 0; $q < count($cekbarang); $q++) {
                            if ($cekbarang[$q]['nama_barang'] == $cleanpenjualan[3]) {
                                $barang = $cekbarang[$q]['id_barang'];
                            }
                        }
                        $cekpenjualanorder = $this->ordermodel->where('id_barang', $barang)->where('id_penjualan', $primarypenjualan)->first();
                        $data = [
                            'id_penjualan' => $primarypenjualan,
                            'id_barang' => $barang,
                            'jumlah_barang' => $cleanpenjualan[4],
                            'harga_beli_barang' => $cleanpenjualan[5],
                            'harga_jual_barang' => $cleanpenjualan[6],
                            'bulan' => $bulan,
                            'created_at' => $waktu_penjualan
                        ];
                        if ($cekpenjualanorder == null) {
                            $this->ordermodel->insert($data);
                        }
                    }
                }
                $this->session->setFlashdata('berhasil_import', 'Data Berhasil Diimport!');
                return redirect()->to("/datapenjualan");
            }
        } else {
            $this->session->setFlashdata('gagal_import', 'Data anda tidak sesuai');
            return redirect()->to("/datapenjualan");
        }
    }
}
