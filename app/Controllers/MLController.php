<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\MLModel;
use App\Models\OrderModel;
use App\Models\BarangModel;
use Firebase\JWT\JWT;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class MLController extends BaseController
{
    private $session;
    protected $mlmodel;
    protected $ordermodel;
    protected $barangmodel;
    protected $decoded;

    public function __construct()
    {
        helper(['cookie', 'rupiah', 'form']);

        if (!get_cookie("access_token")) {
            return redirect()->to("/");
        }

        $token = get_cookie("access_token");
        $this->decoded = JWT::decode($token, 'JWT_SECRET', ['HS256']);

        $this->session = \Config\Services::session();

        $this->mlmodel = new MLModel();
        $this->ordermodel = new OrderModel();
        $this->barangmodel = new Barangmodel();
    }

    public function index()
    {
        if (!get_cookie("access_token")) {
            return redirect()->to("/");
        }

        $nilai = [
            "menu" => "datamodel",
            "submenu" => " ",
            "title" => "Data Model",
            "model" => $this->mlmodel->orderBy('tb_model.created_at', 'DESC')->findAll(),
            "barang" => $this->barangmodel->findAll()
        ];

        return view("cms/mlmodel/v_mlmodel", $nilai);
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
            "menu" => "datamodel",
            "submenu" => " ",
            "title" => "Tambah Model",
            "barang" => $this->barangmodel->findAll()
        ];

        return view("cms/mlmodel/v_tambahdata", $data);
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
        ];

        $messages = [
            "id_barang" => [
                "required" => "{field} tidak boleh kosong",
            ],
        ];

        if ($this->validate($rules, $messages)) {
            $id_barang = $this->request->getVar("id_barang");
            $nama_barang = $this->barangmodel->select('nama_barang')->where('id_barang', $id_barang)->first();
            $penjualan = $this->ordermodel->select('bulan')->selectSum('jumlah_barang')->where('id_barang', $id_barang)->groupBy('bulan')->findAll();
            // $tgl_awal = $this->ordermodel->selectMin('bulan')->where('id_barang', $id_barang)->first();
            // $tgl_akhir = $this->ordermodel->selectMax('bulan')->where('id_barang', $id_barang)->first();

            $tgl_awal = '1972/01/01';
            $tgl_akhir = '1985/12/01';

            if ($penjualan == null) {
                $this->session->setFlashdata('gagal_tambah', 'Data anda tidak valid');
                return redirect()->to("/datamodel/tambah");
                // $this->session->setFlashdata('gagal_tambah', 'Data anda tidak valid');
                // return redirect()->to("/datamodel");
            }

            $filename = strtolower(str_replace(" ", "_", $nama_barang['nama_barang']));

            $this->exportCsv($penjualan, $filename);

            if (file_exists('machine/' . $filename . '.csv')) {
                $filename = $filename . '.csv';
                $this->createmodel($tgl_awal, $tgl_akhir);
            } else {
                $this->session->setFlashdata('gagal_tambah', 'Data anda tidak valid');
                return redirect()->to("/datamodel/tambah");
            }

            // if (!file_exists('machine/' . $filename . '.csv')) {
            //     $this->session->setFlashdata('gagal_tambah', 'Data anda tidak valid');
            //     return redirect()->to("/datamodel/tambah");
            // }

            // if (file_exists('model/my_model.py')) {
            //     session()->setFlashdata("berhasil_tambah", "Data Barang Berhasil Ditambahkan");
            //     return redirect()->to("/datamodel");
            // } else {
            //     $this->session->setFlashdata('gagal_tambah', 'Data anda tidak valid');
            //     return redirect()->to("/datamodel/tambah");
            // }

            if (file_exists('model/my_model.h5')) {
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

    function createmodel($tgl_awal, $tgl_akhir)
    {
        $filename = 'dataset_produksi_susu';
        $akurasi = 45;
        $py = 'C:/Users/User/AppData/Local/Programs/Python/Python39/python.exe';
        $file = 'c:/xampp/htdocs/SmartSys/mlmodel.py';
        $command = escapeshellcmd("$py $file $filename $tgl_awal $tgl_akhir $akurasi");
        // shell_exec('C:/Users/User/AppData/Local/Programs/Python/Python39/python.exe c:/xampp/htdocs/SmartSys/mlmodel.py');
        // $output = exec('python mlmodel.py');
        // echo $output;
        // $py = escapeshellcmd('python mlmodel.py');
        $hasil = shell_exec($command);
        // $py = escapeshellcmd("python machine/main.py $filename $tgl_awal $tgl_akhir");
        // $hasil = shell_exec($py);
        // return $hasil;
        // echo $hasil;
        // $hasil = shell_exec("python \SmartSys\mlmodel.py");
        // echo $hasil;
        // echo $filename;
        // $output = exec('python mlmodel.py');
        // exec("/usr/bin/python mlmodel.py");
        // echo $output;
        // passthru("python mlmodel.py");
        // system($command, $return);
        // echo $return;
        // $cmd = $command;
        // echo $cmd;
        // exec("$py $file", $output, $return);
        // echo $output;
        // var_dump($hasil);
        // die;
        return $hasil;
    }
}
