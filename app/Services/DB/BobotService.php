<?php

namespace App\Services\DB;

use App\Models\MasterData\Bobot;
use App\Services\BaseService;
use Illuminate\Support\Facades\DB;

class BobotService
{
    public static function new ()
    {
        return new Bobot();
    }

    public static function Data($list = "")
    {
        $data = Bobot::distinct();
        if ($list == "Maple") {
            $data = MapelService::Join($data, "bobot.kd_matapelajaran", "mp");
        }
        return $data;
    }

    public static function Detail($kd_jurusan, $kd_matapelajaran, $action = "Edit")
    {
        $data = self::Data()->where([
            "kd_jurusan" => $kd_jurusan,
            "kd_matapelajaran" => $kd_matapelajaran,
        ])->first();
        if ($action == "Add") {
            if (!empty($data->kd_jurusan)) {
                throw new \Exception(BaseService::MessageDataExists("Data Bobot Kode Jurusan {$kd_jurusan} dan Kode Mapel {$kd_matapelajaran}"), 400);
            }
        } else {
            if (empty($data->kd_jurusan)) {
                throw new \Exception(BaseService::MessageNotFound("Data Bobot Kode Jurusan {$kd_jurusan} dan Kode Mapel {$kd_matapelajaran}"), 400);
            }
        }
        return $data;
    }

    public static function Join($data, $kd_jurusan, $kd_matapelajaran, $alias = "bobot", $type = "join", $versi = "v1")
    {
        $data = $data->{$type}(with(new Bobot)->getTable() . " AS {$alias}", function ($q) use ($alias, $kd_jurusan, $kd_matapelajaran, $versi) {
            $q->on("{$alias}.kd_jurusan", "=", ($versi == "v2" ? DB::raw("'{$kd_jurusan}'") : $kd_jurusan));
            $q->on("{$alias}.kd_matapelajaran", "=", $kd_matapelajaran);
        });
        return $data;
    }
}
