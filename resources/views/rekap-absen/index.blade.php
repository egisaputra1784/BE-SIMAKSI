@extends('layouts.master')
@section('content')

<div class="page-header-bar">
    <div>
        <h3>Rekap Absensi</h3>
        <small>Laporan kehadiran siswa berdasarkan filter periode & kelas</small>
    </div>
    <div class="page-header-actions">
        <button id="btnExport" class="btn-success-custom" onclick="exportExcel()">
            <i class="mdi mdi-microsoft-excel"></i> Export Excel
        </button>
    </div>
</div>

{{-- FILTER CARD --}}
<div class="card mb-4">
    <div class="card-header-custom">
        <span class="card-title-custom"><i class="mdi mdi-filter-outline me-2" style="color:var(--primary);"></i>Filter Data</span>
        <button onclick="resetFilter()" style="background:none;border:none;color:var(--text-muted);font-size:13px;cursor:pointer;font-weight:600;">
            <i class="mdi mdi-refresh"></i> Reset
        </button>
    </div>
    <div style="padding:18px 20px;">
        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label-custom">Tahun Ajar</label>
                <select id="f_tahun" class="form-control-custom" onchange="onTahunChange()">
                    <option value="">— Semua Tahun Ajar —</option>
                    @foreach($tahunAjars as $ta)
                        <option value="{{ $ta->id }}" {{ $ta->aktif ? 'selected' : '' }}>
                            {{ $ta->nama }} {{ $ta->aktif ? '(Aktif)' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label-custom">Kelas</label>
                <select id="f_kelas" class="form-control-custom" onchange="loadData()">
                    <option value="">— Semua Kelas —</option>
                    @foreach($kelasList as $k)
                        <option value="{{ $k->id }}" data-ta="{{ $k->tahun_ajar_id }}">{{ $k->nama_kelas }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label-custom">Mata Pelajaran</label>
                <select id="f_mapel" class="form-control-custom" onchange="loadData()">
                    <option value="">— Semua Mapel —</option>
                    @foreach($mapelList as $m)
                        <option value="{{ $m->id }}">{{ $m->nama_mapel }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label-custom">Tipe Tampilan</label>
                <select id="f_view" class="form-control-custom" onchange="switchView()">
                    <option value="detail">Detail Per Sesi</option>
                    <option value="summary">Ringkasan Per Murid</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label-custom">Tanggal Dari</label>
                <input type="date" id="f_dari" class="form-control-custom" onchange="loadData()">
            </div>
            <div class="col-md-3">
                <label class="form-label-custom">Tanggal Sampai</label>
                <input type="date" id="f_sampai" class="form-control-custom" onchange="loadData()">
            </div>

            <div class="col-md-3">
                <label class="form-label-custom">Status Kehadiran</label>
                <select id="f_status" class="form-control-custom" onchange="filterLocal()">
                    <option value="">— Semua Status —</option>
                    <option value="hadir">Hadir</option>
                    <option value="izin">Izin</option>
                    <option value="sakit">Sakit</option>
                    <option value="alpha">Alpha</option>
                    <option value="terlambat">Terlambat</option>
                </select>
            </div>
        </div>
    </div>
</div>

{{-- STAT MINI CARDS --}}
<div class="row g-3 mb-4" id="statCards" style="display:none!important;">
    <div class="col">
        <div style="background:#fff;border:1px solid var(--border);border-radius:10px;padding:14px 18px;text-align:center;box-shadow:var(--card-shadow);">
            <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--text-muted);">Total Sesi</div>
            <div id="st_total" style="font-size:24px;font-weight:800;color:var(--text-main);">0</div>
        </div>
    </div>
    <div class="col">
        <div style="background:#E8F5E9;border:1px solid #C8E6C9;border-radius:10px;padding:14px 18px;text-align:center;">
            <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#2E7D32;">Hadir</div>
            <div id="st_hadir" style="font-size:24px;font-weight:800;color:#2E7D32;">0</div>
        </div>
    </div>
    <div class="col">
        <div style="background:#E3F2FD;border:1px solid #BBDEFB;border-radius:10px;padding:14px 18px;text-align:center;">
            <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#1565C0;">Izin</div>
            <div id="st_izin" style="font-size:24px;font-weight:800;color:#1565C0;">0</div>
        </div>
    </div>
    <div class="col">
        <div style="background:#FFF8E1;border:1px solid #FFE082;border-radius:10px;padding:14px 18px;text-align:center;">
            <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#E65100;">Sakit</div>
            <div id="st_sakit" style="font-size:24px;font-weight:800;color:#E65100;">0</div>
        </div>
    </div>
    <div class="col">
        <div style="background:#FFEBEE;border:1px solid #FFCDD2;border-radius:10px;padding:14px 18px;text-align:center;">
            <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#C62828;">Alpha</div>
            <div id="st_alpha" style="font-size:24px;font-weight:800;color:#C62828;">0</div>
        </div>
    </div>
    <div class="col">
        <div style="background:#FFF3E0;border:1px solid #FFCC80;border-radius:10px;padding:14px 18px;text-align:center;">
            <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#BF360C;">Terlambat</div>
            <div id="st_terlambat" style="font-size:24px;font-weight:800;color:#BF360C;">0</div>
        </div>
    </div>
</div>

{{-- TABLE DETAIL --}}
<div class="card" id="viewDetail">
    <div class="card-header-custom">
        <span class="card-title-custom"><i class="mdi mdi-table me-2" style="color:var(--primary);"></i>Detail Absensi</span>
        <span id="rowCount" style="font-size:12px;color:var(--text-muted);font-weight:600;"></span>
    </div>
    <div style="overflow-x:auto;">
        <table class="simaksi-table">
            <thead>
                <tr>
                    <th width="40px">#</th>
                    <th>Nama Murid</th>
                    <th>NISN</th>
                    <th>Kelas</th>
                    <th>Tahun Ajar</th>
                    <th>Mapel</th>
                    <th>Guru</th>
                    <th>Tanggal</th>
                    <th>Status</th>
                    <th>Waktu Scan</th>
                </tr>
            </thead>
            <tbody id="detailTable">
                <tr><td colspan="10"><div class="empty-state"><i class="mdi mdi-magnify"></i><p>Pilih filter dan data akan tampil di sini</p></div></td></tr>
            </tbody>
        </table>
    </div>
</div>

{{-- TABLE SUMMARY --}}
<div class="card" id="viewSummary" style="display:none;">
    <div class="card-header-custom">
        <span class="card-title-custom"><i class="mdi mdi-account-group me-2" style="color:var(--primary);"></i>Ringkasan Per Murid</span>
        <span id="rowCountSummary" style="font-size:12px;color:var(--text-muted);font-weight:600;"></span>
    </div>
    <div style="overflow-x:auto;">
        <table class="simaksi-table">
            <thead>
                <tr>
                    <th width="40px">#</th>
                    <th>Nama Murid</th>
                    <th>NISN</th>
                    <th style="text-align:center;color:#2E7D32;">Hadir</th>
                    <th style="text-align:center;color:#1565C0;">Izin</th>
                    <th style="text-align:center;color:#E65100;">Sakit</th>
                    <th style="text-align:center;color:#C62828;">Alpha</th>
                    <th style="text-align:center;color:#BF360C;">Terlambat</th>
                    <th style="text-align:center;">Total</th>
                    <th style="text-align:center;">% Hadir</th>
                </tr>
            </thead>
            <tbody id="summaryTable">
                <tr><td colspan="10"><div class="empty-state"><i class="mdi mdi-magnify"></i><p>Pilih filter dan data akan tampil di sini</p></div></td></tr>
            </tbody>
        </table>
    </div>
</div>

@endsection

@push('scripts')
<script>
    let rawData    = []
    let rawSummary = []
    let isLoading  = false

    const STATUS_STYLE = {
        hadir:     { bg: '#E8F5E9', color: '#2E7D32' },
        izin:      { bg: '#E3F2FD', color: '#1565C0' },
        sakit:     { bg: '#FFF8E1', color: '#E65100' },
        alpha:     { bg: '#FFEBEE', color: '#C62828' },
        terlambat: { bg: '#FFF3E0', color: '#BF360C' },
    }


    function getParams() {
        return new URLSearchParams({
            tahun_ajar_id: document.getElementById('f_tahun').value,
            kelas_id:      document.getElementById('f_kelas').value,
            mapel_id:      document.getElementById('f_mapel').value,
            dari:          document.getElementById('f_dari').value,
            sampai:        document.getElementById('f_sampai').value,
        }).toString()
    }

    function onTahunChange() {
        const taId = document.getElementById('f_tahun').value
        // Filter kelas dropdown by tahun ajar
        document.querySelectorAll('#f_kelas option[data-ta]').forEach(opt => {
            opt.style.display = (!taId || opt.dataset.ta == taId) ? '' : 'none'
        })
        document.getElementById('f_kelas').value = ''
        loadData()
    }

    function switchView() {
        const v = document.getElementById('f_view').value
        document.getElementById('viewDetail').style.display  = v === 'detail'  ? '' : 'none'
        document.getElementById('viewSummary').style.display = v === 'summary' ? '' : 'none'
    }

    function filterLocal() {
        const status = document.getElementById('f_status').value.toLowerCase()

        // Filter detail by status
        const filtered = rawData.filter(d => !status || d.status?.toLowerCase() === status)
        renderDetail(filtered)
        updateStats(filtered)
    }

    function updateStats(data) {
        const stats = document.getElementById('statCards')
        stats.style.removeProperty('display')
        document.getElementById('st_total').textContent    = data.length
        document.getElementById('st_hadir').textContent    = data.filter(d => d.status === 'hadir').length
        document.getElementById('st_izin').textContent     = data.filter(d => d.status === 'izin').length
        document.getElementById('st_sakit').textContent    = data.filter(d => d.status === 'sakit').length
        document.getElementById('st_alpha').textContent    = data.filter(d => d.status === 'alpha').length
        document.getElementById('st_terlambat').textContent= data.filter(d => d.status === 'terlambat').length
    }

    function renderDetail(data) {
        const tb = document.getElementById('detailTable')
        document.getElementById('rowCount').textContent = data.length + ' data'
        if (!data.length) {
            tb.innerHTML = `<tr><td colspan="10"><div class="empty-state"><i class="mdi mdi-table-off"></i><p>Tidak ada data sesuai filter</p></div></td></tr>`
            return
        }
        tb.innerHTML = data.map((d, i) => {
            const ss = STATUS_STYLE[d.status] || { bg: '#f0f0f0', color: '#333' }
            return `<tr>
                <td style="font-weight:600;color:var(--text-muted);">${i+1}</td>
                <td>
                    <div style="display:flex;align-items:center;gap:8px;">
                        <div style="width:28px;height:28px;border-radius:50%;background:linear-gradient(135deg,#1976D2,#00ACC1);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="mdi mdi-account" style="color:#fff;font-size:13px;"></i>
                        </div>
                        <span style="font-weight:600;">${d.nama_murid}</span>
                    </div>
                </td>
                <td><code style="background:var(--bg-page);padding:2px 7px;border-radius:4px;font-size:11px;">${d.nisn}</code></td>
                <td><span style="font-weight:700;color:var(--primary);">${d.kelas}</span></td>
                <td style="font-size:12px;color:var(--text-muted);">${d.tahun_ajar}</td>
                <td>${d.mapel}</td>
                <td style="font-size:12.5px;color:var(--text-muted);">${d.guru}</td>
                <td style="font-size:13px;font-weight:600;">${d.tanggal}</td>
                <td><span style="background:${ss.bg};color:${ss.color};padding:3px 9px;border-radius:20px;font-size:11px;font-weight:700;">${d.status?.toUpperCase()}</span></td>
                <td style="font-size:12px;color:var(--text-muted);">${d.waktu_scan ? d.waktu_scan.substring(0,16) : '-'}</td>
            </tr>`
        }).join('')
    }

    function renderSummary(data) {
        const tb = document.getElementById('summaryTable')
        document.getElementById('rowCountSummary').textContent = data.length + ' murid'
        if (!data.length) {
            tb.innerHTML = `<tr><td colspan="10"><div class="empty-state"><i class="mdi mdi-account-off-outline"></i><p>Tidak ada data</p></div></td></tr>`
            return
        }
        tb.innerHTML = data.map((d, i) => {
            const pct = d.pct_hadir
            const pctColor = pct >= 80 ? '#2E7D32' : pct >= 60 ? '#E65100' : '#C62828'
            const pctBg    = pct >= 80 ? '#E8F5E9'  : pct >= 60 ? '#FFF8E1'  : '#FFEBEE'
            return `<tr>
                <td style="font-weight:600;color:var(--text-muted);">${i+1}</td>
                <td>
                    <div style="display:flex;align-items:center;gap:8px;">
                        <div style="width:28px;height:28px;border-radius:50%;background:linear-gradient(135deg,#1976D2,#00ACC1);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="mdi mdi-account" style="color:#fff;font-size:13px;"></i>
                        </div>
                        <span style="font-weight:600;">${d.nama_murid}</span>
                    </div>
                </td>
                <td><code style="background:var(--bg-page);padding:2px 7px;border-radius:4px;font-size:11px;">${d.nisn}</code></td>
                <td style="text-align:center;"><span style="background:#E8F5E9;color:#2E7D32;padding:3px 10px;border-radius:20px;font-weight:700;">${d.hadir}</span></td>
                <td style="text-align:center;"><span style="background:#E3F2FD;color:#1565C0;padding:3px 10px;border-radius:20px;font-weight:700;">${d.izin}</span></td>
                <td style="text-align:center;"><span style="background:#FFF8E1;color:#E65100;padding:3px 10px;border-radius:20px;font-weight:700;">${d.sakit}</span></td>
                <td style="text-align:center;"><span style="background:#FFEBEE;color:#C62828;padding:3px 10px;border-radius:20px;font-weight:700;">${d.alpha}</span></td>
                <td style="text-align:center;"><span style="background:#FFF3E0;color:#BF360C;padding:3px 10px;border-radius:20px;font-weight:700;">${d.terlambat}</span></td>
                <td style="text-align:center;font-weight:700;">${d.total}</td>
                <td style="text-align:center;">
                    <div style="display:flex;align-items:center;gap:6px;justify-content:center;">
                        <div style="flex:1;max-width:60px;background:#eee;border-radius:10px;height:6px;overflow:hidden;">
                            <div style="width:${pct}%;height:100%;background:${pctColor};border-radius:10px;"></div>
                        </div>
                        <span style="background:${pctBg};color:${pctColor};padding:2px 8px;border-radius:20px;font-size:11px;font-weight:700;">${pct}%</span>
                    </div>
                </td>
            </tr>`
        }).join('')
    }

    function loadData() {
        if (isLoading) return
        isLoading = true

        const params  = getParams()
        const tbody   = document.getElementById('detailTable')
        const tbody2  = document.getElementById('summaryTable')
        tbody.innerHTML  = `<tr><td colspan="10"><div class="empty-state"><i class="mdi mdi-loading mdi-spin"></i><p>Memuat data...</p></div></td></tr>`
        tbody2.innerHTML = `<tr><td colspan="10"><div class="empty-state"><i class="mdi mdi-loading mdi-spin"></i><p>Memuat data...</p></div></td></tr>`

        Promise.all([
            fetch('/rekap-absen/data?' + params).then(r => r.json()),
            fetch('/rekap-absen/summary?' + params).then(r => r.json()),
        ]).then(([detail, summary]) => {
            rawData    = detail
            rawSummary = summary
            filterLocal()
            renderSummary(summary)
            isLoading = false
        }).catch(() => {
            tbody.innerHTML = `<tr><td colspan="10"><div class="empty-state"><i class="mdi mdi-alert-circle-outline"></i><p>Gagal memuat data</p></div></td></tr>`
            isLoading = false
        })
    }

    function exportExcel() {
        const params = getParams()
        window.location.href = '/rekap-absen/export?' + params
    }

    function resetFilter() {
        document.getElementById('f_tahun').value  = ''
        document.getElementById('f_kelas').value  = ''
        document.getElementById('f_mapel').value  = ''
        document.getElementById('f_dari').value   = ''
        document.getElementById('f_sampai').value = ''
        document.getElementById('f_status').value = ''
        document.getElementById('statCards').style.display = 'none'
        rawData = []; rawSummary = []
        document.getElementById('detailTable').innerHTML  = `<tr><td colspan="10"><div class="empty-state"><i class="mdi mdi-magnify"></i><p>Pilih filter dan data akan tampil di sini</p></div></td></tr>`
        document.getElementById('summaryTable').innerHTML = `<tr><td colspan="10"><div class="empty-state"><i class="mdi mdi-magnify"></i><p>Pilih filter dan data akan tampil di sini</p></div></td></tr>`
        document.getElementById('rowCount').textContent = ''
        document.getElementById('rowCountSummary').textContent = ''
    }

    // Auto-load with active tahun ajar
    window.addEventListener('DOMContentLoaded', function () {
        const activeTA = document.querySelector('#f_tahun option[selected]')
        if (activeTA) {
            onTahunChange()
            loadData()
        }
    })
</script>
@endpush
