@extends('layouts.master')

@section('content')
    <div class="content-wrapper pb-4">

        <div class="d-flex justify-content-between mb-4">
            <h3 class="fw-bold">Flexibility Items</h3>

            <a href="/flexibility-items/form" class="btn btn-primary">
                Tambah
            </a>
        </div>

        <div class="card">
            <div class="card-body">

                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama Item</th>
                            <th>Point Cost</th>
                            <th>Stock</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody id="table"></tbody>
                </table>

            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script>
        function loadData() {
            fetch('/flexibility-items/data')
                .then(res => res.json())
                .then(data => {
                    let html = ''

                    data.forEach((d, i) => {
                        html += `
                <tr>
                    <td>${i+1}</td>
                    <td>${d.item_name}</td>
                    <td>${d.point_cost}</td>
                    <td>${d.stock_limit ?? '-'}</td>
                    <td>
                        <button onclick="edit(${d.id})" class="btn btn-warning btn-sm">Edit</button>
                        <button onclick="hapus(${d.id})" class="btn btn-danger btn-sm">Hapus</button>
                    </td>
                </tr>`
                    })

                    document.getElementById('table').innerHTML = html
                })
        }

        function edit(id) {
            location.href = '/flexibility-items/form?id=' + id
        }

        function hapus(id) {
            if (!confirm('Hapus item ini?')) return

            fetch('/flexibility-items/' + id, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }).then(() => loadData())
        }

        loadData()
    </script>
@endpush
