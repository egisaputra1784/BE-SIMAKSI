@extends('layouts.master')

@section('content')
    <div class="content-wrapper pb-4">

        <div class="d-flex justify-content-between mb-4">
            <h3 class="fw-bold">Guru Mapel</h3>

            <a href="/guru-mapel/form" class="btn btn-primary">
                Tambah
            </a>
        </div>

        <div class="card">
            <div class="card-body">

                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Guru</th>
                            <th>Mapel</th>
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
            fetch('/guru-mapel/data')
                .then(res => res.json())
                .then(data => {
                    let html = ''

                    data.forEach((d, i) => {
                        html += `
                <tr>
                    <td>${i+1}</td>
                    <td>${d.guru?.name ?? '-'}</td>
                    <td>${d.mapel?.nama_mapel ?? '-'}</td>
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
            location.href = '/guru-mapel/form?id=' + id
        }

        function hapus(id) {
            if (!confirm('Hapus?')) return

            fetch('/guru-mapel/' + id, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }).then(() => loadData())
        }

        loadData()
    </script>
@endpush
