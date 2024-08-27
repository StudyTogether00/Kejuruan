<?php

namespace App\Models;

class Detail_Nilai extends BaseModel
{
    protected $table = 'detail_nilai';
    protected $primaryKey = ['tahun', 'nisn', 'kd_matapelajaran'];
    protected $keyType = ['integer', 'string', 'string'];
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