@extends('layouts.master')

@section('content')
    <div class="content-wrapper pb-4">

        <div class="d-flex justify-content-between mb-4">
            <div>
                <h3 class="fw-bold mb-0">Data Murid</h3>
                <small class="text-muted">Kelola data siswa</small>
            </div>

            <a href="/murid/form" class="btn btn-primary">
                <i class="mdi mdi-plus"></i> Tambah Murid
            </a>
        </div>

        <div class="card">
            <div class="card-body">

                <input type="text" id="search" class="form-control mb-3" placeholder="Cari nama/email/nisn">

                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>NISN</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody id="murid-table">
                        <tr>
                            <td colspan="5">Loading...</td>
                        </tr>
                    </tbody>
                </table>

            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script>
        let allData = []

        function loadData() {
            fetch('/murid/data')
                .then(res => res.json())
                .then(data => {
                    allData = data
                    render(data)
                })
        }

        function render(data) {
            let html = ''

            if (!data.length) {
                html = `<tr><td colspan="5">Tidak ada data</td></tr>`
            } else {
                data.forEach((m, i) => {
                    html += `
            <tr>
                <td>${i+1}</td>
                <td>${m.name}</td>
                <td>${m.email}</td>
                <td>${m.nisn ?? '-'}</td>
                <td>
                    <button onclick="edit(${m.id})" class="btn btn-warning btn-sm">
                        <i class="mdi mdi-pencil"></i>
                    </button>
                    <button onclick="hapus(${m.id})" class="btn btn-sm btn-danger">
                        <i class="mdi mdi-delete"></i>
                    </button>
                </td>
            </tr>`
                })
            }

            document.getElementById('murid-table').innerHTML = html
        }

        function edit(id) {
            location.href = '/murid/form?id=' + id
        }

        function hapus(id) {
            if (!confirm('Hapus?')) return

            fetch('/murid/' + id, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }).then(() => loadData())
        }

        document.getElementById('search').addEventListener('keyup', function() {
            let k = this.value.toLowerCase()

            let filtered = allData.filter(m =>
                m.name.toLowerCase().includes(k) ||
                m.email.toLowerCase().includes(k) ||
                (m.nisn || '').toLowerCase().includes(k)
            )

            render(filtered)
        })

        loadData()
    </script>
@endpush
