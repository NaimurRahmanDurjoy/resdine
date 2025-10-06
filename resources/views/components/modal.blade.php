<!-- Simple Tailwind Modal -->
<div id="modal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 px-4">
    <div id="modal-box" class="bg-white rounded-lg shadow-lg max-h-[90vh] overflow-auto w-full max-w-2xl p-6 transform transition-transform scale-95 opacity-0">
        <button onclick="closeModal()" class="text-gray-500 hover:text-red-600 text-2xl mb-4 float-right">&times;</button>
        <div id="modal-content"></div>
    </div>
</div>



<script>
    // Open modal function
    function openModal(url, size = 'medium') {
        const modal = document.getElementById('modal');
        const box = document.getElementById('modal-box');
        const content = document.getElementById('modal-content');

        // Set size classes (tailwind max-width)
        box.classList.remove('max-w-sm', 'max-w-2xl', 'max-w-5xl');
        if (size === 'small') box.classList.add('max-w-sm');
        else if (size === 'large') box.classList.add('max-w-5xl');
        else box.classList.add('max-w-2xl');

        content.innerHTML = 'Loading...';
        modal.classList.remove('hidden');

        fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.text())
            .then(html => {
                content.innerHTML = html;
                box.classList.remove('scale-95', 'opacity-0');
                box.classList.add('scale-100', 'opacity-100');
            })
            .catch(() => {
                content.innerHTML = '<p class="text-red-500">Failed to load content.</p>';
            });
    }

    // Close modal function
    function closeModal() {
        const modal = document.getElementById('modal');
        const box = document.getElementById('modal-box');
        box.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            modal.classList.add('hidden');
            document.getElementById('modal-content').innerHTML = '';
        }, 150);
    }

    // Close modal on background click or ESC key
    document.getElementById('modal').addEventListener('click', e => {
        if (e.target.id === 'modal') closeModal();
    });
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') closeModal();
    });

    // Listen for buttons with view-type="modal"
    document.addEventListener('click', e => {
        const btn = e.target.closest('[view-type="modal"]');
        if (!btn) return;
        e.preventDefault();
        const url = btn.getAttribute('data-url') || btn.getAttribute('href');
        const size = btn.getAttribute('view-size') || 'medium';
        if (url) openModal(url, size);
    });
</script>