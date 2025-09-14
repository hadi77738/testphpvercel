<?php
require_once 'includes/db.php';
$page_title = 'Kuis Idiom - Uji Pengetahuanmu!';

// Ambil semua idiom dari database untuk digunakan dalam kuis JavaScript
$quiz_sql = "SELECT idiom, meaning_id FROM idioms";
$result = $conn->query($quiz_sql);
$all_idioms = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $all_idioms[] = $row;
    }
}

// Mengubah data PHP menjadi format JSON agar bisa dibaca oleh JavaScript
$idioms_json = json_encode($all_idioms);

require_once 'includes/header.php';
?>

<main>
    <section class="quiz-section">
        <div id="quiz-container" class="quiz-card">
            <h1 class="quiz-title">Kuis Idiom</h1>
            
            <div id="quiz-start-screen">
                <p>Uji seberapa banyak idiom yang kamu ketahui. Klik tombol di bawah untuk memulai!</p>
                <button id="start-quiz-btn" class="quiz-button">Mulai Kuis</button>
            </div>

            <div id="quiz-game-screen" style="display: none;">
                <div class="quiz-header">
                    <p id="question-counter">Pertanyaan 1/5</p>
                    <p id="score-display">Skor: 0</p>
                </div>
                <div class="question-area">
                    <p>Apa arti dari idiom di bawah ini?</p>
                    <h2 id="question-text">Idiom akan muncul di sini...</h2>
                </div>
                <div id="options-container" class="options-grid">
                    <!-- Opsi jawaban akan dibuat oleh JavaScript -->
                </div>
                <div id="feedback-container"></div>
                <button id="next-question-btn" class="quiz-button" style="display: none;">Lanjut</button>
            </div>

            <div id="quiz-end-screen" style="display: none;">
                <h2>Kuis Selesai!</h2>
                <p>Skor akhir kamu adalah:</p>
                <h1 id="final-score" class="final-score-display">0</h1>
                <button id="restart-quiz-btn" class="quiz-button">Coba Lagi</button>
            </div>
        </div>
    </section>
</main>

<script>
// --- LOGIKA KUIS INTERAKTIF ---

// Ambil data idiom dari PHP
const idiomsData = <?= $idioms_json ?>;

// Elemen-elemen UI
const startScreen = document.getElementById('quiz-start-screen');
const gameScreen = document.getElementById('quiz-game-screen');
const endScreen = document.getElementById('quiz-end-screen');
const startBtn = document.getElementById('start-quiz-btn');
const nextBtn = document.getElementById('next-question-btn');
const restartBtn = document.getElementById('restart-quiz-btn');
const questionCounter = document.getElementById('question-counter');
const scoreDisplay = document.getElementById('score-display');
const questionText = document.getElementById('question-text');
const optionsContainer = document.getElementById('options-container');
const feedbackContainer = document.getElementById('feedback-container');
const finalScore = document.getElementById('final-score');

// Variabel status kuis
let currentQuestionIndex = 0;
let score = 0;
const MAX_QUESTIONS = 5; // Batas maksimal pertanyaan
let totalQuestionsInGame = 0; // Jumlah pertanyaan aktual di ronde ini
let shuffledIdioms = [];
let currentCorrectAnswer = '';

// Fungsi untuk mengacak array (algoritma Fisher-Yates)
function shuffleArray(array) {
    for (let i = array.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [array[i], array[j]] = [array[j], array[i]];
    }
}

// Fungsi untuk memulai kuis
function startQuiz() {
    // Cek jika idiom tidak cukup untuk membuat 1 soal (1 benar + 3 salah)
    if (idiomsData.length < 4) {
        alert("Maaf, perlu minimal 4 idiom di database untuk memulai kuis.");
        return;
    }

    score = 0;
    currentQuestionIndex = 0;
    shuffledIdioms = [...idiomsData];
    shuffleArray(shuffledIdioms);
    
    // Tentukan jumlah pertanyaan aktual: antara MAX_QUESTIONS atau jumlah idiom yang ada
    totalQuestionsInGame = Math.min(MAX_QUESTIONS, shuffledIdioms.length);
    
    startScreen.style.display = 'none';
    endScreen.style.display = 'none';
    gameScreen.style.display = 'block';

    loadNextQuestion();
}

// Fungsi untuk memuat pertanyaan berikutnya
function loadNextQuestion() {
    resetState();
    if (currentQuestionIndex >= totalQuestionsInGame) {
        endQuiz();
        return;
    }

    // Update counter dengan jumlah soal yang dinamis
    questionCounter.textContent = `Pertanyaan ${currentQuestionIndex + 1}/${totalQuestionsInGame}`;
    scoreDisplay.textContent = `Skor: ${score}`;

    // Siapkan pertanyaan dan jawaban
    const questionIdiom = shuffledIdioms[currentQuestionIndex];
    currentCorrectAnswer = questionIdiom.meaning_id;
    questionText.textContent = `"${questionIdiom.idiom}"`;

    // Siapkan opsi jawaban (1 benar, 3 salah)
    const options = [questionIdiom.meaning_id];
    let tempIdioms = [...idiomsData].filter(item => item.idiom !== questionIdiom.idiom);
    shuffleArray(tempIdioms);
    for (let i = 0; i < 3; i++) {
        // Pastikan tempIdioms masih punya item untuk diambil
        if(tempIdioms[i]) {
            options.push(tempIdioms[i].meaning_id);
        }
    }
    shuffleArray(options);

    // Tampilkan opsi jawaban
    options.forEach(option => {
        const button = document.createElement('button');
        button.innerText = option;
        button.classList.add('option-btn');
        button.addEventListener('click', selectAnswer);
        optionsContainer.appendChild(button);
    });
}

// Fungsi untuk mereset tampilan sebelum pertanyaan baru
function resetState() {
    optionsContainer.innerHTML = '';
    feedbackContainer.innerHTML = '';
    nextBtn.style.display = 'none';
}

// Fungsi saat pengguna memilih jawaban
function selectAnswer(e) {
    const selectedButton = e.target;
    const selectedAnswer = selectedButton.innerText;

    // Tampilkan feedback
    if (selectedAnswer === currentCorrectAnswer) {
        // Nilai per soal disesuaikan dengan jumlah soal
        score += (100 / totalQuestionsInGame); 
        feedbackContainer.innerHTML = `<p class="feedback correct">Benar!</p>`;
        selectedButton.classList.add('correct');
    } else {
        feedbackContainer.innerHTML = `<p class="feedback incorrect">Salah!</p>`;
        selectedButton.classList.add('incorrect');
    }

    // Update skor dan nonaktifkan semua tombol
    scoreDisplay.textContent = `Skor: ${Math.round(score)}`;
    Array.from(optionsContainer.children).forEach(button => {
        if (button.innerText === currentCorrectAnswer) {
            button.classList.add('correct');
        }
        button.disabled = true;
    });

    nextBtn.style.display = 'block';
}

// Fungsi untuk mengakhiri kuis
function endQuiz() {
    gameScreen.style.display = 'none';
    endScreen.style.display = 'block';
    finalScore.textContent = Math.round(score);
}

// Event Listeners
startBtn.addEventListener('click', startQuiz);
nextBtn.addEventListener('click', () => {
    currentQuestionIndex++;
    loadNextQuestion();
});
restartBtn.addEventListener('click', startQuiz);

</script>

<?php 
$conn->close();
require_once 'includes/footer.php'; 
?>

