@extends('layouts.master')

@section('content')

    {{-- PAGE HEADER --}}
    <div class="page-header-bar">
        <div>
            <h3>Dashboard</h3>
            <small>Selamat datang di Panel Kontrol SIMAKSI</small>
        </div>
        <div class="page-header-actions">
            <span style="font-size:13px;color:var(--text-muted);">
                <i class="mdi mdi-clock-outline me-1"></i>
                {{ now()->format('l, d F Y') }}
            </span>
        </div>
    </div>

    {{-- STAT CARDS --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-sm-6">
            <div class="stat-card">
                <div>
                    <div class="stat-label">Total Murid</div>
                    <div class="stat-value">{{ $totalMurid }}</div>
                </div>
                <div class="stat-icon" style="background:#E3F2FD;">
                    <i class="mdi mdi-account-group" style="color:#1565C0;"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="stat-card">
                <div>
                    <div class="stat-label">Total Guru</div>
                    <div class="stat-value">{{ $totalGuru }}</div>
                </div>
                <div class="stat-icon" style="background:#E8F5E9;">
                    <i class="mdi mdi-teach" style="color:#2E7D32;"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="stat-card">
                <div>
                    <div class="stat-label">Total Kelas</div>
                    <div class="stat-value">{{ $totalKelas }}</div>
                </div>
                <div class="stat-icon" style="background:#FFF8E1;">
                    <i class="mdi mdi-google-classroom" style="color:#E65100;"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="stat-card">
                <div>
                    <div class="stat-label">Sesi Absen Hari Ini</div>
                    <div class="stat-value">{{ $totalSesiHariIni }}</div>
                </div>
                <div class="stat-icon" style="background:#FCE4EC;">
                    <i class="mdi mdi-qrcode-scan" style="color:#C62828;"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- ROW 2 --}}
    <div class="row g-3">

        {{-- Tahun Ajar Aktif --}}
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header-custom">
                    <span class="card-title-custom"><i class="mdi mdi-calendar-check-outline me-2" style="color:var(--primary);"></i>Tahun Ajar Aktif</span>
                </div>
                <div class="card-body p-4">
                    @if ($tahunAjar)
                        <div style="font-size:22px;font-weight:800;color:var(--primary);margin-bottom:4px;">
                            {{ $tahunAjar->nama }}
                        </div>
                        <span class="badge-success">● Aktif</span>
                    @else
                        <div style="font-size:14px;color:var(--danger);font-weight:600;">Belum ada tahun ajar aktif</div>
                    @endif

                    <hr style="margin:18px 0;border-color:var(--border);">

                    <div style="display:flex;flex-direction:column;gap:10px;">
                        <div style="display:flex;align-items:center;justify-content:space-between;font-size:13.5px;">
                            <span style="color:var(--text-muted);"><i class="mdi mdi-google-classroom me-1"></i>Total Kelas</span>
                            <span style="font-weight:700;color:var(--text-main);">{{ $totalKelas }}</span>
                        </div>
                        <div style="display:flex;align-items:center;justify-content:space-between;font-size:13.5px;">
                            <span style="color:var(--text-muted);"><i class="mdi mdi-school-outline me-1"></i>Total Murid</span>
                            <span style="font-weight:700;color:var(--text-main);">{{ $totalMurid }}</span>
                        </div>
                        <div style="display:flex;align-items:center;justify-content:space-between;font-size:13.5px;">
                            <span style="color:var(--text-muted);"><i class="mdi mdi-account-tie me-1"></i>Total Guru</span>
                            <span style="font-weight:700;color:var(--text-main);">{{ $totalGuru }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Trend Chart --}}
        <div class="col-lg-8">
            <div class="card h-100">
                <div class="card-header-custom">
                    <span class="card-title-custom"><i class="mdi mdi-chart-line me-2" style="color:var(--primary);"></i>Trend Murid & Guru</span>
                </div>
                <div class="card-body p-4">
                    <canvas id="trendChart" height="90"></canvas>
                </div>
            </div>
        </div>

    </div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('trendChart');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($trend['labels']) !!},
            datasets: [{
                label: 'Murid',
                data: {!! json_encode($trend['murid']) !!},
                borderColor: '#1565C0',
                backgroundColor: 'rgba(21,101,192,0.08)',
                tension: 0.4, fill: true, pointRadius: 4,
                pointBackgroundColor: '#1565C0'
            },{
                label: 'Guru',
                data: {!! json_encode($trend['guru']) !!},
                borderColor: '#00ACC1',
                backgroundColor: 'rgba(0,172,193,0.08)',
                tension: 0.4, fill: true, pointRadius: 4,
                pointBackgroundColor: '#00ACC1'
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'top', labels: { font: { family: 'Inter', size: 12 }, usePointStyle: true } } },
            scales: {
                y: { beginAtZero: true, grid: { color: '#f0f0f0' }, ticks: { font: { family: 'Inter' } } },
                x: { grid: { display: false }, ticks: { font: { family: 'Inter' } } }
            }
        }
    });
</script>
@endpush
