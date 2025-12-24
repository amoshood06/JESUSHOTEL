document.addEventListener('DOMContentLoaded', () => {
    const slides = document.querySelectorAll('#hero-slider .slide');
    const dots = document.querySelectorAll('#hero-slider .dot');
    let currentSlide = 0;
    let slideInterval;

    if (slides.length > 0 && dots.length > 0) {
        function showSlide(index) {
            slides.forEach((slide, i) => {
                slide.style.display = 'none';
                slide.classList.remove('active');
                dots[i].classList.remove('active', 'bg-teal-600');
                dots[i].classList.add('bg-gray-300');
            });

            slides[index].style.display = 'block';
            slides[index].classList.add('active');
            dots[index].classList.add('active', 'bg-teal-600');
            dots[index].classList.remove('bg-gray-300');
            currentSlide = index;
        }

        function nextSlide() {
            currentSlide = (currentSlide + 1) % slides.length;
            showSlide(currentSlide);
        }

        dots.forEach((dot, index) => {
            dot.addEventListener('click', () => {
                showSlide(index);
                resetInterval();
            });
        });

        function startInterval() {
            slideInterval = setInterval(nextSlide, 5000); // Change slide every 5 seconds
        }

        function resetInterval() {
            clearInterval(slideInterval);
            startInterval();
        }

        // Initially hide all slides except the first one and show the first one
        showSlide(0);

        startInterval();

        // Add basic styling for transitions
        const style = document.createElement('style');
        style.innerHTML = `
            #hero-slider .slide {
                display: none;
                animation: fade 1.5s;
            }
            #hero-slider .slide.active {
                display: block;
            }
            @keyframes fade {
                from {opacity: .4}
                to {opacity: 1}
            }
        `;
        document.head.appendChild(style);
    }
});
