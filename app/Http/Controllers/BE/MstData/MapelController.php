<?php

namespace App\Http\Controllers\BE\MstData;

use App\Http\Controllers\BE\BaseController;
use App\Services\BaseService;
use App\Services\DB\MapelService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MapelController extends BaseController
{
    protected $pns = "Data Mata Pelajaran";
    public function __construct()
    {
        parent::__construct();
    }

    public function Lists(Request $request)
    {
        try {
            $data = MapelService::Data();
            $data = $data->select("kd_matapelajaran", "nama_matapelajaran");
            $data = $data->orderBy("kd_matapelajaran")->get();
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
                "kd_matapelajaran" => "required",
                "nama_matapelajaran" => "required",
            ]);
            if ($validation->fails()) {
                $this->error = $validation->errors();
                throw new \Exception(BaseService::MessageCheckData(), 400);
            }

            // Check Nama Mapel
            $cek = MapelService::Data()->where("nama_matapelajaran", $request->nama_matapelajaran)->where("kd_matapelajaran", "<>", $request->kd_matapelajaran)->count();
            if ($cek > 0) {
                throw new \Exception(BaseService::MessageDataExists("Nama Mata Pelajaran {$request->nama_matapelajaran}"), 400);
            }

            // Save Or Update
            $data = MapelService::Detail($request->kd_matapelajaran, $request->action);
            if ($request->action == "Add") {
                $data = MapelService::new();
                $data->kd_matapelajaran = $request->kd_matapelajaran;
                $data->created_at = Carbon::now();
            }
            $data->nama_matapelajaran = $request->nama_matapelajaran;
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
            $validation = Validator::make($request->all(), ["kd_matapelajaran" => "required"]);
            if ($validation->fails()) {
                $this->error = $validation->errors();
                throw new \Exception(BaseService::MessageCheckData(), 400);
            }

            // Delete
            $data = MapelService::Detail($request->kd_matapelajaran);
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
