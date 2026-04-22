@extends('layouts.master')
@section('content')

<div class="page-header-bar">
    <div><h3>Point Rules</h3><small>Kelola aturan poin kehadiran</small></div>
    <div class="page-header-actions">
        <a href="/point-rules/export/excel" class="btn-success-custom"><i class="mdi mdi-microsoft-excel"></i> Export Excel</a>
        <a href="/point-rules/form" class="btn-primary-custom"><i class="mdi mdi-plus"></i> Tambah Rule</a>
    </div>
</div>

<div class="card">
    <div class="card-header-custom">
        <span class="card-title-custom"><i class="mdi mdi-star-circle-outline me-2" style="color:var(--primary);"></i>Daftar Peraturan Poin</span>
    </div>
    <div style="overflow-x:auto;">
        <table class="simaksi-table">
            <thead>
                <tr><th>#</th><th>Nama Rule</th><th>Target Role</th><th>Kondisi</th><th>Point Modifier</th><th style="text-align:center;">Aksi</th></tr>
            </thead>
            <tbody id="table">
                <tr><td colspan="6"><div class="empty-state"><i class="mdi mdi-loading mdi-spin"></i><p>Memuat data...</p></div></td></tr>
            </tbody>
        </table>
    </div>
</div>

@endsection
@push('scripts')
<script>
    function formatCond(d) {
        if (d.condition_type === 'ALPHA') return '<span class="badge-danger">Alpha</span>'
        if (d.min_value !== null && d.max_value !== null) return `${d.min_value} – ${d.max_value} menit`
        if (d.min_value !== null) return `> ${d.min_value} menit`
        if (d.max_value !== null) return `< ${d.max_value} menit`
        return '-'
    }
    function loadData() {
        fetch('/point-rules/data').then(r => r.json()).then(data => {
            const tb = document.getElementById('table')
            if (!data.length) { tb.innerHTML = `<tr><td colspan="6"><div class="empty-state"><i class="mdi mdi-star-off-outline"></i><p>Tidak ada rule</p></div></td></tr>`; return }
            tb.innerHTML = data.map((d, i) => `
                <tr>
                    <td><span style="font-weight:600;color:var(--text-muted);">${i+1}</span></td>
                    <td><span style="font-weight:600;">${d.rule_name}</span></td>
                    <td><span class="badge-primary">${d.target_role}</span></td>
                    <td>${formatCond(d)}</td>
                    <td>
                        <span style="font-size:15px;font-weight:800;color:${d.point_modifier>=0?'#2E7D32':'#C62828'};">
                            ${d.point_modifier >= 0 ? '+' : ''}${d.point_modifier}
                        </span>
                    </td>
                    <td style="text-align:center;">
                        <button onclick="edit(${d.id})" class="btn-sm-warn"><i class="mdi mdi-pencil"></i> Edit</button>
                        <button onclick="hapus(${d.id})" class="btn-sm-danger"><i class="mdi mdi-delete"></i> Hapus</button>
                    </td>
                </tr>`).join('')
        })
    }
    function edit(id) { location.href = '/point-rules/form?id=' + id }
    function hapus(id) {
        if (!confirm('Hapus rule ini?')) return
        fetch('/point-rules/' + id, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } })
            .then(() => { loadData(); showToast('Rule berhasil dihapus'); })
    }
    loadData()
</script>
@endpush