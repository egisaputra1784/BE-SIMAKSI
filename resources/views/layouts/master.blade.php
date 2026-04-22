<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>SIMAKSI – Sistem Informasi Akademik</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('plus-admin-free/src') }}/assets/vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="{{ asset('plus-admin-free/src') }}/assets/vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="{{ asset('plus-admin-free/src') }}/assets/css/style.css">
    <style>
        :root {
            --primary:       #1565C0;
            --primary-light: #1976D2;
            --primary-dark:  #0D47A1;
            --primary-soft:  #E3F2FD;
            --accent:        #00ACC1;
            --success:       #2E7D32;
            --warning:       #F57F17;
            --danger:        #C62828;
            --sidebar-bg:    #0D1B2A;
            --sidebar-w:     255px;
            --topbar-h:      62px;
            --text-main:     #1a2332;
            --text-muted:    #6b7a90;
            --border:        #e4eaf2;
            --bg-page:       #f4f6fb;
            --card-shadow:   0 2px 16px rgba(21,101,192,.08);
            --radius:        14px;
            --radius-sm:     8px;
        }
        * { box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: var(--bg-page); color: var(--text-main); margin: 0; }

        /* ─── SIDEBAR ─────────────────────────────────────── */
        .simaksi-sidebar {
            position: fixed; top: 0; left: 0; height: 100vh;
            width: var(--sidebar-w); background: var(--sidebar-bg);
            display: flex; flex-direction: column;
            z-index: 1000; overflow-y: auto; overflow-x: hidden;
            transition: width .3s ease;
        }
        .simaksi-sidebar::-webkit-scrollbar { width: 4px; }
        .simaksi-sidebar::-webkit-scrollbar-track { background: transparent; }
        .simaksi-sidebar::-webkit-scrollbar-thumb { background: rgba(255,255,255,.15); border-radius: 4px; }

        /* Brand */
        .sidebar-brand {
            padding: 20px 20px 16px;
            border-bottom: 1px solid rgba(255,255,255,.07);
            display: flex; align-items: center; gap: 12px;
        }
        .sidebar-brand .brand-icon {
            width: 40px; height: 40px; border-radius: 10px;
            background: linear-gradient(135deg, var(--primary-light), var(--accent));
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .sidebar-brand .brand-icon i { font-size: 20px; color: #fff; }
        .sidebar-brand .brand-text { line-height: 1.2; }
        .sidebar-brand .brand-name { font-size: 15px; font-weight: 700; color: #fff; letter-spacing: .3px; }
        .sidebar-brand .brand-sub  { font-size: 10px; color: rgba(255,255,255,.45); font-weight: 500; text-transform: uppercase; letter-spacing: 1px; }

        /* Profile */
        .sidebar-profile {
            padding: 16px 20px;
            border-bottom: 1px solid rgba(255,255,255,.07);
            display: flex; align-items: center; gap: 12px;
        }
        .sidebar-profile .avatar {
            width: 38px; height: 38px; border-radius: 50%;
            background: linear-gradient(135deg, #1976D2, #00ACC1);
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .sidebar-profile .avatar i { color: #fff; font-size: 18px; }
        .sidebar-profile .profile-name  { font-size: 13px; font-weight: 600; color: #fff; }
        .sidebar-profile .profile-role  { font-size: 11px; color: rgba(255,255,255,.45); }

        /* Nav */
        .sidebar-nav { padding: 12px 0; flex: 1; }
        .nav-section-label {
            font-size: 10px; font-weight: 700; text-transform: uppercase;
            letter-spacing: 1.2px; color: rgba(255,255,255,.3);
            padding: 16px 20px 6px;
        }
        .sidebar-nav a {
            display: flex; align-items: center; gap: 10px;
            padding: 9px 20px; color: rgba(255,255,255,.65);
            text-decoration: none; font-size: 13.5px; font-weight: 500;
            border-left: 3px solid transparent;
            transition: all .2s;
        }
        .sidebar-nav a:hover {
            color: #fff; background: rgba(255,255,255,.06);
            border-left-color: rgba(255,255,255,.25);
        }
        .sidebar-nav a.active {
            color: #fff; background: rgba(25,118,210,.25);
            border-left-color: var(--primary-light);
        }
        .sidebar-nav a i { font-size: 18px; width: 22px; flex-shrink: 0; }

        /* Submenu */
        .sidebar-nav .collapse-link {
            display: flex; align-items: center; gap: 10px;
            padding: 9px 20px; color: rgba(255,255,255,.65);
            text-decoration: none; font-size: 13.5px; font-weight: 500;
            border-left: 3px solid transparent;
            cursor: pointer; transition: all .2s;
            justify-content: space-between;
        }
        .sidebar-nav .collapse-link:hover { color: #fff; background: rgba(255,255,255,.06); }
        .sidebar-nav .collapse-link .link-left { display: flex; align-items: center; gap: 10px; }
        .sidebar-nav .collapse-link .arrow { font-size: 14px; transition: transform .25s; }
        .sidebar-nav .collapse-link[aria-expanded="true"] .arrow { transform: rotate(90deg); }
        .sidebar-nav .sub-nav { padding: 4px 0 4px 52px; }
        .sidebar-nav .sub-nav a {
            font-size: 13px; font-weight: 400; padding: 6px 16px;
            border-left: none; color: rgba(255,255,255,.55);
        }
        .sidebar-nav .sub-nav a:hover { background: none; color: #fff; }
        .sidebar-nav .sub-nav a.active { color: var(--accent); background: none; font-weight: 600; }

        /* ─── TOPBAR ──────────────────────────────────────── */
        .simaksi-topbar {
            position: fixed; top: 0; left: var(--sidebar-w); right: 0;
            height: var(--topbar-h); background: #fff;
            border-bottom: 1px solid var(--border);
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 24px; z-index: 999;
            box-shadow: 0 1px 8px rgba(0,0,0,.06);
        }
        .topbar-left { display: flex; align-items: center; gap: 14px; }
        .topbar-toggle {
            width: 36px; height: 36px; border-radius: 8px; border: none;
            background: var(--bg-page); color: var(--text-muted);
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; font-size: 18px; transition: all .2s;
        }
        .topbar-toggle:hover { background: var(--primary-soft); color: var(--primary); }
        .topbar-breadcrumb { display: flex; align-items: center; gap: 6px; font-size: 13px; color: var(--text-muted); }
        .topbar-breadcrumb a { color: var(--text-muted); text-decoration: none; }
        .topbar-breadcrumb a:hover { color: var(--primary); }
        .topbar-right { display: flex; align-items: center; gap: 10px; }
        .topbar-avatar {
            width: 36px; height: 36px; border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-light), var(--accent));
            display: flex; align-items: center; justify-content: center;
            cursor: pointer;
        }
        .topbar-avatar i { color: #fff; font-size: 18px; }
        .topbar-btn {
            width: 36px; height: 36px; border-radius: 8px; border: none;
            background: var(--bg-page); color: var(--text-muted);
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; font-size: 18px; text-decoration: none; transition: all .2s;
        }
        .topbar-btn:hover { background: var(--primary-soft); color: var(--primary); }
        .topbar-label { font-size: 13px; font-weight: 600; color: var(--text-main); }

        /* ─── MAIN CONTENT ────────────────────────────────── */
        .simaksi-content {
            margin-left: var(--sidebar-w);
            margin-top: var(--topbar-h);
            padding: 28px 28px 40px;
            min-height: calc(100vh - var(--topbar-h));
        }

        /* ─── CARDS ───────────────────────────────────────── */
        .card { border: 1px solid var(--border); border-radius: var(--radius); box-shadow: var(--card-shadow); }
        .card-header-custom {
            padding: 18px 20px 14px;
            border-bottom: 1px solid var(--border);
            display: flex; align-items: center; justify-content: space-between;
        }
        .card-title-custom { font-size: 15px; font-weight: 700; color: var(--text-main); margin: 0; }

        /* ─── PAGE HEADER ─────────────────────────────────── */
        .page-header-bar {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 22px; flex-wrap: wrap; gap: 12px;
        }
        .page-header-bar h3 { font-size: 22px; font-weight: 800; color: var(--text-main); margin: 0; }
        .page-header-bar small { font-size: 13px; color: var(--text-muted); margin-top: 2px; display: block; }
        .page-header-actions { display: flex; gap: 8px; flex-wrap: wrap; }

        /* ─── BUTTONS ─────────────────────────────────────── */
        .btn-primary-custom {
            background: var(--primary); color: #fff; border: none;
            padding: 8px 18px; border-radius: var(--radius-sm);
            font-size: 13.5px; font-weight: 600; display: inline-flex;
            align-items: center; gap: 6px; cursor: pointer;
            text-decoration: none; transition: all .2s;
        }
        .btn-primary-custom:hover { background: var(--primary-dark); color: #fff; transform: translateY(-1px); }
        .btn-success-custom {
            background: #1B5E20; color: #fff; border: none;
            padding: 8px 18px; border-radius: var(--radius-sm);
            font-size: 13.5px; font-weight: 600; display: inline-flex;
            align-items: center; gap: 6px; cursor: pointer;
            text-decoration: none; transition: all .2s;
        }
        .btn-success-custom:hover { background: #2E7D32; color: #fff; transform: translateY(-1px); }
        .btn-sm-warn {
            background: #FFF8E1; color: #E65100; border: 1px solid #FFE082;
            padding: 4px 10px; border-radius: 6px; font-size: 12px;
            font-weight: 600; cursor: pointer; transition: all .2s;
        }
        .btn-sm-warn:hover { background: #FFE082; }
        .btn-sm-danger {
            background: #FFEBEE; color: var(--danger); border: 1px solid #FFCDD2;
            padding: 4px 10px; border-radius: 6px; font-size: 12px;
            font-weight: 600; cursor: pointer; transition: all .2s;
        }
        .btn-sm-danger:hover { background: #FFCDD2; }

        /* ─── TABLE ───────────────────────────────────────── */
        .simaksi-table { width: 100%; border-collapse: collapse; font-size: 13.5px; }
        .simaksi-table thead th {
            background: var(--primary-soft); color: var(--primary-dark);
            font-weight: 700; font-size: 12px; text-transform: uppercase;
            letter-spacing: .5px; padding: 11px 14px;
            border-bottom: 2px solid #BBDEFB; white-space: nowrap;
        }
        .simaksi-table tbody td {
            padding: 12px 14px; border-bottom: 1px solid var(--border);
            color: var(--text-main); vertical-align: middle;
        }
        .simaksi-table tbody tr:last-child td { border-bottom: none; }
        .simaksi-table tbody tr:hover td { background: #fafcff; }

        /* ─── BADGE ───────────────────────────────────────── */
        .badge-primary { background: var(--primary-soft); color: var(--primary); padding: 3px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; }
        .badge-success { background: #E8F5E9; color: #2E7D32; padding: 3px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; }
        .badge-warning { background: #FFF8E1; color: #E65100; padding: 3px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; }
        .badge-danger  { background: #FFEBEE; color: var(--danger); padding: 3px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; }

        /* ─── SEARCH INPUT ────────────────────────────────── */
        .search-input {
            border: 1.5px solid var(--border); border-radius: 8px;
            padding: 8px 14px 8px 38px; font-size: 13.5px; width: 260px;
            background: var(--bg-page) url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%236b7a90' viewBox='0 0 16 16'%3E%3Cpath d='M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.099zm-5.442 1.656a5.5 5.5 0 1 1 0-11 5.5 5.5 0 0 1 0 11z'/%3E%3C/svg%3E") no-repeat 12px center;
            outline: none; font-family: 'Inter', sans-serif; transition: all .2s;
            color: var(--text-main);
        }
        .search-input:focus { border-color: var(--primary-light); background-color: #fff; box-shadow: 0 0 0 3px rgba(25,118,210,.12); }

        /* ─── STAT CARDS ──────────────────────────────────── */
        .stat-card {
            border-radius: var(--radius); padding: 20px 22px;
            display: flex; align-items: center; justify-content: space-between;
            box-shadow: var(--card-shadow); border: 1px solid var(--border);
            background: #fff; transition: transform .2s, box-shadow .2s;
        }
        .stat-card:hover { transform: translateY(-3px); box-shadow: 0 8px 28px rgba(21,101,192,.13); }
        .stat-card .stat-icon {
            width: 52px; height: 52px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 24px; flex-shrink: 0;
        }
        .stat-card .stat-label { font-size: 12px; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: .5px; }
        .stat-card .stat-value { font-size: 28px; font-weight: 800; color: var(--text-main); line-height: 1.1; margin-top: 4px; }

        /* ─── FOOTER ──────────────────────────────────────── */
        .simaksi-footer {
            margin-left: var(--sidebar-w);
            padding: 14px 28px;
            border-top: 1px solid var(--border);
            background: #fff;
            font-size: 12.5px; color: var(--text-muted);
            display: flex; align-items: center; justify-content: space-between;
        }

        /* ─── FORM ────────────────────────────────────────── */
        .form-label-custom { font-size: 13px; font-weight: 600; color: var(--text-main); margin-bottom: 5px; display: block; }
        .form-control-custom {
            width: 100%; border: 1.5px solid var(--border); border-radius: 8px;
            padding: 9px 13px; font-size: 13.5px; font-family: 'Inter', sans-serif;
            outline: none; transition: all .2s; color: var(--text-main); background: #fff;
        }
        .form-control-custom:focus { border-color: var(--primary-light); box-shadow: 0 0 0 3px rgba(25,118,210,.12); }

        /* ─── EMPTY STATE ─────────────────────────────────── */
        .empty-state { text-align: center; padding: 48px 20px; color: var(--text-muted); }
        .empty-state i { font-size: 48px; display: block; margin-bottom: 12px; color: #CBD5E1; }
        .empty-state p { font-size: 14px; margin: 0; }

        /* ─── ALERT / TOAST ───────────────────────────────── */
        .toast-container-custom { position: fixed; top: 76px; right: 24px; z-index: 9999; display: flex; flex-direction: column; gap: 8px; }
        .toast-custom {
            background: #fff; border-radius: 10px; box-shadow: 0 4px 20px rgba(0,0,0,.12);
            padding: 13px 18px; display: flex; align-items: center; gap: 10px;
            font-size: 13.5px; font-weight: 500; min-width: 240px;
            border-left: 4px solid var(--primary); animation: slideInRight .3s ease;
        }
        .toast-custom.success { border-left-color: var(--success); }
        .toast-custom.error   { border-left-color: var(--danger); }
        @keyframes slideInRight { from { opacity: 0; transform: translateX(30px); } to { opacity: 1; transform: translateX(0); } }

        /* ─── RESPONSIVE ──────────────────────────────────── */
        @media (max-width: 992px) {
            .simaksi-sidebar { transform: translateX(-100%); }
            .simaksi-sidebar.open { transform: translateX(0); }
            .simaksi-topbar { left: 0; }
            .simaksi-content, .simaksi-footer { margin-left: 0; }
        }

        /* Override vendor styles */
        .container-scroller, .container-fluid, .page-body-wrapper { display: none !important; }
    </style>
</head>
<body>

    {{-- SIDEBAR --}}
    @includeIf('layouts.sidebar')

    {{-- TOPBAR --}}
    @includeIf('layouts.navbar')

    {{-- MAIN CONTENT --}}
    <main class="simaksi-content">
        @yield('content')
    </main>

    {{-- FOOTER --}}
    @includeIf('layouts.footer')

    {{-- TOAST CONTAINER --}}
    <div class="toast-container-custom" id="toastContainer"></div>

    {{-- SCRIPTS --}}
    <script src="{{ asset('plus-admin-free/src') }}/assets/vendors/js/vendor.bundle.base.js"></script>
    <script>
        // Sidebar toggle
        const sidebar = document.getElementById('simaksiSidebar');
        const toggleBtn = document.getElementById('sidebarToggleBtn');
        if (toggleBtn && sidebar) {
            toggleBtn.addEventListener('click', () => sidebar.classList.toggle('open'));
        }

        // Active nav link
        (function() {
            const path = window.location.pathname;
            document.querySelectorAll('.sidebar-nav a').forEach(a => {
                const href = a.getAttribute('href');
                if (href && href !== '#' && path.startsWith(href) && href !== '/') {
                    a.classList.add('active');
                    const collapse = a.closest('.collapse');
                    if (collapse) {
                        collapse.classList.add('show');
                        const trigger = document.querySelector('[data-bs-target="#' + collapse.id + '"]');
                        if (trigger) trigger.setAttribute('aria-expanded', 'true');
                    }
                }
                if (href === '/' && path === '/') a.classList.add('active');
            });
        })();

        // Toast helper
        window.showToast = function(msg, type = 'success') {
            const tc = document.getElementById('toastContainer');
            const t = document.createElement('div');
            t.className = 'toast-custom ' + type;
            t.innerHTML = `<i class="mdi mdi-${type === 'success' ? 'check-circle' : 'alert-circle'}" style="font-size:18px;color:${type==='success'?'#2E7D32':'#C62828'}"></i><span>${msg}</span>`;
            tc.appendChild(t);
            setTimeout(() => t.remove(), 3500);
        }
    </script>

    @stack('scripts')
</body>
</html>
