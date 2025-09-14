<?php
require_once 'includes/db.php';
$page_title = 'Idiomatch - Jelajahi Idiom per Unit';

// Ambil 1 idiom populer acak
$popular_sql = "SELECT idioms.*, units.name AS unit_name FROM idioms JOIN units ON idioms.unit_id = units.id ORDER BY RAND() LIMIT 1";
$popular_result = $dbconn->query($popular_sql);
$popular_idiom = $popular_result->fetch_assoc();

// Ambil SEMUA idiom dan unit, diurutkan berdasarkan unit
$all_idioms_sql = "SELECT idioms.*, units.name AS unit_name, units.id as unit_id_val FROM idioms JOIN units ON idioms.unit_id = units.id ORDER BY units.id, idioms.idiom ASC";
$all_idioms_result = $dbconn->query($all_idioms_sql);

// Kelompokkan idiom berdasarkan unitnya
$units_with_idioms = [];
while ($row = $all_idioms_result->fetch_assoc()) {
    $units_with_idioms[$row['unit_name']][] = $row;
}

require_once 'includes/header.php';
?>

<main>
    <section class="search-section">
        <form action="search_results.php" method="GET" class="search-form">
            <input type="text" name="keyword" placeholder="Ketik kata kunci idiom..." class="search-input" required>
            <button type="submit" class="search-button">Cari</button>
        </form>
    </section>

    <?php if ($popular_idiom): ?>
    <section class="results-section">
        <h2 class="results-title">Idiom Populer Hari Ini</h2>
        <div class="idiom-card">
            <span class="unit-tag"><?= htmlspecialchars($popular_idiom['unit_name']) ?></span>
            <h3 class="idiom-title"><?= htmlspecialchars($popular_idiom['idiom']) ?></h3>
            <p><strong>Artinya:</strong> <?= htmlspecialchars($popular_idiom['meaning_id']) ?></p>
        </div>
    </section>
    <?php endif; ?>

    <section class="unit-explorer">
        <h2 class="results-title">Jelajahi Berdasarkan Unit</h2>
        
        <?php foreach ($units_with_idioms as $unit_name => $idioms): ?>
            <div class="unit-accordion">
                <button class="unit-header">
                    <span><?= htmlspecialchars($unit_name) ?></span>
                    <span class="icon">+</span>
                </button>
                <div class="unit-content">
                    <?php foreach ($idioms as $idiom): ?>
                        <div class="idiom-item">
                            <!-- Tombol ini akan memicu popup -->
                            <button class="idiom-item-btn" 
                                data-idiom="<?= htmlspecialchars($idiom['idiom']) ?>"
                                data-unit="<?= htmlspecialchars($idiom['unit_name']) ?>"
                                data-meaning="<?= htmlspecialchars($idiom['meaning_id']) ?>"
                                data-sentence="<?= htmlspecialchars($idiom['example_sentence']) ?>"
                                data-translation="<?= htmlspecialchars($idiom['sentence_translation']) ?>"
                                data-conversation="<?= htmlspecialchars(str_replace('\n', "\n", $idiom['example_conversation'])) ?>">
                                <?= htmlspecialchars($idiom['idiom']) ?>
                            </button>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </section>
</main>

<!-- STRUKTUR HTML UNTUK POPUP/MODAL -->
<div id="idiom-modal" class="modal-overlay">
    <div class="modal-content">
        <button class="modal-close-btn">&times;</button>
        <div class="modal-header">
            <h3 id="modal-title">Judul Idiom</h3>
            <span id="modal-unit" class="unit-tag">Nama Unit</span>
        </div>
        <div class="modal-body">
            <p><strong>Artinya:</strong> <span id="modal-meaning"></span></p>
            <h4>Contoh Kalimat:</h4>
            <blockquote id="modal-sentence-block" class="example-quote">
                <p id="modal-sentence"></p>
                <footer id="modal-translation"></footer>
            </blockquote>
            <h4>Contoh Percakapan:</h4>
            <div class="conversation">
                <pre id="modal-conversation"></pre>
            </div>
        </div>
    </div>
</div>


<?php 
$dbconn->close();
require_once 'includes/footer.php'; 
?>

