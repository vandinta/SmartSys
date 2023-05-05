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
            "title" => "Tambah Data Model",
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

            if ($penjualan == null) {
                $this->session->setFlashdata('gagal_tambah', 'Data anda tidak valid');
                return redirect()->to("/datamodel/tambah");
            }

            $filename = strtolower(str_replace(" ", "_", $nama_barang['nama_barang']));

            $this->exportCsv($penjualan, $filename);

            
            // session()->setFlashdata("berhasil_tambah", "Data Barang Berhasil Ditambahkan");
            return redirect()->to("/datamodel");
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
        $filename = 'data_' . $filename . '.csv';
        header('Content-Type: application/vnd.openxmlformat-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $filename);
        header('Cache-Control: max-age=0');
        // $writer->save('php://output');
        $writer->save("machine/" . $filename);
        // exit();
    }
}
