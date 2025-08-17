<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
       <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <title>Đăng Nhập - Telesale Vinamilk</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
       body {
            font-family: 'Open Sans', sans-serif;
            margin: 0;
            padding: 0;
            background: url('https://i.ibb.co/yFb4vr9j/BgPCBD.png') no-repeat center center fixed;
            background-size: cover;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #333;
            overflow: hidden;
        }
        .login-container {
         
            padding: 32px;
            border-radius: 16px;
         
            width: 100%;
            max-width: 400px;
            backdrop-filter: blur(10px);
        }
        .logo { text-align: center; margin-bottom: 24px; }
        .logo img { max-width: 180px; height: auto; transition: transform 0.3s ease; }
        .logo img:hover { transform: scale(1.05); }
        .tab-container { display: flex; gap: 8px; margin-bottom: 24px; border-bottom: 1px solid #e5e7eb; }
        .tab { flex: 1; padding: 12px; text-align: center; font-size: 14px; font-weight: 500; color: #6b7280; cursor: pointer; transition: all 0.3s ease; border-radius: 8px 8px 0 0; }
        .tab.active { color: #0300ff; background: #f5f3ff; font-weight: 600; }
        .tab-content { display: none; }
        .tab-content.active { display: block; }
        .form-group { margin-bottom: 16px; }
        .form-group label { font-size: 13px; font-weight: 500; color: #0300ff; margin-bottom: 8px; display: block; }
        .input-group { position: relative; }
        .input-field { width: 100%; padding: 12px 12px 12px 36px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; transition: all 0.2s ease; background: #f9fafb; }
        .input-field:focus { border-color: #7c3aed; box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.1); outline: none; background: #ffffff; }
        .input-group svg { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); width: 16px; height: 16px; fill: #0300ff; }
        .password-toggle { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); cursor: pointer; width: 16px; height: 16px; fill: #7c3aed; transition: fill 0.2s ease; }
        .password-toggle:hover { fill: #6d28d9; }
        .btn { width: 100%; padding: 12px; background: #0300ff; color: #ffffff; border: none; border-radius: 8px; font-size: 14px; font-weight: 500; cursor: pointer; transition: all 0.3s ease; position: relative; overflow: hidden; }
        .btn:hover { background: linear-gradient(90deg, #6d28d9 0%, #4c1d95 100%); box-shadow: 0 4px 12px rgba(124, 58, 237, 0.3); }
        .btn:disabled { background: #d1d5db; cursor: not-allowed; }
        .link { display: block; text-align: center; margin-top: 16px; font-size: 13px; color: #0300ff; text-decoration: none; transition: color 0.2s ease; }
        .link:hover { color: #6d28d9; text-decoration: underline; }
        .error-message { color: #dc2626; font-size: 12px; margin-top: 4px; display: none; }
        .checkbox-label { display: flex; align-items: center; font-size: 12px; color: #6b7280; margin-bottom: 16px; }
        .checkbox-label input { margin-right: 8px; accent-color: #7c3aed; }
        .strength-meter { height: 4px; background: #e5e7eb; border-radius: 2px; margin-top: 4px; }
        .strength-fill { height: 100%; border-radius: 2px; transition: width 0.3s ease, background-color 0.3s ease; }
        #strength-text { font-size: 12px; color: #6b7280; margin-top: 4px; }
        .security-notice { display: flex; align-items: center; font-size: 12px; color: #6b7280; margin-bottom: 16px; }
        .security-notice svg { margin-right: 8px; width: 16px; height: 16px; fill: #6b7280; }
        .loading-spinner { display: inline-block; width: 16px; height: 16px; border: 2px solid rgba(255, 255, 255, 0.3); border-top: 2px solid #ffffff; border-radius: 50%; animation: spin 1s linear infinite; margin-right: 8px; vertical-align: middle; }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        @media (max-width: 400px) {
            .login-container { padding: 24px; margin: 16px; }
            .logo img { max-width: 140px; }
            .btn { font-size: 13px; }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <img src="https://i.ibb.co/BVkB4HWJ/Telesale-Logo.png" alt="Telesale Vinamilk Logo" onerror="this.src='https://via.placeholder.com/180';">
        </div>
        <div class="tab-container">
            <div class="tab active" data-tab="login">Đăng nhập</div>
            <div class="tab" data-tab="register">Đăng ký</div>
            <div class="tab" data-tab="forgot">Quên mật khẩu</div>
        </div>
        <div id="login" class="tab-content active">
            <form id="loginForm" method="POST">
                <div class="form-group">
                    <label for="login-username">Tên đăng nhập</label>
                    <div class="input-group">
                        <input type="text" id="login-username" name="username" class="input-field" required>
                        <svg><use xlink:href="#user-icon"></use></svg>
                    </div>
                    <div class="error-message" id="login-usernameError">Vui lòng nhập tên đăng nhập</div>
                </div>
                <div class="form-group">
                    <label for="login-password">Mật khẩu</label>
                    <div class="input-group">
                        <input type="password" id="login-password" name="password" class="input-field" required>
                        <svg><use xlink:href="#lock-icon"></use></svg>
                        <svg class="password-toggle" onclick="togglePassword(this)" style="display: none;"><use xlink:href="#eye-icon"></use></svg>
                    </div>
                    <div class="error-message" id="login-passwordError">Vui lòng nhập mật khẩu</div>
                </div>
                <button type="submit" id="loginBtn" class="btn">Đăng nhập</button>
                <a href="#" class="link" onclick="showTab('forgot')">Quên mật khẩu?</a>
            </form>
        </div>
        <div id="register" class="tab-content">
            <form id="form-signup" method="POST">
                <div class="form-group">
                    <label>Tên đăng nhập</label>
                    <div class="input-group">
                        <input type="text" name="username" class="input-field" placeholder="3-20 ký tự" required>
                        <svg><use xlink:href="#user-icon"></use></svg>
                    </div>
                    <div class="error-message" id="register-usernameError">Vui lòng nhập tên đăng nhập</div>
                </div>
                <div class="form-group">
                    <label>Mật khẩu</label>
                    <div class="input-group">
                        <input type="password" name="password" class="input-field" placeholder="Tối thiểu 6 ký tự" required onkeyup="checkPasswordStrength(this)">
                        <svg><use xlink:href="#lock-icon"></use></svg>
                        <svg class="password-toggle" onclick="togglePassword(this)" style="display: none;"><use xlink:href="#eye-icon"></use></svg>
                    </div>
                    <div class="strength-meter">
                        <div id="strength-fill" class="strength-fill"></div>
                    </div>
                    <div id="strength-text">Độ mạnh mật khẩu</div>
                    <div class="error-message" id="register-passwordError">Vui lòng nhập mật khẩu</div>
                </div>
                <div class="form-group">
                    <label>Xác nhận mật khẩu</label>
                    <div class="input-group">
                        <input type="password" name="repassword" class="input-field" placeholder="Nhập lại mật khẩu" required>
                        <svg><use xlink:href="#lock-icon"></use></svg>
                        <svg class="password-toggle" onclick="togglePassword(this)" style="display: none;"><use xlink:href="#eye-icon"></use></svg>
                    </div>
                    <div class="error-message" id="register-repasswordError">Mật khẩu không khớp</div>
                </div>
                <div class="form-group">
                    <label>Mã giới thiệu</label>
                    <div class="input-group">
                        <input type="text" name="invitecode" class="input-field" placeholder="Mã giới thiệu (không bắt buộc)">
                        <svg><use xlink:href="#gift-icon"></use></svg>
                    </div>
                </div>
                <label class="checkbox-label">
                    <input type="checkbox" required>
                    Tôi đồng ý với <a href="#" class="link">Điều khoản sử dụng</a> và <a href="#" class="link">Chính sách bảo mật</a>
                </label>
                <button type="submit" id="registerBtn" class="btn">Tạo tài khoản</button>
                <a href="#" class="link" onclick="showTab('login')">Đã có tài khoản? Đăng nhập</a>
            </form>
        </div>
        <div id="forgot" class="tab-content">
            <div class="security-notice">
                <svg><use xlink:href="#question-icon"></use></svg>
                Gửi liên kết khôi phục qua số điện thoại
            </div>
            <form id="form-forgot" method="POST">
                <div class="form-group">
                    <label>Tên đăng nhập</label>
                    <div class="input-group">
                        <input type="text" name="username" class="input-field" placeholder="Tên đăng nhập" required>
                        <svg><use xlink:href="#user-icon"></use></svg>
                    </div>
                    <div class="error-message" id="forgot-usernameError">Vui lòng nhập tên đăng nhập</div>
                </div>
                <div class="form-group">
                    <label>Số điện thoại</label>
                    <div class="input-group">
                        <input type="tel" name="phone" class="input-field" placeholder="Số điện thoại" required>
                        <svg><use xlink:href="#phone-icon"></use></svg>
                    </div>
                    <div class="error-message" id="forgot-phoneError">Vui lòng nhập số điện thoại</div>
                </div>
                <button type="submit" id="forgotBtn" class="btn">Gửi yêu cầu</button>
                <a href="#" class="link" onclick="showTab('login')">Quay lại đăng nhập</a>
            </form>
        </div>
    </div>

    <svg style="display: none;">
        <defs>
            <symbol id="user-icon" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></symbol>
            <symbol id="lock-icon" viewBox="0 0 24 24"><path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3-9H9V6c0-1.66 1.34-3 3-3s3 1.34 3 3v2z"/></symbol>
            <symbol id="eye-icon" viewBox="0 0 24 24"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></symbol>
            <symbol id="eye-slash-icon" viewBox="0 0 24 24"><path d="M3.71 3.71a1 1 0 00-1.42 0L1.29 4.71a1 1 0 000 1.42l16.58 16.58a1 1 0 001.42 0l1-1a1 1 0 000-1.42L3.71 3.71zM12 19c-3.87 0-7.47-2.18-9.24-5.66l2.95-2.95C6.45 12.32 8.59 14 12 14c3.41 0 5.55-1.68 6.29-3.61l2.95-2.95C19.47 9.82 15.87 12 12 12c-3.41 0-5.55-1.68-6.29-3.61L3.76 6.44C5.53 3.82 9.13 2 12 2c4.2 0 7.93 2.61 9.43 6.56l-2.95 2.95C17.55 9.68 15.41 8 12 8c-3.41 0-5.55 1.68-6.29 3.61l-2.95 2.95C4.53 17.18 8.13 19 12 19c.87 0 1.73-.15 2.56-.43l-2.95-2.95C11.45 15.32 11.22 15 12 15z"/></symbol>
            <symbol id="gift-icon" viewBox="0 0 24 24"><path d="M20 6h-2.18c.11-.31.18-.65.18-1 0-1.66-1.34-3-3-3-1.05 0-1.96.54-2.5 1.35l-.5.67-.5-.68C10.96 2.54 10.05 2 9 2 7.34 2 6 3.34 6 5c0 .35.07.69.18 1H4c-1.11 0-1.99.89-1.99 2L2 19c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V8c0-1.11-.89-2-2-2zm-5-2c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zM9 4c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm11 15H4v-2h16v2zm0-5H4V8h5.08L7 10.83 8.62 12 11 9.67V8h2v1.67l2.38 2.33L17 10.83 14.92 8H20v6z"/></symbol>
            <symbol id="phone-icon" viewBox="0 0 24 24"><path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.24 1.02l-2.2 2.2z"/></symbol>
            <symbol id="question-icon" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 17h-2v-2h2v2zm2.07-7.75l-.9.92C13.45 12.9 13 13.5 13 15h-2v-.5c0-1.1.45-2.1 1.17-2.83l1.24-1.26c.37-.36.59-.86.59-1.41 0-1.1-.9-2-2-2s-2 .9-2 2H8c0-2.21 1.79-4 4-4s4 1.79 4 4c0 .88-.36 1.68-.93 2.25z"/></symbol>
        </defs>
    </svg>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function showTab(tabId) {
            document.querySelectorAll('.tab').forEach(tab => tab.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
            document.querySelector(`.tab[data-tab="${tabId}"]`).classList.add('active');
            document.getElementById(tabId).classList.add('active');
        }

        function togglePassword(element) {
            const input = element.closest('.input-group').querySelector('.input-field');
            const isPassword = input.type === 'password';
            input.type = isPassword ? 'text' : 'password';
            element.querySelector('use').setAttribute('xlink:href', isPassword ? '#eye-slash-icon' : '#eye-icon');
        }

        function checkPasswordStrength(input) {
            const password = input.value;
            const strengthFill = document.getElementById('strength-fill');
            const strengthText = document.getElementById('strength-text');
            let strength = 0;

            if (password.length > 0) strength += 10;
            if (password.length >= 6) strength += 20;
            if (/[A-Z]/.test(password)) strength += 20;
            if (/[0-9]/.test(password)) strength += 20;
            if (/[^A-Za-z0-9]/.test(password)) strength += 30;

            strength = Math.min(100, strength);

            strengthFill.style.width = `${strength}%`;
            if (strength <= 30) {
                strengthFill.style.backgroundColor = '#dc2626';
                strengthText.textContent = 'Yếu';
            } else if (strength <= 70) {
                strengthFill.style.backgroundColor = '#f59e0b';
                strengthText.textContent = 'Trung bình';
            } else {
                strengthFill.style.backgroundColor = '#10b981';
                strengthText.textContent = 'Mạnh';
            }
        }

        const customSwal = Swal.mixin({
            customClass: {
                popup: 'rounded-xl text-sm bg-white shadow-xl',
                title: 'font-semibold text-lg text-gray-800',
                content: 'text-gray-600',
                confirmButton: 'bg-gradient-to-r from-violet-500 to-purple-600 text-white px-4 py-2 rounded-md font-medium',
                cancelButton: 'bg-gray-200 text-gray-700 px-4 py-2 rounded-md font-medium'
            },
            buttonsStyling: false,
            width: '350px',
            background: '#ffffff',
            showClass: { popup: 'animate__animated animate__fadeInDown' },
            hideClass: { popup: 'animate__animated animate__fadeOutUp' }
        });

        const forms = {
            login: document.getElementById('loginForm'),
            register: document.getElementById('form-signup'),
            forgot: document.getElementById('form-forgot')
        };

        const buttons = {
            login: document.getElementById('loginBtn'),
            register: document.getElementById('registerBtn'),
            forgot: document.getElementById('forgotBtn')
        };

        const buttonText = {
            login: document.querySelector('#loginBtn span'),
            register: document.querySelector('#registerBtn span'),
            forgot: document.querySelector('#forgotBtn span')
        };

        forms.login.addEventListener('submit', function(e) {
            e.preventDefault();
            const username = document.getElementById('login-username');
            const password = document.getElementById('login-password');
            const errors = {
                username: document.getElementById('login-usernameError'),
                password: document.getElementById('login-passwordError')
            };

            let isValid = true;
            if (!username.value.trim()) {
                errors.username.style.display = 'block';
                isValid = false;
            } else {
                errors.username.style.display = 'none';
            }

            if (!password.value) {
                errors.password.style.display = 'block';
                isValid = false;
            } else {
                errors.password.style.display = 'none';
            }

            if (!isValid) return;

            const originalText = buttons.login.innerHTML;
            buttons.login.innerHTML = '<span class="loading-spinner"></span>Đang đăng nhập...';
            buttons.login.disabled = true;

            $.ajax({
                url: '/ajax/index.php',
                type: 'POST',
                data: {
                    action_type: "login",
                    username: username.value.trim(),
                    password: password.value
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        customSwal.fire({
                            icon: 'success',
                            title: 'Đăng nhập thành công!',
                            text: 'Chào mừng bạn trở lại!',
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            window.location.href = '/';
                        });
                    } else {
                        customSwal.fire({
                            icon: 'error',
                            title: 'Đăng nhập thất bại!',
                            text: response.message || 'Tên đăng nhập hoặc mật khẩu không đúng!',
                            confirmButtonText: 'Thử lại'
                        });
                    }
                },
                error: function() {
                    customSwal.fire({
                        icon: 'error',
                        title: 'Lỗi kết nối!',
                        text: 'Không thể kết nối đến máy chủ. Vui lòng thử lại.'
                    });
                },
                complete: function() {
                    buttons.login.innerHTML = originalText;
                    buttons.login.disabled = false;
                }
            });
        });

        forms.register.addEventListener('submit', function(e) {
            e.preventDefault();
            const fields = {
                username: document.querySelector('#form-signup input[name="username"]'),
                password: document.querySelector('#form-signup input[name="password"]'),
                repassword: document.querySelector('#form-signup input[name="repassword"]'),
                invitecode: document.querySelector('#form-signup input[name="invitecode"]'),
                terms: document.querySelector('.checkbox-label input')
            };
            const errors = {
                username: document.getElementById('register-usernameError'),
                password: document.getElementById('register-passwordError'),
                repassword: document.getElementById('register-repasswordError')
            };

            let isValid = true;
            if (!fields.username.value.trim() || fields.username.value.length < 3 || fields.username.value.length > 20) {
                errors.username.textContent = 'Tên đăng nhập phải từ 3-20 ký tự';
                errors.username.style.display = 'block';
                isValid = false;
            } else {
                errors.username.style.display = 'none';
            }

            if (!fields.password.value || fields.password.value.length < 6) {
                errors.password.textContent = 'Mật khẩu phải tối thiểu 6 ký tự';
                errors.password.style.display = 'block';
                isValid = false;
            } else {
                errors.password.style.display = 'none';
            }

            if (!fields.repassword.value || fields.repassword.value !== fields.password.value) {
                errors.repassword.textContent = 'Mật khẩu không khớp';
                errors.repassword.style.display = 'block';
                isValid = false;
            } else {
                errors.repassword.style.display = 'none';
            }

            if (!fields.terms.checked) {
                customSwal.fire({
                    icon: 'warning',
                    title: 'Cảnh báo!',
                    text: 'Vui lòng đồng ý với Điều khoản sử dụng và Chính sách bảo mật.'
                });
                isValid = false;
            }

            if (!isValid) return;

            const originalText = buttons.register.innerHTML;
            buttons.register.innerHTML = '<span class="loading-spinner"></span>Đang đăng ký...';
            buttons.register.disabled = true;

            $.ajax({
                url: '/ajax/index.php',
                type: 'POST',
                data: {
                    action_type: "register",
                    username: fields.username.value.trim(),
                    password: fields.password.value,
                    repassword: fields.repassword.value,
                    invitecode: fields.invitecode.value || ''
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        customSwal.fire({
                            icon: 'success',
                            title: 'Đăng ký thành công!',
                            text: 'Tài khoản của bạn đã được tạo.',
                            confirmButtonText: 'Đăng nhập ngay'
                        }).then(() => showTab('login'));
                    } else {
                        customSwal.fire({
                            icon: 'error',
                            title: 'Đăng ký thất bại!',
                            text: response.message || 'Có lỗi xảy ra. Vui lòng thử lại.',
                            confirmButtonText: 'Thử lại'
                        });
                    }
                },
                error: function() {
                    customSwal.fire({
                        icon: 'error',
                        title: 'Lỗi kết nối!',
                        text: 'Không thể kết nối đến máy chủ. Vui lòng thử lại.'
                    });
                },
                complete: function() {
                    buttons.register.innerHTML = originalText;
                    buttons.register.disabled = false;
                }
            });
        });

        forms.forgot.addEventListener('submit', function(e) {
            e.preventDefault();
            const fields = {
                username: document.querySelector('#form-forgot input[name="username"]'),
                phone: document.querySelector('#form-forgot input[name="phone"]')
            };
            const errors = {
                username: document.getElementById('forgot-usernameError'),
                phone: document.getElementById('forgot-phoneError')
            };

            let isValid = true;
            if (!fields.username.value.trim()) {
                errors.username.style.display = 'block';
                isValid = false;
            } else {
                errors.username.style.display = 'none';
            }

            if (!fields.phone.value || !/^\d{10}$/.test(fields.phone.value)) {
                errors.phone.textContent = 'Vui lòng nhập số điện thoại hợp lệ (10 số)';
                errors.phone.style.display = 'block';
                isValid = false;
            } else {
                errors.phone.style.display = 'none';
            }

            if (!isValid) return;

            const originalText = buttons.forgot.innerHTML;
            buttons.forgot.innerHTML = '<span class="loading-spinner"></span>Đang gửi...';
            buttons.forgot.disabled = true;

            $.ajax({
                url: '/ajax/index.php',
                type: 'POST',
                data: {
                    action_type: "forgot",
                    username: fields.username.value.trim(),
                    phone: fields.phone.value
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        customSwal.fire({
                            icon: 'success',
                            title: 'Yêu cầu thành công!',
                            text: 'Liên kết khôi phục đã được gửi qua số điện thoại.',
                            confirmButtonText: 'Đã hiểu'
                        }).then(() => showTab('login'));
                    } else {
                        customSwal.fire({
                            icon: 'error',
                            title: 'Yêu cầu thất bại!',
                            text: response.message || 'Thông tin không đúng. Vui lòng thử lại.',
                            confirmButtonText: 'Thử lại'
                        });
                    }
                },
                error: function() {
                    customSwal.fire({
                        icon: 'error',
                        title: 'Lỗi kết nối!',
                        text: 'Không thể kết nối đến máy chủ. Vui lòng thử lại.'
                    });
                },
                complete: function() {
                    buttons.forgot.innerHTML = originalText;
                    buttons.forgot.disabled = false;
                }
            });
        });

        document.querySelectorAll('.tab').forEach(tab => {
            tab.addEventListener('click', () => showTab(tab.getAttribute('data-tab')));
        });

        document.querySelectorAll('.input-field').forEach(input => {
            input.addEventListener('focus', function() {
                this.closest('.form-group').querySelector('.error-message').style.display = 'none';
            });
        });

        document.querySelectorAll('.password-toggle').forEach(toggle => {
            toggle.style.display = 'block';
        });
    </script>
</body>
</html>