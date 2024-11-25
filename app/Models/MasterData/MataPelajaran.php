<?php

namespace App\Models\MasterData;

use App\Models\BaseModel;

class MataPelajaran extends BaseModel
{
    protected $table = 'matapelajaran';
    protected $primaryKey = 'kd_matapelajaran';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        "kd_matapelajaran",
        "nama_matapelajaran",
        "created_at",
        "update_at",
    ];
}
