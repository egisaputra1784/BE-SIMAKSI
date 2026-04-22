@extends('layouts.master')
@section('content')

<div class="page-header-bar">
    <div><h3>Kelas</h3><small>Kelola data kelas sekolah</small></div>
    <div class="page-header-actions">
        <a href="/kelas/export/excel" class="btn-success-custom"><i class="mdi mdi-microsoft-excel"></i> Export Excel</a>
        <a href="/kelas/form" class="btn-primary-custom"><i class="mdi mdi-plus"></i> Tambah Kelas</a>
    </div>
</div>

<div class="card">
    <div class="card-header-custom">
        <span class="card-title-custom"><i class="mdi mdi-google-classroom me-2" style="color:var(--primary);"></i>Daftar Kelas</span>
    </div>
    <div style="overflow-x:auto;">
        <table class="simaksi-table">
            <thead>
                <tr><th>#</th><th>Nama Kelas</th><th>Tahun Ajar</th><th>Wali Kelas</th><th style="text-align:center;">Aksi</th></tr>
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
        fetch('/kelas/data').then(r => r.json()).then(data => {
            const tb = document.getElementById('table')
            if (!data.length) { tb.innerHTML = `<tr><td colspan="5"><div class="empty-state"><i class="mdi mdi-google-classroom"></i><p>Tidak ada data kelas</p></div></td></tr>`; return }
            tb.innerHTML = data.map((d, i) => `
                <tr>
                    <td><span style="font-weight:600;color:var(--text-muted);">${i+1}</span></td>
                    <td><span style="font-weight:700;color:var(--primary);">${d.nama_kelas}</span></td>
                    <td><span class="badge-primary">${d.tahun_ajar?.nama ?? '-'}</span></td>
                    <td>${d.wali_guru?.name ?? '<span style="color:var(--text-muted);">Belum ditentukan</span>'}</td>
                    <td style="text-align:center;">
                        <button onclick="edit(${d.id})" class="btn-sm-warn"><i class="mdi mdi-pencil"></i> Edit</button>
                        <button onclick="hapus(${d.id})" class="btn-sm-danger"><i class="mdi mdi-delete"></i> Hapus</button>
                    </td>
                </tr>`).join('')
        })
    }
    function edit(id) { location.href = '/kelas/form?id=' + id }
    function hapus(id) {
        if (!confirm('Hapus kelas ini?')) return
        fetch('/kelas/' + id, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } })
            .then(() => { loadData(); showToast('Kelas berhasil dihapus'); })
    }
    loadData()
</script>
@endpush
