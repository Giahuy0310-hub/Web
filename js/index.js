var images = [
    "images/banner1.jpg",
    "images/banner2.jpg",
];

var num = 0;
function next(){
    var slider = document.getElementById("slider");
        num++;
    if (num >= images.length){
        num=0;
    }

    slider.src = images[num];
    
}

function prev(){
    var slider = document.getElementById("slider");
        num--;
    if(num < 0){
        num = images.length-1;
    }
    slider.src = images[num];
}


let scrollPro_new = document.querySelector(".list_product--new");
let btnprev = document.getElementById("pro_prev");
let btnnext = document.getElementById("pro_next");

btnnext.addEventListener("click", () =>{
    scrollPro_new.style.scrollBehavior = "smooth";
    scrollPro_new.scrollLeft += 320;
});
btnprev.addEventListener("click", () =>{
    scrollPro_new.style.scrollBehavior = "smooth";
    scrollPro_new.scrollLeft -= 320;
});

let scrollPro_sold = document.querySelector(".list_product--sold");
let btnprev_sold = document.getElementById("pro_prev--sold");
let btnnext_sold = document.getElementById("pro_next--sold");

btnnext_sold.addEventListener("click", () =>{
    scrollPro_sold.style.scrollBehavior = "smooth";
    scrollPro_sold.scrollLeft += 320;
});
btnprev_sold.addEventListener("click", () =>{
    scrollPro_sold.style.scrollBehavior = "smooth";
    scrollPro_sold.scrollLeft -= 320;
});

let scrollPro_hot = document.querySelector(".list_product--hot");
let btnprev_hot = document.getElementById("pro_prev--hot");
let btnnext_hot = document.getElementById("pro_next--hot");

btnnext_hot.addEventListener("click", () =>{
    scrollPro_hot.style.scrollBehavior = "smooth";
    scrollPro_hot.scrollLeft += 320;
});
btnprev_hot.addEventListener("click", () =>{
    scrollPro_hot.style.scrollBehavior = "smooth";
    scrollPro_hot.scrollLeft -= 320;
});

function changeProductImage(productId, imageUrl, type) {
    const productImage = document.getElementById('product-image-' + productId + '-' + type);
    productImage.src = imageUrl;
}

function resetProductImage(productId, imageUrl, type) {
    const productImage = document.getElementById('product-image-' + productId + '-' + type);
    productImage.src = imageUrl;
}

// Gắn sự kiện cho cả ba danh sách sản phẩm
const productItemsNew = document.querySelectorAll('.product_new .pro--new img');
productItemsNew.forEach(image => {
    const productId = image.id.split('-')[2];
    image.addEventListener('mouseover', function () {
        changeProductImage(productId, this.src, 'new');
    });

    image.addEventListener('mouseout', function () {
        resetProductImage(productId, this.src, 'new');
    });
});

const productItemsSold = document.querySelectorAll('.product_sold .pro--sold img');
productItemsSold.forEach(image => {
    const productId = image.id.split('-')[2];
    image.addEventListener('mouseover', function () {
        changeProductImage(productId, this.src, 'sold');
    });

    image.addEventListener('mouseout', function () {
        resetProductImage(productId, this.src, 'sold');
    });
});

const productItemsHot = document.querySelectorAll('.product_hot .pro--hot img');
productItemsHot.forEach(image => {
    const productId = image.id.split('-')[2];
    image.addEventListener('mouseover', function () {
        changeProductImage(productId, this.src, 'hot');
    });

    image.addEventListener('mouseout', function () {
        resetProductImage(productId, this.src, 'hot');
    });
});