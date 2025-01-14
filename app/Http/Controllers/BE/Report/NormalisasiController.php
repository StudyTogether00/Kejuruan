<?php

namespace App\Http\Controllers\BE\Report;

use App\Http\Controllers\BE\BaseController;
use App\Services\BaseService;
use App\Services\DB\BobotService;
use App\Services\DB\JurusanService;
use App\Services\DB\NilaiService;
use App\Services\DB\SiswaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class NormalisasiController extends BaseController
{
    protected $pns = "Data Normalisasi";
    public function __construct()
    {
        parent::__construct();
    }

    public function DataKeputusan(Request $request)
    {
        try {
            $data = [];
            $tahun = $request->tahun;
            if (!empty($tahun)) {
                // Data Nilai
                $nilai = NilaiService::DataDetail($tahun);
                $nilai = BobotService::Join($nilai, "", "detail_nilai.kd_matapelajaran", "b", "join", "v3");
                $nilai = $nilai->select(
                    "detail_nilai.tahun",
                    "detail_nilai.nisn",
                    "detail_nilai.kd_matapelajaran",
                    "detail_nilai.nilai",
                    "b.kd_jurusan",
                    "b.bobot"
                );
                $nilai = $nilai->toSql();

                //Group Data Nilai By Jurusan
                $group = DB::table(DB::raw("($nilai) dn"));
                $group = $group->select("dn.tahun", "dn.nisn", "dn.kd_jurusan");
                $group = $group->selectRaw(
                    "SUM(dn.nilai * dn.bobot / 100) AS nilai,
                    ROW_NUMBER() OVER (PARTITION by dn.tahun, dn.nisn ORDER BY SUM(dn.nilai * dn.bobot / 100) DESC) rn"
                );
                $group = $group->groupBy("dn.tahun", "dn.nisn", "dn.kd_jurusan");

                $data = SiswaService::Data();
                $data = $data->joinSub($group, 'dn', function ($join) {
                    $join->on('dn.nisn', '=', 'siswa.nisn');
                    $join->on('dn.rn', '=', DB::raw("1"));
                });
                $data = JurusanService::Join($data, "dn.kd_jurusan", "j");
                $data = $data->select("dn.tahun", "siswa.nisn", "siswa.nama_siswa", "dn.kd_jurusan", "j.nama_jurusan", "dn.nilai");
                $data = $data->orderBy("siswa.nama_siswa");
                // dd($data->toSql());
                $data = $data->get();
            }
            $this->respon = BaseService::ResponseSuccess(BaseService::MsgSuccess($this->pns, 1), $data);
        } catch (\Throwable $th) {
            $this->respon = BaseService::ResponseError($th->getMessage(), $this->error, $th->getCode());
        }
        return $this->SendResponse();
    }

    public function DataNilai(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), ["tahun" => "required"]);
            if ($validation->fails()) {
                $this->error = $validation->errors();
                throw new \Exception(BaseService::MessageCheckData(), 400);
            }
            $data = [];
            if (!empty($request->kd_jurusan) && !empty($request->nisn)) {
                // data matrix min and max nilai per tahun
                $dtmatrix = NilaiService::DataDetail();
                $dtmatrix = $dtmatrix->select("tahun", "kd_matapelajaran");
                $dtmatrix = $dtmatrix->selectRaw("MIN(nilai) AS minnilai, MAX(nilai) AS maxnilai");
                $dtmatrix = $dtmatrix->groupBy("tahun", "kd_matapelajaran");

                $data = BobotService::Data("Maple");
                $data = NilaiService::JoinDetail($data, $request->tahun, $request->nisn, "bobot.kd_matapelajaran", "dn", "leftJoin", "v2");
                $data = $data->leftJoinSub($dtmatrix, "mt", function ($q) {
                    $q->on("mt.tahun", "=", "dn.tahun");
                    $q->on("mt.kd_matapelajaran", "=", "dn.kd_matapelajaran");
                });
                $data = $data->where("bobot.kd_jurusan", $request->kd_jurusan);
                $data = $data->select("bobot.kd_jurusan", "bobot.kd_matapelajaran", "mp.nama_matapelajaran", "bobot.bobot");
                $data = $data->selectRaw("IFNULL(dn.nilai, 0) AS nilai, IFNULL(mt.minnilai, 0) AS minnilai, IFNULL(mt.maxnilai, 0) AS maxnilai");
                $data = $data->orderBy("mp.nama_matapelajaran")->get();
            }

            $this->respon = BaseService::ResponseSuccess(BaseService::MsgSuccess($this->pns, 1), $data);
        } catch (\Throwable $th) {
            $this->respon = BaseService::ResponseError($th->getMessage(), $this->error, $th->getCode());
        }
        return $this->SendResponse();
    }

    public function DownloadReport(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), ["tahun" => "required"]);
            if ($validation->fails()) {
                $this->error = $validation->errors();
                throw new \Exception(BaseService::MessageCheckData(), 400);
            }
            $data = [];
            if (!empty($request->tahun)) {
                $data = [];
            }

            $this->respon = BaseService::ResponseSuccess(BaseService::MsgSuccess($this->pns, 1), $data);
        } catch (\Throwable $th) {
            $this->respon = BaseService::ResponseError($th->getMessage(), $this->error, $th->getCode());
        }
        return $this->SendResponse();
    }
}
