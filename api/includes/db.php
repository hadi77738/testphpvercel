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
} 
?>

