<?php

namespace App\Http\Controllers\FE;

use Illuminate\Http\Request;

class ProcessController extends RouteController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function Nilai(Request $request)
    {
        $this->data["title"] = "Input Nilai Siswa";
        return view("pages.Process.InputNilai", $this->data);
    }
}
