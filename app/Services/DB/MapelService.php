<?php

namespace App\Services\DB;

use App\Models\MasterData\MataPelajaran;
use App\Services\BaseService;

class MapelService
{
    public static function new ()
    {
        return new MataPelajaran();
    }

    public static function Data()
    {
        $data = MataPelajaran::distinct();
        return $data;
    }

    public static function Detail($kd_matapelajaran, $action = "Edit")
    {
        $data = self::Data()->find($kd_matapelajaran);
        if ($action == "Add") {
            if (!empty($data->kd_matapelajaran)) {
                throw new \Exception(BaseService::MessageDataExists("Kode Mata Pelajaran {$kd_matapelajaran}"), 400);
            }
        } else {
            if (empty($data->kd_matapelajaran)) {
                throw new \Exception(BaseService::MessageNotFound("Kode Mata Pelajaran {$kd_matapelajaran}"), 400);
            }
        }
        return $data;
    }
}
