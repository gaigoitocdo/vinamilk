<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Sections Sản Phẩm</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #3b82f6;
            --primary-dark: #1e40af;
            --secondary-color: #64748b;
            --success-color: #059669;
            --warning-color: #d97706;
            --danger-color: #dc2626;
            --info-color: #0891b2;
            --gray-50: #f8fafc;
            --gray-100: #f1f5f9;
            --gray-200: #e2e8f0;
            --gray-300: #cbd5e0;
            --gray-400: #94a3b8;
            --gray-500: #64748b;
            --gray-600: #475569;
            --gray-700: #334155;
            --gray-800: #1e293b;
            --gray-900: #0f172a;
            --white: #ffffff;
            --border-radius: 12px;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }



        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: var(--gray-800);
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

       

      
      
        /* Main Content */
        .main-content {
            margin-left: 280px;
            flex: 1;
            background: var(--gray-50);
            min-height: 100vh;
        }

        .main-header {
            background: var(--white);
            padding: 1.5rem 2rem;
            border-bottom: 1px solid var(--gray-200);
            box-shadow: var(--shadow-sm);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .main-title {
            font-size: 1.875rem;
            font-weight: 700;
            color: var(--gray-900);
            margin-bottom: 0.5rem;
        }

        .main-subtitle {
            color: var(--gray-600);
            font-size: 1rem;
        }

        .content-area {
            padding: 2rem;
            max-width: 1400px;
            margin: 0 auto;
        }

        /* Cards */
        .card {
            background: var(--white);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            border: 1px solid var(--gray-200);
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .card:hover {
            box-shadow: var(--shadow-lg);
            border-color: var(--primary-color);
        }

        .card-header {
            padding: 1.5rem 2rem;
            border-bottom: 1px solid var(--gray-200);
            background: var(--gray-50);
        }

        .card-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--gray-900);
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .card-body {
            padding: 2rem;
        }

        /* Status Dashboard */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: var(--white);
            padding: 1.5rem;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            border: 1px solid var(--gray-200);
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-color), var(--info-color));
        }

        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: var(--border-radius);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: var(--white);
        }

        .stat-icon.primary { background: linear-gradient(135deg, var(--primary-color), var(--primary-dark)); }
        .stat-icon.success { background: linear-gradient(135deg, var(--success-color), #047857); }
        .stat-icon.warning { background: linear-gradient(135deg, var(--warning-color), #b45309); }
        .stat-icon.info { background: linear-gradient(135deg, var(--info-color), #0e7490); }

        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: var(--gray-900);
            display: block;
        }

        .stat-label {
            color: var(--gray-600);
            font-size: 0.875rem;
            font-weight: 500;
        }

        .stat-change {
            font-size: 0.75rem;
            font-weight: 500;
            padding: 0.25rem 0.5rem;
            border-radius: 0.375rem;
            background: var(--gray-100);
            color: var(--gray-600);
        }

        /* Form Styles */
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--gray-700);
            margin-bottom: 0.5rem;
        }

        .form-label.required::after {
            content: ' *';
            color: var(--danger-color);
        }

        .form-input, .form-select, .form-textarea {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 2px solid var(--gray-300);
            border-radius: var(--border-radius);
            font-size: 0.875rem;
            transition: all 0.2s ease;
            background: var(--white);
            color: var(--gray-900);
        }

        .form-input:focus, .form-select:focus, .form-textarea:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .form-input:disabled, .form-select:disabled {
            background: var(--gray-100);
            color: var(--gray-500);
            cursor: not-allowed;
        }

        .form-help {
            font-size: 0.75rem;
            color: var(--gray-500);
            margin-top: 0.25rem;
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            font-size: 0.875rem;
            font-weight: 600;
            border: none;
            border-radius: var(--border-radius);
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
            white-space: nowrap;
            position: relative;
            overflow: hidden;
        }

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: var(--white);
            box-shadow: var(--shadow-md);
        }

        .btn-primary:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .btn-success {
            background: linear-gradient(135deg, var(--success-color), #047857);
            color: var(--white);
            box-shadow: var(--shadow-md);
        }

        .btn-success:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .btn-warning {
            background: linear-gradient(135deg, var(--warning-color), #b45309);
            color: var(--white);
            box-shadow: var(--shadow-md);
        }

        .btn-danger {
            background: linear-gradient(135deg, var(--danger-color), #b91c1c);
            color: var(--white);
            box-shadow: var(--shadow-md);
        }

        .btn-outline {
            background: transparent;
            border: 2px solid var(--gray-300);
            color: var(--gray-700);
        }

        .btn-outline:hover:not(:disabled) {
            background: var(--gray-100);
            border-color: var(--gray-400);
        }

        .btn-group {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            margin-top: 1.5rem;
        }

        /* Image Upload */
        .image-upload-area {
            border: 2px dashed var(--gray-300);
            border-radius: var(--border-radius);
            padding: 2rem;
            text-align: center;
            background: var(--gray-50);
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
        }

        .image-upload-area:hover {
            border-color: var(--primary-color);
            background: var(--primary-color)/5;
        }

        .image-upload-area.dragover {
            border-color: var(--success-color);
            background: var(--success-color)/10;
        }

        .image-upload-area.has-image {
            border-style: solid;
            border-color: var(--success-color);
            background: var(--white);
        }

        .upload-icon {
            font-size: 3rem;
            color: var(--gray-400);
            margin-bottom: 1rem;
        }

        .upload-text {
            color: var(--gray-600);
            font-weight: 500;
            margin-bottom: 0.5rem;
        }

        .upload-subtext {
            color: var(--gray-500);
            font-size: 0.875rem;
        }

        .preview-image {
            max-width: 100%;
            max-height: 200px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-md);
        }

        /* Section List */
        .section-item {
            background: var(--white);
            border: 1px solid var(--gray-200);
            border-radius: var(--border-radius);
            padding: 1.5rem;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 1.5rem;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .section-item::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            background: linear-gradient(135deg, var(--primary-color), var(--info-color));
        }

        .section-item:hover {
            border-color: var(--primary-color);
            box-shadow: var(--shadow-lg);
        }

        .section-thumbnail {
            width: 80px;
            height: 80px;
            border-radius: var(--border-radius);
            object-fit: cover;
            background: var(--gray-100);
            border: 2px solid var(--gray-200);
        }

        .section-details {
            flex: 1;
        }

        .section-name {
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--gray-900);
            margin-bottom: 0.5rem;
        }

        .section-subtitle {
            color: var(--gray-600);
            font-size: 0.875rem;
            margin-bottom: 0.75rem;
        }

        .section-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            font-size: 0.75rem;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 0.25rem;
            color: var(--gray-500);
        }

        .meta-value {
            font-weight: 600;
            color: var(--gray-700);
        }

        .section-actions {
            display: flex;
            gap: 0.5rem;
        }

        .action-btn {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
            font-size: 0.875rem;
        }

        .action-btn:hover {
            transform: scale(1.1);
        }

        .action-btn.edit {
            background: var(--warning-color);
            color: var(--white);
        }

        .action-btn.delete {
            background: var(--danger-color);
            color: var(--white);
        }

        .action-btn.toggle {
            background: var(--success-color);
            color: var(--white);
        }

        .action-btn.toggle.inactive {
            background: var(--gray-400);
        }

        /* Status Badges */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .status-badge.active {
            background: var(--success-color)/10;
            color: var(--success-color);
        }

        .status-badge.inactive {
            background: var(--gray-400)/10;
            color: var(--gray-500);
        }

        /* Preview Section */
        .preview-section {
            background: linear-gradient(135deg, var(--primary-color)/5, var(--info-color)/5);
            border: 2px dashed var(--primary-color);
            border-radius: var(--border-radius);
            padding: 2rem;
            text-align: center;
            margin-top: 2rem;
        }

        .preview-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-dark);
            margin-bottom: 0.5rem;
        }

        .preview-subtitle {
            color: var(--gray-600);
            margin-bottom: 1rem;
        }

        .preview-image {
            max-width: 300px;
            max-height: 150px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-lg);
        }

        /* Toast Notifications */
        .toast {
            position: fixed;
            top: 2rem;
            right: 2rem;
            padding: 1rem 1.5rem;
            border-radius: var(--border-radius);
            color: var(--white);
            font-weight: 600;
            z-index: 10000;
            transform: translateX(100%);
            transition: transform 0.3s ease;
            max-width: 400px;
            box-shadow: var(--shadow-xl);
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .toast.show {
            transform: translateX(0);
        }

        .toast.success { background: linear-gradient(135deg, var(--success-color), #047857); }
        .toast.error { background: linear-gradient(135deg, var(--danger-color), #b91c1c); }
        .toast.info { background: linear-gradient(135deg, var(--info-color), #0e7490); }
        .toast.warning { background: linear-gradient(135deg, var(--warning-color), #b45309); }

        /* Loading States */
        .loading {
            position: relative;
            pointer-events: none;
            opacity: 0.7;
        }

        .loading::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 20px;
            height: 20px;
            margin: -10px 0 0 -10px;
            border: 2px solid var(--primary-color);
            border-radius: 50%;
            border-top-color: transparent;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

      
        /* Dark mode support */
        @media (prefers-color-scheme: dark) {
            :root {
                --gray-50: #1a202c;
                --gray-100: #2d3748;
                --gray-200: #4a5568;
                --gray-300: #718096;
                --white: #1a202c;
                --gray-800: #e2e8f0;
                --gray-900: #f7fafc;
            }
        }

        /* Animations */
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .slide-in {
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from { transform: translateX(-20px); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
    </style>
</head>
<body>
 
        
            <header class="main-header">
                <button class="btn btn-outline" id="menuToggle" onclick="toggleSidebar()" style="display: none;">
                    <i class="fas fa-bars"></i>
                </button>
                <div>
                    <h1 class="main-title">Quản Lý Sections Sản Phẩm</h1>
                    <p class="main-subtitle">Tạo và quản lý các phần hiển thị sản phẩm trên trang chủ</p>
                </div>
            </header>

            <div class="content-area">
                <!-- Dashboard Section -->
                <div id="dashboard-section" class="content-section">
                    <!-- Statistics -->
                    <div class="stats-grid">
                        <div class="stat-card fade-in">
                            <div class="stat-header">
                                <div>
                                    <span class="stat-number" id="sectionsCount">0</span>
                                    <div class="stat-label">Tổng Sections</div>
                                </div>
                                <div class="stat-icon primary">
                                    <i class="fas fa-layer-group"></i>
                                </div>
                            </div>
                            <div class="stat-change">+12% so với tháng trước</div>
                        </div>

                        <div class="stat-card fade-in">
                            <div class="stat-header">
                                <div>
                                    <span class="stat-number" id="activeSections">0</span>
                                    <div class="stat-label">Đang hoạt động</div>
                                </div>
                                <div class="stat-icon success">
                                    <i class="fas fa-play-circle"></i>
                                </div>
                            </div>
                            <div class="stat-change">+5% hoạt động</div>
                        </div>

                        <div class="stat-card fade-in">
                            <div class="stat-header">
                                <div>
                                    <span class="stat-number" id="productsCount">0</span>
                                    <div class="stat-label">Sản phẩm</div>
                                </div>
                                <div class="stat-icon warning">
                                    <i class="fas fa-box"></i>
                                </div>
                            </div>
                            <div class="stat-change">+8% sản phẩm mới</div>
                        </div>

                        <div class="stat-card fade-in">
                            <div class="stat-header">
                                <div>
                                    <span class="stat-number" id="categoriesCount">0</span>
                                    <div class="stat-label">Danh mục</div>
                                </div>
                                <div class="stat-icon info">
                                    <i class="fas fa-tags"></i>
                                </div>
                            </div>
                            <div class="stat-change">+3 danh mục mới</div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="card">
                        <div class="card-header">
                            <h2 class="card-title">
                                <i class="fas fa-bolt"></i>
                                Thao tác nhanh
                            </h2>
                        </div>
                        <div class="card-body">
                            <div class="btn-group">
                                <button class="btn btn-primary" onclick="showSection('create')">
                                    <i class="fas fa-plus"></i>
                                    Tạo Section mới
                                </button>
                                <button class="btn btn-success" onclick="syncSectionsToFrontend()">
                                    <i class="fas fa-sync"></i>
                                    Đồng bộ Frontend
                                </button>
                                <button class="btn btn-warning" onclick="createDefaultSections()">
                                    <i class="fas fa-magic"></i>
                                    Tạo mẫu
                                </button>
                                <button class="btn btn-outline" onclick="window.sectionsManager?.exportSections()">
                                    <i class="fas fa-download"></i>
                                    Xuất dữ liệu
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Create Section -->
                <div id="create-section" class="content-section" style="display: none;">
                    <div class="card">
                        <div class="card-header">
                            <h2 class="card-title">
                                <i class="fas fa-plus-circle"></i>
                                Tạo Section Mới
                            </h2>
                        </div>
                        <div class="card-body">
                            <form id="sectionForm">
                                <div class="form-grid">
                                    <div class="form-group">
                                        <label class="form-label required">Tiêu đề Section</label>
                                        <input type="text" class="form-input" id="sectionTitle" 
                                               placeholder="VD: Sản phẩm nổi bật" required>
                                        <div class="form-help">Tiêu đề chính sẽ hiển thị trên trang chủ</div>
                                    </div>

                                    <div class="form-group">
                                        <label class="form-label">Phụ đề</label>
                                        <input type="text" class="form-input" id="sectionSubtitle" 
                                               placeholder="VD: Những sản phẩm được yêu thích nhất">
                                        <div class="form-help">Mô tả ngắn về section này</div>
                                    </div>
                                </div>

                                <div class="form-grid">
                                    <div class="form-group">
                                        <label class="form-label required">Loại Section</label>
                                        <select class="form-select" id="sectionType" required>
                                            <option value="">Chọn loại section</option>
                                            <option value="featured">Sản phẩm nổi bật</option>
                                            <option value="trending">Xu hướng</option>
                                            <option value="new">Sản phẩm mới</option>
                                            <option value="special">Khuyến mãi đặc biệt</option>
                                            <option value="category">Theo danh mục</option>
                                            <option value="bestseller">Bán chạy</option>
                                            <option value="discount">Giảm giá</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label class="form-label">Danh mục (nếu chọn loại "Theo danh mục")</label>
                                        <select class="form-select" id="sectionCategory">
                                            <option value="">Chọn danh mục</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-grid">
                                    <div class="form-group">
                                        <label class="form-label">Số lượng sản phẩm hiển thị</label>
                                        <select class="form-select" id="productLimit">
                                            <option value="4">4 sản phẩm</option>
                                            <option value="6">6 sản phẩm</option>
                                            <option value="8" selected>8 sản phẩm</option>
                                            <option value="12">12 sản phẩm</option>
                                            <option value="16">16 sản phẩm</option>
                                            <option value="20">20 sản phẩm</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label class="form-label">Kiểu hiển thị</label>
                                        <select class="form-select" id="sectionLayout">
                                            <option value="grid">Lưới thông thường</option>
                                            <option value="special_grid">Lưới đặc biệt (cho khuyến mãi)</option>
                                            <option value="carousel">Carousel (cuộn ngang)</option>
                                            <option value="list">Danh sách</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-grid">
                                    <div class="form-group">
                                        <label class="form-label">Thứ tự hiển thị</label>
                                        <input type="number" class="form-input" id="sectionOrder" 
                                               value="1" min="1" max="99">
                                        <div class="form-help">Số thứ tự từ 1-99, càng nhỏ càng hiển thị trên</div>
                                    </div>

                                    <div class="form-group">
                                        <label class="form-label">Trạng thái</label>
                                        <select class="form-select" id="sectionStatus">
                                            <option value="active">Hoạt động</option>
                                            <option value="inactive">Tạm dừng</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Image Upload Section -->
                                <div class="form-group">
                                    <label class="form-label">Ảnh Section</label>
                                    <div class="image-upload-area" id="imageUploadArea">
                                        <input type="file" id="sectionImage" accept="image/*" style="display: none;">
                                        <div class="upload-content" id="uploadContent">
                                            <i class="fas fa-cloud-upload-alt upload-icon"></i>
                                            <div class="upload-text">Kéo thả hoặc nhấp để chọn ảnh</div>
                                            <div class="upload-subtext">Hỗ trợ: JPG, PNG, WEBP (tối đa 5MB)</div>
                                        </div>
                                        <img id="previewImage" class="preview-image" style="display: none;">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Hoặc nhập URL ảnh</label>
                                    <input type="url" class="form-input" id="sectionImageUrl" 
                                           placeholder="VD: https://example.com/image.jpg">
                                    <div class="form-help">URL ảnh từ nguồn bên ngoài</div>
                                </div>

                                <!-- Preview Section -->
                                <div class="preview-section" id="previewSection" style="display: none;">
                                    <div class="preview-title" id="previewTitle">Tiêu đề section</div>
                                    <div class="preview-subtitle" id="previewSubtitle">Phụ đề section</div>
                                    <img class="preview-image" id="previewSectionImage" src="" alt="" style="display: none;">
                                </div>

                                <div class="btn-group">
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-save"></i>
                                        Tạo Section
                                    </button>
                                    <button type="button" class="btn btn-primary" onclick="previewSection()">
                                        <i class="fas fa-eye"></i>
                                        Xem trước
                                    </button>
                                    <button type="reset" class="btn btn-outline">
                                        <i class="fas fa-redo"></i>
                                        Reset Form
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Manage Sections -->
                <div id="manage-section" class="content-section" style="display: none;">
                    <div class="card">
                        <div class="card-header">
                            <h2 class="card-title">
                                <i class="fas fa-list-alt"></i>
                                Danh Sách Sections
                            </h2>
                        </div>
                        <div class="card-body">
                            <div class="btn-group" style="margin-bottom: 1.5rem;">
                                <button class="btn btn-primary" onclick="loadSections()">
                                    <i class="fas fa-sync"></i>
                                    Tải lại
                                </button>
                                <button class="btn btn-success" onclick="syncSectionsToFrontend()">
                                    <i class="fas fa-upload"></i>
                                    Đồng bộ Frontend
                                </button>
                                <button class="btn btn-outline" onclick="window.sectionsManager?.activateAllSections()">
                                    <i class="fas fa-play"></i>
                                    Kích hoạt tất cả
                                </button>
                                <button class="btn btn-outline" onclick="window.sectionsManager?.deactivateAllSections()">
                                    <i class="fas fa-pause"></i>
                                    Tạm dừng tất cả
                                </button>
                            </div>

                            <div class="sections-list" id="sectionsList">
                                <div style="text-align: center; padding: 3rem; color: var(--gray-500);">
                                    <i class="fas fa-layer-group" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.5;"></i>
                                    <p>Chưa có section nào. Hãy tạo section đầu tiên!</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Settings Section -->
                <div id="settings-section" class="content-section" style="display: none;">
                    <div class="card">
                        <div class="card-header">
                            <h2 class="card-title">
                                <i class="fas fa-cog"></i>
                                Cài đặt và Công cụ
                            </h2>
                        </div>
                        <div class="card-body">
                            <div class="form-grid">
                                <div class="form-group">
                                    <label class="form-label">Tạo Sections mặc định</label>
                                    <button class="btn btn-warning" onclick="createDefaultSections()">
                                        <i class="fas fa-magic"></i>
                                        Tạo Sections mặc định
                                    </button>
                                    <div class="form-help">Tạo các sections mẫu để bắt đầu nhanh</div>
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Xóa tất cả dữ liệu</label>
                                    <button class="btn btn-danger" onclick="clearAllSections()">
                                        <i class="fas fa-trash"></i>
                                        Xóa tất cả Sections
                                    </button>
                                    <div class="form-help">⚠️ Hành động này không thể hoàn tác</div>
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Xuất/Nhập dữ liệu</label>
                                    <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
                                        <button class="btn btn-outline" onclick="window.sectionsManager?.exportSections()">
                                            <i class="fas fa-download"></i>
                                            Xuất JSON
                                        </button>
                                        <input type="file" id="importFile" accept=".json" style="display: none;" onchange="handleImport(event)">
                                        <button class="btn btn-outline" onclick="document.getElementById('importFile').click()">
                                            <i class="fas fa-upload"></i>
                                            Nhập JSON
                                        </button>
                                    </div>
                                    <div class="form-help">Sao lưu và khôi phục dữ liệu sections</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Toast Container -->
    <div id="toast" class="toast"></div>

    <script>
        // ===== GLOBAL VARIABLES =====
        let sections = [];
        let categories = [];
        let products = [];
        let images = [];
        let currentSectionImageUrl = '';
        let currentSection = 'dashboard';

        // ===== NAVIGATION =====
        function showSection(sectionName) {
            // Hide all sections
            document.querySelectorAll('.content-section').forEach(section => {
                section.style.display = 'none';
            });
            
            // Remove active class from all nav links
            document.querySelectorAll('.nav-link').forEach(link => {
                link.classList.remove('active');
            });
            
            // Show target section
            const targetSection = document.getElementById(sectionName + '-section');
            if (targetSection) {
                targetSection.style.display = 'block';
                targetSection.classList.add('fade-in');
            }
            
            // Add active class to corresponding nav link
            const targetLink = document.querySelector(`[onclick="showSection('${sectionName}')"]`);
            if (targetLink) {
                targetLink.classList.add('active');
            }
            
            currentSection = sectionName;
            
            // Load data if needed
            if (sectionName === 'manage') {
                loadSections();
            }
        }

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('mobileOverlay');
            
            sidebar.classList.toggle('open');
            overlay.classList.toggle('show');
        }

        // ===== UTILITY FUNCTIONS =====
        function showToast(message, type = 'info') {
            const toast = document.getElementById('toast');
            const icon = getToastIcon(type);
            
            toast.innerHTML = `
                <i class="${icon}"></i>
                <span>${message}</span>
            `;
            toast.className = `toast ${type} show`;
            
            setTimeout(() => {
                toast.classList.remove('show');
            }, 4000);
        }

        function getToastIcon(type) {
            const icons = {
                success: 'fas fa-check-circle',
                error: 'fas fa-exclamation-circle',
                warning: 'fas fa-exclamation-triangle',
                info: 'fas fa-info-circle'
            };
            return icons[type] || icons.info;
        }

        function generateId() {
            return Date.now() + Math.random().toString(36).substr(2, 9);
        }

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        function createPlaceholderImage(width = 80, height = 80, text = 'No Image') {
            // Tạo canvas để vẽ placeholder image
            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');
            
            canvas.width = width;
            canvas.height = height;
            
            // Background
            ctx.fillStyle = '#f1f5f9';
            ctx.fillRect(0, 0, width, height);
            
            // Border
            ctx.strokeStyle = '#e2e8f0';
            ctx.lineWidth = 2;
            ctx.strokeRect(0, 0, width, height);
            
            // Icon
            ctx.fillStyle = '#94a3b8';
            ctx.font = '24px Arial';
            ctx.textAlign = 'center';
            ctx.fillText('📷', width/2, height/2 - 5);
            
            // Text
            ctx.fillStyle = '#64748b';
            ctx.font = '10px Arial';
            ctx.fillText(text, width/2, height/2 + 15);
            
            return canvas.toDataURL();
        }

        // ===== DATA MANAGEMENT =====
        function loadData() {
            try {
                sections = JSON.parse(localStorage.getItem('adminSections') || '[]');
                categories = JSON.parse(localStorage.getItem('adminCategories') || '[]');
                products = JSON.parse(localStorage.getItem('adminProducts') || '[]');
                images = JSON.parse(localStorage.getItem('productImages') || '[]');
                
                console.log('✅ Đã tải dữ liệu:', {
                    sections: sections.length,
                    categories: categories.length,
                    products: products.length,
                    images: images.length
                });
                
                updateStatus();
                loadCategoriesToSelect();
                
            } catch (error) {
                console.error('❌ Lỗi tải dữ liệu:', error);
                showToast('Lỗi tải dữ liệu!', 'error');
            }
        }

        function saveData() {
            try {
                localStorage.setItem('adminSections', JSON.stringify(sections));
                localStorage.setItem('productImages', JSON.stringify(images));
                console.log('✅ Đã lưu dữ liệu');
            } catch (error) {
                console.error('❌ Lỗi lưu dữ liệu:', error);
                showToast('Lỗi lưu dữ liệu!', 'error');
            }
        }

        function updateStatus() {
            document.getElementById('sectionsCount').textContent = sections.length;
            document.getElementById('productsCount').textContent = products.length;
            document.getElementById('categoriesCount').textContent = categories.length;
            document.getElementById('activeSections').textContent = 
                sections.filter(s => s.status === 'active').length;
        }

        function loadCategoriesToSelect() {
            const select = document.getElementById('sectionCategory');
            select.innerHTML = '<option value="">Chọn danh mục</option>';
            
            categories.forEach(category => {
                const option = document.createElement('option');
                option.value = category.id;
                option.textContent = category.name;
                select.appendChild(option);
            });
        }

        // ===== IMAGE MANAGEMENT =====
        function addImage(imageData) {
            const newImage = {
                id: generateId(),
                url: imageData.url,
                name: imageData.name || `section-image-${Date.now()}`,
                size: imageData.size || 0,
                type: imageData.type || 'image',
                created_at: new Date().toISOString(),
                usage: 'section',
                section_id: null // Will be updated when section is created
            };

            images.push(newImage);
            saveData();
            console.log('✅ Đã thêm ảnh section:', newImage.url.substring(0, 50) + '...');
            return newImage;
        }

        function handleImageUpload(file) {
            return new Promise((resolve, reject) => {
                const maxSize = 5 * 1024 * 1024; // 5MB
                if (!file.type.startsWith('image/')) {
                    showToast(`${file.name} không phải là file ảnh!`, 'error');
                    reject(new Error('Not an image file'));
                    return;
                }

                if (file.size > maxSize) {
                    showToast(`${file.name} quá lớn (>5MB)!`, 'error');
                    reject(new Error('File too large'));
                    return;
                }

                console.log(`🖼️ Đang xử lý upload: ${file.name} (${formatFileSize(file.size)})`);

                const reader = new FileReader();
                reader.onload = function(e) {
                    const imageData = {
                        url: e.target.result,
                        name: file.name,
                        size: file.size,
                        type: file.type
                    };
                    
                    // Lưu URL hiện tại để tránh conflict
                    currentSectionImageUrl = imageData.url;
                    
                    console.log('✅ Đã tạo Data URL:', {
                        size: formatFileSize(file.size),
                        type: file.type,
                        urlLength: imageData.url.length
                    });
                    
                    const newImage = addImage(imageData);
                    
                    // Hiển thị preview ngay lập tức
                    showImagePreview(imageData.url);
                    
                    showToast(`Đã upload ảnh ${file.name}!`, 'success');
                    resolve(newImage);
                };
                reader.onerror = function() {
                    showToast(`Lỗi đọc file ${file.name}!`, 'error');
                    reject(new Error('File read error'));
                };
                reader.readAsDataURL(file);
            });
        }

        function isValidImageUrl(url) {
            try {
                new URL(url);
                return /\.(jpg|jpeg|png|gif|webp|svg)(\?.*)?$/i.test(url) || 
                       url.includes('picsum.photos') || 
                       url.includes('unsplash.com') ||
                       url.includes('images.unsplash.com') ||
                       url.startsWith('data:image/');
            } catch {
                return false;
            }
        }

        // ===== SECTION MANAGEMENT =====
        function addSection(sectionData) {
            // Đảm bảo URL ảnh được lưu chính xác
            let finalImageUrl = '';
            
            // Ưu tiên ảnh upload trước, sau đó mới đến URL
            if (currentSectionImageUrl) {
                finalImageUrl = currentSectionImageUrl;
                console.log('🖼️ Sử dụng ảnh upload:', finalImageUrl.substring(0, 50) + '...');
            } else if (sectionData.image_url && sectionData.image_url.trim()) {
                finalImageUrl = sectionData.image_url.trim();
                console.log('🖼️ Sử dụng URL ảnh:', finalImageUrl);
            }

            const newSection = {
                id: generateId(),
                title: sectionData.title,
                subtitle: sectionData.subtitle || '',
                type: sectionData.type,
                category_id: sectionData.category_id || null,
                product_limit: parseInt(sectionData.product_limit) || 8,
                layout: sectionData.layout || 'grid',
                order: parseInt(sectionData.order) || 1,
                status: sectionData.status || 'active',
                image_url: finalImageUrl,
                created_at: new Date().toISOString(),
                updated_at: new Date().toISOString()
            };

            // Cập nhật section_id cho ảnh nếu có
            if (finalImageUrl && finalImageUrl.startsWith('data:image/')) {
                const imageRecord = images.find(img => img.url === finalImageUrl);
                if (imageRecord) {
                    imageRecord.section_id = newSection.id;
                    console.log('🔗 Đã liên kết ảnh với section:', newSection.id);
                }
            }

            sections.push(newSection);
            sections.sort((a, b) => a.order - b.order);
            
            saveData();
            
            // Đồng bộ ngay tới frontend
            syncSectionsToFrontend();
            
            loadSections();
            updateStatus();
            
            showToast(`Đã tạo section "${newSection.title}" với ảnh!`, 'success');
            
            console.log('✅ Section mới:', {
                id: newSection.id,
                title: newSection.title,
                hasImage: !!newSection.image_url,
                imageUrl: newSection.image_url?.substring(0, 50) + '...'
            });
            
            return newSection;
        }

        function deleteSection(sectionId) {
            if (confirm('Bạn có chắc muốn xóa section này?')) {
                sections = sections.filter(s => s.id !== sectionId);
                saveData();
                loadSections();
                updateStatus();
                showToast('Đã xóa section!', 'warning');
            }
        }

        function toggleSectionStatus(sectionId) {
            const section = sections.find(s => s.id === sectionId);
            if (section) {
                section.status = section.status === 'active' ? 'inactive' : 'active';
                section.updated_at = new Date().toISOString();
                saveData();
                loadSections();
                updateStatus();
                
                const status = section.status === 'active' ? 'kích hoạt' : 'tạm dừng';
                showToast(`Đã ${status} section "${section.title}"!`, 'info');
            }
        }

        function editSection(sectionId) {
            const section = sections.find(s => s.id === sectionId);
            if (!section) return;

            // Switch to create section
            showSection('create');
            
            // Fill form with section data
            document.getElementById('sectionTitle').value = section.title;
            document.getElementById('sectionSubtitle').value = section.subtitle;
            document.getElementById('sectionType').value = section.type;
            document.getElementById('sectionCategory').value = section.category_id || '';
            document.getElementById('productLimit').value = section.product_limit;
            document.getElementById('sectionLayout').value = section.layout;
            document.getElementById('sectionOrder').value = section.order;
            document.getElementById('sectionStatus').value = section.status;
            document.getElementById('sectionImageUrl').value = section.image_url || '';

            // Update image preview
            if (section.image_url) {
                showImagePreview(section.image_url);
            }

            // Store editing ID
            document.getElementById('sectionForm').dataset.editingId = sectionId;
            
            showToast(`Đang chỉnh sửa section "${section.title}"`, 'info');
        }

        function loadSections() {
            const container = document.getElementById('sectionsList');
            
            if (sections.length === 0) {
                container.innerHTML = `
                    <div style="text-align: center; padding: 3rem; color: var(--gray-500);">
                        <i class="fas fa-layer-group" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.5;"></i>
                        <p>Chưa có section nào. Hãy tạo section đầu tiên!</p>
                    </div>
                `;
                return;
            }

            const sortedSections = [...sections].sort((a, b) => a.order - b.order);
            
            container.innerHTML = sortedSections.map(section => {
                const categoryName = section.category_id ? 
                    (categories.find(c => c.id == section.category_id)?.name || 'Danh mục không tồn tại') : 
                    'Tất cả';
                
                const typeLabels = {
                    'featured': 'Nổi bật',
                    'trending': 'Xu hướng', 
                    'new': 'Mới',
                    'special': 'Khuyến mãi',
                    'category': 'Danh mục',
                    'bestseller': 'Bán chạy',
                    'discount': 'Giảm giá'
                };

                // Sử dụng placeholder tự tạo thay vì via.placeholder.com
                const placeholderUrl = createPlaceholderImage(80, 80, 'No Image');
                const imageUrl = section.image_url || placeholderUrl;

                return `
                    <div class="section-item slide-in">
                        <img src="${imageUrl}" class="section-thumbnail" alt="${section.title}" 
                             onerror="this.src='${placeholderUrl}'">
                        
                        <div class="section-details">
                            <div class="section-name">${section.title}</div>
                            <div class="section-subtitle">${section.subtitle}</div>
                            
                            <div class="section-meta">
                                <div class="meta-item">
                                    <i class="fas fa-tag"></i>
                                    <span class="meta-value">${typeLabels[section.type] || section.type}</span>
                                </div>
                                <div class="meta-item">
                                    <i class="fas fa-folder"></i>
                                    <span class="meta-value">${categoryName}</span>
                                </div>
                                <div class="meta-item">
                                    <i class="fas fa-box"></i>
                                    <span class="meta-value">${section.product_limit} sản phẩm</span>
                                </div>
                                <div class="meta-item">
                                    <i class="fas fa-sort-numeric-up"></i>
                                    <span class="meta-value">Thứ tự ${section.order}</span>
                                </div>
                                <div class="meta-item">
                                    <span class="status-badge ${section.status}">
                                        <i class="fas fa-${section.status === 'active' ? 'play' : 'pause'}-circle"></i>
                                        ${section.status === 'active' ? 'Hoạt động' : 'Tạm dừng'}
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="section-actions">
                            <button class="action-btn toggle ${section.status}" 
                                    onclick="toggleSectionStatus('${section.id}')"
                                    title="${section.status === 'active' ? 'Tạm dừng' : 'Kích hoạt'}">
                                <i class="fas fa-${section.status === 'active' ? 'pause' : 'play'}"></i>
                            </button>
                            <button class="action-btn edit" onclick="editSection('${section.id}')" title="Chỉnh sửa">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="action-btn delete" onclick="deleteSection('${section.id}')" title="Xóa">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                `;
            }).join('');
        }

        // ===== PREVIEW FUNCTIONALITY =====
        function previewSection() {
            const title = document.getElementById('sectionTitle').value.trim();
            const subtitle = document.getElementById('sectionSubtitle').value.trim();
            const imageUrl = document.getElementById('sectionImageUrl').value.trim() || 
                           document.getElementById('previewImage').src;
            
            if (!title) {
                showToast('Vui lòng nhập tiêu đề để xem trước!', 'warning');
                return;
            }

            const previewSection = document.getElementById('previewSection');
            const previewTitle = document.getElementById('previewTitle');
            const previewSubtitle = document.getElementById('previewSubtitle');
            const previewImage = document.getElementById('previewSectionImage');
            
            previewTitle.textContent = title;
            previewSubtitle.textContent = subtitle || 'Không có phụ đề';
            
            if (imageUrl && imageUrl !== window.location.href) {
                previewImage.src = imageUrl;
                previewImage.style.display = 'block';
            } else {
                previewImage.style.display = 'none';
            }
            
            previewSection.style.display = 'block';
            previewSection.scrollIntoView({ behavior: 'smooth' });
        }

        // ===== IMAGE UPLOAD HANDLING =====
        function setupImageUpload() {
            const imageInput = document.getElementById('sectionImage');
            const uploadArea = document.getElementById('imageUploadArea');
            const uploadContent = document.getElementById('uploadContent');
            const previewImage = document.getElementById('previewImage');

            // Click to upload
            uploadArea.addEventListener('click', () => {
                imageInput.click();
            });

            // File input change
            imageInput.addEventListener('change', function(e) {
                if (e.target.files && e.target.files[0]) {
                    handleImageUpload(e.target.files[0]).then(imageData => {
                        showImagePreview(imageData.url);
                        document.getElementById('sectionImageUrl').value = '';
                    }).catch(() => {});
                }
            });

            // Drag and drop
            let dragCounter = 0;
            
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                uploadArea.addEventListener(eventName, preventDefaults, false);
            });

            ['dragenter', 'dragover'].forEach(eventName => {
                uploadArea.addEventListener(eventName, () => {
                    dragCounter++;
                    uploadArea.classList.add('dragover');
                }, false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                uploadArea.addEventListener(eventName, () => {
                    dragCounter--;
                    if (dragCounter === 0) {
                        uploadArea.classList.remove('dragover');
                    }
                }, false);
            });

            uploadArea.addEventListener('drop', (e) => {
                dragCounter = 0;
                const file = e.dataTransfer.files[0];
                if (file) {
                    handleImageUpload(file).then(imageData => {
                        showImagePreview(imageData.url);
                        document.getElementById('sectionImageUrl').value = '';
                    }).catch(() => {});
                }
            });

            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }

            // URL input handling
            document.getElementById('sectionImageUrl').addEventListener('input', function() {
                const url = this.value.trim();
                if (url && isValidImageUrl(url)) {
                    showImagePreview(url);
                    imageInput.value = '';
                    currentSectionImageUrl = url;
                } else if (!url) {
                    hideImagePreview();
                }
            });
        }
        function showImagePreview(url) {
            const uploadArea = document.getElementById('imageUploadArea');
            const uploadContent = document.getElementById('uploadContent');
            const previewImage = document.getElementById('previewImage');
            
            previewImage.src = url;
            previewImage.style.display = 'block';
            uploadContent.style.display = 'none';
            uploadArea.classList.add('has-image');
        }

        function hideImagePreview() {
            const uploadArea = document.getElementById('imageUploadArea');
            const uploadContent = document.getElementById('uploadContent');
            const previewImage = document.getElementById('previewImage');
            
            previewImage.style.display = 'none';
            uploadContent.style.display = 'block';
            uploadArea.classList.remove('has-image');
            currentSectionImageUrl = '';
        }

        // ===== FORM HANDLING =====
        function setupForm() {
            document.getElementById('sectionForm').addEventListener('submit', async function(e) {
                e.preventDefault();
                
                const submitBtn = this.querySelector('button[type="submit"]');
                submitBtn.classList.add('loading');
                submitBtn.disabled = true;
                
                try {
                    const fileInput = document.getElementById('sectionImage');
                    const imageUrlInput = document.getElementById('sectionImageUrl');
                    let imageUrl = imageUrlInput.value.trim();

                    // Handle file upload if present
                    if (fileInput.files && fileInput.files[0]) {
                        try {
                            const imageData = await handleImageUpload(fileInput.files[0]);
                            imageUrl = imageData.url;
                        } catch (error) {
                            return;
                        }
                    } else if (imageUrl && !isValidImageUrl(imageUrl)) {
                        showToast('URL ảnh không hợp lệ!', 'error');
                        return;
                    }

                    const formData = {
                        title: document.getElementById('sectionTitle').value.trim(),
                        subtitle: document.getElementById('sectionSubtitle').value.trim(),
                        type: document.getElementById('sectionType').value,
                        category_id: document.getElementById('sectionCategory').value || null,
                        product_limit: document.getElementById('productLimit').value,
                        layout: document.getElementById('sectionLayout').value,
                        order: document.getElementById('sectionOrder').value,
                        status: document.getElementById('sectionStatus').value,
                        image_url: imageUrl
                    };

                    // Validation
                    if (!formData.title) {
                        showToast('Vui lòng nhập tiêu đề section!', 'error');
                        return;
                    }

                    if (!formData.type) {
                        showToast('Vui lòng chọn loại section!', 'error');
                        return;
                    }

                    if (formData.type === 'category' && !formData.category_id) {
                        showToast('Vui lòng chọn danh mục cho loại section "Theo danh mục"!', 'error');
                        return;
                    }

                    // Check if editing existing section
                    const editingId = this.dataset.editingId;
                    if (editingId) {
                        // Update existing section
                        const sectionIndex = sections.findIndex(s => s.id === editingId);
                        if (sectionIndex !== -1) {
                            sections[sectionIndex] = {
                                ...sections[sectionIndex],
                                ...formData,
                                updated_at: new Date().toISOString()
                            };
                            
                            sections.sort((a, b) => a.order - b.order);
                            saveData();
                            loadSections();
                            updateStatus();
                            
                            showToast(`Đã cập nhật section "${formData.title}"!`, 'success');
                            
                            // Clear editing state
                            delete this.dataset.editingId;
                        }
                    } else {
                        // Add new section
                        addSection(formData);
                    }

                    // Reset form
                    this.reset();
                    hideImagePreview();
                    document.getElementById('previewSection').style.display = 'none';
                    currentSectionImageUrl = '';
                    
                    // Chuyển sang tab quản lý để xem kết quả
                    setTimeout(() => {
                        showSection('manage');
                    }, 1500);
                    
                } finally {
                    submitBtn.classList.remove('loading');
                    submitBtn.disabled = false;
                }
            });

            // Reset form button
            document.getElementById('sectionForm').addEventListener('reset', function() {
                hideImagePreview();
                document.getElementById('previewSection').style.display = 'none';
                delete this.dataset.editingId;
                currentSectionImageUrl = '';
            });
        }

        // ===== SYNC TO FRONTEND =====
        function syncSectionsToFrontend() {
            try {
                // Đồng bộ cả sections và images
                const syncData = {
                    sections: sections,
                    images: images.filter(img => img.usage === 'section'), // Chỉ ảnh sections
                    timestamp: new Date().toISOString()
                };

                // Lưu vào localStorage với key đặc biệt cho frontend
                localStorage.setItem('frontendSections', JSON.stringify(syncData.sections));
                localStorage.setItem('frontendSectionImages', JSON.stringify(syncData.images));

                // Dispatch event cho frontend
                window.dispatchEvent(new CustomEvent('adminSectionsSync', {
                    detail: syncData
                }));

                // PostMessage cho parent window
                if (window.parent !== window) {
                    window.parent.postMessage({
                        type: 'adminSectionsSync',
                        data: syncData
                    }, '*');
                }

                console.log('📡 Đã đồng bộ tới frontend:', {
                    sections: syncData.sections.length,
                    images: syncData.images.length,
                    activeSections: syncData.sections.filter(s => s.status === 'active').length
                });
                
                showToast(`Đã đồng bộ ${syncData.sections.length} sections và ${syncData.images.length} ảnh tới frontend!`, 'success');
                
            } catch (error) {
                console.error('❌ Lỗi đồng bộ:', error);
                showToast('Lỗi đồng bộ tới frontend!', 'error');
            }
        }

        // ===== DEFAULT SECTIONS =====
        function createDefaultSections() {
            if (sections.length > 0 && !confirm('Đã có sections. Bạn có muốn thêm sections mặc định?')) {
                return;
            }

            const defaultSections = [
                {
                    title: 'Khám phá ngày hôm nay',
                    subtitle: 'Những sản phẩm hot nhất được Coupang lựa chọn kỹ lưỡng hiện nay!',
                    type: 'featured',
                    product_limit: 8,
                    layout: 'grid',
                    order: 1,
                    status: 'active',
                    image_url: 'https://picsum.photos/300/200?random=1'
                },
                {
                    title: 'Khuyến mãi đặc biệt',
                    subtitle: 'Của người bán hôm nay',
                    type: 'special',
                    product_limit: 20,
                    layout: 'special_grid',
                    order: 2,
                    status: 'active',
                    image_url: 'https://picsum.photos/300/200?random=2'
                },
                {
                    title: 'Xu hướng thịnh hành',
                    subtitle: 'Sản phẩm được quan tâm nhiều nhất',
                    type: 'trending',
                    product_limit: 12,
                    layout: 'grid',
                    order: 3,
                    status: 'active',
                    image_url: 'https://picsum.photos/300/200?random=3'
                },
                {
                    title: 'Sản phẩm mới nhất',
                    subtitle: 'Vừa ra mắt, đáng chú ý nhất',
                    type: 'new',
                    product_limit: 12,
                    layout: 'grid',
                    order: 4,
                    status: 'active',
                    image_url: 'https://picsum.photos/300/200?random=4'
                }
            ];

            defaultSections.forEach(sectionData => {
                addSection(sectionData);
            });

            showToast(`Đã tạo ${defaultSections.length} sections mặc định!`, 'success');
        }

        function clearAllSections() {
            if (confirm('Bạn có chắc muốn xóa TẤT CẢ sections? Hành động này không thể hoàn tác!')) {
                sections = [];
                saveData();
                loadSections();
                updateStatus();
                showToast('Đã xóa tất cả sections!', 'warning');
            }
        }

        // ===== IMPORT/EXPORT =====
        function handleImport(event) {
            const file = event.target.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = function(e) {
                try {
                    const importedData = JSON.parse(e.target.result);
                    if (Array.isArray(importedData)) {
                        sections = importedData;
                        saveData();
                        loadSections();
                        updateStatus();
                        showToast(`Đã nhập ${sections.length} sections!`, 'success');
                    } else {
                        throw new Error('Dữ liệu không hợp lệ');
                    }
                } catch (error) {
                    showToast('Lỗi nhập dữ liệu! File không hợp lệ.', 'error');
                    console.error('Import error:', error);
                }
            };
            reader.readAsText(file);
            
            // Reset input
            event.target.value = '';
        }

        // ===== REAL-TIME PREVIEW =====
        function setupRealTimePreview() {
            ['sectionTitle', 'sectionSubtitle', 'sectionImageUrl'].forEach(id => {
                document.getElementById(id).addEventListener('input', function() {
                    const title = document.getElementById('sectionTitle').value.trim();
                    const subtitle = document.getElementById('sectionSubtitle').value.trim();
                    const imageUrl = document.getElementById('sectionImageUrl').value.trim() || 
                                   currentSectionImageUrl;
                    
                    if (title || subtitle || imageUrl) {
                        const previewSection = document.getElementById('previewSection');
                        const previewTitle = document.getElementById('previewTitle');
                        const previewSubtitle = document.getElementById('previewSubtitle');
                        const previewImage = document.getElementById('previewSectionImage');
                        
                        previewTitle.textContent = title || 'Tiêu đề section';
                        previewSubtitle.textContent = subtitle || 'Phụ đề section';
                        
                        if (imageUrl && imageUrl !== window.location.href) {
                            previewImage.src = imageUrl;
                            previewImage.style.display = 'block';
                        } else {
                            previewImage.style.display = 'none';
                        }
                        
                        previewSection.style.display = 'block';
                    } else {
                        document.getElementById('previewSection').style.display = 'none';
                    }
                });
            });
        }

        // ===== RESPONSIVE HANDLING =====
        function handleResponsive() {
            const menuToggle = document.getElementById('menuToggle');
            
    function checkScreenSize() {
  const sidebarToggle = document.getElementById('sidebarToggle');
  if (!sidebarToggle) return;          // thêm dòng này
  if (window.innerWidth < 992) {
    sidebarToggle.classList.add('collapsed');
  } else {
    sidebarToggle.classList.remove('collapsed');
  }
}

function handleResponsive() {
  checkScreenSize();
  window.addEventListener('resize', checkScreenSize);
}

document.addEventListener('DOMContentLoaded', handleResponsive);

            
            window.addEventListener('resize', checkScreenSize);
            checkScreenSize();
        }

        // ===== INITIALIZATION =====
        document.addEventListener('DOMContentLoaded', function() {
            console.log('🎨 Khởi tạo Sections Manager Professional...');
            
            // Initialize all modules
            loadData();
            setupForm();
            setupImageUpload();
            setupRealTimePreview();
            handleResponsive();
            
            // Load initial data
            loadSections();
            updateStatus();
            
            // Auto-refresh every 30 seconds
            setInterval(() => {
                loadData();
            }, 30000);
            
            showToast('Sections Manager đã sẵn sàng!', 'success');
        });

        // ===== EXPORT FUNCTIONS =====
        window.sectionsManager = {
            loadData,
            saveData,
            addSection,
            deleteSection,
            toggleSectionStatus,
            editSection,
            syncSectionsToFrontend,
            createDefaultSections,
            clearAllSections,
            previewSection,
            getSections: () => sections,
            getActiveSections: () => sections.filter(s => s.status === 'active'),
            activateAllSections: () => {
                sections.forEach(s => s.status = 'active');
                saveData();
                loadSections();
                updateStatus();
                showToast('Đã kích hoạt tất cả sections!', 'success');
            },
            deactivateAllSections: () => {
                sections.forEach(s => s.status = 'inactive');
                saveData();
                loadSections();
                updateStatus();
                showToast('Đã tạm dừng tất cả sections!', 'warning');
            },
            exportSections: () => {
                const dataStr = JSON.stringify(sections, null, 2);
                const blob = new Blob([dataStr], { type: 'application/json' });
                const url = URL.createObjectURL(blob);
                
                const a = document.createElement('a');
                a.href = url;
                a.download = `sections_${new Date().toISOString().split('T')[0]}.json`;
                a.click();
                
                URL.revokeObjectURL(url);
                showToast('Đã xuất dữ liệu sections!', 'success');
            },
            importSections: (jsonData) => {
                try {
                    const importedSections = JSON.parse(jsonData);
                    if (Array.isArray(importedSections)) {
                        sections = importedSections;
                        saveData();
                        loadSections();
                        updateStatus();
                        showToast(`Đã nhập ${sections.length} sections!`, 'success');
                    } else {
                        throw new Error('Dữ liệu không hợp lệ');
                    }
                } catch (error) {
                    showToast('Lỗi nhập dữ liệu!', 'error');
                    console.error('Import error:', error);
                }
            }
        };

        // ===== KEYBOARD SHORTCUTS =====
        document.addEventListener('keydown', function(e) {
            // Ctrl/Cmd + S: Save form
            if ((e.ctrlKey || e.metaKey) && e.key === 's') {
                e.preventDefault();
                if (currentSection === 'create') {
                    document.getElementById('sectionForm').dispatchEvent(new Event('submit'));
                }
            }
            
            // Ctrl/Cmd + R: Reset form
            if ((e.ctrlKey || e.metaKey) && e.key === 'r') {
                e.preventDefault();
                if (currentSection === 'create') {
                    document.getElementById('sectionForm').reset();
                    hideImagePreview();
                    document.getElementById('previewSection').style.display = 'none';
                    showToast('Đã reset form!', 'info');
                }
            }
            
            // Ctrl/Cmd + D: Create default sections
            if ((e.ctrlKey || e.metaKey) && e.key === 'd') {
                e.preventDefault();
                createDefaultSections();
            }
            
            // Ctrl/Cmd + 1-4: Navigate sections
            if ((e.ctrlKey || e.metaKey) && ['1', '2', '3', '4'].includes(e.key)) {
                e.preventDefault();
                const sections = ['dashboard', 'create', 'manage', 'settings'];
                const sectionIndex = parseInt(e.key) - 1;
                if (sections[sectionIndex]) {
                    showSection(sections[sectionIndex]);
                }
            }
            
            // ESC: Close mobile menu
            if (e.key === 'Escape') {
                const sidebar = document.getElementById('sidebar');
                const overlay = document.getElementById('mobileOverlay');
                if (sidebar.classList.contains('open')) {
                    sidebar.classList.remove('open');
                    overlay.classList.remove('show');
                }
            }
        });

        console.log('✅ Sections Manager Professional đã được tải hoàn toàn!');
        console.log('📋 Keyboard shortcuts:');
        console.log('- Ctrl/Cmd + S: Lưu form');
        console.log('- Ctrl/Cmd + R: Reset form');
        console.log('- Ctrl/Cmd + D: Tạo sections mặc định');
        console.log('- Ctrl/Cmd + 1-4: Chuyển đổi tab');
        console.log('- ESC: Đóng menu mobile');
    </script>
</body>
</html>