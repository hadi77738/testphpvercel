<?php
// Mengatur header untuk output JSON dan izin akses (CORS)
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Menyertakan file koneksi database
require_once '../includes/db.php';

// --- Pengaturan Kuis ---
const NUM_QUESTIONS = 5; // Jumlah pertanyaan yang akan dibuat
const NUM_OPTIONS = 4;   // Jumlah pilihan jawaban per pertanyaan (1 benar, 3 salah)

// 1. Ambil semua data idiom dan artinya dari database
$result = $conn->query("SELECT idiom, meaning_id FROM idioms");
if (!$result) {
    echo json_encode(['error' => 'Gagal mengambil data dari database.']);
    exit;
}

$all_idioms = [];
while ($row = $result->fetch_assoc()) {
    $all_idioms[] = $row;
}

// 2. Periksa apakah jumlah idiom cukup untuk membuat kuis
if (count($all_idioms) < NUM_OPTIONS) {
    echo json_encode(['error' => 'Data idiom tidak cukup untuk membuat kuis. Minimal butuh ' . NUM_OPTIONS . ' idiom.']);
    exit;
}

// 3. Acak semua idiom untuk mendapatkan urutan yang berbeda setiap kali
shuffle($all_idioms);

// 4. Siapkan array untuk menampung pertanyaan kuis
$quiz_questions = [];
$used_indices = []; // Untuk memastikan pertanyaan tidak berulang

// Ambil jumlah pertanyaan yang sebenarnya, jaga-jaga jika idiom kurang dari NUM_QUESTIONS
$actual_num_questions = min(NUM_QUESTIONS, count($all_idioms));

for ($i = 0; $i < $actual_num_questions; $i++) {
    // Ambil satu idiom sebagai pertanyaan dan jawaban benar
    $correct_idiom_data = $all_idioms[$i];
    
    // Siapkan array pilihan jawaban, dimulai dengan jawaban yang benar
    $options = [$correct_idiom_data['meaning_id']];
    
    // Buat daftar idiom lain untuk diambil sebagai jawaban salah
    $other_idioms = $all_idioms;
    unset($other_idioms[$i]); // Hapus idiom yang benar dari daftar

    // Acak sisa idiom untuk mendapatkan pilihan jawaban salah yang acak
    shuffle($other_idioms);

    // Ambil (NUM_OPTIONS - 1) jawaban salah yang unik
    $incorrect_options_added = 0;
    foreach($other_idioms as $incorrect_idiom) {
        if ($incorrect_options_added < (NUM_OPTIONS - 1)) {
            // Pastikan jawaban salah tidak sama dengan jawaban benar (jaga-jaga ada duplikat)
            if (!in_array($incorrect_idiom['meaning_id'], $options)) {
                $options[] = $incorrect_idiom['meaning_id'];
                $incorrect_options_added++;
            }
        } else {
            break;
        }
    }
    
    // Acak urutan pilihan jawaban agar posisi jawaban benar tidak selalu sama
    shuffle($options);
    
    // Susun data pertanyaan menjadi format yang rapi
    $question_data = [
        'question_idiom' => $correct_idiom_data['idiom'],
        'correct_answer' => $correct_idiom_data['meaning_id'],
        'options' => $options
    ];
    
    // Tambahkan pertanyaan ke dalam daftar kuis
    $quiz_questions[] = $question_data;
}

// 5. Kembalikan data kuis dalam format JSON
echo json_encode($quiz_questions);

// Tutup koneksi database
$conn->close();
