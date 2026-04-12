@extends('layouts.master')

@section('content')
    <div class="content-wrapper pb-4">

        <div class="d-flex justify-content-between mb-4">
            <h3>Anggota Kelas</h3>
            <a href="/anggota-kelas/form" class="btn btn-primary">Tambah</a>
        </div>

        <div class="card">
            <div class="card-body">

                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Kelas</th>
                            <th>Murid</th>
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
            fetch('/anggota-kelas/data')
                .then(res => res.json())
                .then(data => {
                    let html = ''

                    data.forEach((d, i) => {
                        html += `
                <tr>
                    <td>${i+1}</td>
                    <td>${d.kelas?.nama_kelas ?? '-'}</td>
                    <td>${d.murid?.name ?? '-'}</td>
                    <td>
                        <button onclick="hapus(${d.id})" class="btn btn-danger btn-sm">Hapus</button>
                    </td>
                </tr>`
                    })

                    document.getElementById('table').innerHTML = html
                })
        }

        function hapus(id) {
            if (!confirm('Hapus?')) return

            fetch('/anggota-kelas/' + id, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }).then(() => loadData())
        }

        loadData()
    </script>
@endpush
