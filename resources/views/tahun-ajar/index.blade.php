@extends('layouts.master')
@section('content')

<div class="page-header-bar">
    <div><h3>Tahun Ajar</h3><small>Kelola tahun ajaran sekolah</small></div>
    <div class="page-header-actions">
        <a href="/tahun-ajar/export/excel" class="btn-success-custom"><i class="mdi mdi-microsoft-excel"></i> Export Excel</a>
        <a href="/tahun-ajar/form" class="btn-primary-custom"><i class="mdi mdi-plus"></i> Tambah</a>
    </div>
</div>

<div class="card">
    <div class="card-header-custom">
        <span class="card-title-custom"><i class="mdi mdi-calendar-clock-outline me-2" style="color:var(--primary);"></i>Daftar Tahun Ajar</span>
    </div>
    <div style="overflow-x:auto;">
        <table class="simaksi-table">
            <thead>
                <tr><th>#</th><th>Nama Tahun Ajar</th><th>Status</th><th style="text-align:center;">Aksi</th></tr>
            </thead>
            <tbody id="table">
                <tr><td colspan="4"><div class="empty-state"><i class="mdi mdi-loading mdi-spin"></i><p>Memuat data...</p></div></td></tr>
            </tbody>
        </table>
    </div>
</div>

@endsection
@push('scripts')
<script>
    function loadData() {
        fetch('/tahun-ajar/data').then(r => r.json()).then(data => {
            const tb = document.getElementById('table')
            if (!data.length) { tb.innerHTML = `<tr><td colspan="4"><div class="empty-state"><i class="mdi mdi-calendar-remove-outline"></i><p>Tidak ada data tahun ajar</p></div></td></tr>`; return }
            tb.innerHTML = data.map((d, i) => `
                <tr>
                    <td><span style="font-weight:600;color:var(--text-muted);">${i+1}</span></td>
                    <td><span style="font-weight:600;">${d.nama}</span></td>
                    <td>${d.aktif ? '<span class="badge-success">● Aktif</span>' : '<span class="badge-warning">Nonaktif</span>'}</td>
                    <td style="text-align:center;">
                        <button onclick="edit(${d.id})" class="btn-sm-warn"><i class="mdi mdi-pencil"></i> Edit</button>
                        <button onclick="hapus(${d.id})" class="btn-sm-danger"><i class="mdi mdi-delete"></i> Hapus</button>
                    </td>
                </tr>`).join('')
        })
    }
    function edit(id) { location.href = '/tahun-ajar/form?id=' + id }
    function hapus(id) {
        if (!confirm('Hapus tahun ajar ini?')) return
        fetch('/tahun-ajar/' + id, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } })
            .then(() => { loadData(); showToast('Tahun ajar berhasil dihapus'); })
    }
    loadData()
</script>
@endpush
