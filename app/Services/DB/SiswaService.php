<?php

namespace App\Services\DB;

use App\Models\MasterData\Siswa;
use App\Services\BaseService;

class SiswaService
{
    public static function new ()
    {
        return new Siswa();
    }

    public static function Data()
    {
        $data = Siswa::distinct();
        return $data;
    }

    public static function Detail($nisn, $action = "Edit")
    {
        $data = self::Data()->find($nisn);
        if ($action == "Add") {
            if (!empty($data->nisn)) {
                throw new \Exception(BaseService::MessageDataExists("NISN {$nisn}"), 400);
            }
        } else {
            if (empty($data->nisn)) {
                throw new \Exception(BaseService::MessageNotFound("NISN {$nisn}"), 400);
            }
        }
        return $data;
    }
}
