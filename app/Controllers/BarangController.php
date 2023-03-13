<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\BarangModel;
use App\Models\KategoriModel;
use App\Models\CartModel;
use Firebase\JWT\JWT;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Libraries\Pdfgenerator;

class BarangController extends BaseController
{
    private $session;
    protected $barangmodel;
    protected $kategorimodel;
    protected $cartmodel;
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

        $this->barangmodel = new BarangModel();
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
            "submenu" => "databarang",
            "title" => "Data Barang",
            "barang" => $this->barangmodel->join('tb_kategori', 'tb_kategori.id_kategori=tb_barang.id_kategori', 'left')->orderBy('created_at', 'DESC')->findAll(),
            // "barangrelasi" => $this->barangmodel->join('tb_kategori', 'tb_kategori.id_kategori=tb_barang.id_kategori')->findAll()
        ];

        return view("cms/barang/v_barang", $nilai);
    }

    // public function show($id)
    // {
    //     if (!get_cookie("access_token")) {
    //         return redirect()->to("/");
    //     }

    //     $nilai = [
    //         "menu" => "masterdata",
    //         "submenu" => "databarang",
    //         "title" => "Data Barang",
    //         "barang" => $this->barangmodel->where('id_barang', $id)->first()
    //     ];

    //     return view("cms/barang/v_detaildata", $nilai);
    // }

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
            "submenu" => "databarang",
            "title" => "Tambah Data Barang",
            "kategori" => $this->kategorimodel->findAll(),
            "validation" => \Config\Services::validation()
        ];

        return view("cms/barang/v_tambahdata", $data);
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
            ],
            "image_barang" => [
                'uploaded' => '{field} tidak boleh kosong',
                'mime_in' => '{field} Harus Berupa jpg, jpeg, png atau webp',
                'max_size' => 'Ukuran {field} Maksimal 4 MB'
            ],
        ];
        $messages_image = [
            "image_barang" => [
                'uploaded' => '{field} tidak boleh kosong',
                'mime_in' => '{field} Harus Berupa jpg, jpeg, png atau webp',
                'max_size' => 'Ukuran {field} Maksimal 4 MB'
            ],
        ];

        if ($this->validate($rules, $messages)) {
            if ($this->validate($rules_image, $messages_image)) {
                $dataimagebarang = $this->request->getFile('image_barang');
                if ($dataimagebarang->isValid() && !$dataimagebarang->hasMoved()) {
                    $imagebarangFileName = $dataimagebarang->getRandomName();
                    $dataimagebarang->move('assets/image/barang/', $imagebarangFileName);
                }

                $data = [
                    "id_kategori" => $this->request->getVar("id_kategori"),
                    "nama_barang" => $this->request->getVar("nama_barang"),
                    "stok_barang" => $this->request->getVar("stok_barang"),
                    "harga_beli" => $this->request->getVar("harga_beli"),
                    "harga_jual" => $this->request->getVar("harga_jual"),
                    "image_barang" => $imagebarangFileName
                ];

                $this->barangmodel->save($data);
                session()->setFlashdata("berhasil_tambah", "Data Barang Berhasil Ditambahkan");
                return redirect()->to("/databarang");
            }
            // } else {
            //     // echo "gagal";
            //     // var_dump($this->session);
            //     // die;
            //     $this->session->setFlashdata('gagal_tambah', 'Data anda tidak valid');
            //     $kesalahan = $this->validator;
            //     return redirect()
            //         ->to("/databarang/tambah")
            //         ->withInput()
            //         // ->with('gagal_tambah', 'Data anda tidak valid')
            //         ->with("validation", $kesalahan);
            // }
            // $dataimagebarang = $this->request->getFile('image_barang');
            // if ($dataimagebarang->isValid() && !$dataimagebarang->hasMoved()) {
            //     $imagebarangFileName = $dataimagebarang->getRandomName();
            //     $dataimagebarang->move('assets/image/barang/', $imagebarangFileName);
            // }

            // $data = [
            //     "id_kategori" => $this->request->getVar("id_kategori"),
            //     "nama_barang" => $this->request->getVar("nama_barang"),
            //     "stok_barang" => $this->request->getVar("stok_barang"),
            //     "harga_beli" => $this->request->getVar("harga_beli"),
            //     "harga_jual" => $this->request->getVar("harga_jual"),
            //     "image_barang" => $imagebarangFileName
            // ];

            // $this->barangmodel->save($data);
            // session()->setFlashdata("berhasil_tambah", "Data Barang Berhasil Ditambahkan");
            // return redirect()->to("/databarang");
        } else {
            // echo "gagal";
            // var_dump($this->session);
            // die;
            $this->session->setFlashdata('gagal_tambah', 'Data anda tidak valid');
            $kesalahan = $this->validator;
            return redirect()
                ->to("/databarang/tambah")
                ->withInput()
                ->with("validation", $kesalahan);
        }
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
            "submenu" => "databarang",
            "title" => "Data Barang",
            "path" => "assets/image/barang/",
            "barang" => $this->barangmodel->join('tb_kategori', 'tb_kategori.id_kategori=tb_barang.id_kategori')->where('id_barang', $id)->first(),
            "kategori" => $this->kategorimodel->findAll(),
            "validation" => \Config\Services::validation()
        ];

        return view("cms/barang/v_editdata", $data);
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

        $cek = $this->barangmodel->where('id_barang', $id)->first();

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

                $this->barangmodel->save($data);
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

            $this->barangmodel->save($data);
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
        if (!get_cookie("access_token")) {
            return redirect()->to("/");
        }

        $cek = $this->barangmodel->where('id_barang', $id)->first();

        if ($this->barangmodel->delete($id)) {
            return $this->response->setJSON([
                'error' => false,
                'message' => 'Data Barang ' . $cek['nama_barang'] . ' Berhasil Dihapus!'
            ]);
        }
    }

    public function exportExcel()
    {
        if (!get_cookie("access_token")) {
            return redirect()->to("/");
        }

        $barang = $this->barangmodel->join('tb_kategori', 'tb_kategori.id_kategori=tb_barang.id_kategori', 'left')->findAll();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Nama Barang');
        $sheet->setCellValue('C1', 'Kategori');
        $sheet->setCellValue('D1', 'Stok');
        $sheet->setCellValue('E1', 'Harga Beli');
        $sheet->setCellValue('F1', 'Harga Jual');

        $kolom = 2;
        foreach ($barang as $value) {
            $sheet->setCellValue('A' . $kolom, ($kolom - 1));
            $sheet->setCellValue('B' . $kolom, $value['nama_barang']);
            $sheet->setCellValue('C' . $kolom, $value['nama_kategori']);
            $sheet->setCellValue('D' . $kolom, $value['stok_barang']);
            $sheet->setCellValue('E' . $kolom, $value['harga_beli']);
            $sheet->setCellValue('F' . $kolom, $value['harga_jual']);
            $kolom++;
        }

        $sheet->getStyle('A1:F1')->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle('A1:F1')->getFill()
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
        $sheet->getStyle('A1:F' . ($kolom - 1))->applyFromArray($styleArray);

        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setAutoSize(true);
        $sheet->getColumnDimension('E')->setAutoSize(true);
        $sheet->getColumnDimension('F')->setAutoSize(true);

        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformat-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=data-barang.xlsx');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit();
    }

    public function exportCsv()
    {
        if (!get_cookie("access_token")) {
            return redirect()->to("/");
        }

        $barang = $this->barangmodel->join('tb_kategori', 'tb_kategori.id_kategori=tb_barang.id_kategori', 'left')->findAll();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Nama Barang');
        $sheet->setCellValue('C1', 'Kategori');
        $sheet->setCellValue('D1', 'Stok');
        $sheet->setCellValue('E1', 'Harga Beli');
        $sheet->setCellValue('F1', 'Harga Jual');

        $kolom = 2;
        foreach ($barang as $value) {
            $sheet->setCellValue('A' . $kolom, ($kolom - 1));
            $sheet->setCellValue('B' . $kolom, $value['nama_barang']);
            $sheet->setCellValue('C' . $kolom, $value['nama_kategori']);
            $sheet->setCellValue('D' . $kolom, $value['stok_barang']);
            $sheet->setCellValue('E' . $kolom, $value['harga_beli']);
            $sheet->setCellValue('F' . $kolom, $value['harga_jual']);
            $kolom++;
        }

        $sheet->getStyle('A1:F1')->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle('A1:F1')->getFill()
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
        $sheet->getStyle('A1:F' . ($kolom - 1))->applyFromArray($styleArray);

        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setAutoSize(true);
        $sheet->getColumnDimension('E')->setAutoSize(true);
        $sheet->getColumnDimension('F')->setAutoSize(true);

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Csv($spreadsheet);
        header('Content-Type: application/vnd.openxmlformat-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=data-barang.csv');
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

        $file_pdf = 'data-barang';
        $paper = 'A4';
        $orientation = "portrait";

        $data = [
            "title" => "Data Barang",
            "barang" => $this->barangmodel->join('tb_kategori', 'tb_kategori.id_kategori=tb_barang.id_kategori', 'left')->findAll()
        ];

        $html = view("cms/barang/v_pdf", $data);

        $output = $Pdfgenerator->generate($html, $file_pdf, $paper, $orientation);
        file_put_contents('data-barang.pdf', $output);
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
                $barang = $spreadsheet->getActiveSheet()->toArray();
                foreach ($barang as $key => $value) {
                    if ($key == 0) {
                        continue;
                    }
                    $namabarang = $value[1];
                    $kategoribarang = $value[2];
                    $cekdata = $this->barangmodel->where('nama_barang', $namabarang)->first();
                    $cekkategori = $this->kategorimodel->findAll();
                    for ($i = 0; $i < count($cekkategori); $i++) {
                        if ($cekkategori[$i]['nama_kategori'] == $kategoribarang) {
                            $kategoribarang = $cekkategori[$i]['id_kategori'];
                        }
                    }
                    $data = [
                        'id_kategori' => $kategoribarang,
                        'nama_barang' => $namabarang,
                        'stok_barang' => $value[3],
                        'harga_beli' => $value[4],
                        'harga_jual' => $value[5]
                    ];
                    if ($cekdata == null) {
                        $this->barangmodel->insert($data);
                    }
                }
                $this->session->setFlashdata('berhasil_import', 'Data Berhasil Diimport!');
                return redirect()->to("/databarang");
            } else {
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
                $spreadsheet = $reader->load($file);
                $barang = $spreadsheet->getActiveSheet()->toArray();
                foreach ($barang as $key => $value) {
                    if ($key == 0) {
                        continue;
                    }

                    if ($value[1] != 0) {
                        $namabarang = $value[1];
                        $kategoribarang = $value[2];
                        $cekdata = $this->barangmodel->where('nama_barang', $namabarang)->first();
                        $cekkategori = $this->kategorimodel->findAll();
                        for ($i = 0; $i < count($cekkategori); $i++) {
                            if ($cekkategori[$i]['nama_kategori'] == $kategoribarang) {
                                $kategoribarang = $cekkategori[$i]['id_kategori'];
                            }
                        }
                        $data = [
                            'id_kategori' => $kategoribarang,
                            'nama_barang' => $namabarang,
                            'stok_barang' => $value[3],
                            'harga_beli' => $value[4],
                            'harga_jual' => $value[5]
                        ];
                        if ($cekdata == null) {
                            $this->barangmodel->insert($data);
                        }
                    } else {
                        $rawbarang = $value[0];
                        $cleanbarang = explode(",", $rawbarang);
                        $namabarang = $cleanbarang[1];
                        $kategoribarang = $cleanbarang[2];
                        $cekdata = $this->barangmodel->where('nama_barang', $namabarang)->first();
                        $cekkategori = $this->kategorimodel->findAll();
                        for ($i = 0; $i < count($cekkategori); $i++) {
                            if ($cekkategori[$i]['nama_kategori'] == $kategoribarang) {
                                $kategoribarang = $cekkategori[$i]['id_kategori'];
                            }
                        }
                        $data = [
                            'id_kategori' => $kategoribarang,
                            'nama_barang' => $namabarang,
                            'stok_barang' => $cleanbarang[3],
                            'harga_beli' => $cleanbarang[4],
                            'harga_jual' => $cleanbarang[5]
                        ];
                        if ($cekdata == null) {
                            $this->barangmodel->insert($data);
                        }
                    }
                }
                $this->session->setFlashdata('berhasil_import', 'Data Berhasil Diimport!');
                return redirect()->to("/databarang");
            }
        } else {
            $this->session->setFlashdata('gagal_import', 'Data anda tidak sesuai');
            return redirect()->to("/databarang");
        }
    }
}
