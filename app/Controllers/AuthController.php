<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UsersModel;
use Firebase\JWT\JWT;

class AuthController extends BaseController
{
    protected $usersmodel;
    private $session;
    
    public function __construct()
    {
        helper('cookie');

        $this->session = \Config\Services::session();
        $this->usersmodel = new UsersModel();
    }

    public function index()
    {
        $data = [
            "title" => "Sign In",
            "validation" => \Config\Services::validation(),
        ];

        if(get_cookie("access_token")){
            return view("cms/v_dashboard");
        } else{
            return view("pages/v_login", $data);
        }

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
        ];

        $user = $this->usersmodel->where('email', $data['email'])->first();

        if ($validation && $user != null) {
            if($user['password'] == md5($data['password'])){
                $key = getenv('TOKEN_SECRET');
                $payload = [
                    'iat'   => 1356999524,
                    'nbf'   => 1357000000,
                    'exp' => time() + (60 * 60),
                    'uid'   => $user['user_id'],
                    'email' => $user['email'],
                    'role' => $user['role'],
                ];
                $token = JWT::encode($payload, $key, 'HS256');
                setcookie("access_token", $token, time() + 60 * 60, '/');

                return redirect()->to("/");
            }
            else{
                $this->session->setFlashdata('error', 'Data anda tidak valid');
                return redirect()->to("/");
            }
        } else {
            $this->session->setFlashdata('error', 'Data anda tidak valid');
            return redirect()->to("/");
        }
    }
    
    public function logout()
    {
        //hancurkan session 
        //balikan ke halaman login
        $this->session->destroy();
        return redirect()->to('/auth/login');
    }
}
