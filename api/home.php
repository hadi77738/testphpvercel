<?php
// ------------------------------------------------------------------
// BAGIAN 1: KONEKSI KE DATABASE SUPABASE (POSTGRESQL)
// ------------------------------------------------------------------
$host = 'aws-1-ap-southeast-1.pooler.supabase.com';
$port = '5432';
$dbname = 'postgres';
$user = 'postgres.ljveqfmifeqquebjvwau';
$password = 'sugihmanik1';
$page_title = 'Idiomatch - Jelajahi Idiom per Unit';

// Membuat string koneksi untuk PostgreSQL
$conn_string = "host={$host} port={$port} dbname={$dbname} user={$user} password={$password} sslmode=require";

// Mencoba terhubung ke database
$dbconn = pg_connect($conn_string);

// Cek Koneksi
if (!$dbconn) {
    die("Koneksi ke database gagal: " . pg_last_error());
}

// ------------------------------------------------------------------
// BAGIAN 2: PENGAMBILAN DATA DARI DATABASE
// ------------------------------------------------------------------

// Ambil 1 idiom populer acak (menggunakan RANDOM() untuk PostgreSQL)
$popular_sql = "SELECT idioms.*, units.name AS unit_name FROM idioms JOIN units ON idioms.unit_id = units.id ORDER BY RANDOM() LIMIT 1";
$popular_result = pg_query($dbconn, $popular_sql);
$popular_idiom = pg_fetch_assoc($popular_result);

// Ambil SEMUA idiom dan unit, diurutkan berdasarkan unit
$all_idioms_sql = "SELECT idioms.*, units.name AS unit_name FROM idioms JOIN units ON idioms.unit_id = units.id ORDER BY units.id, idioms.idiom ASC";
$all_idioms_result = pg_query($dbconn, $all_idioms_sql);

