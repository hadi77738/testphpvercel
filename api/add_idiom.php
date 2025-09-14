<?php
// Mulai session dan cek login
session_start();
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

require_once 'includes/db.php';
$page_title = 'Tambah Idiom Baru';

// Ambil semua unit dari database untuk dropdown
$units = [];
$sql_units = "SELECT id, name FROM units ORDER BY id ASC";
$result_units = $conn->query($sql_units);
if ($result_units->num_rows > 0) {
    while ($row = $result_units->fetch_assoc()) {
        $units[] = $row;
    }
}

// Inisialisasi variabel
$idiom = $meaning_id = $example_sentence = $sentence_translation = $example_conversation = "";
$unit_id = null;
$error_msg = "";

// Proses form saat disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil dan bersihkan input
    $idiom = trim($_POST['idiom']);
    $meaning_id = trim($_POST['meaning_id']);
    $example_sentence = trim($_POST['example_sentence']);
    $sentence_translation = trim($_POST['sentence_translation']);
    $example_conversation = trim($_POST['example_conversation']);
    $unit_id = $_POST['unit_id'];

    // Validasi sederhana
    if (empty($idiom) || empty($meaning_id) || empty($unit_id)) {
        $error_msg = "Kolom Idiom, Arti, dan Unit wajib diisi.";
    } else {
        // Siapkan statement insert
        $sql = "INSERT INTO idioms (idiom, meaning_id, example_sentence, sentence_translation, example_conversation, unit_id) VALUES (?, ?, ?, ?, ?, ?)";
        
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("sssssi", $idiom, $meaning_id, $example_sentence, $sentence_translation, $example_conversation, $unit_id);
            
            if ($stmt->execute()) {
                // Set pesan sukses dan redirect ke halaman admin
                $_SESSION['message'] = "Idiom baru berhasil ditambahkan!";
                $_SESSION['message_type'] = "success";
                header("location: admin.php");
                exit();
            } else {
                $error_msg = "Terjadi kesalahan. Gagal menyimpan data.";
            }
            $stmt->close();
        }
    }
}

require_once 'includes/header.php';
?>

<main>
    <section class="auth-section">
        <div class="auth-card" style="max-width: 800px; text-align: left;">
            <h2>Tambah Idiom Baru</h2>
            <p>Isi formulir di bawah ini untuk menambahkan idiom baru ke database.</p>

            <?php if ($error_msg): ?>
                <div class="form-error-msg"><?= htmlspecialchars($error_msg); ?></div>
            <?php endif; ?>

            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group">
                    <label>Unit</label>
                    <select name="unit_id" class="form-control">
                        <option value="">-- Pilih Unit --</option>
                        <?php foreach ($units as $unit): ?>
                            <option value="<?= $unit['id']; ?>"><?= htmlspecialchars($unit['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Idiom</label>
                    <input type="text" name="idiom" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Arti</label>
                    <textarea name="meaning_id" class="form-control" rows="2" required></textarea>
                </div>
                <div class="form-group">
                    <label>Contoh Kalimat</label>
                    <textarea name="example_sentence" class="form-control" rows="3"></textarea>
                </div>
                <div class="form-group">
                    <label>Terjemahan Kalimat</label>
                    <textarea name="sentence_translation" class="form-control" rows="3"></textarea>
                </div>
                <div class="form-group">
                    <label>Contoh Percakapan</label>
                    <textarea name="example_conversation" class="form-control" rows="5"></textarea>
                </div>
                <div class="form-group">
                    <input type="submit" class="btn-submit" value="Simpan Idiom">
                </div>
            </form>
        </div>
    </section>
</main>

<?php 
$conn->close();
require_once 'includes/footer.php'; 
?>
