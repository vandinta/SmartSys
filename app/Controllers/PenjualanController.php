<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class PenjualanController extends BaseController
{
    public function __construct()
    {
        helper('cookie');

        \Config\Services::session();
    }

    public function index()
    {
        $data = [
            "title" => "Sign In",
            "validation" => \Config\Services::validation(),
        ];

        $nilai = [
            "menu" => "datapenjualan",
            "submenu" => ""
        ];

        if(get_cookie("access_token")){
            return view("cms/v_penjualan", $nilai);
        } else{
            return view("pages/v_login", $data);
        }

    }
}
