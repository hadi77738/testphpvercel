<?php
// Informasi koneksi dari detail Supabase Anda
$host = 'aws-1-ap-southeast-1.pooler.supabase.com';
$port = '5432';
$dbname = 'postgres';
$user = 'postgres.ljveqfmifeqquebjvwau';
$password = 'sugihmanik1';

// Membuat string koneksi untuk PostgreSQL, tambahkan sslmode=require
$conn_string = "host={$host} port={$port} dbname={$dbname} user={$user} password={$password} sslmode=require";

// Mencoba terhubung ke database
$dbconn = pg_connect($conn_string);

// Memeriksa apakah koneksi berhasil atau gagal
if (!$dbconn) {
    // Koneksi gagal, tampilkan pesan error
    die("Error: Tidak dapat terhubung ke database PostgreSQL.");
} else {
    // Koneksi berhasil
    echo "<h3>Berhasil terhubung ke database Supabase!</h3>";

    // Contoh query untuk mengambil data dari tabel 'idioms'
    $query = 'SELECT idiom, meaning_id FROM idioms LIMIT 5';

    $result = pg_query($dbconn, $query);

    // Memeriksa apakah query berhasil dijalankan
    if (!$result) {
        echo "Terjadi error saat menjalankan query.<br>";
        exit;
    }

    // Menampilkan hasil query
    echo "<h4>Menampilkan 5 data pertama dari tabel 'idioms':</h4>";
    echo "<ul>";
    while ($row = pg_fetch_assoc($result)) {
        echo "<li><strong>" . htmlspecialchars($row['idiom']) . ":</strong> " . htmlspecialchars($row['meaning_id']) . "</li>";
    }
    echo "</ul>";

    // Menutup koneksi database
    pg_close($dbconn);
}
?>

