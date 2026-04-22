@extends('layouts.master')
@section('content')

<div class="page-header-bar">
    <div><h3>Jadwal</h3><small>Kelola jadwal pelajaran</small></div>
    <div class="page-header-actions">
        <a href="/jadwal/export/excel" class="btn-success-custom"><i class="mdi mdi-microsoft-excel"></i> Export Excel</a>
        <a href="/jadwal/form" class="btn-primary-custom"><i class="mdi mdi-plus"></i> Tambah Jadwal</a>
    </div>
</div>

<div class="card">
    <div class="card-header-custom">
        <span class="card-title-custom"><i class="mdi mdi-timetable me-2" style="color:var(--primary);"></i>Daftar Jadwal</span>
    </div>
    <div style="overflow-x:auto;">
        <table class="simaksi-table">
            <thead>
                <tr><th>#</th><th>Kelas</th><th>Mata Pelajaran</th><th>Guru</th><th>Hari</th><th>Jam</th><th style="text-align:center;">Aksi</th></tr>
            </thead>
            <tbody id="table">
                <tr><td colspan="7"><div class="empty-state"><i class="mdi mdi-loading mdi-spin"></i><p>Memuat data...</p></div></td></tr>
            </tbody>
        </table>
    </div>
</div>

@endsection
@push('scripts')
<script>
    const hariColor = { senin:'#1565C0', selasa:'#6A1B9A', rabu:'#1B5E20', kamis:'#E65100', jumat:'#B71C1C' }
    function loadData() {
        fetch('/jadwal/data').then(r => r.json()).then(data => {
            const tb = document.getElementById('table')
            if (!data.length) { tb.innerHTML = `<tr><td colspan="7"><div class="empty-state"><i class="mdi mdi-timetable"></i><p>Tidak ada data jadwal</p></div></td></tr>`; return }
            tb.innerHTML = data.map((d, i) => {
                const h = d.hari?.toLowerCase()
                const c = hariColor[h] || '#607D8B'
                return `<tr>
                    <td><span style="font-weight:600;color:var(--text-muted);">${i+1}</span></td>
                    <td><span style="font-weight:700;color:var(--primary);">${d.kelas?.nama_kelas ?? '-'}</span></td>
                    <td>${d.mapel?.nama_mapel ?? '-'}</td>
                    <td>${d.guru?.name ?? '-'}</td>
                    <td><span style="background:${c}22;color:${c};padding:3px 10px;border-radius:20px;font-size:12px;font-weight:700;">${d.hari?.charAt(0).toUpperCase()+d.hari?.slice(1)}</span></td>
                    <td style="font-size:13px;color:var(--text-muted);">${d.jam_mulai} – ${d.jam_selesai}</td>
                    <td style="text-align:center;">
                        <button onclick="edit(${d.id})" class="btn-sm-warn"><i class="mdi mdi-pencil"></i> Edit</button>
                        <button onclick="hapus(${d.id})" class="btn-sm-danger"><i class="mdi mdi-delete"></i> Hapus</button>
                    </td>
                </tr>`
            }).join('')
        })
    }
    function edit(id) { location.href = '/jadwal/form?id=' + id }
    function hapus(id) {
        if (!confirm('Hapus jadwal ini?')) return
        fetch('/jadwal/' + id, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } })
            .then(() => { loadData(); showToast('Jadwal berhasil dihapus'); })
    }
    loadData()
</script>
@endpush
