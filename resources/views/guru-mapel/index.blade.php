@extends('layouts.master')
@section('content')

<div class="page-header-bar">
    <div><h3>Guru Mapel</h3><small>Kelola penugasan guru pada mata pelajaran</small></div>
    <div class="page-header-actions">
        <a href="/guru-mapel/export/excel" class="btn-success-custom"><i class="mdi mdi-microsoft-excel"></i> Export Excel</a>
        <a href="/guru-mapel/form" class="btn-primary-custom"><i class="mdi mdi-plus"></i> Tambah</a>
    </div>
</div>

<div class="card">
    <div class="card-header-custom">
        <span class="card-title-custom"><i class="mdi mdi-book-education-outline me-2" style="color:var(--primary);"></i>Daftar Guru Mata Pelajaran</span>
    </div>
    <div style="overflow-x:auto;">
        <table class="simaksi-table">
            <thead>
                <tr><th>#</th><th>Nama Guru</th><th>Mata Pelajaran</th><th style="text-align:center;">Aksi</th></tr>
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
        fetch('/guru-mapel/data').then(r => r.json()).then(data => {
            const tb = document.getElementById('table')
            if (!data.length) { tb.innerHTML = `<tr><td colspan="4"><div class="empty-state"><i class="mdi mdi-book-education-outline"></i><p>Tidak ada data guru mapel</p></div></td></tr>`; return }
            tb.innerHTML = data.map((d, i) => `
                <tr>
                    <td><span style="font-weight:600;color:var(--text-muted);">${i+1}</span></td>
                    <td>
                        <div style="display:flex;align-items:center;gap:8px;">
                            <div style="width:28px;height:28px;border-radius:50%;background:linear-gradient(135deg,#1976D2,#00ACC1);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <i class="mdi mdi-account-tie" style="color:#fff;font-size:14px;"></i>
                            </div>
                            <span style="font-weight:600;">${d.guru?.name ?? '-'}</span>
                        </div>
                    </td>
                    <td><span class="badge-primary">${d.mapel?.nama_mapel ?? '-'}</span></td>
                    <td style="text-align:center;">
                        <button onclick="edit(${d.id})" class="btn-sm-warn"><i class="mdi mdi-pencil"></i> Edit</button>
                        <button onclick="hapus(${d.id})" class="btn-sm-danger"><i class="mdi mdi-delete"></i> Hapus</button>
                    </td>
                </tr>`).join('')
        })
    }
    function edit(id) { location.href = '/guru-mapel/form?id=' + id }
    function hapus(id) {
        if (!confirm('Hapus data ini?')) return
        fetch('/guru-mapel/' + id, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } })
            .then(() => { loadData(); showToast('Data berhasil dihapus'); })
    }
    loadData()
</script>
@endpush