// Kelompokkan idiom berdasarkan unitnya
$units_with_idioms = [];
if ($all_idioms_result) {
    while ($row = pg_fetch_assoc($all_idioms_result)) {
        $units_with_idioms[$row['unit_name']][] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .unit-content { max-height: 0; overflow: hidden; transition: max-height 0.3s ease-out; }
        .unit-accordion.active .unit-content { max-height: 1000px; /* Adjust as needed */ transition: max-height 0.5s ease-in; }
        .unit-accordion.active .icon { transform: rotate(45deg); }
        .modal-overlay { transition: opacity 0.3s ease; }
        .modal-content { transition: transform 0.3s ease; }
    </style>
</head>
<body class="bg-gray-50 text-gray-800">

<div class="container mx-auto max-w-4xl p-4 md:p-8">
    <header class="text-center mb-8">
        <h1 class="text-4xl font-bold text-indigo-600">Idiomatch Explorer</h1>
        <p class="text-lg text-gray-600 mt-2">Temukan dan pelajari ratusan idiom Bahasa Inggris dengan mudah.</p>
    </header>

    <main>
        <section class="search-section mb-10">
            <form action="search_results.php" method="GET" class="search-form flex gap-2">
                <input type="text" name="keyword" placeholder="Ketik kata kunci idiom..." class="search-input w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition" required>
                <button type="submit" class="search-button bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 transition font-semibold">Cari</button>
            </form>
        </section>

        <?php if ($popular_idiom): ?>
        <section class="results-section mb-10">
            <h2 class="results-title text-2xl font-bold mb-4">Idiom Populer Hari Ini</h2>
            <div class="idiom-card bg-white p-6 rounded-lg shadow-md border border-gray-200">
                <span class="unit-tag bg-indigo-100 text-indigo-800 text-sm font-medium px-3 py-1 rounded-full"><?= htmlspecialchars($popular_idiom['unit_name']) ?></span>
                <h3 class="idiom-title text-2xl font-semibold my-2"><?= htmlspecialchars($popular_idiom['idiom']) ?></h3>
                <p><strong>Artinya:</strong> <?= htmlspecialchars($popular_idiom['meaning_id']) ?></p>
            </div>
        </section>
        <?php endif; ?>

        <section class="unit-explorer">
            <h2 class="results-title text-2xl font-bold mb-4">Jelajahi Berdasarkan Unit</h2>
            
            <div class="space-y-4">
            <?php foreach ($units_with_idioms as $unit_name => $idioms): ?>
                <div class="unit-accordion bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden">
                    <button class="unit-header w-full flex justify-between items-center p-5 text-left font-semibold text-lg hover:bg-gray-50">
                        <span><?= htmlspecialchars($unit_name) ?></span>
                        <span class="icon text-indigo-600 text-2xl transition-transform duration-300">+</span>
                    </button>
                    <div class="unit-content border-t border-gray-200">
                        <div class="p-5 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        <?php foreach ($idioms as $idiom): ?>
                            <div class="idiom-item">
                                <button class="idiom-item-btn text-left w-full text-indigo-600 hover:underline" 
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
                </div>
            <?php endforeach; ?>
            </div>
        </section>
    </main>
</div>

<!-- STRUKTUR HTML UNTUK POPUP/MODAL -->
<div id="idiom-modal" class="modal-overlay hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4">
    <div class="modal-content bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto p-6 relative animate-fade-in-up">
        <button class="modal-close-btn absolute top-4 right-4 text-2xl text-gray-500 hover:text-gray-800">&times;</button>
        <div class="modal-header pb-4 border-b mb-4">
            <span id="modal-unit" class="unit-tag bg-indigo-100 text-indigo-800 text-sm font-medium px-3 py-1 rounded-full mb-2 inline-block">Nama Unit</span>
            <h3 id="modal-title" class="text-3xl font-bold">Judul Idiom</h3>
        </div>
        <div class="modal-body space-y-4">
            <p><strong>Artinya:</strong> <span id="modal-meaning"></span></p>
            <div>
                <h4 class="font-semibold text-lg">Contoh Kalimat:</h4>
                <blockquote id="modal-sentence-block" class="example-quote border-l-4 border-indigo-500 pl-4 py-2 mt-2 bg-gray-50 rounded-r-lg">
                    <p id="modal-sentence" class="italic text-gray-700"></p>
                    <footer id="modal-translation" class="text-sm text-gray-500 mt-1"></footer>
                </blockquote>
            </div>
            <div>
                <h4 class="font-semibold text-lg">Contoh Percakapan:</h4>
                <div class="conversation mt-2 bg-gray-50 p-4 rounded-lg">
                    <pre id="modal-conversation" class="whitespace-pre-wrap text-gray-700"></pre>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Accordion Logic
    document.querySelectorAll('.unit-header').forEach(button => {
        button.addEventListener('click', () => {
            const accordion = button.closest('.unit-accordion');
            accordion.classList.toggle('active');
        });
    });

    // Modal Logic
    const modal = document.getElementById('idiom-modal');
    const closeBtn = document.querySelector('.modal-close-btn');

    document.querySelectorAll('.idiom-item-btn').forEach(button => {
        button.addEventListener('click', () => {
            document.getElementById('modal-title').textContent = button.dataset.idiom;
            document.getElementById('modal-unit').textContent = button.dataset.unit;
            document.getElementById('modal-meaning').textContent = button.dataset.meaning;
            document.getElementById('modal-sentence').textContent = button.dataset.sentence;
            document.getElementById('modal-translation').textContent = button.dataset.translation;
            document.getElementById('modal-conversation').textContent = button.dataset.conversation;
            modal.classList.remove('hidden');
        });
    });

    function closeModal() {
        modal.classList.add('hidden');
    }

    closeBtn.addEventListener('click', closeModal);
    modal.addEventListener('click', (event) => {
        if (event.target === modal) {
            closeModal();
        }
    });

    // Close modal with Escape key
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && !modal.classList.contains('hidden')) {
            closeModal();
        }
    });
});
</script>

</body>
</html>
<?php 
// Menutup koneksi database di akhir skrip
pg_close($dbconn); 
?>
