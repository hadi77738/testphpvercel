<?php
header('Content-Type: application/json');
require_once '../includes/db.php';

$response = [
    'status' => 'success',
    'data' => []
];

// Pastikan keyword ada
if (!isset($_GET['keyword']) || empty(trim($_GET['keyword']))) {
    http_response_code(400); // Bad Request
    $response['status'] = 'error';
    $response['message'] = 'Keyword pencarian tidak boleh kosong.';
    echo json_encode($response);
    exit;
}

$keyword = trim($_GET['keyword']);
$search_term = "%" . $keyword . "%";

// Gunakan LEFT JOIN untuk mendapatkan nama unit
// Pilih semua kolom dari idioms (i.*) dan kolom `name` dari units (diberi alias unit_name)
$sql = "SELECT i.*, u.name as unit_name 
        FROM idioms i 
        LEFT JOIN units u ON i.unit_id = u.id 
        WHERE i.idiom LIKE ? OR i.meaning_id LIKE ?";

if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("ss", $search_term, $search_term);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $response['data'][] = $row;
    }
    
    $stmt->close();
} else {
    http_response_code(500);
    $response['status'] = 'error';
    $response['message'] = 'Gagal menyiapkan statement pencarian.';
}

$conn->close();
echo json_encode($response);
?>

