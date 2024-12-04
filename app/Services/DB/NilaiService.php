<?php

namespace App\Services\DB;

use App\Models\Process\DetailNilai;
use App\Models\Process\HeaderNilai;
use App\Services\BaseService;
use Illuminate\Support\Facades\DB;

class NilaiService
{
    public static function NewHeader()
    {
        return new HeaderNilai();
    }
    public static function DataHeader($tahun)
    {
        $data = HeaderNilai::distinct()->where("nilai_header.tahun", $tahun);
        return $data;
    }
    public static function DetailHeader($tahun, $nisn, $action = "Edit", $check = true)
    {
        $data = self::DataHeader($tahun)->where("nisn", $nisn)->first();
        if ($check) {
            if ($action == "Add") {
                if (!empty($data->nisn)) {
                    throw new \Exception(BaseService::MessageDataExists("Data Nilai NIS/NISN {$nisn}"), 400);
                }
            } else {
                if (empty($data->nisn)) {
                    throw new \Exception(BaseService::MessageNotFound("Data Nilai NIS/NISN {$nisn}"), 400);
                }
            }
        }
        return $data;
    }
    public static function JoinHeader($data, $tahun, $nisn, $alias = "nilai_header", $type = "join", $versi = "v1")
    {
        $data = $data->{$type}(with(new HeaderNilai)->getTable() . " AS {$alias}", function ($q) use ($alias, $tahun, $nisn, $versi) {
            $q->on("{$alias}.nisn", "=", ($versi == "v2" ? DB::raw("'{$nisn}'") : $nisn));
            $q->on("{$alias}.tahun", "=", DB::raw("'{$tahun}'"));
        });
        return $data;
    }

    public static function NewDetail()
    {
        return new DetailNilai();
    }

    public static function DataDetail($tahun, $list = "")
    {
        $data = DetailNilai::distinct()->where("detail_nilai.tahun", $tahun);
        if ($list == "Maple") {
            $data = MapelService::Join($data, "detail_nilai.kd_matapelajaran", "mp");
        }
        return $data;
    }
    public static function DetailDetail($tahun, $nisn, $kd_matapelajaran, $action = "Edit")
    {
        $data = self::DataDetail($tahun)->where([
            "nisn" => $nisn,
            "kd_matapelajaran" => $kd_matapelajaran,
        ])->first();
        if ($action == "Add") {
            if (!empty($data->nisn)) {
                throw new \Exception(BaseService::MessageDataExists("Data Nilai NIS/NISN {$nisn} dan Kode Mapel {$kd_matapelajaran}"), 400);
            }
        } else {
            if (empty($data->nisn)) {
                throw new \Exception(BaseService::MessageNotFound("Data Nilai NIS/NISN {$nisn} dan Kode Mapel {$kd_matapelajaran}"), 400);
            }
        }
        return $data;
    }
    public static function JoinDetail($data, $tahun, $nisn, $kd_matapelajaran, $alias = "detail_nilai", $type = "join", $versi = "v1")
    {
        $data = $data->{$type}(with(new DetailNilai)->getTable() . " AS {$alias}", function ($q) use ($alias, $tahun, $nisn, $kd_matapelajaran, $versi) {
            $q->on("{$alias}.nisn", "=", ($versi == "v2" ? DB::raw("'{$nisn}'") : $nisn));
            $q->on("{$alias}.kd_matapelajaran", "=", $kd_matapelajaran);
            $q->on("{$alias}.tahun", "=", DB::raw("'{$tahun}'"));
        });
        return $data;
    }
}
