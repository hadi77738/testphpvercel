<?php
require_once 'includes/db.php';

$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
$page_title = 'Hasil Pencarian untuk "' . htmlspecialchars($keyword) . '"';
$results = [];

if (!empty($keyword)) {
    $search_term = "%" . $keyword . "%";
    $sql = "SELECT idioms.*, units.name AS unit_name FROM idioms JOIN units ON idioms.unit_id = units.id WHERE idioms.idiom LIKE ? OR idioms.meaning_id LIKE ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $search_term, $search_term);
    $stmt->execute();
    $result_set = $stmt->get_result();
    $results = $result_set->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

require_once 'includes/header.php';
?>

<main>
    <section class="results-section">
        <h2 class="results-title">Ditemukan <?= count($results) ?> hasil untuk "<?= htmlspecialchars($keyword) ?>"</h2>

        <?php if (!empty($results)): ?>
            <?php foreach ($results as $row): ?>
                <div class="idiom-card">
                    <span class="unit-tag"><?= htmlspecialchars($row['unit_name']) ?></span>
                    <h3 class="idiom-title"><?= htmlspecialchars($row['idiom']) ?></h3>
                    <p><strong>Artinya:</strong> <?= htmlspecialchars($row['meaning_id']) ?></p>
                    
                    <h4>Contoh Kalimat:</h4>
                    <blockquote class="example-quote">
                        <p>"<?= htmlspecialchars($row['example_sentence']) ?>"</p>
                        <footer>- Terjemahan: <em><?= htmlspecialchars($row['sentence_translation']) ?></em></footer>
                    </blockquote>

                    <h4>Contoh Percakapan:</h4>
                    <div class="conversation">
                        <pre><?= htmlspecialchars(str_replace('\n', "\n", $row['example_conversation'])) ?></pre>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="placeholder">
                <h2>Tidak Ada Hasil</h2>
                <p>Maaf, tidak ada idiom yang cocok dengan kata kunci Anda. <a href="index.php">Kembali ke beranda</a>.</p>
            </div>
        <?php endif; ?>
    </section>
</main>

<?php
$conn->close();
require_once 'includes/footer.php';
?>
