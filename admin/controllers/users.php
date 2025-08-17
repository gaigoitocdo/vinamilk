<?php
include __DIR__ . "/../../models/UserModel.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>

</head>   
<style>
    :root {
        --dark-bg: #1a1d29;
        --dark-surface: #242938;
        --dark-card: #2a2f42;
        --dark-border: #363b4d;
        --dark-hover: #3a4052;
        --text-primary: #ffffff;
        --text-secondary: #b8bcc8;
        --text-muted: #8b92a5;
        --text-accent: #e2e8f0;
        --primary-color: #6366f1;
        --primary-gradient: linear-gradient(135deg, #6366f1, #8b5cf6);
        --success-color: #10b981;
        --warning-color: #f59e0b;
        --danger-color: #ef4444;
        --border-radius: 8px;
        --border-radius-sm: 6px;
        --transition: all 0.2s ease;
        --shadow-lg-dark: 0 8px 16px rgba(0, 0, 0, 0.2);
        --shadow-dark: 0 4px 12px rgba(0, 0, 0, 0.3);
    }

    body {
        background: var(--dark-bg);
        color: var(--text-primary);
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        margin: 0;
        font-size: 14px;
    }

    .content-area {
        padding: 1rem;
        background: var(--dark-bg);
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 0.75rem;
        margin-bottom: 1rem;
    }

    .stat-card {
        background: var(--dark-surface);
        border: 1px solid var(--dark-border);
        border-radius: var(--border-radius-sm);
        padding: 0.75rem;
        text-align: center;
        transition: var(--transition);
        position: relative;
        overflow: hidden;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: var(--primary-gradient);
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-dark);
    }

    .stat-number {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 0.25rem;
    }

    .stat-label {
        color: var(--text-muted);
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .stat-icon {
        font-size: 1.5rem;
        color: var(--primary-color);
        margin-bottom: 0.5rem;
        opacity: 0.8;
    }

  

    .card {
        background: var(--dark-surface);
        border: 1px solid var(--dark-border);
        border-radius: var(--border-radius);
        box-shadow: var(--shadow-lg-dark);
        transition: var(--transition);
        margin-bottom: 1rem;
    }

    .card-header {
        background: var(--dark-card);
        border-bottom: 1px solid var(--dark-border);
        padding: 0.75rem 1rem;
        border-radius: var(--border-radius) var(--border-radius) 0 0;
    }

    .card-body {
        padding: 1rem;
    }

    .card-title {
        font-size: 1rem;
        font-weight: 600;
        color: var(--text-primary);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .form-grid {
        display: grid;
        grid-template-columns: 1fr 400px;
        gap: 1rem;
    }

    .form-group {
        margin-bottom: 1rem;
    }

    .form-group label {
        display: block;
        margin-bottom: 0.25rem;
        font-weight: 500;
        color: var(--text-accent);
        font-size: 0.8rem;
    }

    .form-control, .form-select {
        width: 100%;
        padding: 0.5rem 0.75rem;
      
        border: 1px solid var(--dark-border);
        color: var(--text-primary);
        border-radius: var(--border-radius-sm);
        font-size: 0.8rem;
        transition: var(--transition);
    }

    .form-control:focus, .form-select:focus {
        outline: none;
   
        box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.2);
       
    }

    .form-control::placeholder {
        color: var(--text-muted);
    }

    textarea.form-control {
        resize: vertical;
        min-height: 100px;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.25rem;
        padding: 0.5rem 1rem;
        border: none;
        border-radius: var(--border-radius-sm);
        font-size: 0.8rem;
        font-weight: 500;
        text-decoration: none;
        cursor: pointer;
        transition: var(--transition);
        white-space: nowrap;
    }

    .btn-primary {
        background: var(--primary-gradient);
        color: white;
        box-shadow: 0 2px 8px rgba(99, 102, 241, 0.3);
    }

    .btn-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.4);
    }

    .btn-primary:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }

    .select-all-btn {
        background: var(--dark-hover);
        color: var(--text-primary);
        border: none;
        padding: 0.25rem 0.5rem;
        border-radius: var(--border-radius-sm);
        font-size: 0.7rem;
        cursor: pointer;
        transition: var(--transition);
    }

    .select-all-btn:hover {
        background: var(--primary-color);
    }

    .preview-area {
        background: var(--dark-card);
        border-radius: var(--border-radius);
        padding: 1rem;
        border: 1px solid var(--dark-border);
        position: sticky;
        top: 1rem;
    }

    .preview-title {
        font-size: 0.9rem;
        font-weight: 600;
        margin-bottom: 0.75rem;
        color: var(--text-primary);
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }

    .notification-preview {
        background: var(--dark-surface);
        border-radius: var(--border-radius-sm);
        padding: 0.75rem;
        border-left: 3px solid var(--primary-color);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
    }

    .notification-header {
        display: flex;
        align-items: center;
        margin-bottom: 0.5rem;
    }

    .notification-icon {
        margin-right: 0.5rem;
        font-size: 1rem;
        color: var(--primary-color);
    }

    .notification-title {
        font-weight: 600;
        font-size: 0.85rem;
        color: var(--text-primary);
    }

    .notification-content {
        color: var(--text-secondary);
        line-height: 1.4;
        margin-bottom: 0.5rem;
        font-size: 0.8rem;
    }

    .notification-time {
        font-size: 0.7rem;
        color: var(--text-muted);
    }

    .user-selection {
        margin-bottom: 1rem;
    }

    .user-search-container {
        margin-bottom: 0.5rem;
        display: flex;
        gap: 0.5rem;
        align-items: center;
    }

    .user-search {
        flex: 1;
        padding: 0.5rem;
        border: 1px solid var(--dark-border);
        border-radius: var(--border-radius-sm);
        font-size: 0.75rem;
        background: var(--dark-card);
        color: var(--text-primary);
    }

    .user-controls {
        display: flex;
        gap: 0.5rem;
        margin-bottom: 0.5rem;
        align-items: center;
        flex-wrap: wrap;
    }

    .selected-count {
        font-size: 0.7rem;
        color: var(--text-secondary);
        padding: 0.25rem 0.5rem;
        background: var(--dark-card);
        border-radius: var(--border-radius-sm);
        border: 1px solid var(--dark-border);
    }

    .user-list-container {
        max-height: 200px;
        overflow-y: auto;
        border: 1px solid var(--dark-border);
        border-radius: var(--border-radius-sm);
        background: var(--dark-surface);
    }

    .user-checkbox {
        display: flex;
        align-items: center;
        padding: 0.5rem;
        border-bottom: 1px solid var(--dark-border);
        transition: var(--transition);
        cursor: pointer;
    }

    .user-checkbox:hover {
        background: rgba(99, 102, 241, 0.05);
    }

    .user-checkbox.selected {
        background: rgba(99, 102, 241, 0.1);
        border-left: 3px solid var(--primary-color);
    }

    .user-info {
        display: flex;
        align-items: center;
        flex-grow: 1;
        min-width: 0;
    }

    .user-avatar {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: var(--primary-gradient);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        margin-right: 0.5rem;
        font-size: 0.7rem;
        flex-shrink: 0;
    }

    .user-details {
        flex-grow: 1;
        min-width: 0;
    }

    .user-name {
        font-weight: 500;
        color: var(--text-primary);
        font-size: 0.75rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .user-email {
        font-size: 0.7rem;
        color: var(--text-muted);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .user-badges {
        display: flex;
        gap: 0.25rem;
        flex-shrink: 0;
    }

    .vip-badge {
        background: linear-gradient(135deg, #FFD700, #FFA500);
        color: #333;
        padding: 0.125rem 0.375rem;
        border-radius: 8px;
        font-size: 0.6rem;
        font-weight: 600;
    }

    .status-badge {
        padding: 0.125rem 0.375rem;
        border-radius: 8px;
        font-size: 0.6rem;
        font-weight: 500;
    }

    .status-active {
        background: rgba(16, 185, 129, 0.2);
        color: var(--success-color);
    }

    .status-inactive {
        background: rgba(239, 68, 68, 0.2);
        color: var(--danger-color);
    }

    .form-check {
        display: flex;
        align-items: center;
        gap: 0.25rem;
        margin-bottom: 0.75rem;
    }

    .form-check input[type="checkbox"] {
        width: 16px;
        height: 16px;
        accent-color: var(--primary-color);
    }

    .form-check label {
        margin: 0;
        color: var(--text-secondary);
        cursor: pointer;
        font-size: 0.8rem;
    }

    .char-counter {
        color: var(--text-muted);
        font-size: 0.7rem;
        float: right;
        margin-top: 0.25rem;
    }

    .loading {
        text-align: center;
        padding: 1rem;
        color: var(--text-muted);
        font-size: 0.8rem;
    }

    .no-users-found {
        text-align: center;
        padding: 1rem;
        color: var(--text-muted);
        font-style: italic;
        font-size: 0.8rem;
    }

    .api-status {
        position: fixed;
        top: 10px;
        right: 10px;
        padding: 8px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        z-index: 1000;
        background: #2a2f42;
        color: #f59e0b;
        border: 1px solid #f59e0b;
        cursor: pointer;
        transition: all 0.3s ease;
        max-width: 200px;
        text-align: center;
    }

    @media (max-width: 768px) {
        .content-area {
            padding: 0.75rem;
        }

        .form-grid {
            grid-template-columns: 1fr;
            gap: 0.75rem;
        }

        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 0.5rem;
        }

        .preview-area {
            position: static;
        }

        .api-status {
            position: relative;
            top: auto;
            right: auto;
            margin: 10px 0;
            display: block;
        }
    }
</style>
    <style>
        .card {


            border: 1px solid var(--dark-border);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-dark);
            margin-bottom: 1.5rem;
            transition: var(--transition-slow);
        }

        .card:hover {
            transform: translateY(-4px);
            box-shadow: 0 15px 25px rgba(0, 0, 0, 0.4);
        }

        .card-header {
            background: var(--dark-surface);
            padding: 1rem;
            border-bottom: 1px solid var(--dark-border);
        }

        .card-body {
            padding: 1.5rem;
            background: var(--dark-card);
            color: var(--text-primary);
        }

        /* Table Styles */
        .table-responsive {
            border-radius: var(--border-radius);
            overflow: hidden;
        }

        .table {
    
            background: var(--dark-card);
            border-color: var(--dark-border);
        }

        .table thead th {
            background: var(--primary-gradient);
            color: var(--text-primary);
            border-bottom: 2px solid var(--dark-border);
            padding: 1rem;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .table tbody tr {
            transition: var(--transition);
            background: var(--dark-card);
        }

        .table tbody tr:hover {
  
            transform: translateX(2px);
        }

        .table td {
            border-bottom: 1px solid var(--dark-border);
            padding: 1rem;
            color: var(--text-primary);
        }

        /* Badge Styles */
        .badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            box-shadow: var(--shadow-light);
            transition: var(--transition);
        }

        .badge.bg-gray-500 {
            background: var(--dark-hover);
            color: var(--text-primary);
        }

        .badge.bg-red-600 {
            background: var(--danger-color);
            color: white;
        }

        .badge.bg-green-600 {
            background: var(--success-color);
            color: white;
        }

        .badge.bg-yellow-500 {
            background: var(--warning-color);
            color: white;
        }

        .badge:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }

        /* Button Styles */
        .btn {
            position: relative;
            overflow: hidden;
            border: none;
            border-radius: var(--border-radius-sm);
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            transition: var(--transition);
        }

        .btn.bg-blue-600 {
            background: var(--primary-color);
            color: white;
        }

        .btn.bg-blue-600:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
            box-shadow: 0 8px 15px rgba(139, 92, 246, 0.3);
        }

        .btn.bg-red-600 {
            background: var(--danger-color);
            color: white;
        }

        .btn.bg-red-600:hover {
            background: #d32f2f;
            transform: translateY(-1px);
            box-shadow: 0 8px 15px rgba(239, 68, 68, 0.3);
        }

        .btn.bg-gray-500 {
            background: var(--dark-hover);
            color: var(--text-primary);
        }

        .btn.bg-gray-500:hover {
            background: #5a6578;
            transform: translateY(-1px);
            box-shadow: 0 8px 15px rgba(107, 114, 128, 0.3);
        }

        .btn::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            transition: width 0.6s ease, height 0.6s ease;
        }

        .btn:hover::after {
            width: 300px;
            height: 300px;
        }

        /* Modal Styles */
        .modal-content {
            background: var(--dark-card);
            border: 1px solid var(--dark-border);
            border-radius: var(--border-radius);
            color: var(--text-primary);
        }

        .modal-header {
            background: var(--primary-gradient);
            color: white;
            border-bottom: 1px solid var(--dark-border);
            padding: 1rem;
        }

        .modal-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-primary);
        }

        .modal-body {
            background: var(--dark-surface);
            padding: 1.5rem;
        }

        .modal-footer {
            background: var(--dark-surface);
            border-top: 1px solid var(--dark-border);
            padding: 1rem;
        }

        .close, .btn-close {
            color: white;
            opacity: 0.8;
            transition: var(--transition);
        }

        .close:hover, .btn-close:hover {
            opacity: 1;
            transform: rotate(90deg);
        }

        /* Form Styles */
        .form-control, .form-select {
            background: var(--dark-surface);
            border: 1px solid var(--dark-border);
            color: var(--text-primary);
            border-radius: var(--border-radius-sm);
            padding: 0.75rem;
            transition: var(--transition);
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(139, 92, 246, 0.25);
            background: var(--dark-surface);
            color: var(--text-primary);
        }

        .form-label {
            color: var(--text-secondary);
            font-weight: 500;
            margin-bottom: 0.5rem;
        }

        /* Animations */
        .fade-in {
            animation: fadeIn 0.5s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Responsive Design */
        @media (max-width: 1200px) {
            .admin-sidebar {
                width: 240px;
            }

            .admin-main {
                margin-left: 240px;
            }
        }

        @media (max-width: 768px) {
            .admin-sidebar {
                transform: translateX(-100%);
                width: 280px;
            }

            .admin-sidebar.open {
                transform: translateX(0);
            }

            .admin-main {
                margin-left: 0;
            }

            .table-responsive {
                font-size: 0.9rem;
            }

            .table td, .table th {
                padding: 0.75rem;
            }
        }

        @media (max-width: 576px) {
            .card-body {
                padding: 1rem;
            }

            .btn {
                padding: 0.5rem 1rem;
                font-size: 0.85rem;
            }

            .modal-dialog {
                margin: 0.5rem;
            }
        }

        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: var(--dark-card);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb {
            background: var(--primary-gradient);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--primary-dark);
        }

        /* Accessibility */
        .btn:focus, .nav-item:focus, .form-control:focus, .form-select:focus {
            outline: 2px solid var(--primary-color);
            outline-offset: 2px;
        }

        .sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            border: 0;
        }
        /* Card Styles */
        .card {
            background: var(--dark-card);
            border: 1px solid var(--dark-border);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-dark);
            margin-bottom: 1.5rem;
            transition: var(--transition-slow);
        }

        .card:hover {
            transform: translateY(-4px);
            box-shadow: 0 15px 25px rgba(0, 0, 0, 0.4);
        }

        .card-header {
            background: var(--dark-surface);
            padding: 1rem;
            border-bottom: 1px solid var(--dark-border);
        }

        .card-body {
            padding: 1.5rem;
            background: var(--dark-card);
            color: var(--text-primary);
        }

        /* Table Styles */
        .table-responsive {
            border-radius: var(--border-radius);
            overflow: hidden;
        }

        .table {
        
            background: var(--dark-card);
            border-color: var(--dark-border);
        }

        .table thead th {
            background: var(--primary-gradient);
            color: var(--text-primary);
            border-bottom: 2px solid var(--dark-border);
            padding: 1rem;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .table tbody tr {
            transition: var(--transition);
            background: var(--dark-card);
        }

        .table tbody tr:hover {
            background: rgba(139, 92, 246, 0.1);
            transform: translateX(2px);
        }

        .table td {
            border-bottom: 1px solid var(--dark-border);
            padding: 1rem;
            color: var(--text-primary);
        }

        /* Badge Styles */
        .badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            box-shadow: var(--shadow-light);
            transition: var(--transition);
        }

        .badge.bg-gray-500 {
            background: #000000;
            color: var(--text-primary);
        }

        .badge.bg-red-600 {
            background: var(--danger-color);
            color: white;
        }

        .badge.bg-green-600 {
            background: var(--success-color);
            color: white;
        }

        .badge.bg-yellow-500 {
            background: var(--warning-color);
            color: white;
        }

        .badge:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }

        /* Button Styles */
        .btn {
            position: relative;
            overflow: hidden;
            border: none;
            border-radius: var(--border-radius-sm);
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            transition: var(--transition);
        }

        .btn.bg-blue-600 {
            background: var(--primary-color);
            color: white;
        }

        .btn.bg-blue-600:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
            box-shadow: 0 8px 15px rgba(139, 92, 246, 0.3);
        }

        .btn.bg-red-600 {
            background: var(--danger-color);
            color: white;
        }

        .btn.bg-red-600:hover {
            background: #d32f2f;
            transform: translateY(-1px);
            box-shadow: 0 8px 15px rgba(239, 68, 68, 0.3);
        }

        .btn.bg-gray-500 {
            background: var(--dark-hover);
            color: var(--text-primary);
        }

        .btn.bg-gray-500:hover {
            background: #5a6578;
            transform: translateY(-1px);
            box-shadow: 0 8px 15px rgba(107, 114, 128, 0.3);
        }

        .btn::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            transition: width 0.6s ease, height 0.6s ease;
        }

        .btn:hover::after {
            width: 300px;
            height: 300px;
        }

        /* Modal Styles */
        .modal-content {
            background: var(--dark-card);
            border: 1px solid var(--dark-border);
            border-radius: var(--border-radius);
            color: var(--text-primary);
        }

        .modal-header {
            background: var(--primary-gradient);
            color: white;
            border-bottom: 1px solid var(--dark-border);
            padding: 1rem;
        }

        .modal-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-primary);
        }

        .modal-body {
            background: var(--dark-surface);
            padding: 1.5rem;
        }

        .modal-footer {
            background: var(--dark-surface);
            border-top: 1px solid var(--dark-border);
            padding: 1rem;
        }

        .close, .btn-close {
            color: white;
            opacity: 0.8;
            transition: var(--transition);
        }

        .close:hover, .btn-close:hover {
            opacity: 1;
            transform: rotate(90deg);
        }

        /* Form Styles */
        .form-control, .form-select {
            background: var(--dark-surface);
            border: 1px solid var(--dark-border);
            color: var(--text-primary);
            border-radius: var(--border-radius-sm);
            padding: 0.75rem;
            transition: var(--transition);
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(139, 92, 246, 0.25);
            background: var(--dark-surface);
            color: var(--text-primary);
        }

        .form-label {
            color: var(--text-secondary);
            font-weight: 500;
            margin-bottom: 0.5rem;
        }

        /* Animations */
        .fade-in {
            animation: fadeIn 0.5s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Responsive Design */
        @media (max-width: 1200px) {
            .admin-sidebar {
                width: 240px;
            }

            .admin-main {
                margin-left: 240px;
            }
        }

        @media (max-width: 768px) {
            .admin-sidebar {
                transform: translateX(-100%);
                width: 280px;
            }

            .admin-sidebar.open {
                transform: translateX(0);
            }

            .admin-main {
                margin-left: 0;
            }

            .table-responsive {
                font-size: 0.9rem;
            }

            .table td, .table th {
                padding: 0.75rem;
            }
        }

        @media (max-width: 576px) {
            .card-body {
                padding: 1rem;
            }

            .btn {
                padding: 0.5rem 1rem;
                font-size: 0.85rem;
            }

            .modal-dialog {
                margin: 0.5rem;
            }
        }

        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: var(--dark-card);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb {
            background: var(--primary-gradient);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--primary-dark);
        }

        /* Accessibility */
        .btn:focus, .nav-item:focus, .form-control:focus, .form-select:focus {
            outline: 2px solid var(--primary-color);
            outline-offset: 2px;
        }

        .sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            border: 0;
        }
            </style>
