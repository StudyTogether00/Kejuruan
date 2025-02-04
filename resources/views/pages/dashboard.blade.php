@extends('layout.Layout')

@section('content')
    @verbatim
        <style>
            h1 {
                color: #333;
                /* Tomato color */
                font-family: 'Arial', sans-serif;
                font-size: 36px;
                text-transform: uppercase;
            }

            .card {
                border: none;
                border-radius: 12px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                /* Soft shadow */
                overflow: hidden;
            }

            .card .card-header {
                background-color: #E91E63;
                color: black;
                padding: 20px;
                font-weight: bold;
                text-align: center;
                font-size: 20px;
                border-radius: 8px;
            }

            .card-body {
                background-color: white;
                /* Soft peach */
                color: #333;
                padding: 30px;
                font-size: 16px;
                line-height: 1.8;
            }

            p {
                margin-bottom: 20px;
            }

            .card-body p:last-child {
                margin-bottom: 0;
            }
        </style>
        <div class="container mt-5">
            <div class="card my-4">
                <div class="card-header">
                    SPK SAW
                </div>
                <div class="card-body">
                    <div style="text-align: center;">
                        <p><strong>Selamat Datang </strong></p>
                        <p><strong>di Sistem Pendukung Keputusan Pemilihan Jurusan Berbasis Web Menggunakan Metode Simple
                                Additive Weighting</strong></p>
                    </div>
                </div>
            </div>
        </div>
    @endverbatim
@endsection
