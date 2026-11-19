/* Slideshow NexTech: auto-play, panah, dan indikator */
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-slider]').forEach(function (slider) {
        const slides = Array.from(slider.querySelectorAll('.slide'));
        const dots = Array.from(slider.querySelectorAll('.slider-dot'));
        const prev = slider.querySelector('.slider-prev');
        const next = slider.querySelector('.slider-next');

        if (slides.length < 2) return;

        let current = 0;
        let timer = null;
        const interval = 5000;

        function showSlide(index) {
            current = (index + slides.length) % slides.length;

            slides.forEach(function (slide, i) {
                const active = i === current;
                slide.classList.toggle('active', active);
                slide.setAttribute('aria-hidden', active ? 'false' : 'true');
            });

            dots.forEach(function (dot, i) {
                const active = i === current;
                dot.classList.toggle('active', active);
                if (active) {
                    dot.setAttribute('aria-current', 'true');
                } else {
                    dot.removeAttribute('aria-current');
                }
            });
        }

        function stopAutoPlay() {
            if (timer) window.clearInterval(timer);
            timer = null;
        }

        function startAutoPlay() {
            stopAutoPlay();
            timer = window.setInterval(function () {
                showSlide(current + 1);
            }, interval);
        }

        if (prev) {
            prev.addEventListener('click', function () {
                showSlide(current - 1);
                startAutoPlay();
            });
        }

        if (next) {
            next.addEventListener('click', function () {
                showSlide(current + 1);
                startAutoPlay();
            });
        }

        dots.forEach(function (dot, i) {
            dot.addEventListener('click', function () {
                showSlide(i);
                startAutoPlay();
            });
        });

        slider.addEventListener('mouseenter', stopAutoPlay);
        slider.addEventListener('mouseleave', startAutoPlay);
        slider.addEventListener('focusin', stopAutoPlay);
        slider.addEventListener('focusout', startAutoPlay);

        showSlide(0);
        startAutoPlay();
    });
});
