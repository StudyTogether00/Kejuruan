<?php

namespace App\Http\Controllers\BE\Process;

use App\Http\Controllers\BE\BaseController;
use App\Services\BaseService;
use App\Services\DB\MapelService;
use App\Services\DB\NilaiService;
use App\Services\DB\SiswaService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class NilaiController extends BaseController
{
    protected $pns = "Data Nilai Siswa";
    public function __construct()
    {
        parent::__construct();
    }

    public function Lists(Request $request)
    {
        try {
            $data = [];
            $tahun = $request->tahun;
            if (!empty($tahun)) {
                $data = SiswaService::Data();
                $data = NilaiService::JoinHeader($data, $tahun, "siswa.nisn", "hn", "leftJoin");
                $data = $data->select("siswa.nisn", "siswa.nama_siswa", "siswa.jenis_kelamin");
                $data = $data->selectRaw("'{$tahun}' AS tahun, IFNULL(hn.`nilai_rata-rata`, 0) AS rata2, CASE WHEN hn.nisn IS NOT NULL THEN 1 ELSE 0 END AS setup");
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
            $validation = Validator::make($request->all(), ["tahun" => "required", "nisn" => "required"]);
            if ($validation->fails()) {
                $this->error = $validation->errors();
                throw new \Exception(BaseService::MessageCheckData(), 400);
            }
            $data = MapelService::Data();
            $data = NilaiService::JoinDetail($data, $request->tahun, $request->nisn, "matapelajaran.kd_matapelajaran", "dn", "leftJoin", "v2");
            $data = $data->select("matapelajaran.kd_matapelajaran", "matapelajaran.nama_matapelajaran");
            $data = $data->selectRaw("IFNULL(dn.nilai, 0) AS nilai");
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
