@extends('layouts.master')
@section('content')

<div class="page-header-bar">
    <div><h3>Mata Pelajaran</h3><small>Kelola daftar mata pelajaran</small></div>
    <div class="page-header-actions">
        <a href="/mapel/export/excel" class="btn-success-custom"><i class="mdi mdi-microsoft-excel"></i> Export Excel</a>
        <a href="/mapel/form" class="btn-primary-custom"><i class="mdi mdi-plus"></i> Tambah Mapel</a>
    </div>
</div>

<div class="card">
    <div class="card-header-custom">
        <span class="card-title-custom"><i class="mdi mdi-book-open-page-variant-outline me-2" style="color:var(--primary);"></i>Daftar Mata Pelajaran</span>
        <input type="text" id="search" class="search-input" placeholder="Cari nama / kode...">
    </div>
    <div style="overflow-x:auto;">
        <table class="simaksi-table">
            <thead>
                <tr><th>#</th><th>Nama Mapel</th><th>Kode Mapel</th><th style="text-align:center;">Aksi</th></tr>
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
    let allData = []
    function loadData() {
        fetch('/mapel/data').then(r => r.json()).then(data => { allData = data; render(data); })
    }
    function render(data) {
        const tb = document.getElementById('table')
        if (!data.length) { tb.innerHTML = `<tr><td colspan="4"><div class="empty-state"><i class="mdi mdi-book-off-outline"></i><p>Tidak ada mata pelajaran</p></div></td></tr>`; return }
        tb.innerHTML = data.map((d, i) => `
            <tr>
                <td><span style="font-weight:600;color:var(--text-muted);">${i+1}</span></td>
                <td><span style="font-weight:600;">${d.nama_mapel}</span></td>
                <td><code style="background:var(--primary-soft);color:var(--primary);padding:3px 10px;border-radius:6px;font-size:12px;font-weight:700;">${d.kode_mapel}</code></td>
                <td style="text-align:center;">
                    <button onclick="edit(${d.id})" class="btn-sm-warn"><i class="mdi mdi-pencil"></i> Edit</button>
                    <button onclick="hapus(${d.id})" class="btn-sm-danger"><i class="mdi mdi-delete"></i> Hapus</button>
                </td>
            </tr>`).join('')
    }
    function edit(id) { location.href = '/mapel/form?id=' + id }
    function hapus(id) {
        if (!confirm('Hapus mata pelajaran ini?')) return
        fetch('/mapel/' + id, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } })
            .then(() => { loadData(); showToast('Mata pelajaran berhasil dihapus'); })
    }
    document.getElementById('search').addEventListener('keyup', function() {
        const k = this.value.toLowerCase()
        render(allData.filter(d => d.nama_mapel.toLowerCase().includes(k) || d.kode_mapel.toLowerCase().includes(k)))
    })
    loadData()
</script>
@endpush
