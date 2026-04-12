@extends('layouts.master')

@section('content')
    <div class="content-wrapper pb-4">

        {{-- HEADER --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold mb-0">Data Guru</h3>
                <small class="text-muted">Kelola akun guru & pengajar</small>
            </div>

            <a href="/guru/form" class="btn btn-success">
                <i class="mdi mdi-plus"></i> Tambah Guru
            </a>
        </div>


        {{-- CARD --}}
        <div class="card shadow-sm">
            <div class="card-body">

                {{-- SEARCH --}}
                <div class="row mb-3">
                    <div class="col-md-4">
                        <input type="text" class="form-control" placeholder="Cari nama / email / NIP...">
                    </div>
                </div>


                {{-- TABLE --}}
                <div class="table-responsive">
                    <table class="table table-hover align-middle">

                        <thead class="table-light">
                            <tr>
                                <th width="5%">#</th>
                                <th>Guru</th>
                                <th>Email</th>
                                <th>NIP</th>
                                <th>Role</th>
                                <th width="15%" class="text-center">Aksi</th>
                            </tr>
                        </thead>

                        <tbody id="guru-table">
                            <tr>
                                <td colspan="6" class="text-center text-muted">Loading...</td>
                            </tr>
                        </tbody>

                    </table>
                </div>

            </div>
        </div>

    </div>
@endsection


@push('scripts')
    <script>
        let allGuru = []

        function loadGuru() {
            fetch('/guru/data')
                .then(res => res.json())
                .then(data => {
                    allGuru = data
                    renderData(data)
                })
        }

        function renderData(data) {
            let html = ''

            if (data.length === 0) {
                html = `<tr><td colspan="6" class="text-center">Tidak ada data</td></tr>`
            } else {
                data.forEach((g, i) => {
                    html += `
            <tr>
                <td>${i + 1}</td>
                <td>${g.name}</td>
                <td>${g.email}</td>
                <td>${g.nip ?? '-'}</td>
                <td><span class="badge bg-success">guru</span></td>
                <td class="text-center">
                    <button onclick="editGuru(${g.id})" class="btn btn-warning btn-sm">Edit</button>
                    <button onclick="deleteGuru(${g.id})" class="btn btn-danger btn-sm">Hapus</button>
                </td>
            </tr>
            `
                })
            }

            document.getElementById('guru-table').innerHTML = html
        }

        function editGuru(id) {
            window.location.href = '/guru/form?id=' + id
        }

        function deleteGuru(id) {
            if (!confirm('Yakin hapus?')) return

            fetch('/guru/' + id, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }).then(() => loadGuru())
        }

        loadGuru()
    </script>
@endpush
