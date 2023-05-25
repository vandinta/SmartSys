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
        helper(['cookie', 'date', 'form', 'bulan_indo']);

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

        // $t = now('Asia/Jakarta');
        // $bulan_sekarang = date("Y-m", $t);
        // $bulan = $bulan_sekarang . '-01';

        // $tanggal = [];
        // for ($i=0; $i < 6; $i++) { 
        //     $bulan = date("Y-m-01", strtotime("+1 months", strtotime($bulan)));
        //     array_push($tanggal,$bulan);
        // }

        // foreach ($tanggal as $tgl) {
        //     echo $tgl . ' ';
        // }
        // die;

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

        if ($this->decoded->role != "admin") {
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
            
            $waktu =[];
            $bulan_start = $bulan_sekarang . '-01';
            $bulan_start = date("Y-m-01", strtotime("-14 months", strtotime($bulan_start)));
            for ($a=0; $a < 13; $a++) { 
                $bulan_start = date("Y-m-01", strtotime("+1 months", strtotime($bulan_start)));
                $finish["bulan"] = $bulan_start;
                array_push($waktu,$finish);
            }

            var_dump($waktu);
            die;

            for ($b=0; $b < count($waktu); $b++) { 
                $penjualan[$b] = $this->ordermodel->select('bulan')->selectSum('jumlah_barang')->where('id_barang', $id_barang)->where('bulan', $waktu["bulan"])->groupBy('bulan')->findAll();
                // $penjualan = $penjualan[$a];    
                var_dump($penjualan);
                die;
            }
            
            // var_dump($bulan);
            // die;

            for ($i=0; $i < 6; $i++) { 
                $bulan = date("Y-m-01", strtotime("+1 months", strtotime($bulan)));
                $finish["bulan"] = $bulan;
                $finish["jumlah_barang"] = "0";
                array_push($penjualan,$finish);
            }

            var_dump($penjualan);
            die;

            if ($penjualan == null) {
                $this->session->setFlashdata('gagal_tambah', 'Data anda tidak valid');
                return redirect()->to("/datamodel/tambah");
            }

            if ($penjualan < 24) {
                $this->session->setFlashdata('gagal_proses', 'Data anda tidak mencukupi');
                return redirect()->to("/datamodel/tambah");
            }

            $filename = strtolower(str_replace(" ", "_", $nama_barang['nama_barang']));

            $this->exportCsv($penjualan, $filename);

            if (file_exists('machine/' . $filename . '.csv')) {
                $this->createmodel($filename, $id_barang);
            } else {
                $this->session->setFlashdata('gagal_tambah', 'Data anda tidak valid');
                return redirect()->to("/datamodel/tambah");
            }

            if (file_exists('model/model_' . $filename . '.h5')) {
                session()->setFlashdata("berhasil_tambah", "Data Barang Berhasil Ditambahkan");
                return redirect()->to("/datamodel");
            }

            $this->session->setFlashdata('gagal_tambah', 'Data anda tidak valid');
            return redirect()->to("/datamodel/tambah");
        } else {
            $this->session->setFlashdata('gagal_tambah', 'Data anda tidak valid');
            return redirect()->to("/datamodel/tambah");
        }
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
        // $writer->save('php://output');
        // $file = readfile('data_' . $filename . '.csv');

        $writer->save("machine/" . $filename);

        // exit();
    }

    function createmodel($filename, $id_barang)
    {
        $py = 'C:/Users/User/AppData/Local/Programs/Python/Python39/python.exe';
        $file = 'c:/xampp/htdocs/SmartSys/model.py';
        $command = escapeshellcmd("$py $file $filename $id_barang");
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

        $nilai = $this->hasilprakiraanmodel->where('id_prakiraan', $id)->findAll();

        $barang = $this->barangmodel->where('id_barang', $data_prakiraan['id_barang'])->first();

        $nilai_perhitungan = $barang['stok_barang'];
        for ($b = 0; $b < count($data_bulan); $b++) {
            $nilai_perhitungan -= $data_bulan[$b]['hasil_prakiraan'];
            if ($nilai_perhitungan < 0) {
                $nilai[$b]['status'] = 'kurang';
                $nilai[$b]['selisih'] = $nilai_perhitungan;
            } elseif ($nilai_perhitungan >= 0 && $nilai_perhitungan <= 20) {
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
