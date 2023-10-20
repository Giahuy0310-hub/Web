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

