<?php
// api/home.php

// Sertakan file koneksi database PostgreSQL
require_once 'db.php';

// Inisialisasi array untuk menampung data
$popular_idiom = null;
$units = [];
$all_idioms = [];
$error_message = '';

// Cek apakah koneksi berhasil
if ($dbconn) {
    // 1. Ambil satu idiom populer secara acak
    // Catatan: PostgreSQL menggunakan RANDOM() bukan RAND()
    $sql_popular = "SELECT i.*, u.name as unit_name 
                    FROM idioms i 
                    JOIN units u ON i.unit_id = u.id 
                    ORDER BY RANDOM() LIMIT 1";
    $result_popular = pg_query($dbconn, $sql_popular);
    if ($result_popular) {
        $popular_idiom = pg_fetch_assoc($result_popular);
    }

    // 2. Ambil semua data unit
    $sql_units = "SELECT * FROM units ORDER BY id ASC";
    $result_units = pg_query($dbconn, $sql_units);
    if ($result_units) {
        while ($row = pg_fetch_assoc($result_units)) {
            $units[] = $row;
        }
    }

    // 3. Ambil semua data idiom
    $sql_idioms = "SELECT * FROM idioms";
    $result_idioms = pg_query($dbconn, $sql_idioms);
    if ($result_idioms) {
        while ($row = pg_fetch_assoc($result_idioms)) {
            $all_idioms[] = $row;
        }
    }
    
    // Tutup koneksi setelah selesai
    pg_close($dbconn);
} else {
    $error_message = "Tidak dapat terhubung ke database untuk memuat data.";
}

// Sertakan header
require_once 'header.php';
?>

<!-- Bagian Pencarian -->
<section class="search-section">
    <form action="/search_results.php" method="GET" class="search-form">
        <input type="text" name="keyword" placeholder="Ketik kata kunci idiom..." class="search-input" required>
        <button type="submit" class="search-button">Cari</button>
    </form>
</section>

<!-- Menampilkan pesan error jika ada -->
<?php if ($error_message): ?>
    <div class="placeholder"><?php echo htmlspecialchars($error_message); ?></div>
<?php endif; ?>

<!-- Bagian Idiom Populer -->
<?php if ($popular_idiom): ?>
<section class="popular-section">
    <h2>Idiom Populer Hari Ini</h2>
    <div class="idiom-card">
        <span class="unit-tag"><?php echo htmlspecialchars($popular_idiom['unit_name']); ?></span>
        <h3><?php echo htmlspecialchars($popular_idiom['idiom']); ?></h3>
        <p><strong>Artinya:</strong> <?php echo htmlspecialchars($popular_idiom['meaning_id']); ?></p>
    </div>
</section>
<?php endif; ?>

<!-- Bagian Jelajahi Unit (Accordion) -->
<section class="unit-explorer">
    <h2>Jelajahi Berdasarkan Unit</h2>
    <?php foreach ($units as $unit): ?>
        <div class="unit-accordion">
            <button class="unit-header">
                <span><?php echo htmlspecialchars($unit['name']); ?></span>
                <span class="icon">+</span>
            </button>
            <div class="unit-content">
                <?php foreach ($all_idioms as $idiom): ?>
                    <?php if ($idiom['unit_id'] == $unit['id']): ?>
                        <div class="idiom-item">
                            <button class="idiom-item-btn"
                                data-idiom="<?php echo htmlspecialchars($idiom['idiom']); ?>"
                                data-meaning="<?php echo htmlspecialchars($idiom['meaning_id']); ?>"
                                data-sentence="<?php echo htmlspecialchars($idiom['example_sentence']); ?>"
                                data-translation="<?php echo htmlspecialchars($idiom['sentence_translation']); ?>"
                                data-conversation="<?php echo htmlspecialchars($idiom['example_conversation']); ?>"
                                data-unit-name="<?php echo htmlspecialchars($unit['name']); ?>">
                                <?php echo htmlspecialchars($idiom['idiom']); ?>
                            </button>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endforeach; ?>
</section>

<?php
// Sertakan footer
require_once 'footer.php';
?>
