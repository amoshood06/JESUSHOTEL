document.addEventListener('DOMContentLoaded', () => {
    const foodPages = document.querySelectorAll('#food-carousel .food-page');
    const dots = document.querySelectorAll('#food-pagination-dots .food-dot');

    if (foodPages.length > 0 && dots.length > 0) {
        function showPage(index) {
            foodPages.forEach((page, i) => {
                page.style.display = 'none';
                page.classList.remove('active');
                dots[i].classList.remove('active', 'bg-teal-600');
                dots[i].classList.add('bg-gray-300');
            });

            foodPages[index].style.display = 'block';
            foodPages[index].classList.add('active');
            dots[index].classList.add('active', 'bg-teal-600');
            dots[index].classList.remove('bg-gray-300');
        }

        dots.forEach((dot, index) => {
            dot.addEventListener('click', () => {
                showPage(index);
            });
        });

        // Initially show the first page
        showPage(0);
        
        const style = document.createElement('style');
        style.innerHTML = `
            #food-carousel .food-page {
                display: none;
                animation: fade 1.5s;
            }
            #food-carousel .food-page.active {
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
