<?php
// api/index.php

// Ambil path URL yang diminta, contoh: "/", "/quiz", "/admin.php"
$request_uri = $_SERVER['REQUEST_URI'];

// Hapus query string jika ada (contoh: /search?keyword=test -> /search)
$request_path = strtok($request_uri, '?');

// Logika Router Sederhana
switch ($request_path) {
    case '/':
        // Jika pengguna mengakses halaman utama
        require __DIR__ . '/home.php';
        break;

    case '/quiz':
    case '/quiz.php':
        // Jika pengguna mengakses halaman kuis
        require __DIR__ . '/quiz.php';
        break;

    case '/admin':
    case '/admin.php':
        // Jika pengguna mengakses halaman admin
        require __DIR__ . '/admin.php';
        break;

    // Tambahkan case lain untuk halaman login, about, dll.

    default:
        // Jika halaman tidak ditemukan
        http_response_code(404);
        echo "<h1>404 Not Found</h1>";
        break;
}