<?php

namespace App\Http\Controllers;

use App\Models\Jurusan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


class Jurusancontroller extends Controller
{
    public function index(Request $request)
    {
        $data = Jurusan::all();
        return response ()->json(["data" =>$data]);
    }
}
