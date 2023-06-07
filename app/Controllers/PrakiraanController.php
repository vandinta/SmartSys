<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\BarangModel;
use App\Models\OrderModel;
use App\Models\PrakiraanModel;
use App\Models\HasilPrakiraanModel;
use Firebase\JWT\JWT;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

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
        helper(['cookie', 'date', 'form', 'nama_bulan_indo', 'bulan_indo', 'tgl_indo']);

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

        if ($this->decoded->role != "admin") {
            return redirect()->to("/");
        }

        $nilai = [
            "menu" => "prakiraan",
            "submenu" => "",
            "title" => "Data Prediksi Penjualan",
            "prakiraan" => $this->prakiraanmodel->join('tb_barang', 'tb_barang.id_barang=tb_prakiraan.id_barang', 'left')->orderBy('tb_barang.created_at', 'DESC')->orderBy('tb_prakiraan.created_at', 'DESC')->findAll(),
        ];

        return view("cms/prakiraan/v_prakiraan", $nilai);
    }

    public function create()
    {
        if (!get_cookie("access_token")) {
            return redirect()->to("/");
        }

        if ($this->decoded->role != "admin") {
            return redirect()->to("/");
        }

        $data = [
            "menu" => "prakiraan",
            "submenu" => " ",
            "title" => "Tambah Prediksi Penjualan",
            "barang" => $this->barangmodel->findAll()
        ];

        return view("cms/prakiraan/v_tambahdata", $data);
    }

    public function model()
    {
        if (!get_cookie("access_token")) {
            return redirect()->to("/");
        }

        if ($this->decoded->role != "admin") {
            return redirect()->to("/");
        }

        $rules = [
            'id_barang' => 'required',
        ];

        $messages = [
            "id_barang" => [
                "required" => "Nama Barang tidak boleh kosong",
            ],
        ];

        if ($this->validate($rules, $messages)) {
            $id_barang = $this->request->getVar("id_barang");

            $t = now('Asia/Jakarta');
            $bulan_sekarang = date("Y-m", $t);
            $bulan = $bulan_sekarang . '-01';

            $nama_barang = $this->barangmodel->select('nama_barang')->where('id_barang', $id_barang)->first();

            $namabarang = strtolower(str_replace(" ", "_", $nama_barang['nama_barang']));

            if (!file_exists('model/model_' . $namabarang . '.h5')) {
                $this->session->setFlashdata('gagal_diproses', 'Data anda tidak mencukupi');
                return redirect()->to("/dataprakiraan/tambah");
            }

            $cek_penjualan = $this->ordermodel->select('bulan')->selectSum('jumlah_barang')->where('id_barang', $id_barang)->where('bulan !=', $bulan)->groupBy('bulan')->findAll();

            if ($cek_penjualan < 12 || $cek_penjualan == null) {
                $this->session->setFlashdata('gagal_proses', 'Data anda tidak mencukupi');
                return redirect()->to("/dataprakiraan/tambah");
            }

            $waktu = [];
            $bulan_start = $bulan_sekarang . '-01';
            $bulan_start = date("Y-m-01", strtotime("-13 months", strtotime($bulan_start)));
            for ($a = 0; $a < 13; $a++) {
                $bulan_start = date("Y-m-01", strtotime("+1 months", strtotime($bulan_start)));
                $finish["bulan"] = $bulan_start;
                array_push($waktu, $finish);
            }

            for ($b = 0; $b < count($waktu); $b++) {
                $penjualan[$b] = $this->ordermodel->select('bulan')->selectSum('jumlah_barang')->where('id_barang', $id_barang)->where('bulan', $waktu[$b]["bulan"])->groupBy('bulan')->find();
                if ($penjualan[$b] != null) {
                    $penjualan[$b] = $penjualan[$b][0];
                } else {
                    $penjualan[$b]['bulan'] = $waktu[$b]["bulan"];
                    $penjualan[$b]['jumlah_barang'] = 0;
                }
            }

            if ($penjualan == null) {
                $this->session->setFlashdata('gagal_tambah', 'Data anda tidak valid');
                return redirect()->to("/dataprakiraan/tambah");
            }

            if ($penjualan < 12) {
                $this->session->setFlashdata('gagal_proses', 'Data anda tidak mencukupi');
                return redirect()->to("/dataprakiraan/tambah");
            }

            for ($i = 0; $i < 6; $i++) {
                $bulan = date("Y-m-01", strtotime("+1 months", strtotime($bulan)));
                $finish["bulan"] = $bulan;
                $finish["jumlah_barang"] = "0";
                array_push($penjualan, $finish);
            }

            $filename = 'prediksi_' . $namabarang;
            $namamodel = 'model_' . $namabarang;

            $time = date("Y-m-d H:i:s", $t);
            $tanggal = tgl_indonesia($time);

            $namaprediksi = $nama_barang['nama_barang'] . " " . $tanggal;
            $namaprediksi = strtolower(str_replace(" ", "_", $namaprediksi));

            $getidprakiraan = $this->prakiraanmodel->where('id_barang', $id_barang)->first();
            
            if ($getidprakiraan != null) {
                $cek_data = $this->hasilprakiraanmodel->where('id_prakiraan', $getidprakiraan["id_prakiraan"])->findAll();
                if ($cek_data != null) {
                    $deleteall = $this->hasilprakiraanmodel->where('id_prakiraan', $getidprakiraan["id_prakiraan"])->delete();
                }
            }

            $this->exportCsv($penjualan, $filename);

            if (file_exists('dataset/' . $filename . '.csv')) {
                $this->createprediksi($filename, $namamodel, $id_barang, $namaprediksi);
            } else {
                $this->session->setFlashdata('gagal_tambah', 'Data anda tidak valid');
                return redirect()->to("/dataprakiraan/tambah");
            }

            $this->session->setFlashdata("berhasil_tambah", "Data Barang Berhasil Ditambahkan");
            return redirect()->to("/dataprakiraan");
        } else {
            $this->session->setFlashdata('gagal_tambah', 'Data anda tidak valid');
            return redirect()->to("/dataprakiraan/tambah");
        }
    }

    public function updatePrediksi($id)
    {
        if (!get_cookie("access_token")) {
            return redirect()->to("/");
        }

        if ($this->decoded->role != "admin") {
            return redirect()->to("/");
        }

        $id_barang = $id;

        $getidprakiraan = $this->prakiraanmodel->where('id_barang', $id_barang)->first();
        $cek_data = $this->hasilprakiraanmodel->where('id_prakiraan', $getidprakiraan["id_prakiraan"])->findAll();
        
        if ($cek_data != null) {
            $deleteall = $this->hasilprakiraanmodel->where('id_prakiraan', $getidprakiraan["id_prakiraan"])->delete();
        }

        $t = now('Asia/Jakarta');
        $bulan_sekarang = date("Y-m", $t);
        $bulan = $bulan_sekarang . '-01';

        $nama_barang = $this->barangmodel->select('nama_barang')->where('id_barang', $id_barang)->first();

        $namabarang = strtolower(str_replace(" ", "_", $nama_barang['nama_barang']));

        if (!file_exists('model/model_' . $namabarang . '.h5')) {
            $this->session->setFlashdata('gagal_diproses', 'Data anda tidak mencukupi');
            return redirect()->to("/dataprakiraan/tambah");
        }

        $cek_penjualan = $this->ordermodel->select('bulan')->selectSum('jumlah_barang')->where('id_barang', $id_barang)->where('bulan !=', $bulan)->groupBy('bulan')->findAll();

        if ($cek_penjualan < 12 || $cek_penjualan == null) {
            $this->session->setFlashdata('gagal_proses', 'Data anda tidak mencukupi');
            return redirect()->to("/dataprakiraan/tambah");
        }

        $waktu = [];
        $bulan_start = $bulan_sekarang . '-01';
        $bulan_start = date("Y-m-01", strtotime("-13 months", strtotime($bulan_start)));
        for ($a = 0; $a < 13; $a++) {
            $bulan_start = date("Y-m-01", strtotime("+1 months", strtotime($bulan_start)));
            $finish["bulan"] = $bulan_start;
            array_push($waktu, $finish);
        }

        for ($b = 0; $b < count($waktu); $b++) {
            $penjualan[$b] = $this->ordermodel->select('bulan')->selectSum('jumlah_barang')->where('id_barang', $id_barang)->where('bulan', $waktu[$b]["bulan"])->groupBy('bulan')->find();
            $penjualan[$b] = $penjualan[$b][0];
        }

        if ($penjualan == null) {
            $this->session->setFlashdata('gagal_diperbarui', 'Data anda tidak valid');
            return redirect()->to("/dataprakiraan/detail/" . $getidprakiraan["id_prakiraan"]);
        }

        if ($penjualan < 12) {
            $this->session->setFlashdata('gagal_proses', 'Data anda tidak mencukupi');
            return redirect()->to("/dataprakiraan/detail/" . $getidprakiraan["id_prakiraan"]);
        }

        for ($i = 0; $i < 6; $i++) {
            $bulan = date("Y-m-01", strtotime("+1 months", strtotime($bulan)));
            $finish["bulan"] = $bulan;
            $finish["jumlah_barang"] = "0";
            array_push($penjualan, $finish);
        }

        $filename = 'prediksi_' . $namabarang;
        $namamodel = 'model_' . $namabarang;

        $time = date("Y-m-d H:i:s", $t);
        $tanggal = tgl_indonesia($time);

        $namaprediksi = $nama_barang['nama_barang'] . " " . $tanggal;
        $namaprediksi = strtolower(str_replace(" ", "_", $namaprediksi));

        $getidprakiraan = $this->prakiraanmodel->where('id_barang', $id_barang)->first();
        $cek_data = $this->hasilprakiraanmodel->where('id_prakiraan', $getidprakiraan["id_prakiraan"])->findAll();

        if ($cek_data != null) {
            $deleteall = $this->hasilprakiraanmodel->where('id_prakiraan', $getidprakiraan["id_prakiraan"])->delete();
        }

        $this->exportCsv($penjualan, $filename);

        if (file_exists('dataset/' . $filename . '.csv')) {
            $this->createprediksi($filename, $namamodel, $id_barang, $namaprediksi);
        } else {
            $this->session->setFlashdata('gagal_diperbarui', 'Data anda tidak valid');
            return redirect()->to("/dataprakiraan/detail/" . $getidprakiraan["id_prakiraan"]);
        }

        $this->session->setFlashdata("berhasil_diperbarui", "Data Barang Berhasil Ditambahkan");
        return redirect()->to("/dataprakiraan/detail/" . $getidprakiraan["id_prakiraan"]);
    }

    function exportCsv($penjualan, $filename)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'tanggal');
        $sheet->setCellValue('B1', 'pembelian');

        $kolom = 2;
        foreach ($penjualan as $value) {
            $sheet->setCellValue('A' . $kolom, $value["bulan"]);
            $sheet->setCellValue('B' . $kolom, $value["jumlah_barang"]);
            $kolom++;
        }

        $sheet->getStyle('A1:B1')->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle('A1:B1')->getFill()
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
        $sheet->getStyle('A1:B' . ($kolom - 1))->applyFromArray($styleArray);

        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Csv($spreadsheet);
        $filename = $filename . '.csv';
        header('Content-Type: application/vnd.openxmlformat-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $filename);
        header('Cache-Control: max-age=0');

        $writer->save("dataset/" . $filename);
    }

    function createprediksi($filename, $namamodel, $id_barang, $namaprediksi)
    {
        $py = 'C:/Users/User/AppData/Local/Programs/Python/Python39/python.exe';
        $file = 'c:/xampp/htdocs/SmartSys/prakiraan.py';
        $command = escapeshellcmd("$py $file $filename $namamodel $id_barang $namaprediksi");
        $hasil = shell_exec($command);
        return $hasil;
    }

    public function detail($id)
    {
        if (!get_cookie("access_token")) {
            return redirect()->to("/");
        }

        if ($this->decoded->role != "admin") {
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

        $t = now('Asia/Jakarta');
        $bulan_sekarang = date("Y-m", $t);

        $bulan_start = $bulan_sekarang . '-01';
        $bulan_1 = date("Y-m-01", strtotime("-1 months", strtotime($bulan_start)));
        $bulan_2 = date("Y-m-01", strtotime("-2 months", strtotime($bulan_start)));
        $bulan_pra1 = date("Y-m-01", strtotime("+4 months", strtotime($bulan_start)));

        $nilai = $this->hasilprakiraanmodel->where('id_prakiraan', $id)->findAll();
        $barang = $this->barangmodel->where('id_barang', $data_prakiraan['id_barang'])->first();

        for ($c = 0; $c < count($nilai); $c++) {
            if (strtotime($nilai[$c]['bulan']) >= strtotime($bulan_start)) {
                $perbandingan[$c] = $nilai[$c];
            }
        }

        for ($d = 0; $d < count($nilai); $d++) {
            if (strtotime($nilai[$d]['bulan']) >= strtotime($bulan_2) && strtotime($nilai[$d]['bulan']) < strtotime($bulan_pra1)) {
                $grafik[$d] = $nilai[$d];
            }
        }

        $nilai_perhitungan = $barang['stok_barang'];
        $stok = $barang['stok_barang'];

        for ($b = 0; $b < count($data_bulan); $b++) {
            if (strtotime($data_bulan[$b]["bulan"]) >= strtotime($bulan_start)) {
                $nilai_perhitungan -= $data_bulan[$b]['hasil_prakiraan'];
                
                $bulan_sebelumnya = date("Y-m-01", strtotime("-1 months", strtotime($data_bulan[$b]["bulan"])));
                $data_bulan_sebelumnya = $this->hasilprakiraanmodel->select("hasil_prakiraan")->where('bulan', $bulan_sebelumnya)->first();
                
                if ($data_bulan[$b]["bulan"] == $bulan_start) {
                    $pengurangan_stok = 0;
                    $perbandingan[$b]['catatan'] = 1;
                } else {
                    $pengurangan_stok = $data_bulan_sebelumnya["hasil_prakiraan"];
                    $perbandingan[$b]['catatan'] = 0;
                }

                $stok -= $pengurangan_stok;

                if ($stok <= 0) {
                    $stok = 0;
                }

                if ($nilai_perhitungan < 0) {
                    $perbandingan[$b]['status'] = 'kurang';
                    $perbandingan[$b]['stok'] = $stok;
                    $perbandingan[$b]['selisih'] = $nilai_perhitungan;
                } elseif ($nilai_perhitungan >= 0 && $nilai_perhitungan <= 20) {
                    $perbandingan[$b]['status'] = 'cukup';
                    $perbandingan[$b]['stok'] = $stok;
                    $perbandingan[$b]['selisih'] = $nilai_perhitungan;
                } else {
                    $perbandingan[$b]['status'] = 'aman';
                    $perbandingan[$b]['stok'] = $stok;
                    $perbandingan[$b]['selisih'] = $nilai_perhitungan;
                }
            }
        }

        $data = [
            "menu" => "prakiraan",
            "submenu" => "",
            "title" => "Detail Prediksi Penjualan",
            "dataprakiraan" => $this->prakiraanmodel->join('tb_barang', 'tb_barang.id_barang=tb_prakiraan.id_barang')->where('tb_prakiraan.id_prakiraan', $id)->first(),
            "grafik" => $grafik,
            "penjualan" => $penjualan,
            "perbandingan" => $perbandingan,
        ];

        return view("cms/prakiraan/v_detaildata", $data);
    }
}
