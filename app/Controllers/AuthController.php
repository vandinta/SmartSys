<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UsersModel;
use App\Models\CartModel;
use Firebase\JWT\JWT;

class AuthController extends BaseController
{
    private $session;
    private $sendemail;
    protected $usersmodel;
    protected $cartmodel;
    protected $decoded;

    public function __construct()
    {
        helper(['cookie', 'date', 'tgl_indo', 'form']);

        $this->session = \Config\Services::session();
        $this->sendemail = \Config\Services::email();
        $this->usersmodel = new UsersModel();
        $this->cartmodel = new Cartmodel();

        if (!get_cookie("access_token")) {
            return redirect()->to("/");
        }

        $token = get_cookie("access_token");
        $this->decoded = JWT::decode($token, 'JWT_SECRET', ['HS256']);

        $delete_all = $this->cartmodel->delete_all();
    }

    public function index()
    {
        $data = [
            "title" => "Sign In",
            "validation" => \Config\Services::validation(),
        ];

        $nilai = [
            "menu" => "dashboard",
            "submenu" => ""
        ];

        if (get_cookie("access_token")) {
            return view("cms/v_dashboard", $nilai);
        } else {
            return view("pages/v_login", $data);
        }
    }

    public function listUsers()
    {
        if (!get_cookie("access_token")) {
            return redirect()->to("/");
        }

        if ($this->decoded->role != "superadmin") {
            return redirect()->to("/");
        }

        $data = [
            "menu" => "users",
            "submenu" => " ",
            "title" => "Data Users",
            "users" => $this->usersmodel->orderBy('created_at', 'DESC')->findAll(),
        ];

        return view("cms/auth/v_users", $data);
    }

    public function listKaryawan()
    {
        if (!get_cookie("access_token")) {
            return redirect()->to("/");
        }

        if ($this->decoded->role != "admin") {
            return redirect()->to("/");
        }

        $data = [
            "menu" => "karyawan",
            "submenu" => " ",
            "title" => "Data Karyawan",
            "karyawan" => $this->usersmodel->where('role', "petugas")->orderBy('created_at', 'DESC')->findAll(),
        ];

        return view("cms/auth/v_karyawan", $data);
    }

    public function login()
    {
        $validation = $this->validate([
            "email" => [
                "rules" => "required|valid_email",
                "errors" => [
                    "required" => "{field} harus diisi.",
                    "valid_email" => "Format {field} tidak sesuai."
                ],
            ],
            "password" => [
                "rules" => "required",
                "errors" => ["required" => "{field} harus diisi."],
            ]
        ]);

        $data = [
            "email" => $this->request->getVar("email"),
            "password" => $this->request->getVar("password"),
            "remember" => $this->request->getVar("remember"),
        ];

        $user = $this->usersmodel->where('email', $data['email'])->first();

        if ($validation && $user != null) {
            if ($user['activation_status'] != 0) {
                if ($user['password'] == md5($data['password'])) {
                    $t = now('Asia/Jakarta');
                    $time = date("Y-m-d H:i:s", $t);
                    $last_login = [
                        "user_id" => $user['user_id'],
                        "last_login" => $time
                    ];
                    $this->usersmodel->save($last_login);

                    // $key = getenv('TOKEN_SECRET');
                    $payload = [
                        'iat'   => 1356999524,
                        'nbf'   => 1357000000,
                        'exp' => time() + (60 * 60 * 2),
                        'uid'   => $user['user_id'],
                        'email' => $user['email'],
                        'role' => $user['role'],
                    ];
                    $token = JWT::encode($payload, 'JWT_SECRET', 'HS256');
                    setcookie("access_token", $token, time() + 60 * 60 * 2, '/');
                    $profile = [
                        'email' => $user['email'],
                        'username' => $user['username'],
                        'profile_picture' => $user['profile_picture'],
                        'role' => $user['role']
                    ];

                    $this->session->set($profile);

                    if ($data['remember'] != null) {
                        setcookie("email", $data['email'], time() + 60 * 60 * 24 * 2, '/');
                        $enkripsipwd = base64_encode($data['password']);
                        setcookie("password", $enkripsipwd, time() + 60 * 60 * 24 * 2, '/');
                    }
                    return redirect()->to("/");
                } else {
                    $this->session->setFlashdata('error', 'Data anda tidak valid');
                    return redirect()->to("/");
                }
            } else {
                $this->session->setFlashdata('error', 'Akun anda belum diaktivasi');
                return redirect()->to("/");
            }
        } else {
            $this->session->setFlashdata('error', 'Data anda tidak valid');
            return redirect()->to("/");
        }
    }

