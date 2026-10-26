<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistem Absensi Mahasiswa')</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --primary-light: #818cf8;
            --primary-50: #eef2ff;
            --secondary: #0ea5e9;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --info: #06b6d4;
            --dark: #0f172a;
            --dark-700: #1e293b;
            --dark-600: #334155;
            --dark-500: #475569;
            --gray: #64748b;
            --gray-light: #94a3b8;
            --light: #f1f5f9;
            --white: #ffffff;
            --sidebar-width: 260px;
            --border-radius: 12px;
            --shadow: 0 1px 3px rgba(0,0,0,0.08), 0 1px 2px rgba(0,0,0,0.06);
            --shadow-md: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -1px rgba(0,0,0,0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0,0,0,0.1), 0 4px 6px -2px rgba(0,0,0,0.05);
            --shadow-xl: 0 20px 25px -5px rgba(0,0,0,0.1), 0 10px 10px -5px rgba(0,0,0,0.04);
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--light);
            color: var(--dark);
            min-height: 100vh;
            display: flex;
        }

        /* ========== SIDEBAR ========== */
        .sidebar {
            width: var(--sidebar-width);
            background: linear-gradient(180deg, var(--dark) 0%, var(--dark-700) 100%);
            min-height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            z-index: 100;
            display: flex;
            flex-direction: column;
            transition: var(--transition);
            box-shadow: 4px 0 20px rgba(0,0,0,0.15);
        }

        .sidebar-brand {
            padding: 20px 20px 16px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }

        .sidebar-brand-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .sidebar-close-btn {
            display: none;
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.12);
            color: var(--white);
            width: 32px;
            height: 32px;
            border-radius: 8px;
            cursor: pointer;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            transition: var(--transition);
        }

        .sidebar-close-btn:hover {
            background: rgba(239, 68, 68, 0.25);
            border-color: rgba(239, 68, 68, 0.4);
            color: #fca5a5;
        }

        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(4px);
            -webkit-backdrop-filter: blur(4px);
            z-index: 999;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s cubic-bezier(0.4, 0, 0.2, 1), visibility 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .sidebar-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .sidebar-brand h2 {
            color: var(--white);
            font-size: 16px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
            letter-spacing: -0.3px;
        }

        .sidebar-brand h2 i {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            padding: 8px;
            border-radius: 10px;
            font-size: 14px;
        }

        .sidebar-brand small {
            display: block;
            color: var(--gray-light);
            font-size: 11px;
            font-weight: 400;
            margin-top: 4px;
            margin-left: 38px;
        }

        .sidebar-menu {
            padding: 16px 12px;
            flex: 1;
        }

        .menu-label {
            color: var(--gray);
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            padding: 0 8px;
            margin-bottom: 8px;
            margin-top: 16px;
        }

        .menu-label:first-child {
            margin-top: 0;
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 12px;
            color: var(--gray-light);
            text-decoration: none;
            border-radius: 8px;
            font-size: 13.5px;
            font-weight: 500;
            transition: var(--transition);
            margin-bottom: 2px;
        }

        .sidebar-menu a:hover {
            background: rgba(255,255,255,0.06);
            color: var(--white);
        }

        .sidebar-menu a.active {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: var(--white);
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.35);
        }

        .sidebar-menu a i {
            width: 20px;
            text-align: center;
            font-size: 14px;
        }

        .sidebar-user {
            padding: 16px 12px;
            border-top: 1px solid rgba(255,255,255,0.08);
        }

        .sidebar-user .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 8px;
        }

        .sidebar-user .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 14px;
            font-weight: 700;
            flex-shrink: 0;
        }

        .sidebar-user .user-detail {
            overflow: hidden;
        }

        .sidebar-user .user-detail h4 {
            color: var(--white);
            font-size: 13px;
            font-weight: 600;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .sidebar-user .user-detail span {
            color: var(--gray);
            font-size: 11px;
            text-transform: capitalize;
        }

        /* ========== MAIN CONTENT ========== */
        .main-content {
            margin-left: var(--sidebar-width);
            flex: 1;
            min-height: 100vh;
        }

        /* ========== TOP NAV ========== */
        .top-nav {
            background: var(--white);
            padding: 16px 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid #e2e8f0;
            position: sticky;
            top: 0;
            z-index: 50;
            backdrop-filter: blur(10px);
            background: rgba(255,255,255,0.9);
        }

        .top-nav-left {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .btn-toggle-sidebar {
            display: none;
            align-items: center;
            justify-content: center;
            width: 38px;
            height: 38px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            background: var(--white);
            color: var(--dark-600);
            font-size: 16px;
            cursor: pointer;
            transition: var(--transition);
            flex-shrink: 0;
            box-shadow: var(--shadow);
        }

        .btn-toggle-sidebar:hover {
            background: var(--light);
            color: var(--primary);
            border-color: #cbd5e1;
        }

        .btn-toggle-sidebar:active {
            transform: scale(0.96);
        }

        .top-nav .page-title {
            font-size: 20px;
            font-weight: 700;
            color: var(--dark);
            letter-spacing: -0.4px;
        }

        .top-nav .page-subtitle {
            font-size: 13px;
            color: var(--gray);
            margin-top: 2px;
        }

        .top-nav-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .btn-logout {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            background: var(--light);
            color: var(--dark-600);
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            font-family: 'Inter', sans-serif;
        }

        .btn-logout:hover {
            background: #fee2e2;
            color: var(--danger);
            border-color: #fca5a5;
        }

        /* ========== PAGE CONTENT ========== */
        .page-content {
            padding: 28px 32px;
        }

        /* ========== CARDS ========== */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-bottom: 28px;
        }

        .stat-card {
            background: var(--white);
            padding: 20px 24px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            border: 1px solid #e2e8f0;
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
            border-radius: var(--border-radius) var(--border-radius) 0 0;
        }

        .stat-card.primary::before { background: linear-gradient(90deg, var(--primary), var(--primary-light)); }
        .stat-card.success::before { background: linear-gradient(90deg, var(--success), #34d399); }
        .stat-card.warning::before { background: linear-gradient(90deg, var(--warning), #fbbf24); }
        .stat-card.danger::before { background: linear-gradient(90deg, var(--danger), #f87171); }
        .stat-card.info::before { background: linear-gradient(90deg, var(--info), #22d3ee); }
        .stat-card.secondary::before { background: linear-gradient(90deg, var(--secondary), #38bdf8); }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .stat-card .stat-icon {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            margin-bottom: 12px;
        }

        .stat-card.primary .stat-icon { background: var(--primary-50); color: var(--primary); }
        .stat-card.success .stat-icon { background: #ecfdf5; color: var(--success); }
        .stat-card.warning .stat-icon { background: #fffbeb; color: var(--warning); }
        .stat-card.danger .stat-icon { background: #fef2f2; color: var(--danger); }
        .stat-card.info .stat-icon { background: #ecfeff; color: var(--info); }
        .stat-card.secondary .stat-icon { background: #f0f9ff; color: var(--secondary); }

        .stat-card .stat-value {
            font-size: 28px;
            font-weight: 800;
            color: var(--dark);
            letter-spacing: -1px;
            line-height: 1;
        }

        .stat-card .stat-label {
            font-size: 13px;
            color: var(--gray);
            margin-top: 4px;
            font-weight: 500;
        }

        /* ========== TABLES ========== */
        .card {
            background: var(--white);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            border: 1px solid #e2e8f0;
            margin-bottom: 24px;
            overflow: hidden;
        }

        .card-header {
            padding: 18px 24px;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .card-header h3 {
            font-size: 16px;
            font-weight: 700;
            color: var(--dark);
            display: flex;
            align-items: center;
            gap: 8px;
            letter-spacing: -0.3px;
        }

        .card-header h3 i {
            color: var(--primary);
        }

        .card-body {
            padding: 24px;
        }

        .table-responsive {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        table thead th {
            background: var(--light);
            padding: 12px 16px;
            font-size: 12px;
            font-weight: 600;
            color: var(--dark-600);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }

        table thead th:first-child {
            border-radius: 8px 0 0 0;
        }

        table thead th:last-child {
            border-radius: 0 8px 0 0;
        }

        table tbody td {
            padding: 12px 16px;
            font-size: 13.5px;
            color: var(--dark-600);
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }

        table tbody tr {
            transition: var(--transition);
        }

        table tbody tr:hover {
            background: #f8fafc;
        }

        table tbody tr:last-child td {
            border-bottom: none;
        }

        /* ========== BADGES ========== */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11.5px;
            font-weight: 600;
            letter-spacing: 0.2px;
        }

        .badge-hadir { background: #ecfdf5; color: #059669; }
        .badge-izin { background: #f0f9ff; color: #0284c7; }
        .badge-sakit { background: #fffbeb; color: #d97706; }
        .badge-alpa { background: #fef2f2; color: #dc2626; }
        .badge-role { background: var(--primary-50); color: var(--primary); }

        /* ========== FORMS ========== */
        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: var(--dark-600);
            margin-bottom: 6px;
        }

        .form-control {
            width: 100%;
            padding: 10px 14px;
            border: 1.5px solid #e2e8f0;
            border-radius: 8px;
            font-size: 14px;
            font-family: 'Inter', sans-serif;
            color: var(--dark);
            transition: var(--transition);
            background: var(--white);
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
        }

        select.form-control {
            cursor: pointer;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2364748b' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 16px;
            padding-right: 40px;
        }

        textarea.form-control {
            resize: vertical;
            min-height: 80px;
        }

        /* ========== BUTTONS ========== */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 13.5px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            border: none;
            font-family: 'Inter', sans-serif;
            text-decoration: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: var(--white);
            box-shadow: 0 2px 8px rgba(99, 102, 241, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.4);
        }

        .btn-success {
            background: linear-gradient(135deg, var(--success), #059669);
            color: var(--white);
            box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
        }

        .btn-success:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4);
        }

        /* ========== ALERTS ========== */
        .alert {
            padding: 14px 18px;
            border-radius: 10px;
            font-size: 13.5px;
            font-weight: 500;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: slideDown 0.3s ease;
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .alert-success {
            background: #ecfdf5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }

        .alert-danger {
            background: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        /* ========== STATUS RADIO CARDS ========== */
        .status-options {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
        }

        .status-option {
            position: relative;
        }

        .status-option input[type="radio"] {
            position: absolute;
            opacity: 0;
            width: 0;
            height: 0;
        }

        .status-option label {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
            padding: 16px 8px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            cursor: pointer;
            transition: var(--transition);
            text-align: center;
        }

        .status-option label i {
            font-size: 22px;
            transition: var(--transition);
        }

        .status-option label span {
            font-size: 12px;
            font-weight: 600;
        }

        .status-option input[type="radio"]:checked + label {
            border-width: 2px;
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .status-option.hadir input:checked + label {
            border-color: var(--success);
            background: #ecfdf5;
            color: #059669;
        }

        .status-option.izin input:checked + label {
            border-color: var(--secondary);
            background: #f0f9ff;
            color: #0284c7;
        }

        .status-option.sakit input:checked + label {
            border-color: var(--warning);
            background: #fffbeb;
            color: #d97706;
        }

        .status-option.alpa input:checked + label {
            border-color: var(--danger);
            background: #fef2f2;
            color: #dc2626;
        }

        /* ========== PROGRESS BAR ========== */
        .progress-bar-wrapper {
            background: #e2e8f0;
            border-radius: 10px;
            height: 8px;
            overflow: hidden;
            width: 100%;
        }

        .progress-bar-fill {
            height: 100%;
            border-radius: 10px;
            transition: width 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .progress-bar-fill.high { background: linear-gradient(90deg, var(--success), #34d399); }
        .progress-bar-fill.medium { background: linear-gradient(90deg, var(--warning), #fbbf24); }
        .progress-bar-fill.low { background: linear-gradient(90deg, var(--danger), #f87171); }

        /* ========== EMPTY STATE ========== */
        .empty-state {
            text-align: center;
            padding: 48px 24px;
            color: var(--gray);
        }

        .empty-state i {
            font-size: 48px;
            color: #cbd5e1;
            margin-bottom: 16px;
        }

        .empty-state h4 {
            font-size: 16px;
            font-weight: 600;
            color: var(--dark-600);
            margin-bottom: 4px;
        }

        .empty-state p {
            font-size: 13px;
        }

        /* ========== RESPONSIVE ========== */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                z-index: 1000;
                box-shadow: 10px 0 25px rgba(0,0,0,0.3);
            }

            .sidebar.open {
                transform: translateX(0);
            }

            .btn-toggle-sidebar {
                display: inline-flex;
            }

            .sidebar-close-btn {
                display: inline-flex;
            }

            .main-content {
                margin-left: 0;
            }

            .page-content {
                padding: 20px 16px;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 12px;
            }

            .status-options {
                grid-template-columns: repeat(2, 1fr);
            }

            .top-nav {
                padding: 12px 16px;
            }

            .top-nav .page-title {
                font-size: 17px;
            }

            .top-nav .page-subtitle {
                display: none;
            }
        }

        /* ========== ANIMATIONS ========== */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .animate-in {
            animation: fadeInUp 0.4s ease forwards;
        }

        .animate-in:nth-child(1) { animation-delay: 0.05s; }
        .animate-in:nth-child(2) { animation-delay: 0.1s; }
        .animate-in:nth-child(3) { animation-delay: 0.15s; }
        .animate-in:nth-child(4) { animation-delay: 0.2s; }
        .animate-in:nth-child(5) { animation-delay: 0.25s; }
        .animate-in:nth-child(6) { animation-delay: 0.3s; }
    </style>
    @stack('styles')
</head>
<body>
    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-brand">
            <div class="sidebar-brand-content">
                <h2>
                    <i class="fas fa-graduation-cap"></i>
                    SI Absensi
                </h2>
                <button type="button" class="sidebar-close-btn" id="sidebarClose" aria-label="Tutup navigasi">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <small>Sistem Informasi Absensi Mahasiswa</small>
        </div>

        <nav class="sidebar-menu">
            @auth
                @if(auth()->user()->isMahasiswa())
                    <div class="menu-label">Menu Utama</div>
                    <a href="{{ route('mahasiswa.dashboard') }}" class="{{ request()->routeIs('mahasiswa.dashboard') ? 'active' : '' }}">
                        <i class="fas fa-th-large"></i> Dashboard
                    </a>
                    <a href="{{ route('mahasiswa.jadwal') }}" class="{{ request()->routeIs('mahasiswa.jadwal') ? 'active' : '' }}">
                        <i class="fas fa-calendar-alt"></i> Jadwal Kuliah
                    </a>
                    <a href="{{ route('mahasiswa.absensi') }}" class="{{ request()->routeIs('mahasiswa.absensi') ? 'active' : '' }}">
                        <i class="fas fa-clipboard-check"></i> Absensi
                    </a>
                @else
                    <div class="menu-label">Menu Utama</div>
                    <a href="{{ route('dosen.dashboard') }}" class="{{ request()->routeIs('dosen.dashboard') ? 'active' : '' }}">
                        <i class="fas fa-th-large"></i> Dashboard
                    </a>
                    <a href="{{ route('dosen.sesi-absensi') }}" class="{{ request()->routeIs('dosen.sesi-absensi') ? 'active' : '' }}">
                        <i class="fas fa-calendar-check"></i> Sesi Pertemuan
                    </a>
                    <div class="menu-label">Data & Laporan</div>
                    <a href="{{ route('dosen.rekap') }}" class="{{ request()->routeIs('dosen.rekap') ? 'active' : '' }}">
                        <i class="fas fa-clipboard-list"></i> Rekap Absensi
                    </a>
                    <a href="{{ route('dosen.laporan') }}" class="{{ request()->routeIs('dosen.laporan') ? 'active' : '' }}">
                        <i class="fas fa-chart-bar"></i> Laporan
                    </a>

                    @if(auth()->user()->isAdmin())
                        <div class="menu-label">Administrasi</div>
                        <a href="{{ route('admin.mahasiswa.index') }}" class="{{ request()->routeIs('admin.mahasiswa.*') ? 'active' : '' }}">
                            <i class="fas fa-users-cog"></i> Kelola Mahasiswa
                        </a>
                    @endif
                @endif
            @endauth
        </nav>

        @auth
        <div class="sidebar-user">
            <div class="user-info">
                <div class="user-avatar">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div class="user-detail">
                    <h4>{{ auth()->user()->name }}</h4>
                    <span>{{ auth()->user()->role }}</span>
                </div>
            </div>
        </div>
        @endauth
    </aside>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Navigation -->
        <header class="top-nav">
            <div class="top-nav-left">
                <button type="button" class="btn-toggle-sidebar" id="sidebarToggle" aria-label="Buka menu navigasi">
                    <i class="fas fa-bars"></i>
                </button>
                <div>
                    <div class="page-title">@yield('page-title', 'Dashboard')</div>
                    <div class="page-subtitle">@yield('page-subtitle', '')</div>
                </div>
            </div>
            <div class="top-nav-actions">
                @auth
                <form action="{{ route('logout') }}" method="POST" style="margin:0;">
                    @csrf
                    <button type="submit" class="btn-logout">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </button>
                </form>
                @endauth
            </div>
        </header>

        <!-- Page Content -->
        <main class="page-content">
            @if(session('success'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                </div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i>
                    {{ $errors->first() }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const sidebar = document.querySelector('.sidebar');
            const toggleBtn = document.getElementById('sidebarToggle');
            const closeBtn = document.getElementById('sidebarClose');
            const overlay = document.getElementById('sidebarOverlay');

            function openSidebar() {
                if (sidebar) sidebar.classList.add('open');
                if (overlay) overlay.classList.add('active');
                document.body.style.overflow = 'hidden';
            }

            function closeSidebar() {
                if (sidebar) sidebar.classList.remove('open');
                if (overlay) overlay.classList.remove('active');
                document.body.style.overflow = '';
            }

            if (toggleBtn) {
                toggleBtn.addEventListener('click', function (e) {
                    e.stopPropagation();
                    if (sidebar && sidebar.classList.contains('open')) {
                        closeSidebar();
                    } else {
                        openSidebar();
                    }
                });
            }

            if (closeBtn) {
                closeBtn.addEventListener('click', closeSidebar);
            }

            if (overlay) {
                overlay.addEventListener('click', closeSidebar);
            }

            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape' && sidebar && sidebar.classList.contains('open')) {
                    closeSidebar();
                }
            });
        });
    </script>
    @stack('scripts')
</body>
</html>
