/* ============================================
   CineVault — Client-Side JavaScript
   ============================================ */

document.addEventListener('DOMContentLoaded', () => {

    // ---------- Navbar scroll effect ----------
    const navbar = document.getElementById('navbar');
    if (navbar) {
        window.addEventListener('scroll', () => {
            navbar.classList.toggle('scrolled', window.scrollY > 30);
        });
    }

    // ---------- Mobile nav toggle ----------
    const navToggle = document.getElementById('navToggle');
    const navLinks = document.getElementById('navLinks');
    if (navToggle && navLinks) {
        navToggle.addEventListener('click', () => {
            navLinks.classList.toggle('open');
            navToggle.classList.toggle('active');
        });
        // Close on link click
        navLinks.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', () => {
                navLinks.classList.remove('open');
                navToggle.classList.remove('active');
            });
        });
    }

    // ---------- Flash message auto-dismiss ----------
    const flash = document.getElementById('flashMsg');
    if (flash) {
        setTimeout(() => {
            flash.style.opacity = '0';
            flash.style.transform = 'translateX(-50%) translateY(-20px)';
            setTimeout(() => flash.remove(), 400);
        }, 4000);
    }

    // ---------- Interactive star rating ----------
    initStarRating();

    // ---------- File upload preview ----------
    initFileUpload();

    // ---------- Animate on scroll ----------
    initScrollAnimations();

    // ---------- Hero search redirect ----------
    const heroSearch = document.getElementById('heroSearch');
    if (heroSearch) {
        heroSearch.addEventListener('keypress', (e) => {
            if (e.key === 'Enter' && heroSearch.value.trim()) {
                window.location.href = 'movies.php?search=' + encodeURIComponent(heroSearch.value.trim());
            }
        });
    }

    // ---------- Live search on movies page ----------
    const movieSearch = document.getElementById('movieSearch');
    if (movieSearch) {
        let debounceTimer;
        movieSearch.addEventListener('input', () => {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                applyFilters();
            }, 400);
        });
    }

    // ---------- Filter dropdowns ----------
    const filterSelects = document.querySelectorAll('.filter-select');
    filterSelects.forEach(select => {
        select.addEventListener('change', () => applyFilters());
    });

    // ---------- Delete confirmation modal ----------
    initDeleteModal();

    // ---------- Animated counters ----------
    initCounters();

});

// ---------- Star Rating ----------
function initStarRating() {
    const starContainers = document.querySelectorAll('.stars-interactive');
    starContainers.forEach(container => {
        const labels = container.querySelectorAll('.star-label');
        const inputs = container.querySelectorAll('input[type="radio"]');
        
        labels.forEach(label => {
            label.addEventListener('mouseenter', () => {
                const val = parseInt(label.dataset.value);
                labels.forEach(l => {
                    const lVal = parseInt(l.dataset.value);
                    l.classList.toggle('filled', lVal <= val);
                });
            });

            label.addEventListener('click', () => {
                const val = parseInt(label.dataset.value);
                inputs.forEach(input => {
                    input.checked = parseInt(input.value) === val;
                });
                labels.forEach(l => {
                    const lVal = parseInt(l.dataset.value);
                    l.classList.toggle('filled', lVal <= val);
                });
            });
        });

        container.addEventListener('mouseleave', () => {
            let checkedVal = 0;
            inputs.forEach(input => {
                if (input.checked) checkedVal = parseInt(input.value);
            });
            labels.forEach(l => {
                const lVal = parseInt(l.dataset.value);
                l.classList.toggle('filled', lVal <= checkedVal);
            });
        });
    });
}

// ---------- File Upload ----------
function initFileUpload() {
    const fileUploads = document.querySelectorAll('.file-upload');
    fileUploads.forEach(upload => {
        const input = upload.querySelector('input[type="file"]');
        const nameEl = upload.querySelector('.file-name');
        if (input && nameEl) {
            input.addEventListener('change', () => {
                if (input.files.length > 0) {
                    nameEl.textContent = input.files[0].name;
                    nameEl.style.display = 'block';
                }
            });
        }
    });
}

// ---------- Scroll Animations ----------
function initScrollAnimations() {
    const elements = document.querySelectorAll('.animate-on-scroll');
    if (elements.length === 0) return;

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-in');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1 });

    elements.forEach(el => observer.observe(el));
}

// ---------- Apply Filters ----------
function applyFilters() {
    const search = document.getElementById('movieSearch')?.value || '';
    const genre = document.getElementById('genreFilter')?.value || '';
    const year = document.getElementById('yearFilter')?.value || '';
    const rating = document.getElementById('ratingFilter')?.value || '';

    const params = new URLSearchParams();
    if (search) params.set('search', search);
    if (genre) params.set('genre', genre);
    if (year) params.set('year', year);
    if (rating) params.set('rating', rating);

    window.location.href = 'movies.php?' + params.toString();
}

// ---------- Delete Modal ----------
function initDeleteModal() {
    const deleteButtons = document.querySelectorAll('[data-delete]');
    const modal = document.getElementById('deleteModal');
    const confirmBtn = document.getElementById('confirmDelete');
    const cancelBtn = document.getElementById('cancelDelete');

    if (!modal) return;

    deleteButtons.forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const url = btn.dataset.delete;
            confirmBtn.href = url;
            modal.classList.add('active');
        });
    });

    if (cancelBtn) {
        cancelBtn.addEventListener('click', () => {
            modal.classList.remove('active');
        });
    }

    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.classList.remove('active');
        }
    });
}

// ---------- Animated Counters ----------
function initCounters() {
    const counters = document.querySelectorAll('[data-count]');
    if (counters.length === 0) return;

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const el = entry.target;
                const target = parseInt(el.dataset.count);
                animateCounter(el, target);
                observer.unobserve(el);
            }
        });
    }, { threshold: 0.5 });

    counters.forEach(el => observer.observe(el));
}

function animateCounter(el, target) {
    let current = 0;
    const duration = 1500;
    const step = target / (duration / 16);

    function update() {
        current += step;
        if (current >= target) {
            el.textContent = target;
            return;
        }
        el.textContent = Math.floor(current);
        requestAnimationFrame(update);
    }
    requestAnimationFrame(update);
}