    public function create()
    {
        if (!get_cookie("access_token")) {
            return redirect()->to("/");
        }

        if ($this->decoded->role != "superadmin") {
            return redirect()->to("/");
        }

        $data = [
            "menu" => "datausers",
            "submenu" => " ",
            "title" => "Tambah Data User",
            "validation" => \Config\Services::validation()
        ];

        return view("cms/auth/v_tambahdata", $data);
    }

    public function save()
    {
        if (!get_cookie("access_token")) {
            return redirect()->to("/");
        }

        if ($this->decoded->role != "superadmin") {
            return redirect()->to("/");
        }

        $rules = [
            'email' => 'required|min_length[6]|max_length[255]|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[8]|alpha_numeric',
            'confpassword' => 'required|matches[password]',
            'username' => 'required|max_length[255]',
            'role' => 'required',
        ];

        $rules_image = [
            "profile_picture" => "uploaded[profile_picture]|is_image[profile_picture]|mime_in[profile_picture,image/jpg,image/jpeg,image/png]|max_size[profile_picture,4000]",
        ];

        $messages = [
            "email" => [
                "required" => "Email Tidak Boleh Kosong",
                "min_length" => "Email Minimal 6 Karakter",
                "max_length" => "Email Maksimal 255 Karakter",
                "valid_email" => "Email Harus Berupa Email",
                "is_unique" => "Email Sudah Terdaftar",
            ],
            "password" => [
                "required" => "Password Tidak Boleh Kosong",
                "min_length" => "Password Minimal 8 Karakter",
                "alpha_numeric" => "Password Harus Berisi Gabungan Huruf & Angka",
            ],
            "confpassword" => [
                "required" => "Konfirmasi Password Tidak Boleh Kosong",
                "matches" => "Konfirmasi Password Tidak Sama Dengan Password",
            ],
            "username" => [
                "required" => "Username Tidak Boleh Kosong",
                "max_length" => "Username Maksimal 255 Karakter"
            ],
            "role" => [
                "required" => "Role Tidak Boleh Kosong",
            ]
        ];

        $messages_image = [
            "profile_picture" => [
                'uploaded' => 'Foto Profile Tidak Boleh Kosong',
                'mime_in' => 'Foto Profile Harus Berupa jpg, jpeg, png atau webp',
                'max_size' => 'Ukuran Foto Profile Maksimal 4 MB'
            ],
        ];

        if ($this->validate($rules, $messages)) {
            $password = md5($this->request->getVar("password"));

            if ($this->validate($rules_image, $messages_image)) {
                $dataprofileimage = $this->request->getFile('profile_picture');
                if ($dataprofileimage->isValid() && !$dataprofileimage->hasMoved()) {
                    $profileimageFileName = $dataprofileimage->getRandomName();
                    $dataprofileimage->move('assets/image/profile/', $profileimageFileName);
                }

                $data = [
                    "email" => $this->request->getVar("email"),
                    "password" => $password,
                    "username" => $this->request->getVar("username"),
                    "role" => $this->request->getVar("role"),
                    "activation_status" => 0,
                    "profile_picture" => $profileimageFileName
                ];

                $this->usersmodel->save($data);
                session()->setFlashdata("berhasil_tambah", "Data User Berhasil Ditambahkan");
                return redirect()->to("/datausers");
            }

            $dataemail = $this->request->getVar("email");

            $data = [
                "email" => $dataemail,
                "password" => $password,
                "username" => $this->request->getVar("username"),
                "role" => $this->request->getVar("role"),
                "activation_status" => 0
            ];

            if ($this->usersmodel->save($data)) {
                $dataemail = $this->request->getVar("email");

                $payload = array(
                    "iat" => 1356999524,
                    "nbf" => 1357000000,
                    "exp" => time() + (60 * 60 * 1),
                    "email" => $dataemail
                );
                $token = JWT::encode($payload, 'JWT_SECRET', 'HS256');

                $link = base_url() . "/aktivasi?token=" . $token;
                $datatemplate = [
                    "email" => $dataemail,
                    "link" => $link,
                    "logo" => base_url("Atlantis/assets/img/logo.svg")
                ];

                $message = view('template/email.html', $datatemplate);

                $email = \Config\Services::email();
                $email->setTo($dataemail);
                $email->setFrom('smartsysindo@gmail.com', 'Konfirmasi Pendaftaran');

                $email->setSubject("Link Aktivasi Akun");
                $email->setMessage($message);
                $email->send();
                session()->setFlashdata("berhasil_tambah", "Data User Berhasil Ditambahkan");
                return redirect()->to("/datausers");
            }
            $this->session->setFlashdata('gagal_tambah', 'Data anda tidak valid');
        } else {
            $this->session->setFlashdata('gagal_tambah', 'Data anda tidak valid');
            return redirect()
                ->to("/datausers/tambah")
                ->withInput();
        }
    }

