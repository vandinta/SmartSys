<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\KategoriModel;
use App\Models\CartModel;
use Firebase\JWT\JWT;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Libraries\Pdfgenerator;

class KategoriController extends BaseController
{
    private $session;
    protected $kategorimodel;
    protected $cartmodel;
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
        $this->cartmodel = new Cartmodel();

        $delete_all = $this->cartmodel->delete_all();
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
        if (!get_cookie("access_token")) {
            return redirect()->to("/");
        }

        $cek = $this->kategorimodel->where('id_kategori', $id)->first();

        if ($this->kategorimodel->delete($id)) {
            return $this->response->setJSON([
                'error' => false,
                'message' => 'Data Kategori ' . $cek['nama_kategori'] . ' Berhasil Dihapus!'
            ]);
        }
    }

    public function exportExcel()
    {
        if (!get_cookie("access_token")) {
            return redirect()->to("/");
        }

        $kategori = $this->kategorimodel->findAll();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Nama Kategori');

        $kolom = 2;
        foreach ($kategori as $value) {
            $sheet->setCellValue('A' . $kolom, ($kolom - 1));
            $sheet->setCellValue('B' . $kolom, $value['nama_kategori']);
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

        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformat-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=data-kategori-barang.xlsx');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit();
    }

    public function exportCsv()
    {
        if (!get_cookie("access_token")) {
            return redirect()->to("/");
        }

        $kategori = $this->kategorimodel->findAll();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Nama Kategori');

        $kolom = 2;
        foreach ($kategori as $value) {
            $sheet->setCellValue('A' . $kolom, ($kolom - 1));
            $sheet->setCellValue('B' . $kolom, $value['nama_kategori']);
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
        header('Content-Type: application/vnd.openxmlformat-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=data-kategori-barang.csv');
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

        $file_pdf = 'data-kategori-barang';
        $paper = 'A4';
        $orientation = "portrait";

        $data = [
            "title" => "Data Kategori Barang",
            "kategori" => $this->kategorimodel->findAll(),
        ];

        $html = view("cms/kategori/v_pdf", $data);

        $output = $Pdfgenerator->generate($html, $file_pdf, $paper, $orientation);
        file_put_contents('data-kategori-barang.pdf', $output);
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
                $kategori = $spreadsheet->getActiveSheet()->toArray();
                foreach ($kategori as $key => $value) {
                    if ($key == 0) {
                        continue;
                    }
                    $namakategori = $value[1];
                    $cekdata = $this->kategorimodel->where('nama_kategori', $namakategori)->first();
                    $data = [
                        'nama_kategori' => $namakategori
                    ];
                    if ($cekdata == null) {
                        $this->kategorimodel->insert($data);
                    }
                }
                $this->session->setFlashdata('berhasil_import', 'Data Berhasil Diimport!');
                return redirect()->to("/datakategori");
            } else {
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
                $spreadsheet = $reader->load($file);
                $kategori = $spreadsheet->getActiveSheet()->toArray();
                foreach ($kategori as $key => $value) {
                    if ($key == 0) {
                        continue;
                    }

                    if ($value[1] != 0) {
                        $namakategori = $value[1];
                        $cekdata = $this->kategorimodel->where('nama_kategori', $namakategori)->first();
                        $data = [
                            'nama_kategori' => $namakategori
                        ];
                        if ($cekdata == null) {
                            $this->kategorimodel->insert($data);
                        }
                    } else {
                        $rawkategori = $value[0];
                        $cleankategori = explode(",", $rawkategori);
                        $namakategori = $cleankategori[1];
                        $cekdata = $this->kategorimodel->where('nama_kategori', $namakategori)->first();
                        $data = [
                            'nama_kategori' => $namakategori
                        ];
                        if ($cekdata == null) {
                            $this->kategorimodel->insert($data);
                        }
                    }
                }
                $this->session->setFlashdata('berhasil_import', 'Data Berhasil Diimport!');
                return redirect()->to("/datakategori");
            }
        } else {
            $this->session->setFlashdata('gagal_import', 'Data anda tidak sesuai');
            return redirect()->to("/datakategori");
        }
    }
}
