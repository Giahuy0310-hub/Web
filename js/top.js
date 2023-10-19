let currentBestSellingSlide = 0;
let currentExpensiveSlide = 0;
const bestSellingSlides = document.querySelectorAll("#bestSellingContainer .product");
const expensiveSlides = document.querySelectorAll("#expensiveContainer .product");
const showCount = 4;

function showSlides(startIndex, slides) {
    for (let i = 0; i < slides.length; i++) {
        slides[i].style.display = "none";
    }

    for (let i = 0; i < showCount; i++) {
        slides[(startIndex + i) % slides.length].style.display = "block";
    }
}

function plusSlides(n, containerId) {
    if (containerId === 'bestSellingContainer') {
        currentBestSellingSlide = (currentBestSellingSlide + n + bestSellingSlides.length) % bestSellingSlides.length;
        showSlides(currentBestSellingSlide, bestSellingSlides);
    } else if (containerId === 'expensiveContainer') {
        currentExpensiveSlide = (currentExpensiveSlide + n + expensiveSlides.length) % expensiveSlides.length;
        showSlides(currentExpensiveSlide, expensiveSlides);
    }
}

showSlides(currentBestSellingSlide, bestSellingSlides);
showSlides(currentExpensiveSlide, expensiveSlides);