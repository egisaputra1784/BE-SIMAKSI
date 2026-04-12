@extends('layouts.master')

@section('content')
    <div class="content-wrapper pb-4">

        {{-- HEADER --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold mb-0">
                    {{ request('id') ? 'Edit Murid' : 'Tambah Murid' }}
                </h3>
                <small class="text-muted">
                    {{ request('id') ? 'Edit akun murid' : 'Buat akun murid baru' }}
                </small>
            </div>

            <a href="/murid" class="btn btn-light">
                <i class="mdi mdi-arrow-left"></i> Kembali
            </a>
        </div>

        <div class="row">
            <div class="col-lg-6">

                <div class="card shadow-sm">
                    <div class="card-body">

                        <form id="form-murid">
                            @csrf

                            {{-- Nama --}}
                            <div class="mb-3">
                                <label class="form-label">Nama Lengkap</label>
                                <input type="text" name="name" class="form-control" required>
                            </div>

                            {{-- Email --}}
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>

                            {{-- NISN --}}
                            <div class="mb-3">
                                <label class="form-label">NISN</label>
                                <input type="text" name="nisn" class="form-control" required>
                            </div>

                            {{-- Password --}}
                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" class="form-control">
                            </div>

                            {{-- Confirm --}}
                            <div class="mb-3">
                                <label class="form-label">Konfirmasi Password</label>
                                <input type="password" id="confirm" class="form-control">
                            </div>

                            <input type="hidden" name="role" value="murid">

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-info">
                                    <i class="mdi mdi-content-save"></i> Simpan
                                </button>

                                <a href="/murid" class="btn btn-secondary">
                                    Batal
                                </a>
                            </div>

                        </form>

                    </div>
                </div>

            </div>
        </div>

    </div>
@endsection


@push('scripts')
    <script>
        const urlParams = new URLSearchParams(window.location.search)
        const id = urlParams.get('id')

        // EDIT MODE
        if (id) {
            fetch('/murid/' + id)
                .then(res => res.json())
                .then(data => {
                    document.querySelector('[name=name]').value = data.name
                    document.querySelector('[name=email]').value = data.email
                    document.querySelector('[name=nisn]').value = data.nisn
                })
        }

        document.getElementById('form-murid').addEventListener('submit', function(e) {
            e.preventDefault()

            const password = document.querySelector('[name=password]').value
            const confirm = document.getElementById('confirm').value

            // validasi password kalau diisi
            if (password && password !== confirm) {
                alert('Password tidak sama')
                return
            }

            const formData = new FormData(this)

            let url = '/murid'

            if (id) {
                url = '/murid/' + id
                formData.append('_method', 'PUT')
            }

            fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: formData
                })
                .then(res => res.json())
                .then(res => {
                    if (res.success) {
                        alert(id ? 'Murid diupdate' : 'Murid ditambah')
                        window.location.href = '/murid'
                    }
                })
        })
    </script>
@endpush
