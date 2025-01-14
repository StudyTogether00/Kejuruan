@extends("layout.Layout")

@section('content')
    @verbatim
    <style>
    h1 {
        color: #333; /* Tomato color */
        font-family: 'Arial', sans-serif;
        font-size: 36px;
        text-transform: uppercase;
    }

    .card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Soft shadow */
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
        background-color: white; /* Soft peach */
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
            Pengertian SPK SAW
        </div>
        <div class="card-body">
            <p>Sistem Pendukung Keputusan (SPK) dengan metode Simple Additive Weighting (SAW) adalah salah satu metode pengambilan keputusan yang digunakan untuk memilih alternatif terbaik berdasarkan beberapa kriteria yang telah ditentukan. 
            Metode ini bekerja dengan cara menjumlahkan bobot setiap kriteria setelah dikalikan dengan nilai dari alternatif pada kriteria tersebut. 
            Hasil dari metode ini berupa nilai yang menunjukkan peringkat dari masing-masing alternatif.</p>
            <p><strong>Langkah-langkah utama dalam SAW</strong> meliputi:</p>
            <ol>
                <li>Menentukan kriteria yang digunakan dalam pengambilan keputusan.</li>
                <li>Memberikan bobot pada setiap kriteria sesuai dengan tingkat kepentingannya.</li>
                <li>Menilai setiap alternatif berdasarkan kriteria yang telah ditentukan.</li>
                <li>Menghitung total nilai untuk setiap alternatif dengan menjumlahkan hasil perkalian antara bobot dan nilai alternatif pada setiap kriteria.</li>
                <li>Menentukan alternatif terbaik berdasarkan nilai tertinggi.</li>
            </ol>

            <p>Metode SAW memberikan kemudahan dalam memilih alternatif terbaik secara objektif dengan mempertimbangkan berbagai kriteria yang ada.</p>
        </div>
    </div>
</div>
    @endverbatim
@endsection