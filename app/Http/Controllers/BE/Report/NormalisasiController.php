<?php

namespace App\Http\Controllers\BE\Report;

use App\Http\Controllers\BE\BaseController;
use App\Services\BaseService;
use App\Services\DB\BobotService;
use App\Services\DB\JurusanService;
use App\Services\DB\MapelService;
use App\Services\DB\NilaiService;
use App\Services\DB\SiswaService;
use Carbon\Carbon;
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
                $group = $group->selectRaw("SUM(dn.nilai * dn.bobot / 100) AS nilai, ROW_NUMBER() OVER (PARTITION by tahun, nisn ORDER BY nilai DESC) rn");
                $group = $group->groupBy("dn.tahun", "dn.nisn", "dn.kd_jurusan");

                $data = SiswaService::Data();
                $data = $data->joinSub($group, 'dn', function ($join) {
                    $join->on('dn.nisn', '=', 'siswa.nisn');
                    $join->on('dn.rn', '=', DB::raw("1"));
                });
                $data = JurusanService::Join($data, "dn.kd_jurusan", "j");
                $data = $data->select("dn.tahun", "siswa.nisn", "siswa.nama_siswa", "dn.kd_jurusan", "j.nama_jurusan", "dn.nilai");
                $data = $data->orderBy("siswa.nama_siswa")->get();
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
                $data = MapelService::Data();
                $data = NilaiService::JoinDetail($data, $request->tahun, $request->nisn, "matapelajaran.kd_matapelajaran", "dn", "leftJoin", "v2");
                $data = $data->select("matapelajaran.kd_matapelajaran", "matapelajaran.nama_matapelajaran");
                $data = $data->selectRaw("IFNULL(dn.nilai, 0) AS nilai");
                $data = $data->orderBy("matapelajaran.nama_matapelajaran")->get();
            }

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
                "tahun" => "required",
                "nisn" => "required",
                "dtmapel" => "required|array|min:1",
            ]);
            if ($validation->fails()) {
                $this->error = $validation->errors();
                throw new \Exception(BaseService::MessageCheckData(), 400);
            }

            // Check Siswa
            $dtsiswa = SiswaService::Detail($request->nisn);

            $timeupdate = Carbon::now();
            // Save or Update Header Nilai
            $dtheader = NilaiService::DetailHeader($request->tahun, $request->nisn, "", false);
            if (empty($dtheader->nisn)) {
                $dtheader = NilaiService::NewHeader();
                $dtheader->tahun = $request->tahun;
                $dtheader->nisn = $request->nisn;
                $dtheader->{"nilai_rata-rata"} = 0;
                $dtheader->created_at = $timeupdate;
                $dtheader->save();
            } else {
                // Delete Data Detail Nilai
                NilaiService::DataDetail($request->tahun)->where("detail_nilai.nisn", $request->nisn)->delete();
            }

            // Insert Data Detail Nilai
            $totalNilai = 0;
            $countNilai = 0;
            foreach ($request->dtmapel as $val) {
                // Check Mapel
                $dtmapel = MapelService::Detail($val["kd_matapelajaran"]);

                $dtdetail = NilaiService::NewDetail();
                $dtdetail->tahun = $dtheader->tahun;
                $dtdetail->nisn = $dtheader->nisn;
                $dtdetail->kd_matapelajaran = $val["kd_matapelajaran"];
                $dtdetail->nilai = $val["nilai"];
                $dtdetail->created_at = $timeupdate;
                $dtdetail->updated_at = $timeupdate;
                $dtdetail->save();

                $totalNilai += $val["nilai"];
                $countNilai++;
            }

            // update nilai rata2
            $dtheader->{"nilai_rata-rata"} = $totalNilai / $countNilai;
            $dtheader->updated_at = $timeupdate;
            $dtheader->save();

            $data = $dtheader;
            $data->detail = NilaiService::DataDetail($dtheader->tahun)->where("detail_nilai.nisn", $dtheader->nisn)->get();
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
            $validation = Validator::make($request->all(), ["tahun" => "required", "nisn" => "required"]);
            if ($validation->fails()) {
                $this->error = $validation->errors();
                throw new \Exception(BaseService::MessageCheckData(), 400);
            }

            // DataHeader
            $dtheader = NilaiService::DetailHeader($request->tahun, $request->nisn);

            $data = $dtheader;
            $data->detail = NilaiService::DataDetail($dtheader->tahun)->where("detail_nilai.nisn", $dtheader->nisn)->get();

            // Delete Data
            NilaiService::DataDetail($dtheader->tahun)->where("detail_nilai.nisn", $dtheader->nisn)->delete();
            $dtheader->delete();

            DB::commit();
            $this->respon = BaseService::ResponseSuccess(BaseService::MsgSuccess($this->pns, 3), $data);
        } catch (\Throwable $th) {
            DB::rollBack();
            $this->respon = BaseService::ResponseError($th->getMessage(), $this->error, $th->getCode());
        }
        return $this->SendResponse();
    }
}
