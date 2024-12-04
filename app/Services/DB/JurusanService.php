<?php

namespace App\Services\DB;

use App\Models\MasterData\Jurusan;
use App\Services\BaseService;

class JurusanService
{
    public static function new ()
    {
        return new Jurusan();
    }

    public static function Data()
    {
        $data = Jurusan::distinct();
        return $data;
    }

    public static function Detail($kd_jurusan, $action = "Edit")
    {
        $data = self::Data()->find($kd_jurusan);
        if ($action == "Add") {
            if (!empty($data->kd_jurusan)) {
                throw new \Exception(BaseService::MessageDataExists("Kode Jurusan {$kd_jurusan}"), 400);
            }
        } else {
            if (empty($data->kd_jurusan)) {
                throw new \Exception(BaseService::MessageNotFound("Kode Jurusan {$kd_jurusan}"), 400);
            }
        }
        return $data;
    }

    public static function Join($data, $kd_jurusan, $alias = "jurusan", $type = "join")
    {
        $data = $data->{$type}(with(new Jurusan)->getTable() . " AS {$alias}", function ($q) use ($alias, $kd_jurusan) {
            $q->on("{$alias}.kd_jurusan", "=", $kd_jurusan);
        });
        return $data;
    }
}