    public function aktivasiUser()
    {
        $token = $this->request->getVar('token');

        try {
            $decoded = JWT::decode($token, 'JWT_SECRET', ['HS256']);

            $this->usersmodel->activationUsers([
                'activation_status' => 1
            ], $decoded->email);
            return redirect()->to("/");
        } catch (\Firebase\JWT\ExpiredException $e) {
            $this->session->setFlashdata('gagal_tambah', 'Token aktivasi kadaluarsa');
            return redirect()->to("/viewgetemail");
        }
    }

    public function viewgetEmail()
    {
        $data = [
            "title" => "Get Aktivasi",
            "validation" => \Config\Services::validation(),
        ];

        return view("pages/v_getemail", $data);
    }

    public function getEmail()
    {
        $rules = [
            'email' => 'required|min_length[6]|max_length[255]|valid_email',
        ];

        $messages = [
            "email" => [
                "required" => "{field} tidak boleh kosong",
                "min_length" => "{field} minimal 6 karakter",
                "max_length" => "{field} maksimal 255 karakter",
                "valid_email" => "{field} harus berupa email",
            ]
        ];

        if ($this->validate($rules, $messages)) {
            $dataemail = $this->request->getVar("email");
            $cekdata = $this->usersmodel->where('email', $dataemail)->first();

            if ($cekdata != null) {
                if ($cekdata['activation_status'] == 0) {
                    $payload = array(
                        "iat" => 1356999524,
                        "nbf" => 1357000000,
                        "exp" => time() + (60 * 60 * 1),
                        "email" => $dataemail
                    );
                    $token = JWT::encode($payload, 'JWT_SECRET', 'HS256');

                    $link = base_url() . "/aktivasi?token=" . $token;
                    $datatemplate = [
                        "email" => $dataemail,
                        "link" => $link,
                        "logo" => base_url("Atlantis/assets/img/logo.svg")
                    ];

                    $message = view('template/email_aktivasi.html', $datatemplate);

                    $email = \Config\Services::email();
                    $email->setTo($dataemail);
                    $email->setFrom('smartsysindo@gmail.com', 'Konfirmasi Pendaftaran');

                    $email->setSubject("Link Aktivasi Akun");
                    $email->setMessage($message);
                    $email->send();
                    session()->setFlashdata("berhasil", "Email aktivasi berhasil dikirimkan");
                    return redirect()->to("/");
                }
                session()->setFlashdata("berhasil", "Email sudah teraktivasi");
                return redirect()->to("/");
            }
            $this->session->setFlashdata('error', 'Email tidak ditemukan');
            return redirect()->to("/viewgetemail");
        } else {
            $this->session->setFlashdata('error', 'Email gagal dikirimkan');
            return redirect()->to("/viewgetemail");
        }
    }

    public function ubah($email)
    {
        if (!get_cookie("access_token")) {
            return redirect()->to("/");
        }

        $data = [
            "menu" => "datausers",
            "submenu" => " ",
            "title" => "Ubah Password",
            "user" => $this->usersmodel->where('email', $email)->first(),
        ];

        return view("cms/auth/v_editusers", $data);
    }

    public function edit($email)
    {
        if (!get_cookie("access_token")) {
            return redirect()->to("/");
        }

        $data = [
            "menu" => "dashboard",
            "submenu" => " ",
            "title" => "Pengaturan Akun",
            "akun" => $this->usersmodel->where('email', $email)->first(),
        ];

        return view("cms/auth/v_editakun", $data);
    }

