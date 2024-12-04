<?php

namespace App\Http\Controllers\BE\MstData;

use App\Http\Controllers\BE\BaseController;
use App\Models\MasterData\Bobot;
use App\Services\BaseService;
use App\Services\DB\BobotService;
use App\Services\DB\JurusanService;
use App\Services\DB\MapelService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class BobotController extends BaseController
{
    protected $pns = "Data Bobot";
    public function __construct()
    {
        parent::__construct();
    }

    public function Lists(Request $request)
    {
        try {
            // Data Bobot
            $dtbobot = BobotService::Data();
            $dtbobot = $dtbobot->selectRaw("kd_jurusan, COUNT(*) as cmapel");
            $dtbobot = $dtbobot->groupBy("kd_jurusan");

            $data = JurusanService::Data();
            $data = $data->leftJoinSub($dtbobot, "b", function ($q) {
                $q->on("b.kd_jurusan", "=", "jurusan.kd_jurusan");
            });
            $data = $data->select("jurusan.kd_jurusan", "jurusan.nama_jurusan");
            $data = $data->selectRaw("IFNULL(b.cmapel, 0) AS cmapel, CASE WHEN b.kd_jurusan IS NOT NULL THEN 1 ELSE 0 END AS setup");
            $data = $data->orderBy("jurusan.nama_jurusan")->get();
            $this->respon = BaseService::ResponseSuccess(BaseService::MsgSuccess($this->pns, 1), $data);
        } catch (\Throwable $th) {
            $this->respon = BaseService::ResponseError($th->getMessage(), $this->error, $th->getCode());
        }
        return $this->SendResponse();
    }

    public function DataBobot(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), ["kd_jurusan" => "required"]);
            if ($validation->fails()) {
                $this->error = $validation->errors();
                throw new \Exception(BaseService::MessageCheckData(), 400);
            }
            $data = BobotService::Data("Maple")->where("bobot.kd_jurusan", $request->kd_jurusan);
            $data = $data->select("bobot.kd_matapelajaran", "mp.nama_matapelajaran", "bobot.bobot");
            $data = $data->orderBy("mp.nama_matapelajaran")->get();
            $this->respon = BaseService::ResponseSuccess(BaseService::MsgSuccess($this->pns, 1), $data);
        } catch (\Throwable $th) {
            $this->respon = BaseService::ResponseError($th->getMessage(), $this->error, $th->getCode());
        }
        return $this->SendResponse();
    }

    public function MapleReady(Request $request)
    {
        try {
            $kd_jurusan = !empty($request->kd_jurusan) ? $request->kd_jurusan : "";
            $data = MapelService::Data();
            $data = BobotService::Join($data, $kd_jurusan, "matapelajaran.kd_matapelajaran", "b", "leftJoin", "v2");
            $data = $data->whereNull("b.kd_matapelajaran");
            if (isset($request->dtmapel) && count($request->dtmapel) > 0) {
                $dt = $request->dtmapel;
                $data = $data->where(function ($q) use ($dt) {
                    foreach ($dt as $val) {
                        $q->where("matapelajaran.kd_matapelajaran", "<>", $val);
                    }
                });
            }
            $data = $data->select("matapelajaran.kd_matapelajaran", "matapelajaran.nama_matapelajaran");
            $data = $data->orderBy("matapelajaran.nama_matapelajaran")->get();

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
                "kd_jurusan" => "required",
                "dtmapel" => "required|array|min:1",
            ]);
            if ($validation->fails()) {
                $this->error = $validation->errors();
                throw new \Exception(BaseService::MessageCheckData(), 400);
            }

            // Check Jurusan
            $dtjurusan = JurusanService::Detail($request->kd_jurusan);
            // Check Jumlah Mapel
            $dtcmapel = BobotService::Data()->where("bobot.kd_jurusan", $request->kd_jurusan)->count();
            if ($dtcmapel > 0) {
                // Delete Data mapel
                BobotService::Data()->where("bobot.kd_jurusan", $request->kd_jurusan)->delete();
            }

            // Insert Data Mapel
            $total = 0;
            foreach ($request->dtmapel as $val) {
                // Check Mapel
                $dtmapel = MapelService::Detail($val["kd_matapelajaran"]);
                $total += $val["bobot"];

                $data = BobotService::new ();
                $data->kd_jurusan = $request->kd_jurusan;
                $data->kd_matapelajaran = $val["kd_matapelajaran"];
                $data->bobot = $val["bobot"];
                $data->created_at = Carbon::now();
                $data->updated_at = Carbon::now();
                $data->save();
            }
            // Check Total Harus 100
            if ($total != 100) {
                throw new \Exception("Total Bobot tidak 100%!", 400);
            }
            $data = BobotService::Data()->where("bobot.kd_jurusan", $request->kd_jurusan)->get();
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
            $data = BobotService::Data()->where("bobot.kd_jurusan", $request->kd_jurusan);

            // Check Jumlah Mapel
            $dtcmapel = $data->count();
            if ($dtcmapel > 0) {
                // Delete Data mapel
                BobotService::Data()->where("bobot.kd_jurusan", $request->kd_jurusan)->delete();
            } else {
                throw new \Exception(BaseService::MessageNotFound("Setup Bobot"), 400);
            }
            $data = $data->get();
            DB::commit();
            $this->respon = BaseService::ResponseSuccess(BaseService::MsgSuccess($this->pns, 3), $data);
        } catch (\Throwable $th) {
            DB::rollBack();
            $this->respon = BaseService::ResponseError($th->getMessage(), $this->error, $th->getCode());
        }
        return $this->SendResponse();
    }
}