<body class="bg-gray-100 text-gray-900 font-sans">
    <div class="card shadow-md mb-4">
        <div class="card-header py-3" style="">
            <button type="button" id="btn-create" class="btn bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded-md text-sm transition duration-300" style="background-color: #000000 !important; color: #000000 !important;">Tạo user</button>
        </div>
        <div class="card-body p-4 text-gray-900">
            <div class="table-responsive">
                <table class="table w-full border-collapse" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th class="p-2 text-left border-b border-gray-300 text-sm" style="background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #a855f7 100%); color: #000;">ID</th>
                            <th class="p-2 text-left border-b border-gray-300 text-sm" style="background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #a855f7 100%); color: #ffffff;">Username</th>
                            <th class="p-2 text-left border-b border-gray-300 text-sm" style="background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #a855f7 100%); color: #ffffff;">Giới tính</th>
                            <th class="p-2 text-left border-b border-gray-300 text-sm" style="background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #a855f7 100%); color: #ffffff;">Role</th>
                            <th class="p-2 text-left border-b border-gray-300 text-sm" style="background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #a855f7 100%); color: #ffffff;">Trạng thái</th>
                            <th class="p-2 text-left border-b border-gray-300 text-sm" style="background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #a855f7 100%); color: #ffffff;">Số dư</th>
                            <th class="p-2 text-left border-b border-gray-300 text-sm" style="background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #a855f7 100%); color: #ffffff;">Tín nhiệm</th>
                            <th class="p-2 text-left border-b border-gray-300 text-sm" style="background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #a855f7 100%); color: #ffffff;">Hành động</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Create Modal -->
    <div class="modal fade" id="createModel" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog max-w-3xl" role="document">
            <div class="modal-content">
                <div class="modal-header bg-gradient-to-br from-blue-500 to-blue-700 text-white p-4">
                    <h5 class="modal-title text-gray-900 text-lg font-semibold">Tạo user</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body p-4 text-gray-900">
                    <form id="form-create">
                        <!-- Form fields unchanged -->
                        <button type="submit" class="btn w-full bg-blue-600 hover:bg-blue-700 text-white p-2 rounded-md text-sm transition duration-300" style="background-color: #2563eb !important; color: #ffffff !important;">Create</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Edit Money -->
    <div class="modal fade" id="modalEditMoney" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog max-w-md" role="document">
            <div class="modal-content">
                <div class="modal-header bg-gradient-to-br from-blue-500 to-blue-700 text-white p-4">
                    <h5 class="modal-title text-gray-900 text-lg font-semibold">Chỉnh sửa số dư</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body p-4 text-gray-900">
                    <!-- Form fields unchanged -->
                </div>
                <div class="modal-footer p-4">
                    <button type="button" class="btn bg-gray-500 hover:bg-gray-600 text-white px-3 py-1 rounded-md text-sm transition duration-300" style="background-color: #6b7280 !important; color: #ffffff !important;">Close</button>
                    <button type="button" id="btn-editmoney-save" class="btn bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded-md text-sm transition duration-300" style="background-color: #2563eb !important; color: #ffffff !important;">Save</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModel" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog max-w-3xl" role="document">
            <div class="modal-content">
                <div class="modal-header bg-gradient-to-br from-blue-500 to-blue-700 text-white p-4">
                    <h5 class="modal-title text-gray-900 text-lg font-semibold" id="modal-edit-title"></h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body p-4 text-gray-900">
                    <form id="form-edit">
                        <div class="form-group mb-4">
                            <label for="" class="text-gray-600 font-medium">Mật khẩu người dùng</label>
                            <input type="text" class="form-control w-full border border-gray-300 text-gray-900 rounded-md p-2 text-sm focus:border-blue-500 focus:ring focus:ring-blue-200" name="password" aria-describedby="helpId" placeholder="">
                        </div>
                        <div class="form-group mb-4">
                            <label for="" class="text-gray-600 font-medium">Số dư</label>
                            <input type="number" class="form-control w-full border border-gray-300 text-gray-900 rounded-md p-2 text-sm focus:border-blue-500 focus:ring focus:ring-blue-200" name="money" step="0.01" aria-describedby="helpId" value="0" placeholder="">
                        </div>
                        <div class="form-group mb-4">
                            <label for="" class="text-gray-600 font-medium">Role</label>
                            <select class="form-control w-full border border-gray-300 text-gray-900 rounded-md p-2 text-sm focus:border-blue-500 focus:ring focus:ring-blue-200" name="type">
                                <option value="0">Người dùng</option>
                                <option value="3">Quản trị viên</option>
                            </select>
                        </div>
                        <div class="form-group mb-4">
                            <label for="" class="text-gray-600 font-medium">Điểm tín dụng</label>
                            <input type="number" class="form-control w-full border border-gray-300 text-gray-900 rounded-md p-2 text-sm focus:border-blue-500 focus:ring focus:ring-blue-200" name="credit" step="0.01" aria-describedby="helpId" value="" placeholder="">
                        </div>
                        <div class="form-group mb-4">
                            <label for="" class="text-gray-600 font-medium">VIP</label>
                            <input type="number" class="form-control w-full border border-gray-300 text-gray-900 rounded-md p-2 text-sm focus:border-blue-500 focus:ring focus:ring-blue-200" name="vip" aria-describedby="helpId" value="" placeholder="">
                        </div>
                        <div class="form-group mb-4">
                            <label for="" class="text-gray-600 font-medium">Tổng số tiền đã nạp</label>
                            <input type="number" class="form-control w-full border border-gray-300 text-gray-900 rounded-md p-2 text-sm focus:border-blue-500 focus:ring focus:ring-blue-200" name="total_topup" aria-describedby="helpId" value="" placeholder="">
                        </div>
                        <div class="form-group mb-4">
                            <label for="" class="text-gray-600 font-medium">Số lần rút tiền của Người dùng</label>
                            <input type="number" class="form-control w-full border border-gray-300 text-gray-900 rounded-md p-2 text-sm focus:border-blue-500 focus:ring focus:ring-blue-200" name="num" aria-describedby="helpId" value="" placeholder="">
                        </div>
              
                        <div class="form-group mb-4">
                            <label for="" class="text-gray-600 font-medium">Số tiền rút tối đa của Người dùng</label>
                            <input type="number" class="form-control w-full border border-gray-300 text-gray-900 rounded-md p-2 text-sm focus:border-blue-500 focus:ring focus:ring-blue-200" name="max" aria-describedby="helpId" value="" placeholder="">
                        </div>
                        <button type="submit" class="btn w-full bg-blue-600 hover:bg-blue-700 text-white p-2 rounded-md text-sm transition duration-300" style="background-color: #2563eb !important; color: #ffffff !important;">Edit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php admin_footer(); ?>

    <script>
        var current_edit_id = -1;

        function render_gender(type) {
            if (type == 2) return "Nữ";
            if (type == 1) return "Nam";
            return "Không rõ";
        }

        function render_role(type) {
            if (type == 0) return `<span class="badge background: #000000; text-white px-2 py-1 rounded-md text-xs">Người dùng</span>`;
            if (type == 3) return `<span class="badge bg-red-600 text-white px-2 py-1 rounded-md text-xs">Quản trị viên</span>`;
            return "Không rõ";
        }

        function render_status(type, id) {
            if (type == 0) return `<a href="#" id="btn-fast-togglestatus" data-type=${type} data-id='${id}' class="badge bg-red-600 text-white px-2 py-1 rounded-md text-xs">Khoá</a>`;
            if (type == 1) return `<a href="#" id="btn-fast-togglestatus" data-type=${type} data-id='${id}' class="badge bg-green-600 text-white px-2 py-1 rounded-md text-xs">Bình thường</a>`;
            return "Không rõ";
        }

        $(document).ready(function() {
            const dbtable = $("#dataTable").DataTable({
                ajax: {
                    url: "<?= route('ajax/users-db.php') ?>",
                    dataSrc: 'data'
                },
                'processing': true,
                'serverSide': true,
                'serverMethod': 'post',
                columns: [
                    { data: 'id' },
                    { data: 'username' },
                    { 
                        data: 'gender',
                        render: (data, type, row) => render_gender(data)
                    },
                    { 
                        data: 'type',
                        render: (data, type, row) => render_role(data)
                    },
                    { 
                        data: 'status',
                        render: (data, type, row) => render_status(data, row["id"])
                    },
                    { 
                        data: 'money',
                        render: (data, type, row) => `<a href="#" id="btn-fast-addmoney" data-id='${row["id"]}' class="badge bg-green-600 text-white px-2 py-1 rounded-md text-xs">${data} vnd</a>`
                    },
                    { 
                        data: 'credit',
                        render: (data, type, row) => `<a href="#" class="badge bg-yellow-500 text-white px-2 py-1 rounded-md text-xs">${data}</a>`
                    },
                    {
                        data: 'id',
                        render: (data, type, row) => `<div class="btn-group flex gap-1" role="group" aria-label="">
                                        <button type="button" id="btn-edit" data-id='${data}' class="btn bg-blue-600 hover:bg-blue-700 text-white px-2 py-1 rounded-md text-xs transition duration-300" style="background-color: #2563eb !important; color: #ffffff !important;">Chỉnh sửa</button>
                                        <button type="button" id="btn-delete" data-id='${data}' class="btn bg-red-600 hover:bg-red-700 text-white px-2 py-1 rounded-md text-xs transition duration-300" style="background-color: #dc2626 !important; color: #ffffff !important;">Xoá</button>
                                    </div>`
                    }
                ]
            });

            $("#form-create").on("submit", function(e) {
                e.preventDefault();
                var data = {};
                data.action_type = "add_user";
                $(this).serializeArray().forEach(element => {
                    data[element.name] = element.value;
                });
                api({
                    data,
                    onSuccess: function(response) {
                        if (response.success) {
                            toastr.success("Thêm mới thành công");
                            $("#createModel").modal("hide");
                            dbtable.ajax.reload(null, false);
                        } else {
                            toastr.error("Thêm mới thất bại: " + (response.message || "Lỗi không xác định"));
                        }
                    },
                    onError: function(error) {
                        toastr.error("Lỗi hệ thống khi thêm user: " + error.message);
                    }
                });
            });

            $("#form-edit").on("submit", function(e) {
                e.preventDefault();
                if (current_edit_id === -1) {
                    toastr.error("Không tìm thấy ID người dùng để chỉnh sửa");
                    return;
                }
                var data = {};
                data.action_type = "edit_user";
                data.id = current_edit_id;
                $(this).serializeArray().forEach(element => {
                    data[element.name] = element.value;
                });
                api({
                    data,
                    onSuccess: function(response) {
                        if (response.success) {
                            toastr.success("Chỉnh sửa thành công");
                            $("#editModel").modal("hide");
                            dbtable.ajax.reload(null, false);
                        } else {
                            toastr.error("Chỉnh sửa thất bại: " + (response.message || "Lỗi không xác định"));
                        }
                    },
                    onError: function(error) {
                        toastr.error("Lỗi hệ thống khi chỉnh sửa user: " + error.message);
                    }
                });
            });

            $(document).delegate("a[id='btn-fast-addmoney']", "click", function() {
                let id = $(this).data("id");
                current_edit_id = id;
                $("#modalEditMoney").modal("show");
            });

            $(document).delegate("a[id='btn-fast-togglestatus']", "click", function() {
                let id = $(this).data("id");
                let type = $(this).data("type");
                api({
                    data: {
                        action_type: "update_user_status",
                        id,
                        status: type == 0 ? 1 : 0
                    },
                    onSuccess: function(response) {
                        if (response.success) {
                            toastr.success("Cập nhật trạng thái thành công");
                            dbtable.ajax.reload(null, false);
                        } else {
                            toastr.error("Cập nhật trạng thái thất bại: " + (response.message || "Lỗi không xác định"));
                        }
                    },
                    onError: function(error) {
                        toastr.error("Lỗi hệ thống khi cập nhật trạng thái: " + error.message);
                    }
                });
            });

            $("#btn-editmoney-save").on("click", function() {
                let num = $("#txt-editmoney-money").val();
                let charge_type = $("#select-editmoney-type").val();
                let desc = $("#txt-editmoney-desc").val();
                api({
                    data: {
                        action_type: "update_user_money",
                        id: current_edit_id,
                        num,
                        charge_type,
                        desc
                    },
                    onSuccess: function(response) {
                        if (response.success) {
                            toastr.success("Chỉnh sửa số dư thành công");
                            $("#modalEditMoney").modal("hide");
                            dbtable.ajax.reload(null, false);
                        } else {
                            toastr.error("Chỉnh sửa số dư thất bại: " + (response.message || "Lỗi không xác định"));
                        }
                    },
                    onError: function(error) {
                        toastr.error("Lỗi hệ thống khi chỉnh sửa số dư: " + error.message);
                    }
                });
            });

            $(document).delegate("button[id='btn-delete']", "click", function() {
                let id = $(this).data("id");
                if (confirm("Bạn có muốn xoá user này: " + id)) {
                    api({
                        data: {
                            action_type: "delete_user",
                            id
                        },
                        onSuccess: function(response) {
                            if (response.success) {
                                toastr.success("Xoá user thành công");
                                dbtable.ajax.reload(null, false);
                            } else {
                                toastr.error("Xoá user thất bại: " + (response.message || "Lỗi không xác định"));
                            }
                        },
                        onError: function(error) {
                            toastr.error("Lỗi hệ thống khi xoá user: " + error.message);
                        }
                    });
                }
            });

            $(document).delegate("button[id='btn-edit']", "click", function() {
                let id = $(this).data("id");
                api({
                    data: {
                        action_type: "get_user",
                        id
                    },
                    onSuccess: function(response) {
                        if (response.success && response.data) {
                            current_edit_id = id;
                            let { data } = response;
                            $("#modal-edit-title").html("Edit: " + (data.username || "Unknown"));
                            $("#form-edit").trigger("reset");
                            $('#form-edit').find('input').each(function() {
                                var name = $(this).attr('name');
                                if (data[name] && name !== 'password') {
                                    $(this).val(data[name]);
                                }
                            });
                            $('#form-edit').find('select').each(function() {
                                var name = $(this).attr('name');
                                if (data[name]) {
                                    $(this).val(data[name]);
                                }
                            });
                            $("#editModel").modal('show');
                        } else {
                            toastr.error("Không thể tải dữ liệu user: " + (response.message || "Lỗi không xác định"));
                        }
                    },
                    onError: function(error) {
                        toastr.error("Lỗi hệ thống khi tải dữ liệu user: " + error.message);
                    }
                });
            });
        });
    </script>
 
</body>
</html>