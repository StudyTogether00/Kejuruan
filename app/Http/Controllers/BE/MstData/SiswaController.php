<?php

namespace App\Http\Controllers\BE\MstData;

use App\Http\Controllers\BE\BaseController;
use App\Services\BaseService;
use App\Services\DB\SiswaService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SiswaController extends BaseController
{
    protected $pns = "Data Siswa";
    public function __construct()
    {
        parent::__construct();
    }

    public function Lists(Request $request)
    {
        try {
            $data = SiswaService::Data();
            $data = $data->select("nisn", "nama_siswa", "jenis_kelamin");
            $data = $data->orderBy("nisn")->get();
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
                "nisn" => "required",
                "nama_siswa" => "required",
                "jenis_kelamin" => "required|integer",
            ]);
            if ($validation->fails()) {
                $this->error = $validation->errors();
                throw new \Exception(BaseService::MessageCheckData(), 400);
            }

            // Save Or Update
            $data = SiswaService::Detail($request->nisn, $request->action);
            if ($request->action == "Add") {
                $data = SiswaService::new ();
                $data->nisn = $request->nisn;
                $data->created_at = Carbon::now();
            }
            $data->nama_siswa = $request->nama_siswa;
            $data->jenis_kelamin = $request->jenis_kelamin;
            $data->updated_at = Carbon::now();
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
            $validation = Validator::make($request->all(), ["nisn" => "required"]);
            if ($validation->fails()) {
                $this->error = $validation->errors();
                throw new \Exception(BaseService::MessageCheckData(), 400);
            }

            // Delete
            $data = SiswaService::Detail($request->nisn);
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
