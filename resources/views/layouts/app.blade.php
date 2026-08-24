<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Sendpeak - Professional Email Marketing Solution">
    <meta name="author" content="Sendpeak Technologies">
    <title>@yield('title')</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- DataTables Bootstrap 5 -->
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <!-- SweetAlert2 -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.1/dist/sweetalert2.min.css" rel="stylesheet">
    <!-- TinyMCE Rich Text Editor -->

    <style>
        :root {
            --bg: #17171c;
            --bg-deepest: #101014;
            --bg-deep: #131317;
            --surface: #1c1c22;
            --surface-raised: #232329;
            --surface-raised2: #2c2c33;
            --glass: rgba(30, 30, 36, 0.5);
            --violet-light: #b388ff;
            --violet-300: #a78bfa;
            --violet: #8b5cf6;
            --violet-deep: #7c3aed;
            --teal: #2dd4bf;
            --teal-500: #14b8a6;
            --emerald: #34d399;
            --text-strong: #ececef;
            --text: #c7c6cf;
            --muted: #8f8e98;
            --subtle: #85848d;
            --line: rgba(255, 255, 255, 0.07);
            --line-strong: rgba(255, 255, 255, 0.12);
            --violet-soft: rgba(139, 92, 246, 0.10);
            --violet-soft-border: rgba(139, 92, 246, 0.20);
            --danger: #f87171;
            --warning: #fbbf24;
            --sidebar-width: 248px;
            --radius: 14px;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { animation-duration: 0.01ms !important; animation-iteration-count: 1 !important; transition-duration: 0.01ms !important; }
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--bg);
            color: var(--text);
            font-size: 14px;
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        h1, h2, h3, h4, h5, h6 { letter-spacing: -0.01em; color: var(--text-strong); font-weight: 600; }

        a { color: var(--violet-300); }

        .accent-text {
            background: linear-gradient(to right, var(--violet-light), var(--teal));
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* ── Sidebar ── */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: var(--bg-deep);
            border-right: 1px solid var(--line);
            z-index: 1020;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
        }

        .sidebar-brand {
            padding: 24px 20px 18px;
        }

        .sidebar-brand .brand-icon {
            width: 34px;
            height: 34px;
            background: linear-gradient(150deg, var(--violet-300), var(--teal));
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 12px;
        }

        .sidebar-brand .brand-icon svg {
            width: 16px;
            height: 16px;
            color: #17131f;
        }

        .sidebar-brand h4 {
            color: var(--text-strong);
            font-size: 15px;
            font-weight: 700;
            margin: 0 0 1px;
            letter-spacing: -0.02em;
        }

        .sidebar-brand small {
            color: var(--muted);
            font-size: 11px;
            font-weight: 500;
            letter-spacing: 0.02em;
        }

        .sidebar-nav {
            flex: 1;
            padding: 8px 0 16px;
            display: flex;
            flex-direction: column;
        }

        .nav-section {
            margin-bottom: 4px;
            padding: 0 12px;
        }

        .nav-section-title {
            color: var(--subtle);
            font-size: 10.5px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.09em;
            padding: 16px 10px 6px;
        }

        .nav-item { list-style: none; }

        .sidebar .nav-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 7px 10px;
            color: var(--muted);
            text-decoration: none;
            font-size: 13.5px;
            font-weight: 500;
            transition: color 0.12s, background 0.12s, border-color 0.12s;
            margin: 1px 0;
            border-radius: 9px;
            border: 1px solid transparent;
        }

        .sidebar .nav-link:hover {
            color: var(--text-strong);
            background: rgba(255, 255, 255, 0.04);
        }

        .sidebar .nav-link.active {
            color: var(--text-strong);
            background: var(--violet-soft);
            border-color: var(--violet-soft-border);
            font-weight: 600;
        }

        .sidebar .nav-link i {
            font-size: 16px;
            width: 18px;
            text-align: center;
            opacity: 0.75;
            flex-shrink: 0;
        }

        .sidebar .nav-link.active i { opacity: 1; color: var(--violet-300); }
        .sidebar .nav-link:hover i { opacity: 1; }

        /* Logout */
        .logout-section {
            margin-top: auto;
            padding: 10px 12px;
            border-top: 1px solid var(--line);
        }

        .logout-btn {
            display: flex;
            align-items: center;
            gap: 10px;
            width: 100%;
            padding: 7px 10px;
            color: var(--muted);
            background: none;
            border: none;
            font-size: 13.5px;
            font-weight: 500;
            font-family: inherit;
            cursor: pointer;
            text-align: left;
            transition: color 0.12s, background 0.12s;
            border-radius: 9px;
        }

        .logout-btn:hover {
            color: var(--danger);
            background: rgba(248, 113, 113, 0.08);
        }

        .logout-btn i {
            font-size: 16px;
            width: 18px;
            text-align: center;
        }

        /* ── Main Content ── */
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
        }

        /* ── Topbar ── */
        .topbar {
            background: var(--bg);
            height: 56px;
            border-bottom: 1px solid var(--line);
            padding: 0 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 1010;
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .topbar-left .page-title {
            font-size: 15px;
            font-weight: 600;
            color: var(--text-strong);
            letter-spacing: -0.2px;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-avatar {
            width: 30px;
            height: 30px;
            background: linear-gradient(180deg, var(--violet-300), var(--violet));
            border-radius: 9px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #17131f;
            font-size: 12px;
            font-weight: 700;
        }

        .user-meta {
            font-size: 12px;
            line-height: 1.3;
        }

        .user-meta .name {
            font-weight: 600;
            color: var(--text-strong);
        }

        .user-meta .role {
            color: var(--muted);
            font-size: 11px;
        }

        .breadcrumb-wrap {
            background: none;
            border: none;
            padding: 0;
        }

        .breadcrumb {
            margin: 0;
            padding: 0;
            background: none;
            font-size: 12.5px;
        }

        .breadcrumb-item + .breadcrumb-item::before {
            content: "/";
            color: var(--muted);
            padding-right: 0.4rem;
        }

        .breadcrumb-item a {
            color: var(--muted);
            text-decoration: none;
        }

        .breadcrumb-item a:hover { color: var(--text-strong); }

        .breadcrumb-item.active { color: var(--text); font-weight: 500; }

        /* ── Page Content ── */
        .page-content {
            padding: 32px;
            max-width: 1400px;
        }

        /* ── Footer ── */
        .app-footer {
            padding: 18px 32px 28px;
            font-size: 12px;
            color: var(--subtle);
            text-align: center;
        }

        .app-footer a {
            color: var(--muted);
            text-decoration: none;
            font-weight: 600;
        }

        .app-footer a:hover { color: var(--violet-300); }

        /* ── Cards ── */
        .card {
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: var(--radius);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.15);
        }

        .card-header {
            background: transparent;
            border-bottom: 1px solid var(--line);
            padding: 15px 20px;
            border-radius: var(--radius) var(--radius) 0 0;
        }

        .card-title {
            font-size: 14.5px;
            font-weight: 600;
            color: var(--text-strong);
            margin: 0;
            letter-spacing: -0.01em;
        }

        .card-body { padding: 20px; color: var(--text); }
        .card-body .text-muted, .card-body small { color: var(--muted) !important; }

        /* ── Buttons ── */
        .btn {
            font-size: 13px;
            font-weight: 500;
            padding: 8px 16px;
            border-radius: 10px;
            font-family: inherit;
            transition: background 0.12s, border-color 0.12s, opacity 0.12s;
        }

        .btn-primary {
            background: var(--violet);
            border-color: var(--violet);
            color: #ffffff;
        }

        .btn-primary:hover {
            background: var(--violet-deep);
            border-color: var(--violet-deep);
            color: #ffffff;
        }

        .btn-outline-primary {
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid var(--line-strong);
            color: var(--text);
            border-radius: 999px;
        }

        .btn-outline-primary:hover {
            background: rgba(255, 255, 255, 0.09);
            border-color: var(--line-strong);
            color: var(--text-strong);
        }

        .btn-success { background: var(--emerald); border-color: var(--emerald); color: #062018; }
        .btn-warning { background: var(--warning); border-color: var(--warning); color: #1f1400; }
        .btn-danger  { background: var(--danger); border-color: var(--danger); color: #2a0a0a; }
        .btn-info     { background: var(--teal); border-color: var(--teal); color: #062018; }

        /* ── Forms ── */
        .form-control, .form-select {
            border: 1px solid var(--line);
            border-radius: 14px;
            font-size: 13.5px;
            padding: 8px 12px;
            font-family: inherit;
            background: rgba(255, 255, 255, 0.025);
            color: var(--text);
            transition: border-color 0.12s, box-shadow 0.12s;
        }

        .form-control::placeholder { color: var(--subtle); opacity: 0.7; }

        .form-control:focus, .form-select:focus {
            border-color: var(--violet);
            box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.15);
            background: rgba(255, 255, 255, 0.04);
            color: var(--text-strong);
        }

        .form-select option { background: var(--surface-raised); color: var(--text); }

        .form-label {
            font-size: 13px;
            font-weight: 500;
            color: var(--muted);
            margin-bottom: 5px;
        }

        .form-check-input {
            background-color: rgba(255,255,255,0.06);
            border-color: var(--line-strong);
        }
        .form-check-input:checked {
            background-color: var(--violet);
            border-color: var(--violet);
        }

        /* ── Tables ── */
        .table {
            --bs-table-bg: transparent;
            --bs-table-color: var(--text);
            --bs-table-striped-bg: rgba(255, 255, 255, 0.03);
            --bs-table-striped-color: var(--text);
            --bs-table-hover-bg: rgba(255, 255, 255, 0.04);
            --bs-table-hover-color: var(--text);
            --bs-table-border-color: var(--line);
            --bs-table-active-bg: rgba(139, 92, 246, 0.08);
            font-size: 13px;
            margin: 0;
            color: var(--text);
            background-color: transparent;
        }

        .table th {
            font-weight: 600;
            font-size: 10.5px;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--muted);
            border-bottom: 1px solid var(--line);
            padding: 11px 12px;
            background: transparent;
        }

        .table td {
            padding: 12px;
            vertical-align: middle;
            border-bottom: 1px solid var(--line);
            color: var(--text);
        }

        .table tbody tr { transition: background 0.12s; }
        .table tbody tr:hover { background: rgba(255, 255, 255, 0.03); }
        .table tbody tr:last-child td { border-bottom: none; }

        /* ── Badges ── */
        .badge {
            font-weight: 500;
            font-size: 11px;
            padding: 4px 10px;
            border-radius: 999px;
        }

        .badge.bg-success { background: rgba(52, 211, 153, 0.12) !important; color: var(--emerald) !important; border: 1px solid rgba(52, 211, 153, 0.25); }
        .badge.bg-secondary { background: rgba(255, 255, 255, 0.06) !important; color: var(--muted) !important; border: 1px solid var(--line); }
        .badge.bg-primary { background: var(--violet-soft) !important; color: var(--violet-300) !important; border: 1px solid var(--violet-soft-border); }
        .badge.bg-danger { background: rgba(248, 113, 113, 0.12) !important; color: var(--danger) !important; border: 1px solid rgba(248, 113, 113, 0.25); }
        .badge.bg-warning { background: rgba(251, 191, 36, 0.12) !important; color: var(--warning) !important; border: 1px solid rgba(251, 191, 36, 0.25); }

        /* ── Alerts ── */
        .alert {
            border-radius: 14px;
            border: 1px solid var(--line);
            font-size: 13px;
            background: var(--surface-raised);
            color: var(--text);
        }
        .alert-success { background: rgba(52, 211, 153, 0.08); border-color: rgba(52, 211, 153, 0.25); color: var(--emerald); }
        .alert-danger { background: rgba(248, 113, 113, 0.08); border-color: rgba(248, 113, 113, 0.25); color: var(--danger); }
        .alert .btn-close { filter: invert(1) grayscale(1); }

        /* ── Misc text/utility overrides for dark canvas ── */
        .text-danger { color: var(--danger) !important; }
        .invalid-feedback { color: var(--danger); }
        code, pre, .font-monospace { color: var(--violet-300); }

        /* ── DataTables / Pagination ── */
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter,
        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_paginate { color: var(--muted); font-size: 12.5px; }
        .dataTables_wrapper .dataTables_filter input,
        .dataTables_wrapper .dataTables_length select {
            background: rgba(255, 255, 255, 0.025);
            border: 1px solid var(--line);
            color: var(--text);
            border-radius: 8px;
            padding: 6px 12px;
            line-height: 1.4;
        }
        .dataTables_wrapper .dataTables_length select {
            padding-right: 30px;
            margin: 0 6px;
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6' viewBox='0 0 10 6'%3E%3Cpath fill='%23999' d='M0 0l5 6 5-6z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
        }
        .page-link { background: transparent; border-color: var(--line); color: var(--text); }
        .page-link:hover { background: rgba(255, 255, 255, 0.06); border-color: var(--line); color: var(--text-strong); }
        .page-item.active .page-link { background: var(--violet); border-color: var(--violet); color: #17131f; }
        .page-item.disabled .page-link { background: transparent; color: var(--subtle); }

        /* ── Modals / dropdowns to match dark glass ── */
        .modal-content {
            background: var(--surface-raised);
            border: 1px solid var(--line);
            border-radius: var(--radius);
            color: var(--text);
        }
        .modal-header, .modal-footer { border-color: var(--line); }
        .dropdown-menu {
            background: var(--surface-raised);
            border: 1px solid var(--line);
            box-shadow: 0 4px 16px rgba(0,0,0,0.3);
        }
        .dropdown-item { color: var(--text); }
        .dropdown-item:hover, .dropdown-item:focus { background: var(--violet-soft); color: var(--text-strong); }

        /* ── Legacy inline-style overrides ──
           Many pages were built against a light canvas and hardcode hex
           colors inline (color:#0f172a, background:#f8fafc, etc). Since
           inline styles can't be beaten by class selectors, re-map the
           known palette here so those pages read correctly on dark. */
        [style*="color:#0f172a"], [style*="color: #0f172a"],
        [style*="color:#333333"], [style*="color: #333333"] { color: var(--text-strong) !important; }
        [style*="color:#334155"], [style*="color: #334155"] { color: var(--text) !important; }
        [style*="color:#64748b"], [style*="color: #64748b"],
        [style*="color:#6b7280"], [style*="color: #6b7280"],
        [style*="color:#475569"], [style*="color: #475569"] { color: var(--muted) !important; }
        [style*="color:#94a3b8"], [style*="color: #94a3b8"],
        [style*="color:#cbd5e1"], [style*="color: #cbd5e1"] { color: var(--subtle) !important; }

        [style*="background:#f8fafc"], [style*="background: #f8fafc"],
        [style*="background-color:#f8fafc"], [style*="background-color: #f8fafc"],
        [style*="background:#f1f5f9"], [style*="background: #f1f5f9"],
        [style*="background-color:#f1f5f9"], [style*="background-color: #f1f5f9"],
        [style*="background:#f8f9fa"], [style*="background: #f8f9fa"] { background: var(--surface-raised) !important; }

        [style*="background:#dcfce7"], [style*="background: #dcfce7"] { background: rgba(52, 211, 153, 0.14) !important; }
        [style*="background:#dbeafe"], [style*="background: #dbeafe"] { background: rgba(45, 212, 191, 0.14) !important; }

        [style*="border:1px solid #e2e8f0"], [style*="border: 1px solid #e2e8f0"],
        [style*="border-color:#e2e8f0"], [style*="border-color: #e2e8f0"],
        [style*="border-bottom:1px solid #e2e8f0"], [style*="border-bottom: 1px solid #e2e8f0"],
        [style*="border-top:1px solid #e2e8f0"], [style*="border-top: 1px solid #e2e8f0"],
        [style*="border:1px solid #f1f5f9"], [style*="border: 1px solid #f1f5f9"],
        [style*="border-top:1px solid #f1f5f9"], [style*="border-top: 1px solid #f1f5f9"],
        [style*="border-color:#e5e7eb"], [style*="border: 1px solid #e5e7eb"] { border-color: var(--line) !important; }

        /* ── Scrollbar ── */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: var(--surface-raised2); border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--violet-deep); }

        .sidebar::-webkit-scrollbar-thumb { background: var(--surface-raised); }

        /* ── Responsive ── */
        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.2s;
            }

            .sidebar.show { transform: translateX(0); box-shadow: 0 0 0 100vmax rgba(0,0,0,0.001); }

            .main-content { margin-left: 0; }

            .page-content { padding: 18px; }

            .app-footer { padding: 12px 18px 22px; }

            .topbar { padding: 0 16px; }
        }
    </style>

    @stack('styles')
