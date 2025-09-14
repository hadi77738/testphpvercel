<?php
session_start();
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

require_once 'includes/db.php';
$page_title = 'Edit Idiom';

// Inisialisasi variabel
$idiom_data = null;
$error_msg = "";
$idiom_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($idiom_id <= 0) {
    $_SESSION['message'] = "ID Idiom tidak valid.";
    $_SESSION['message_type'] = "danger";
    header("location: admin.php");
    exit;
}

// Ambil data idiom yang akan diedit
$sql_idiom = "SELECT * FROM idioms WHERE id = ?";
if ($stmt_idiom = $conn->prepare($sql_idiom)) {
    $stmt_idiom->bind_param("i", $idiom_id);
    $stmt_idiom->execute();
    $result = $stmt_idiom->get_result();
    if ($result->num_rows == 1) {
        $idiom_data = $result->fetch_assoc();
    } else {
        $_SESSION['message'] = "Idiom tidak ditemukan.";
        $_SESSION['message_type'] = "danger";
        header("location: admin.php");
        exit;
    }
    $stmt_idiom->close();
}

// Ambil semua unit dari database untuk dropdown
$units = [];
$sql_units = "SELECT id, name FROM units ORDER BY id ASC";
$result_units = $conn->query($sql_units);
if ($result_units->num_rows > 0) {
    while ($row = $result_units->fetch_assoc()) {
        $units[] = $row;
    }
}

// Proses form saat disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $idiom = trim($_POST['idiom']);
    $meaning_id = trim($_POST['meaning_id']);
    $example_sentence = trim($_POST['example_sentence']);
    $sentence_translation = trim($_POST['sentence_translation']);
    $example_conversation = trim($_POST['example_conversation']);
    $unit_id = $_POST['unit_id'];

    if (empty($idiom) || empty($meaning_id) || empty($unit_id)) {
        $error_msg = "Kolom Idiom, Arti, dan Unit wajib diisi.";
    } else {
        $sql_update = "UPDATE idioms SET idiom=?, meaning_id=?, example_sentence=?, sentence_translation=?, example_conversation=?, unit_id=? WHERE id=?";
        
        if ($stmt_update = $conn->prepare($sql_update)) {
            $stmt_update->bind_param("sssssii", $idiom, $meaning_id, $example_sentence, $sentence_translation, $example_conversation, $unit_id, $idiom_id);
            
            if ($stmt_update->execute()) {
                $_SESSION['message'] = "Idiom berhasil diperbarui!";
                $_SESSION['message_type'] = "success";
                header("location: admin.php");
                exit();
            } else {
                $error_msg = "Terjadi kesalahan. Gagal memperbarui data.";
            }
            $stmt_update->close();
        }
    }
    // Refresh data untuk ditampilkan di form jika ada error
    $idiom_data = $_POST;
    $idiom_data['unit_id'] = $unit_id;
}

require_once 'includes/header.php';
?>

<main>
    <section class="auth-section">
        <div class="auth-card" style="max-width: 800px; text-align: left;">
            <h2>Edit Idiom</h2>
            <p>Ubah detail idiom di bawah ini.</p>

            <?php if ($error_msg): ?>
                <div class="form-error-msg"><?= htmlspecialchars($error_msg); ?></div>
            <?php endif; ?>

            <form action="edit_idiom.php?id=<?= $idiom_id; ?>" method="post">
                <div class="form-group">
                    <label>Unit</label>
                    <select name="unit_id" class="form-control">
                        <option value="">-- Pilih Unit --</option>
                        <?php foreach ($units as $unit): ?>
                            <option value="<?= $unit['id']; ?>" <?= ($idiom_data['unit_id'] == $unit['id']) ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($unit['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Idiom</label>
                    <input type="text" name="idiom" class="form-control" value="<?= htmlspecialchars($idiom_data['idiom']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Arti</label>
                    <textarea name="meaning_id" class="form-control" rows="2" required><?= htmlspecialchars($idiom_data['meaning_id']); ?></textarea>
                </div>
                <div class="form-group">
                    <label>Contoh Kalimat</label>
                    <textarea name="example_sentence" class="form-control" rows="3"><?= htmlspecialchars($idiom_data['example_sentence']); ?></textarea>
                </div>
                <div class="form-group">
                    <label>Terjemahan Kalimat</label>
                    <textarea name="sentence_translation" class="form-control" rows="3"><?= htmlspecialchars($idiom_data['sentence_translation']); ?></textarea>
                </div>
                <div class="form-group">
                    <label>Contoh Percakapan</label>
                    <textarea name="example_conversation" class="form-control" rows="5"><?= htmlspecialchars($idiom_data['example_conversation']); ?></textarea>
                </div>
                <div class="form-group">
                    <input type="submit" class="btn-submit" value="Update Idiom">
                </div>
            </form>
        </div>
    </section>
</main>

<?php 
$conn->close();
require_once 'includes/footer.php'; 
?>
