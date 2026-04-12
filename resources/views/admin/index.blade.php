@extends('layouts.master')

@section('content')
    <div class="content-wrapper pb-4">

        {{-- HEADER --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold mb-0">Data Admin</h3>
                <small class="text-muted">Kelola akun administrator sistem</small>
            </div>

            <a href="/admin/form" class="btn btn-primary">
                <i class="mdi mdi-plus"></i> Tambah Admin
            </a>
        </div>

        {{-- CARD --}}
        <div class="card shadow-sm">
            <div class="card-body">

                {{-- SEARCH --}}
                <div class="row mb-3">
                    <div class="col-md-4">
                        <input type="text" id="search" class="form-control" placeholder="Cari nama / email...">
                    </div>
                </div>

                {{-- TABLE --}}
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">#</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th width="15%" class="text-center">Aksi</th>
                            </tr>
                        </thead>

                        <tbody id="admin-table">
                            <tr>
                                <td colspan="5" class="text-center text-muted">Loading...</td>
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
        let allAdmins = []

        // ambil data sekali
        function loadAdmins() {
            fetch('/admin/data')
                .then(res => res.json())
                .then(data => {
                    allAdmins = data
                    renderData(data)
                })
                .catch(err => {
                    console.error(err)
                    document.getElementById('admin-table').innerHTML =
                        `<tr><td colspan="5" class="text-danger text-center">Gagal load data</td></tr>`
                })
        }

        // render tabel
        function renderData(data) {
            let html = ''

            if (data.length === 0) {
                html = `<tr><td colspan="5" class="text-center text-muted">Tidak ada data</td></tr>`
            } else {
                data.forEach((admin, i) => {
                    html += `
            <tr>
                <td>${i + 1}</td>

                <td>
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-circle bg-primary text-white d-flex justify-content-center align-items-center"
                            style="width:35px;height:35px;">
                            <i class="mdi mdi-account"></i>
                        </div>
                        <span class="fw-semibold">${admin.name}</span>
                    </div>
                </td>

                <td>${admin.email}</td>

                <td>
                    <span class="badge bg-danger">admin</span>
                </td>

                <td class="text-center">
                    <button onclick="editAdmin(${admin.id})" class="btn btn-sm btn-warning">
                        <i class="mdi mdi-pencil"></i>
                    </button>
                    <button onclick="deleteAdmin(${admin.id})" class="btn btn-sm btn-danger">
                        <i class="mdi mdi-delete"></i>
                    </button>
                </td>
            </tr>
            `
                })
            }

            document.getElementById('admin-table').innerHTML = html
        }

        function editAdmin(id) {
            window.location.href = '/admin/form?id=' + id
        }

        // delete
        function deleteAdmin(id) {
            if (!confirm('Yakin hapus admin ini?')) return;

            fetch('/admin/' + id, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(() => {
                    loadAdmins()
                })
        }

        // search ringan (tanpa fetch ulang)
        document.getElementById('search').addEventListener('keyup', function() {
            const keyword = this.value.toLowerCase()

            const filtered = allAdmins.filter(a =>
                a.name.toLowerCase().includes(keyword) ||
                a.email.toLowerCase().includes(keyword)
            )

            renderData(filtered)
        })

        // load awal
        loadAdmins()
    </script>
@endpush
