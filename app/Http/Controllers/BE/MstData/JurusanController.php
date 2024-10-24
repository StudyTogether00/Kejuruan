<?php

namespace App\Http\Controllers\BE\MstData;

use App\Http\Controllers\BE\BaseController;
use App\Services\BaseService;
use App\Services\DB\JurusanService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class JurusanController extends BaseController
{
    protected $pns = "Data Jurusan";
    public function __construct()
    {
        parent::__construct();
    }

    public function Lists(Request $request)
    {
        try {
            $data = JurusanService::Data();
            $data = $data->select("kd_jurusan", "nama_jurusan");
            $data = $data->orderBy("nama_jurusan")->get();

            $this->respon = BaseService::ResponseSuccess(BaseService::MsgSuccess($this->pns, 1), $data);
        } catch (\Throwable $th) {
            $this->respon = BaseService::ResponseError($th->getMessage(), $this->error, $th->getCode());
        }
        return $this->SendResponse();
    }

    public function Save(Request $request)
    {
        try {
            DB::beginTransaction();
            $validation = Validator::make($request->all(), [
                "action" => "required|in:{$this->option_action}",
                "kd_jurusan" => "required",
                "nama_jurusan" => "required",
            ]);
            if ($validation->fails()) {
                $this->error = $validation->errors();
                throw new \Exception(BaseService::MessageCheckData(), 400);
            }

            // Check Nama Jurasan
            $cek = JurusanService::Data()->where("nama_jurusan", $request->nama_jurusan)->where("kd_jurusan", "<>", $request->kd_jurusan)->count();
            if ($cek > 0) {
                throw new \Exception(BaseService::MessageDataExists("Nama Jurusan {$request->nama_jurusan}"), 400);
            }
            // Save Or Update
            $data = JurusanService::Detail($request->kd_jurusan, $request->action);
            if ($request->action == "Add") {
                $data = JurusanService::new ();
                $data->kd_jurusan = $request->kd_jurusan;
                $data->created_at = Carbon::now();
            }
            $data->nama_jurusan = $request->nama_jurusan;
            $data->update_at = Carbon::now();
            $data->save();

            DB::commit();
            $this->respon = BaseService::ResponseSuccess(BaseService::MsgSuccess($this->pns, 2), $data);
        } catch (\Throwable $th) {
            DB::rollBack();
            $this->respon = BaseService::ResponseError($th->getMessage(), $this->error, $th->getCode());
        }
        return $this->SendResponse();
    }

    public function Delete(Request $request)
    {
        try {
            DB::beginTransaction();
            $validation = Validator::make($request->all(), ["kd_jurusan" => "required"]);
            if ($validation->fails()) {
                $this->error = $validation->errors();
                throw new \Exception(BaseService::MessageCheckData(), 400);
            }

            // Delete
            $data = JurusanService::Detail($request->kd_jurusan);
            $data->delete();

            DB::commit();
            $this->respon = BaseService::ResponseSuccess(BaseService::MsgSuccess($this->pns, 3), $data);
        } catch (\Throwable $th) {
            DB::rollBack();
            $this->respon = BaseService::ResponseError($th->getMessage(), $this->error, $th->getCode());
        }
        return $this->SendResponse();
    }
}