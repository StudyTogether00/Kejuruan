<?php

namespace App\Http\Controllers;

use App\Models\Jurusan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class Jurusancontroller extends Controller
{
    public function Lists(Request $request)
    {
        try {
            $data = Jurusan::select("kd_jurusan","nama_jurusan")->get();
            $this -> ResponSucces("List Data Jurusan", $data);
        } catch (\Throwable $th) {
            $this -> ResponError($th->getMessage(), "", $th->GetCode());
       }
        return $this->SendRespon();

        return response()->json($this->respon,$code);
    }

    public function Save(Request $request)
    {
            try {
                $validation = Validator:: make($request->all(), [
                    "action" => "required|in:Add,Edit",
                    "kd_jurusan" => "required_if:action,Edit",
                    "nama_jurusan" => "required",
                ]);
                if ($validation->fails()) {
                    $this->error = $validation->errors();
                    throw new \Exception("Please Cek Data", 400);
                }

                
                $data = $request->all();
                $this->ResponSucces ("List Data Jurusan", $data);

            }catch (\Throwable $th) {
                $this -> ResponError($th->getMessage(), "", $th->GetCode());
    }

    return $this->SendRespon();
}
    public function Delete(Request $request)
    {
            try {
                $data = Jurusan::select("kd_jurusan","nama_jurusan")->get();
                $this -> ResponSucces("List Data Jurusan", $data);

            }catch (\Throwable $th) {
                $this -> ResponError($th->getMessage(), "", $th->GetCode());
    }
    return $this->SendRespond();
    }

    public function index(Request $request){
    }

    public function ResponSucces($message = "",$data ="")
    {
        $this->respon =[
            "code" => 200,
            "content" =>[
                "status" => true,
                "message" => $message,
                "data" => $data,
            ],
        ];
    }

    public function ResponError($message = "",$data ="",$code = 200)
    {
        $this->respon =[
            "code" => 200,
            "content" =>[
                "status" => false,
                "message" => $message,
                "data" => $data,
            ],
        ];
    }

    public function SendRespon()
    {
        return response ()->json($this->respon["content"], $this->respon["code"]);
    }
}
