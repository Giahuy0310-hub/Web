var originalLargeImageSrc = ""; 
var modalImageSrc = ""; 
var ratingValue = 0.0; 
var modalVisible = false; 

function showLargeImage(imageSrc) {
    var largeImage = document.getElementById('largeImage');

    if (originalLargeImageSrc === "") {
        originalLargeImageSrc = largeImage.src;
    }

    modalImageSrc = imageSrc;

    // Đặt độ trong suốt của ảnh lớn thành 0 trước khi thay đổi nguồn ảnh
    largeImage.style.opacity = "0";

    // Sau một khoảng thời gian ngắn, đặt lại nguồn ảnh của ảnh lớn và độ trong suốt
    setTimeout(function () {
        largeImage.src = imageSrc;
        largeImage.style.opacity = "1"; // Đặt lại độ trong suốt
    }, 200); // 200 milliseconds

    modalVisible = true; // Đánh dấu modal đã mở
}

function rateProduct(rating) {
    // Gán giá trị đánh giá từ số sao đã chọn (sử dụng giá trị với thập phân)
    ratingValue = rating;

    // Xóa tất cả các lớp 'selected' trên các sao
    var stars = document.querySelectorAll('.star');
    stars.forEach(function (star) {
        star.classList.remove('selected');
    });

    // Làm tròn giá trị đánh giá với thập phân để đặt lớp 'selected' cho các sao
    var roundedRating = Math.round(rating * 2) / 2;
    var starIndex = (roundedRating - 1) * 2; // Tính toán chỉ số của sao

    for (var i = 0; i <= starIndex; i++) {
        stars[i].classList.add('selected');
    }
}

function openModal() {
    if (modalVisible) {
        var modal = document.getElementById('imageModal');
        modal.style.display = 'block';

        // Lấy đối tượng ảnh modal
        var modalImage = document.getElementById('modalImage');
        modalImage.src = modalImageSrc;

        // Căn giữa ảnh lớn trong modal
        modalImage.style.marginTop = (modal.clientHeight - modalImage.clientHeight) / 2 + 'px';
    }
}

function closeModal() {
    var modal = document.getElementById('imageModal');
    modal.style.display = 'none';

    // Đặt lại nguồn ảnh lớn về nguồn ảnh lớn hiện tại
    originalLargeImageSrc = document.getElementById('largeImage').src;

    // Đặt lại nguồn ảnh của ảnh modal và margin-top
    var modalImage = document.getElementById('modalImage');
    modalImage.src = "";
    modalImage.style.marginTop = '0';
}

window.addEventListener('load', function () {
    closeModal();
});