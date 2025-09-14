<?php
// Header untuk memberitahu browser bahwa ini adalah file JSON
header('Content-Type: application/json');
// Header untuk mengizinkan akses dari mana saja (Cross-Origin Resource Sharing)
header('Access-Control-Allow-Origin: *');

// Sertakan file koneksi database
// '..' berarti naik satu level folder
include '../includes/db.php';

// Inisialisasi array untuk menampung data
$response = [];
$idioms = [];

// Query untuk mengambil semua idiom, diurutkan berdasarkan abjad
$sql = "SELECT id, idiom, meaning_id, example_sentence, sentence_translation, example_conversation FROM idioms ORDER BY idiom ASC";
$result = $conn->query($sql);

if ($result) {
    if ($result->num_rows > 0) {
        // Jika ada data, masukkan ke dalam array $idioms
        while ($row = $result->fetch_assoc()) {
            $idioms[] = $row;
        }
        // Set status sukses dan tambahkan data ke response
        $response['status'] = 'success';
        $response['data'] = $idioms;
    } else {
        // Jika tidak ada data
        $response['status'] = 'success';
        $response['message'] = 'Tidak ada idiom yang ditemukan.';
        $response['data'] = []; // Kirim array kosong
    }
} else {
    // Jika query gagal
    $response['status'] = 'error';
    $response['message'] = 'Gagal menjalankan query: ' . $conn->error;
}

// Tutup koneksi database
$conn->close();

// Tampilkan response dalam format JSON
echo json_encode($response);
?>
