@extends('layouts.master')
@section('content')

<div class="page-header-bar">
    <div><h3>Flexibility Items</h3><small>Kelola item penebus poin (market)</small></div>
    <div class="page-header-actions">
        <a href="/flexibility-items/export/excel" class="btn-success-custom"><i class="mdi mdi-microsoft-excel"></i> Export Excel</a>
        <a href="/flexibility-items/form" class="btn-primary-custom"><i class="mdi mdi-plus"></i> Tambah Item</a>
    </div>
</div>

<div class="card">
    <div class="card-header-custom">
        <span class="card-title-custom"><i class="mdi mdi-shopping-outline me-2" style="color:var(--primary);"></i>Daftar Item Market</span>
    </div>
    <div style="overflow-x:auto;">
        <table class="simaksi-table">
            <thead>
                <tr><th>#</th><th>Nama Item</th><th>Tipe</th><th>Biaya Poin</th><th>Max Terlambat</th><th>Batas Stok</th><th style="text-align:center;">Aksi</th></tr>
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
    function loadData() {
        fetch('/flexibility-items/data').then(r => r.json()).then(data => {
            const tb = document.getElementById('table')
            if (!data.length) { tb.innerHTML = `<tr><td colspan="7"><div class="empty-state"><i class="mdi mdi-shopping-outline"></i><p>Tidak ada item</p></div></td></tr>`; return }
            tb.innerHTML = data.map((d, i) => `
                <tr>
                    <td><span style="font-weight:600;color:var(--text-muted);">${i+1}</span></td>
                    <td><span style="font-weight:600;">${d.item_name}</span></td>
                    <td><span class="badge-${d.type==='LATE'?'warning':'danger'}">${d.type}</span></td>
                    <td><span style="font-weight:700;color:var(--primary);">${d.point_cost} poin</span></td>
                    <td>${d.max_late_minutes ? d.max_late_minutes + ' menit' : '-'}</td>
                    <td>${d.stock_limit ?? '<span style="color:var(--text-muted);">Tak terbatas</span>'}</td>
                    <td style="text-align:center;">
                        <button onclick="edit(${d.id})" class="btn-sm-warn"><i class="mdi mdi-pencil"></i> Edit</button>
                        <button onclick="hapus(${d.id})" class="btn-sm-danger"><i class="mdi mdi-delete"></i> Hapus</button>
                    </td>
                </tr>`).join('')
        })
    }
    function edit(id) { location.href = '/flexibility-items/form?id=' + id }
    function hapus(id) {
        if (!confirm('Hapus item ini?')) return
        fetch('/flexibility-items/' + id, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } })
            .then(() => { loadData(); showToast('Item berhasil dihapus'); })
    }
    loadData()
</script>
@endpush
