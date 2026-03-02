/**
 * Personal CRM — Frontend Validation
 * Covers: registerForm & contactForm
 */

document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('registerForm') || document.getElementById('contactForm');
    if (!form) return;

    // Hapus error saat user mulai mengetik
    form.querySelectorAll('.form-control, .form-select').forEach(function (el) {
        el.addEventListener('input', function () {
            el.classList.remove('is-invalid');
        });
    });

    form.addEventListener('submit', function (e) {
        let valid = true;

        // 1. Required fields
        form.querySelectorAll('[required]').forEach(function (input) {
            if (!input.value.trim()) {
                setInvalid(input, 'Field ini wajib diisi.');
                valid = false;
            }
        });

        // 2. Validasi format email
        const emailInput = form.querySelector('input[type="email"]');
        if (emailInput && emailInput.value.trim()) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(emailInput.value.trim())) {
                setInvalid(emailInput, 'Format email tidak valid.');
                valid = false;
            }
        }

        // 3. Validasi No HP
        const hpInput = form.querySelector('input[name="no_hp"]');
        if (hpInput && hpInput.value.trim()) {
            const hpRegex = /^[0-9+\-\s]{8,16}$/;
            if (!hpRegex.test(hpInput.value.trim())) {
                setInvalid(hpInput, 'No HP tidak valid (8-16 digit angka).');
                valid = false;
            }
        }

        // 4. Konfirmasi password (hanya di registerForm)
        const passInput    = form.querySelector('input[name="password"]');
        const confirmInput = form.querySelector('input[name="confirm_password"]');
        if (passInput && confirmInput) {
            if (passInput.value && confirmInput.value && passInput.value !== confirmInput.value) {
                setInvalid(confirmInput, 'Password tidak cocok.');
                valid = false;
            }
            if (passInput.value && passInput.value.length < 6) {
                setInvalid(passInput, 'Password minimal 6 karakter.');
                valid = false;
            }
        }

        if (!valid) {
            e.preventDefault();
            // Scroll ke error pertama
            const firstError = form.querySelector('.is-invalid');
            if (firstError) firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });

    function setInvalid(input, message) {
        input.classList.add('is-invalid');

        // Tambahkan pesan jika belum ada
        if (!input.nextElementSibling || !input.nextElementSibling.classList.contains('invalid-feedback')) {
            const feedback = document.createElement('div');
            feedback.className = 'invalid-feedback';
            feedback.textContent = message;
            input.parentNode.insertBefore(feedback, input.nextSibling);
        }
    }
});
