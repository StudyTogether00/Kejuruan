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
}
