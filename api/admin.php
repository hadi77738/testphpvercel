<?php
// Mulai session dan pastikan pengguna sudah login
session_start();
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

require_once 'includes/db.php';
$page_title = 'Admin Panel';

// Ambil semua data idiom beserta nama unitnya menggunakan JOIN
// PERBAIKAN: Mengganti u.unit_name menjadi u.name
$sql = "SELECT i.id, i.idiom, i.meaning_id, u.name 
        FROM idioms i 
        LEFT JOIN units u ON i.unit_id = u.id 
        ORDER BY i.idiom ASC";
$result = $conn->query($sql);
$idioms = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $idioms[] = $row;
    }
}

// Ambil pesan notifikasi dari session jika ada
$message = '';
$message_type = '';
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    $message_type = $_SESSION['message_type'];
    // Hapus pesan dari session setelah ditampilkan
    unset($_SESSION['message']);
    unset($_SESSION['message_type']);
}

require_once 'includes/header.php';
?>

<main>
    <section class="admin-section">
        <div class="admin-header">
            <h1>Admin Panel</h1>
            <div class="admin-actions">
                <a href="add_idiom.php" class="btn-admin-action btn-add">Tambah Idiom Baru</a>
                <!-- <a href="manage_units.php" class="btn-admin-action btn-manage">Kelola Unit</a> -->
            </div>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-<?= htmlspecialchars($message_type); ?>" id="notification">
                <?= htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <div class="admin-search-bar">
            <input type="text" id="adminSearchInput" placeholder="Cari idiom di sini...">
        </div>

        <div class="idiom-grid">
            <?php if (!empty($idioms)): ?>
                <?php foreach ($idioms as $idiom): ?>
                    <div class="admin-idiom-card" data-searchable-text="<?= strtolower(htmlspecialchars($idiom['idiom'] . ' ' . $idiom['meaning_id'])); ?>">
                        <!-- PERBAIKAN: Mengganti $idiom['unit_name'] menjadi $idiom['name'] -->
                        <div class="card-unit-tag"><?= htmlspecialchars($idiom['name'] ?: 'Tanpa Unit'); ?></div>
                        <h3 class="card-idiom-title"><?= htmlspecialchars($idiom['idiom']); ?></h3>
                        <p class="card-idiom-meaning"><?= htmlspecialchars($idiom['meaning_id']); ?></p>
                        <div class="card-actions">
                            <a href="edit_idiom.php?id=<?= $idiom['id']; ?>" class="btn-card-action btn-edit">Edit</a>
                            <a href="delete_idiom.php?id=<?= $idiom['id']; ?>" class="btn-card-action btn-delete" onclick="return confirm('Anda yakin ingin menghapus idiom ini?');">Hapus</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Belum ada idiom di database. Silakan tambahkan idiom baru.</p>
            <?php endif; ?>
        </div>
        <p id="noResultsMessage" style="display: none; text-align: center; margin-top: 20px;">Tidak ada idiom yang cocok dengan pencarian Anda.</p>
    </section>
</main>

<script>
// --- Skrip untuk Halaman Admin ---

// 1. Logika untuk Notifikasi yang bisa hilang otomatis
const notification = document.getElementById('notification');
if (notification) {
    setTimeout(() => {
        notification.style.transition = 'opacity 0.5s ease';
        notification.style.opacity = '0';
        setTimeout(() => notification.remove(), 500);
    }, 4000); // Hilang setelah 4 detik
}

// 2. Logika untuk Pencarian Real-time
const searchInput = document.getElementById('adminSearchInput');
const idiomCards = document.querySelectorAll('.admin-idiom-card');
const noResultsMessage = document.getElementById('noResultsMessage');

searchInput.addEventListener('keyup', function() {
    const searchTerm = searchInput.value.toLowerCase();
    let visibleCards = 0;

    idiomCards.forEach(card => {
        const searchableText = card.getAttribute('data-searchable-text');
        if (searchableText.includes(searchTerm)) {
            card.style.display = 'block';
            visibleCards++;
        } else {
            card.style.display = 'none';
        }
    });

    // Tampilkan pesan jika tidak ada hasil
    if (visibleCards === 0) {
        noResultsMessage.style.display = 'block';
    } else {
        noResultsMessage.style.display = 'none';
    }
});

</script>


<?php 
$conn->close();
require_once 'includes/footer.php'; 
?>

