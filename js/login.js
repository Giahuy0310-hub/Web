document.addEventListener('DOMContentLoaded', function() {
    // Function to display error messages
    function displayMessage(elementId, message, isError) {
        var messageBox = document.getElementById(elementId);
        messageBox.textContent = message;
        messageBox.style.color = isError ? 'red' : 'green';
        messageBox.style.display = 'block';
        setTimeout(function() {
            messageBox.style.display = 'none';
        }, 2000);
    }

    // Login form submission
    document.getElementById('login-form').addEventListener('submit', function(e) {
        e.preventDefault();
        var email = document.getElementsByName('login_email')[0].value;
        var password = document.getElementsByName('login_pass')[0].value;

        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'login.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4) {
                if (xhr.status === 200) {
                    var response = xhr.responseText;
                    if (response === 'success') {
                        // Successful login
                        displayMessage('login-message-box', 'Đăng nhập thành công.', false);
                        setTimeout(function() {
                            window.location.href = 'home.php'; // Redirect to another page
                        }, 500);
                    } else {
                        // Login failed, display error
                        displayMessage('login-error-message', response, true);
                    }
                } else {
                    // Handle AJAX request error
                    displayMessage('login-error-message', 'Lỗi kết nối đến máy chủ.', true);
                }
            }
        };
        var data = 'email=' + email + '&pass=' + password + '&action=login';
        xhr.send(data);
    });

    // Registration form submission
    document.getElementById('registration-form').addEventListener('submit', function(e) {
        e.preventDefault();
        var name = document.getElementsByName('register_name')[0].value;
        var phone = document.getElementsByName('register_phone')[0].value;
        var email = document.getElementsByName('register_email')[0].value;
        var password = document.getElementsByName('register_pass')[0].value;

        // Kiểm tra xem các trường nhập liệu có trống không
        if (name.trim() === '' || phone.trim() === '' || email.trim() === '' || password.trim() === '') {
            displayMessage('register-message-box', 'Vui lòng điền đầy đủ thông tin.', true);
            return;
        }

        // Gửi dữ liệu đăng ký đến máy chủ
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'login.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4) {
                if (xhr.status === 200) {
                    var response = xhr.responseText;
                    if (response === 'success') {
                        // Successful registration
                        displayMessage('register-message-box', 'Đăng ký thành công.', false);
                        // Clear form fields
                        document.getElementById('registration-form').reset();
                    } else {
                        // Registration failed, display error
                        displayMessage('register-message-box', response, true);
                    }
                } else {
                    // Handle AJAX request error
                    displayMessage('register-message-box', 'Lỗi kết nối đến máy chủ.', true);
                }
            }
        };
        var data = 'name=' + name + '&phone=' + phone + '&email=' + email + '&pass=' + password + '&action=register';
        xhr.send(data);
    });
});