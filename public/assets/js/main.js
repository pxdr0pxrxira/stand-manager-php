/**
 * ============================================
 * Stand Automóvel - JavaScript (Paulimane Style)
 * ============================================
 */

document.addEventListener('DOMContentLoaded', function () {

    // ============================================
    // Navbar Scroll Effect
    // ============================================
    const navbar = document.getElementById('navbar');

    function handleScroll() {
        if (window.scrollY > 50) {
            navbar.classList.add('scrolled');
        } else {
            if (!navbar.classList.contains('always-scrolled')) {
                navbar.classList.remove('scrolled');
            }
        }

        // Back to top button
        const backToTop = document.getElementById('backToTop');
        if (backToTop) {
            if (window.scrollY > 300) {
                backToTop.classList.add('show');
            } else {
                backToTop.classList.remove('show');
            }
        }
    }

    window.addEventListener('scroll', handleScroll);
    handleScroll(); // Run on load

    // ============================================
    // Mobile Menu Toggle
    // ============================================
    window.toggleMenu = function () {
        const navMenu = document.getElementById('navMenu');
        const hamburger = document.getElementById('hamburger');

        navMenu.classList.toggle('active');
        hamburger.classList.toggle('active');
    };

    // Close menu when clicking a link
    document.querySelectorAll('.nav-link').forEach(link => {
        link.addEventListener('click', () => {
            const navMenu = document.getElementById('navMenu');
            const hamburger = document.getElementById('hamburger');
            navMenu.classList.remove('active');
            hamburger.classList.remove('active');
        });
    });

    // ============================================
    // Scroll to Top
    // ============================================
    window.scrollToTop = function () {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    };

    // ============================================
    // Smooth Scroll for Anchor Links
    // ============================================
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const href = this.getAttribute('href');
            if (href !== '#') {
                e.preventDefault();
                const target = document.querySelector(href);
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            }
        });
    });

    // ============================================
    // Form Filters (Stock Page)
    // ============================================
    const filterForm = document.getElementById('filterForm');

    if (filterForm) {
        // Auto-submit on select change
        filterForm.querySelectorAll('select').forEach(select => {
            select.addEventListener('change', () => {
                filterForm.submit();
            });
        });
    }

    // ============================================
    // Image Preview on Upload
    // ============================================
    const imageInput = document.getElementById('imagem');
    const imagePreview = document.getElementById('imagePreview');

    if (imageInput && imagePreview) {
        imageInput.addEventListener('change', function (e) {
            const file = e.target.files[0];

            if (file) {
                // Validate type
                const validTypes = ['image/jpeg', 'image/png', 'image/webp'];
                if (!validTypes.includes(file.type)) {
                    alert('Por favor, selecione uma imagem válida (JPEG, PNG ou WebP).');
                    imageInput.value = '';
                    return;
                }

                // Validate size (max 5MB)
                if (file.size > 5 * 1024 * 1024) {
                    alert('A imagem não pode ter mais de 5MB.');
                    imageInput.value = '';
                    return;
                }

                // Show preview
                const reader = new FileReader();
                reader.onload = function (e) {
                    imagePreview.innerHTML = `
                        <img src="${e.target.result}" 
                             alt="Preview" 
                             style="max-height: 200px; border-radius: 12px; margin-top: 1rem;">
                    `;
                };
                reader.readAsDataURL(file);
            }
        });
    }

    // ============================================
    // Delete Confirmation
    // ============================================
    document.querySelectorAll('.btn-delete').forEach(btn => {
        btn.addEventListener('click', function (e) {
            if (!confirm('Tem a certeza que deseja eliminar esta viatura?')) {
                e.preventDefault();
            }
        });
    });

    // ============================================
    // Animations on Scroll (Intersection Observer)
    // ============================================
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);

    document.querySelectorAll('.car-card').forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(card);
    });

    // ============================================
    // WhatsApp Button Handler
    // ============================================
    document.querySelectorAll('[data-whatsapp]').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const carName = this.dataset.car || 'veículo';
            const phone = '351912345678';
            const message = encodeURIComponent(
                `Olá! Estou interessado(a) no ${carName}. Podem dar-me mais informações?`
            );
            window.open(`https://wa.me/${phone}?text=${message}`, '_blank');
        });
    });

});

// ============================================
// Loader (Optional)
// ============================================
window.addEventListener('load', function () {
    document.body.classList.add('loaded');
});
// ============================================
// Card Gallery Navigation
// ============================================
window.cardNavigate = function (btn, direction, event) {
    event.preventDefault();
    event.stopPropagation();

    const container = btn.closest('.card-gallery');
    const images = JSON.parse(container.dataset.images);
    let currentIndex = parseInt(container.dataset.index);

    currentIndex += direction;
    if (currentIndex >= images.length) currentIndex = 0;
    if (currentIndex < 0) currentIndex = images.length - 1;

    container.dataset.index = currentIndex;
    const img = container.querySelector('.gallery-main-img');

    // Suporte para diretório uploads com transição suave
    if (img) {
        img.style.opacity = '0.5';
        setTimeout(() => {
            img.src = '../uploads/' + images[currentIndex];
            img.style.opacity = '1';
        }, 100);
    }

    // Atualizar dots
    const dots = container.querySelectorAll('.dot');
    dots.forEach((dot, i) => {
        dot.classList.toggle('active', i === currentIndex);
    });
};
