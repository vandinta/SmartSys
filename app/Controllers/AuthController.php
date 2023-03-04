<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UsersModel;
use App\Models\CartModel;
use Firebase\JWT\JWT;

class AuthController extends BaseController
{
    private $session;
    protected $usersmodel;
    protected $cartmodel;
    protected $decoded;

    public function __construct()
    {
        helper(['cookie', 'date', 'tgl_indo']);

        $this->session = \Config\Services::session();
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
            "users" => $this->usersmodel->findAll(),
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
            "karyawan" => $this->usersmodel->where('role', "petugas")->findAll(),
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
                    setcookie("email", $data['email'], time() + 60 * 60 * 24, '/');
                    $enkripsipwd = base64_encode($data['password']);
                    setcookie("password", $enkripsipwd, time() + 60 * 60 * 24, '/');
                }
                return redirect()->to("/");
            } else {
                $this->session->setFlashdata('error', 'Data anda tidak valid');
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
            'confpassword' => 'matches[password]',
            'username' => 'max_length[255]',
            'role' => 'required',
        ];

        $rules_image = [
            "profile_picture" => "uploaded[profile_picture]|is_image[profile_picture]|mime_in[profile_picture,image/jpg,image/jpeg,image/png]|max_size[profile_picture,4000]",
        ];

        $messages = [
            "email" => [
                "required" => "{field} tidak boleh kosong",
                "min_length" => "{field} minimal 6 karakter",
                "max_length" => "{field} maksimal 255 karakter",
                "valid_email" => "{field} harus berupa email",
                "is_unique" => "{field} sudah terdaftar",
            ],
            "password" => [
                "required" => "{field} tidak boleh kosong",
                "min_length" => "{field} maksimal 8 karakter",
                "alpha_numeric" => "{field} harus berisi gabungan huruf & angka",
            ],
            "confpassword" => [
                "matches" => "{field} tidak sama dengan password",
            ],
            "username" => [
                "max_length" => "{field} maksimal 255 karakter"
            ],
            "role" => [
                "required" => "{field} tidak boleh kosong",
            ]
        ];

        $messages_image = [
            "profile_picture" => [
                'uploaded' => '{field} tidak boleh kosong',
                'mime_in' => '{field} Harus Berupa jpg, jpeg, png atau webp',
                'max_size' => 'Ukuran {field} Maksimal 4 MB'
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
                    "profile_picture" => $profileimageFileName
                ];

                $this->usersmodel->save($data);
                session()->setFlashdata("berhasil_tambah", "Data User Berhasil Ditambahkan");
                return redirect()->to("/datausers");
            }

            $data = [
                "email" => $this->request->getVar("email"),
                "password" => $password,
                "username" => $this->request->getVar("username"),
                "role" => $this->request->getVar("role"),
            ];

            $this->usersmodel->save($data);
            session()->setFlashdata("berhasil_tambah", "Data User Berhasil Ditambahkan");
            return redirect()->to("/datausers");
        } else {
            $this->session->setFlashdata('gagal_tambah', 'Data anda tidak valid');
            $kesalahan = $this->validator;
            return redirect()
                ->to("/datausers/tambah")
                ->withInput()
                ->with("validation", $kesalahan);
        }
    }

    public function ubah($id)
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
            "title" => "Data Users",
            "user" => $this->usersmodel->where('user_id', $id)->first(),
            "validation" => \Config\Services::validation()
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
            "validation" => \Config\Services::validation()
        ];

        return view("cms/auth/v_editakun", $data);
    }

    public function ganti($id)
    {
        if (!get_cookie("access_token")) {
            return redirect()->to("/");
        }

        if ($this->decoded->role != "superadmin") {
            return redirect()->to("/");
        }

        $rules = [
            'username' => 'max_length[255]',
            'role' => 'required',
        ];

        $rules_password = [
            'password' => 'required|min_length[8]|alpha_numeric',
            'confpassword' => 'matches[password]',
        ];

        $rules_image = [
            "profile_picture" => "uploaded[profile_picture]|is_image[profile_picture]|mime_in[profile_picture,image/jpg,image/jpeg,image/png]|max_size[profile_picture,4000]",
        ];

        $messages = [
            "username" => [
                "max_length" => "{field} maksimal 255 karakter"
            ],
            "role" => [
                "required" => "{field} tidak boleh kosong",
            ]
        ];

        $messages_password = [
            "password" => [
                "required" => "{field} tidak boleh kosong",
                "min_length" => "{field} maksimal 8 karakter",
                "alpha_numeric" => "{field} harus berisi gabungan huruf & angka",
            ],
            "confpassword" => [
                "matches" => "{field} tidak sama dengan password",
            ],
        ];

        $messages_image = [
            "profile_picture" => [
                'uploaded' => '{field} tidak boleh kosong',
                'mime_in' => '{field} Harus Berupa jpg, jpeg, png atau webp',
                'max_size' => 'Ukuran {field} Maksimal 4 MB'
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
                    "role" => $this->request->getVar("role"),
                    "profile_picture" => $profileFileName
                ];

                $this->usersmodel->save($data);
                session()->setFlashdata("berhasil_diubah", " ");
                return redirect()->to("/datausers/ubah" . "/" . $id);
            }

            if ($this->validate($rules_password, $messages_password)) {
                $password = md5($this->request->getVar("password"));
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
                        "password" => $password,
                        "username" => $this->request->getVar("username"),
                        "role" => $this->request->getVar("role"),
                        "profile_picture" => $profileFileName
                    ];

                    $this->usersmodel->save($data);
                    session()->setFlashdata("berhasil_diubah", " ");
                    return redirect()->to("/datausers/ubah" . "/" . $id);
                }

                $data = [
                    "user_id" => $id,
                    "password" => $password,
                    "username" => $this->request->getVar("username"),
                    "role" => $this->request->getVar("role"),
                ];

                $this->usersmodel->save($data);
                session()->setFlashdata("berhasil_diubah", " ");
                return redirect()->to("/datausers/ubah" . "/" . $id);
            }

            $data = [
                "user_id" => $id,
                "username" => $this->request->getVar("username"),
                "role" => $this->request->getVar("role"),
            ];

            $this->usersmodel->save($data);
            session()->setFlashdata("berhasil_diubah", " ");
            return redirect()->to("/datausers/ubah" . "/" . $id);
        } else {
            $kesalahan = \Config\Services::validation();
            $this->session->setFlashdata('gagal_diubah', 'Data anda tidak valid');
            return redirect()
                ->to("/datausers/ubah" . "/" . $id)
                ->with("validation", $kesalahan);
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
                "required" => "{field} tidak boleh kosong",
                "max_length" => "{field} maksimal 255 karakter",
            ],
        ];

        $messages_image = [
            "profile_picture" => [
                'uploaded' => '{field} tidak boleh kosong',
                'mime_in' => '{field} Harus Berupa jpg, jpeg, png atau webp',
                'max_size' => 'Ukuran {field} Maksimal 4 MB'
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
            $kesalahan = \Config\Services::validation();
            $this->session->setFlashdata('gagal_diubah', 'Data anda tidak valid');
            return redirect()
                ->to("/setting" . "/" . $cek["email"])
                ->with("validation", $kesalahan);
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
