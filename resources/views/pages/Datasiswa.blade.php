@extends("layout.Layout")
@section('content')
<!-- resources/views/loop.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loop Example</title>
</head>
<body>
    <h1>Contoh While Loop di Blade</h1>

    @php
    $x = 1; // Inisialisasi variabel $x dengan nilai 1
while ($x <= 5) { // Memulai loop while dengan kondisi
    echo "Angka: $x <br>"; // Menampilkan nilai $x dan baris baru
    $x = $x + 1; // Menambahkan 1 ke $x (increment) menggunakan operator penugasan
}
    @endphp

<h1>Contoh do While Loop di Blade</h1>
    @php
$x = 1;
do {
    echo "Angka: $x <br>";
    $x++;
} while ($x <= 5);
@endphp

<h1>Contoh for Loop di Blade</h1>
    @php
    for ($i = 1; $i <= 20; $i++) {
    if ($i % 2 != 0) { // Mengecek apakah $i adalah ganjil
        echo "Nilai ganjil: $i <br>";
    }
}
@endphp

<h1>Contoh foreach Loop di Blade</h1>
    @php
    $buah = ["apel", "pisang", "jeruk"];

foreach ($buah as $item) {
    echo "Buah: $item <br>";
}
@endphp


</body>
</html>
@endsection