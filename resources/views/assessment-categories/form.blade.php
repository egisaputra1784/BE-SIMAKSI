@extends('layouts.master')

@section('content')
    <div class="content-wrapper pb-4">

        <div class="d-flex justify-content-between mb-4">
            <div>
                <h3 class="fw-bold mb-0">
                    {{ request('id') ? 'Edit Kategori Assessment' : 'Tambah Kategori Assessment' }}
                </h3>
            </div>

            <a href="/assessment-categories" class="btn btn-light">Kembali</a>
        </div>

        <div class="card">
            <div class="card-body">

                <form id="form">
                    @csrf

                    <div class="mb-3">
                        <label>Nama Kategori</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label>Deskripsi</label>
                        <textarea name="description" class="form-control"></textarea>
                    </div>

                    <div class="form-check mb-3">
                        <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active">
                        <label class="form-check-label">Aktif</label>
                    </div>

                    <button class="btn btn-info">Simpan</button>
                </form>

            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script>
        const id = new URLSearchParams(window.location.search).get('id')

        if (id) {
            fetch('/assessment-categories/' + id)
                .then(res => res.json())
                .then(data => {
                    document.querySelector('[name=name]').value = data.name
                    document.querySelector('[name=description]').value = data.description ?? ''
                    document.querySelector('[name=is_active]').checked = data.is_active
                })
        }

        document.getElementById('form').addEventListener('submit', function(e) {
            e.preventDefault()

            const formData = new FormData(this)

            let url = '/assessment-categories'

            if (id) {
                url = '/assessment-categories/' + id
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
                .then(() => {
                    alert('Berhasil disimpan')
                    window.location.href = '/assessment-categories'
                })
        })
    </script>
@endpush
