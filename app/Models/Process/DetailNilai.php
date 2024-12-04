<?php

namespace App\Models\Process;

use App\Models\BaseModel;

class DetailNilai extends BaseModel
{
    protected $table = 'detail_nilai';
    protected $primaryKey = ['tahun', 'nisn', 'kd_matapelajaran'];
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        "tahun",
        "nisn",
        "kd_matapelajaran",
        "nilai",
        "created_at",
        "update_at",
    ];
}
