@extends('layouts.master')

@section('content')
    <div class="content-wrapper pb-4">

        <div class="d-flex justify-content-between mb-4">
            <div>
                <h3 class="fw-bold mb-0">
                    {{ request('id') ? 'Edit Tahun Ajar' : 'Tambah Tahun Ajar' }}
                </h3>
            </div>

            <a href="/tahun-ajar" class="btn btn-light">Kembali</a>
        </div>

        <div class="card">
            <div class="card-body">

                <form id="form">
                    @csrf

                    <div class="mb-3">
                        <label>Nama Tahun Ajar</label>
                        <input type="text" name="nama" class="form-control" required>
                    </div>

                    <div class="form-check mb-3">
                        <input type="checkbox" name="aktif" value="1" class="form-check-input" id="aktif">
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
            fetch('/tahun-ajar/' + id)
                .then(res => res.json())
                .then(data => {
                    document.querySelector('[name=nama]').value = data.nama
                    document.querySelector('[name=aktif]').checked = data.aktif
                })
        }

        document.getElementById('form').addEventListener('submit', function(e) {
            e.preventDefault()

            const formData = new FormData(this)

            let url = '/tahun-ajar'

            if (id) {
                url = '/tahun-ajar/' + id
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
                    window.location.href = '/tahun-ajar'
                })
        })
    </script>
@endpush
