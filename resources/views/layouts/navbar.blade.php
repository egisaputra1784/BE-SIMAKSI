<header class="simaksi-topbar">
    <div class="topbar-left">
        <button class="topbar-toggle" id="sidebarToggleBtn" title="Toggle Sidebar">
            <i class="mdi mdi-menu"></i>
        </button>
        <div class="topbar-breadcrumb">
            <a href="/"><i class="mdi mdi-home-outline"></i></a>
            <i class="mdi mdi-chevron-right" style="font-size:14px;"></i>
            <span class="topbar-label" id="topbarTitle">Dashboard</span>
        </div>
    </div>

    <div class="topbar-right">

        {{-- Role badge --}}
        @if(Auth::user()->role === 'superadmin')
            <span style="background:#E3F2FD;color:#1565C0;font-size:11px;font-weight:700;padding:4px 10px;border-radius:20px;letter-spacing:.5px;">
                SUPERADMIN
            </span>
        @else
            <span style="background:#E8F5E9;color:#2E7D32;font-size:11px;font-weight:700;padding:4px 10px;border-radius:20px;letter-spacing:.5px;">
                ADMIN
            </span>
        @endif

        {{-- User name --}}
        <span style="font-size:13px;font-weight:600;color:var(--text-main);">
            {{ Auth::user()->name }}
        </span>

        {{-- Avatar --}}
        <div class="topbar-avatar" title="Profil">
            <i class="mdi mdi-account"></i>
        </div>

        {{-- Logout --}}
        <form method="POST" action="/logout" id="topbarLogout">
            @csrf
            <button type="submit" class="topbar-btn" title="Logout"
                style="background:none;cursor:pointer;"
                onclick="return confirm('Yakin ingin keluar?')">
                <i class="mdi mdi-logout"></i>
            </button>
        </form>
    </div>
</header>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const h = document.querySelector('h3');
        const topbarTitle = document.getElementById('topbarTitle');
        if (h && topbarTitle) topbarTitle.textContent = h.textContent.trim();
    });
</script>
