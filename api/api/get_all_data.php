<?php
// Header untuk memberitahu browser bahwa ini adalah file JSON
header('Content-Type: application/json');

require_once '../includes/db.php';

// Siapkan array untuk respons akhir
$response = [
    'status' => 'success',
    'units' => [],
    'idioms' => []
];

try {
    // 1. Ambil semua data dari tabel units
    $sql_units = "SELECT id, name FROM units ORDER BY id ASC";
    $result_units = $conn->query($sql_units);
    if ($result_units) {
        while ($row = $result_units->fetch_assoc()) {
            $response['units'][] = $row;
        }
    }

    // 2. Ambil semua data dari tabel idioms
    // Kita ambil semua kolom agar bisa digunakan di halaman detail nanti
    $sql_idioms = "SELECT id, unit_id, idiom, meaning_id, example_sentence, sentence_translation, example_conversation FROM idioms ORDER BY idiom ASC";
    $result_idioms = $conn->query($sql_idioms);
    if ($result_idioms) {
        while ($row = $result_idioms->fetch_assoc()) {
            $response['idioms'][] = $row;
        }
    }

} catch (Exception $e) {
    // Jika terjadi error, kirim respons error
    http_response_code(500); // Internal Server Error
    $response['status'] = 'error';
    $response['message'] = 'Gagal mengambil data dari database: ' . $e->getMessage();
}

$conn->close();

// Cetak hasil akhir dalam format JSON
echo json_encode($response);
?>
