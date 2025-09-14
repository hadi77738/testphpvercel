    </div> <!-- .container -->

    <footer class="footer">
        <p>&copy; <?= date('Y') ?> Idiomatch. All Rights Reserved.</p>
    </footer>

    <script>
        // --- Script untuk hamburger menu (tetap sama) ---
        const hamburger = document.getElementById('hamburger');
        const navLinks = document.getElementById('nav-links');
        hamburger.addEventListener('click', () => {
            navLinks.classList.toggle('active');
        });

        // --- Script untuk accordion UNIT SAJA ---
        const unitHeaders = document.querySelectorAll('.unit-header');
        unitHeaders.forEach(header => {
            header.addEventListener('click', () => {
                header.classList.toggle('active');
                const icon = header.querySelector('.icon');
                if (icon) icon.textContent = header.classList.contains('active') ? '−' : '+';
                const content = header.nextElementSibling;
                if (content.style.maxHeight) {
                    content.style.maxHeight = null;
                } else {
                    content.style.maxHeight = content.scrollHeight + "px";
                } 
            });
        });

        // --- SCRIPT BARU UNTUK POPUP/MODAL ---
        const modal = document.getElementById('idiom-modal');
        const modalCloseBtn = modal.querySelector('.modal-close-btn');
        const idiomButtons = document.querySelectorAll('.idiom-item-btn');

        // Fungsi untuk membuka modal dan mengisi data
        function openModal(data) {
            document.getElementById('modal-title').textContent = data.idiom;
            document.getElementById('modal-unit').textContent = data.unit;
            document.getElementById('modal-meaning').textContent = data.meaning;
            document.getElementById('modal-sentence').textContent = `"${data.sentence}"`;
            document.getElementById('modal-translation').innerHTML = `- Terjemahan: <em>${data.translation}</em>`;
            document.getElementById('modal-conversation').textContent = data.conversation;
            modal.style.display = 'flex';
        }

        // Fungsi untuk menutup modal
        function closeModal() {
            modal.style.display = 'none';
        }

        // Tambahkan event listener ke setiap tombol idiom
        idiomButtons.forEach(button => {
            button.addEventListener('click', () => {
                const idiomData = {
                    idiom: button.dataset.idiom,
                    unit: button.dataset.unit,
                    meaning: button.dataset.meaning,
                    sentence: button.dataset.sentence,
                    translation: button.dataset.translation,
                    conversation: button.dataset.conversation,
                };
                openModal(idiomData);
            });
        });

        // Event listener untuk tombol close
        modalCloseBtn.addEventListener('click', closeModal);

        // Event listener untuk menutup modal saat klik di luar area konten
        window.addEventListener('click', (event) => {
            if (event.target == modal) {
                closeModal();
            }
        });
    </script>
</body>
</html>

