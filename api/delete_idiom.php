<?php
session_start();
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

require_once 'includes/db.php';

// Cek apakah ID ada dan valid
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $idiom_id = intval($_GET['id']);

    // Siapkan statement delete
    $sql = "DELETE FROM idioms WHERE id = ?";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $idiom_id);

        if ($stmt->execute()) {
            // Cek apakah ada baris yang terhapus
            if ($stmt->affected_rows > 0) {
                $_SESSION['message'] = "Idiom berhasil dihapus.";
                $_SESSION['message_type'] = "success";
            } else {
                $_SESSION['message'] = "Idiom tidak ditemukan atau sudah dihapus.";
                $_SESSION['message_type'] = "danger";
            }
        } else {
            $_SESSION['message'] = "Terjadi kesalahan saat mencoba menghapus data.";
            $_SESSION['message_type'] = "danger";
        }
        $stmt->close();
    }
} else {
    $_SESSION['message'] = "Permintaan tidak valid. ID tidak ditemukan.";
    $_SESSION['message_type'] = "danger";
}

$conn->close();

// Redirect kembali ke halaman admin
header("location: admin.php");
exit();
?>