    public function ganti($id)
    {
        if (!get_cookie("access_token")) {
            return redirect()->to("/");
        }

        $rules = [
            'password_lama' => 'required',
            'password_baru' => 'required|min_length[8]|alpha_numeric',
            'confpassword_baru' => 'required|matches[password_baru]',
        ];

        $messages = [
            "password_lama" => [
                "required" => "Password Lama Tidak Boleh Kosong",
            ],
            "password_baru" => [
                "required" => "Password Baru Tidak Boleh Kosong",
                "min_length" => "Password Baru Minimal 8 Karakter",
                "alpha_numeric" => "Password Baru Harus Berisi Gabungan Huruf & Angka",
            ],
            "confpassword_baru" => [
                "required" => "Password Konfirmasi Tidak Boleh Kosong",
                "matches" => "Password Konfirmasi Tidak Sama Dengan Password",
            ],
        ];

        $cek = $this->usersmodel->where('user_id', $id)->first();

        if ($this->validate($rules, $messages)) {
            $password_lama = $this->request->getVar("password_lama");

            if ($cek['password'] != md5($password_lama)) {
                session()->setFlashdata("password_gagal", "Password Tidak Cocok");
                return redirect()->to("/ubahpassword" . "/" . $cek["email"]);
            }

            $password_baru = md5($this->request->getVar("password_baru"));
            
            $data = [
                "user_id" => $id,
                "password" => $password_baru
            ];

            $this->usersmodel->save($data);
            session()->setFlashdata("berhasil_diubah", "Password Berhasil Diubah");
            return redirect()->to("/ubahpassword" . "/" . $cek["email"]);
        } else {
            $this->session->setFlashdata('gagal_diubah', 'Data anda tidak valid');
            return redirect()
                ->to("/ubahpassword" . "/" . $cek["email"])
                ->withInput();
        }
    }

    public function update($id)
    {
        if (!get_cookie("access_token")) {
            return redirect()->to("/");
        }

        $rules = [
            'username' => 'required|max_length[255]',
        ];

        $rules_image = [
            "profile_picture" => "uploaded[profile_picture]|is_image[profile_picture]|mime_in[profile_picture,image/jpg,image/jpeg,image/png]|max_size[profile_picture,4000]",
        ];

        $messages = [
            "username" => [
                "required" => "Username tidak boleh kosong",
                "max_length" => "Username maksimal 255 karakter",
            ],
        ];

        $messages_image = [
            "profile_picture" => [
                'uploaded' => 'Foto Profile Tidak Boleh Kosong',
                'mime_in' => 'Foto Profile Harus Berupa jpg, jpeg, png atau webp',
                'max_size' => 'Ukuran Foto Profile Maksimal 4 MB'
            ],
        ];

        $cek = $this->usersmodel->where('user_id', $id)->first();

        if ($this->validate($rules, $messages)) {
            if ($this->validate($rules_image, $messages_image)) {
                $oldprofile = $cek['profile_picture'];
                $dataprofile = $this->request->getFile('profile_picture');
                if ($dataprofile->isValid() && !$dataprofile->hasMoved()) {
                    if (file_exists("assets/image/profile/" . $oldprofile)) {
                        unlink("assets/image/profile/" . $oldprofile);
                    }
                    $profileFileName = $dataprofile->getRandomName();
                    $dataprofile->move('assets/image/profile/', $profileFileName);
                } else {
                    $profileFileName = $oldprofile['profile_picture'];
                }

                $data = [
                    "user_id" => $id,
                    "username" => $this->request->getVar("username"),
                    "profile_picture" => $profileFileName
                ];

                $this->usersmodel->save($data);

                $user_data = [
                    "username" => $data['username'],
                    "profile_picture" => $data['profile_picture']
                ];

                $this->session->push('username', ["username" => $data['username']]);
                $this->session->push('profile_picture', ["profile_picture" => $data['profile_picture']]);

                session()->setFlashdata("berhasil_diubah", " ");
                return redirect()->to("/setting" . "/" . $cek["email"]);
            }

            $data = [
                "user_id" => $id,
                "username" => $this->request->getVar("username")
            ];

            $this->usersmodel->save($data);
            session()->setFlashdata("berhasil_diubah", " ");
            return redirect()->to("/setting" . "/" . $cek["email"]);
        } else {
            $this->session->setFlashdata('gagal_diubah', 'Data anda tidak valid');
            return redirect()
                ->to("/setting" . "/" . $cek["email"])
                ->withInput();
        }
    }

    public function logout()
    {
        $this->session->destroy();
        setcookie("access_token", "", time() - 60 * 60 * 2, '/');
        return redirect()->to('/');
    }

    public function delete($id = null)
    {
        $cek = $this->usersmodel->where('user_id', $id)->first();

        if ($this->usersmodel->delete($id)) {
            return $this->response->setJSON([
                'error' => false,
                'message' => 'Data User ' . $cek['email'] . ' Berhasil Dihapus!'
            ]);
        }
    }
}