</head>

<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <nav class="sidebar" id="sidebar">
            <div class="sidebar-brand">
                <a href="https://sendpeak.in/" target="_blank" rel="noopener" style="text-decoration:none;display:block;">
                    <div class="brand-icon">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h4>Sendpeak</h4>
                    <small>Email Marketing</small>
                </a>
            </div>

            <div class="sidebar-nav">
                <div class="nav-section">
                    <div class="nav-section-title">Main</div>
                    <ul class="navbar-nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                                <i class="bi bi-grid"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('instant.campaign.*') ? 'active' : '' }}" href="{{ route('instant.campaign.create') }}">
                                <i class="bi bi-send"></i>
                                <span>Instant Campaign</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('individual-emails.*') ? 'active' : '' }}" href="{{ route('individual-emails.create') }}">
                                <i class="bi bi-envelope"></i>
                                <span>Individual Emails</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('email-templates.*') ? 'active' : '' }}" href="{{ route('email-templates.index') }}">
                                <i class="bi bi-file-earmark-text"></i>
                                <span>Email Templates</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('contacts.*') ? 'active' : '' }}" href="{{ route('contacts.index') }}">
                                <i class="bi bi-people"></i>
                                <span>Email Contacts</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('tags.*') ? 'active' : '' }}" href="{{ route('tags.index') }}">
                                <i class="bi bi-tags"></i>
                                <span>Contact Tags</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('broadcasts.*') ? 'active' : '' }}" href="{{ route('broadcasts.index') }}">
                                <i class="bi bi-bar-chart-line"></i>
                                <span>Broadcasts</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('email-activity.*') ? 'active' : '' }}" href="{{ route('email-activity.index') }}">
                                <i class="bi bi-activity"></i>
                                <span>Email Activity</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="nav-section">
                    <div class="nav-section-title">Grow</div>
                    <ul class="navbar-nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('lead-forms.*') ? 'active' : '' }}" href="{{ route('lead-forms.index') }}">
                                <i class="bi bi-ui-checks-grid"></i>
                                <span>Forms</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="nav-section">
                    <div class="nav-section-title">Automate</div>
                    <ul class="navbar-nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('automation-rules.*') ? 'active' : '' }}" href="{{ route('automation-rules.index') }}">
                                <i class="bi bi-shuffle"></i>
                                <span>Rules</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('automation-sequences.*') ? 'active' : '' }}" href="{{ route('automation-sequences.index') }}">
                                <i class="bi bi-signpost-split"></i>
                                <span>Sequences</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('rss-feeds.*') ? 'active' : '' }}" href="{{ route('rss-feeds.index') }}">
                                <i class="bi bi-rss"></i>
                                <span>RSS Feeds</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('email-followups.*') ? 'active' : '' }}" href="{{ route('email-followups.index') }}">
                                <i class="bi bi-reply"></i>
                                <span>Follow-ups</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="nav-section">
                    <div class="nav-section-title">Management</div>
                    <ul class="navbar-nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('email-accounts.*') ? 'active' : '' }}" href="{{ route('email-accounts.index') }}">
                                <i class="bi bi-envelope-check"></i>
                                <span>Email Accounts</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="nav-section">
                    <div class="nav-section-title">Account</div>
                    <ul class="navbar-nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}" href="{{ route('profile.edit') }}">
                                <i class="bi bi-person"></i>
                                <span>Profile</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}" href="{{ route('settings.password') }}">
                                <i class="bi bi-shield-lock"></i>
                                <span>Security</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="logout-section">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="logout-btn" onclick="return confirm('Are you sure you want to logout?')">
                            <i class="bi bi-box-arrow-right"></i>
                            <span>Logout</span>
                        </button>
                    </form>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <div class="main-content flex-fill">
            <!-- Topbar -->
            <div class="topbar">
                <div class="topbar-left">
                    <button class="btn btn-link d-lg-none p-0 text-dark" type="button" onclick="toggleSidebar()">
                        <i class="bi bi-list fs-5"></i>
                    </button>
                    <div class="breadcrumb-wrap d-none d-md-block">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                @yield('breadcrumb')
                            </ol>
                        </nav>
                    </div>
                </div>

                <div class="topbar-right">
                    <div class="user-info">
                        <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                        <div class="user-meta d-none d-lg-block">
                            <div class="name">{{ auth()->user()->name }}</div>
                            <div class="role">Administrator</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Page Content -->
            <div class="page-content">
                @yield('content')
            </div>

            <!-- Footer -->
            <div class="app-footer">
                Designed &amp; Developed By <a href="https://kaxon.in/" target="_blank" rel="noopener">Kaxon</a>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.1/dist/sweetalert2.all.min.js"></script>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('show');

            if (sidebar.classList.contains('show')) {
                const backdrop = document.createElement('div');
                backdrop.className = 'sidebar-backdrop';
                backdrop.style.cssText = `
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: rgba(15, 23, 42, 0.5);
                    z-index: 1015;
                `;
                backdrop.onclick = () => {
                    sidebar.classList.remove('show');
                    backdrop.remove();
                };
                document.body.appendChild(backdrop);
            }
        }

        $(document).ready(function() {
            if ($('.data-table').length) {
                $('.data-table').DataTable({
                    responsive: true,
                    pageLength: 10,
                    processing: true,
                    language: {
                        search: 'Search:',
                        lengthMenu: 'Show _MENU_ entries',
                        info: 'Showing _START_ to _END_ of _TOTAL_ entries',
                        paginate: {
                            next: '›',
                            previous: '‹'
                        }
                    },
                });
            }

            $('.alert:not(.alert-permanent)').delay(5000).fadeOut(300);
        });

        $(document).click(function(event) {
            if ($(window).width() < 992) {
                if (!$(event.target).closest('.sidebar, .btn, .sidebar-backdrop').length) {
                    $('#sidebar').removeClass('show');
                    $('.sidebar-backdrop').remove();
                }
            }
        });

        $(window).resize(function() {
            if ($(window).width() >= 992) {
                $('#sidebar').removeClass('show');
                $('.sidebar-backdrop').remove();
            }
        });
    </script>

    @stack('scripts')
</body>
</html>
