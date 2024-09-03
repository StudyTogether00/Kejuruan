<?php

namespace App\Http\Controllers;

use App\Models\Jurusan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


class Jurusancontroller extends Controller
{
    public function lists(Request $request)
    {
        try {
            $data = Jurusan::select("kd_jurusan","nama_jurusan")->get();
            $respon =[
            "status" => true,
            "message" => "List Data Jurusan",
            "data" => $data,
            ];
            $code = 200;
        }catch (\Throwable $th) {
           $respon = [
            "status" => false,
            "message" => $th->getMessage(),
            "data" => [],
            ];
            $code = 400;
       }
        return response()->json($respon,$code);
    }
    public function Add(Request $request)
    {
        
    }
    public function Delete(Request $request)
    {
        
    }
    public function index(Request $request)
    {
        $data = Jurusan::all();
        return response ()->json(["data" =>$data]);
    }
}
