@extends("layout.Layout")
@section('content')
<style>
/* Styling tabel */
table {
            width: 100%;
            border-collapse: collapse;
            background-color: #f9f9f9; /* Warna latar belakang putih terang */
            color: #333; /* Warna teks abu-abu gelap */
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); /* Sedikit bayangan */
        }

        th, td {
            padding: 12px;
            border: 1px solid #ddd; /* Border abu-abu terang */
            text-align: left;
        }

        th {
            background-color: #ff4081; /* Warna pink cerah untuk header tabel */
            color: white; /* Warna teks putih di header */
        }

        tr:nth-child(even) {
            background-color: #f2f2f2; /* Warna latar belakang baris genap abu-abu terang */
        }

        /* Hover effect untuk baris tabel */
        tr:hover {
            background-color: #dcdcdc; /* Warna pink lembut saat hover */
        }

        /* Style heading tabel */
        h1 {
            color: #ff4081; /* Warna pink cerah untuk judul */
            font-family: 'Roboto', sans-serif; /* Font keluarga Roboto */
        }
    </style>
</head>
<body>
<h2>Data Jurusan</h2>
<!-- Membuat tabel -->
<table border="1">
    <!-- Membuat baris judul -->
    <thead>
        <tr>
            <th>No</th>
            <th>Nama Jurusan</th>
            <th></th>
        </tr>
    </thead>

    <!-- Membuat baris isi tabel -->
    <tbody>
        <tr>
            <td>1</td>
            <td>IPA</td>
        </tr>
        <tr>
            <td>2</td>
            <td>IPS</td>
        </tr>
    </tbody>
</table>
</body>
</html>    
@endsection