@extends('layouts.master')
@section('content')

<div class="page-header-bar">
    <div><h3>Anggota Kelas</h3><small>Kelola keanggotaan murid per kelas</small></div>
    <div class="page-header-actions">
        <a href="/anggota-kelas/export/excel" class="btn-success-custom"><i class="mdi mdi-microsoft-excel"></i> Export Excel</a>
        <a href="/anggota-kelas/form" class="btn-primary-custom"><i class="mdi mdi-plus"></i> Tambah Anggota</a>
    </div>
</div>

<div class="card">
    <div class="card-header-custom">
        <span class="card-title-custom"><i class="mdi mdi-account-multiple-plus-outline me-2" style="color:var(--primary);"></i>Daftar Anggota Kelas</span>
    </div>
    <div style="overflow-x:auto;">
        <table class="simaksi-table">
            <thead>
                <tr><th>#</th><th>Kelas</th><th>Nama Murid</th><th>NISN</th><th style="text-align:center;">Aksi</th></tr>
            </thead>
            <tbody id="table">
                <tr><td colspan="5"><div class="empty-state"><i class="mdi mdi-loading mdi-spin"></i><p>Memuat data...</p></div></td></tr>
            </tbody>
        </table>
    </div>
</div>

@endsection
@push('scripts')
<script>
    function loadData() {
        fetch('/anggota-kelas/data').then(r => r.json()).then(data => {
            const tb = document.getElementById('table')
            if (!data.length) { tb.innerHTML = `<tr><td colspan="5"><div class="empty-state"><i class="mdi mdi-account-multiple-outline"></i><p>Tidak ada anggota kelas</p></div></td></tr>`; return }
            tb.innerHTML = data.map((d, i) => `
                <tr>
                    <td><span style="font-weight:600;color:var(--text-muted);">${i+1}</span></td>
                    <td><span style="font-weight:700;color:var(--primary);">${d.kelas?.nama_kelas ?? '-'}</span></td>
                    <td>
                        <div style="display:flex;align-items:center;gap:8px;">
                            <div style="width:28px;height:28px;border-radius:50%;background:linear-gradient(135deg,#1976D2,#00ACC1);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <i class="mdi mdi-account" style="color:#fff;font-size:14px;"></i>
                            </div>
                            <span style="font-weight:600;">${d.murid?.name ?? '-'}</span>
                        </div>
                    </td>
                    <td><code style="background:var(--bg-page);padding:2px 8px;border-radius:4px;font-size:12px;">${d.murid?.nisn ?? '-'}</code></td>
                    <td style="text-align:center;">
                        <button onclick="hapus(${d.id})" class="btn-sm-danger"><i class="mdi mdi-delete"></i> Hapus</button>
                    </td>
                </tr>`).join('')
        })
    }
    function hapus(id) {
        if (!confirm('Hapus anggota kelas ini?')) return
        fetch('/anggota-kelas/' + id, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } })
            .then(() => { loadData(); showToast('Anggota kelas berhasil dihapus'); })
    }
    loadData()
</script>
@endpush
