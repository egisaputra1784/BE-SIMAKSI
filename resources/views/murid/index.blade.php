@extends('layouts.master')
@section('content')

<div class="page-header-bar">
    <div>
        <h3>Data Murid</h3>
        <small>Kelola data siswa</small>
    </div>
    <div class="page-header-actions">
        <a href="/murid/export/excel" class="btn-success-custom">
            <i class="mdi mdi-microsoft-excel"></i> Export Excel
        </a>
        <a href="/murid/form" class="btn-primary-custom">
            <i class="mdi mdi-plus"></i> Tambah Murid
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header-custom">
        <span class="card-title-custom"><i class="mdi mdi-school-outline me-2" style="color:var(--primary);"></i>Daftar Murid</span>
        <input type="text" id="search" class="search-input" placeholder="Cari nama / email / NISN...">
    </div>
    <div style="overflow-x:auto;">
        <table class="simaksi-table">
            <thead>
                <tr>
                    <th width="5%">#</th>
                    <th>Nama Murid</th>
                    <th>Email</th>
                    <th>NISN</th>
                    <th width="140px" style="text-align:center;">Aksi</th>
                </tr>
            </thead>
            <tbody id="murid-table">
                <tr><td colspan="5" class="empty-state"><i class="mdi mdi-loading mdi-spin"></i><p>Memuat data...</p></td></tr>
            </tbody>
        </table>
    </div>
</div>

@endsection
@push('scripts')
<script>
    let allData = []

    function loadData() {
        fetch('/murid/data').then(r => r.json()).then(data => {
            allData = data; render(data);
        })
    }

    function render(data) {
        const tb = document.getElementById('murid-table')
        if (!data.length) {
            tb.innerHTML = `<tr><td colspan="5"><div class="empty-state"><i class="mdi mdi-account-off-outline"></i><p>Tidak ada data murid</p></div></td></tr>`
            return
        }
        tb.innerHTML = data.map((m, i) => `
            <tr>
                <td><span style="font-weight:600;color:var(--text-muted);">${i+1}</span></td>
                <td>
                    <div style="display:flex;align-items:center;gap:10px;">
                        <div style="width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,#E65100,#FF8F00);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="mdi mdi-account" style="color:#fff;font-size:16px;"></i>
                        </div>
                        <span style="font-weight:600;">${m.name}</span>
                    </div>
                </td>
                <td style="color:var(--text-muted);">${m.email}</td>
                <td><code style="background:var(--bg-page);padding:2px 8px;border-radius:4px;font-size:12px;">${m.nisn ?? '-'}</code></td>
                <td style="text-align:center;">
                    <button onclick="editData(${m.id})" class="btn-sm-warn"><i class="mdi mdi-pencil"></i> Edit</button>
                    <button onclick="hapus(${m.id})" class="btn-sm-danger"><i class="mdi mdi-delete"></i> Hapus</button>
                </td>
            </tr>`).join('')
    }

    function editData(id) { location.href = '/murid/form?id=' + id }

    function hapus(id) {
        if (!confirm('Yakin ingin menghapus murid ini?')) return
        fetch('/murid/' + id, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } })
            .then(() => { loadData(); showToast('Data murid berhasil dihapus'); })
    }

    document.getElementById('search').addEventListener('keyup', function() {
        const k = this.value.toLowerCase()
        render(allData.filter(m => m.name.toLowerCase().includes(k) || m.email.toLowerCase().includes(k) || (m.nisn||'').toLowerCase().includes(k)))
    })

    loadData()
</script>
@endpush
