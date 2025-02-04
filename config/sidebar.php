<?php
return [
    "menu" => [
        [
            "icon" => '<i class="material-icons">dashboard</i>',
            "title" => "Dashboard",
            "url" => "/",
        ],
        [
            "icon" => '<i class="material-icons">content_copy</i>',
            "title" => "Master Data",
            "sub_menu" => [
                [
                    "icon" => '<span class="sidebar-mini">DJ</span>',
                    "title" => "Data Jurusan",
                    "url" => "MasterData/Jurusan",
                ],
                [
                    "icon" => '<span class="sidebar-mini">DS</span>',
                    "title" => "Data Siswa",
                    "url" => "MasterData/Siswa",
                ],
                [
                    "icon" => '<span class="sidebar-mini">DK</span>',
                    "title" => "Data Kriteria",
                    "url" => "MasterData/MataPelajaran",
                ],
                [
                    "icon" => '<span class="sidebar-mini">DB</span>',
                    "title" => "Data Bobot",
                    "url" => "MasterData/Bobot",
                ],
            ],
        ],
        [
            "icon" => '<i class="material-icons">assignment_add</i>',
            "title" => "Penilaian",
            "url" => "Process/Nilai",
        ],
        [
            "icon" => '<i class="material-icons">print</i>',
            "title" => "Report",
            "sub_menu" => [
                [
                    "icon" => '<span class="sidebar-mini">N</span>',
                    "title" => "Normalisasi",
                    "url" => "Report/Normalisasi",
                ],
            ],
        ],
    ],
    "user" => [
        [
            "icon" => '<i class="material-icons">dashboard</i>',
            "title" => "Dashboard",
            "url" => "/",
        ],
        [
            "icon" => '<i class="material-icons">print</i>',
            "title" => "Report",
            "sub_menu" => [
                [
                    "icon" => '<span class="sidebar-mini">N</span>',
                    "title" => "Normalisasi",
                    "url" => "Report/Normalisasi",
                ],
            ],
        ],
    ],
];
