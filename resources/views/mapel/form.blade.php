@extends('layouts.master')

@section('content')
    <div class="content-wrapper pb-4">

        <div class="d-flex justify-content-between mb-4">
            <h3>{{ request('id') ? 'Edit Mapel' : 'Tambah Mapel' }}</h3>
            <a href="/mapel" class="btn btn-light">Kembali</a>
        </div>

        <div class="card">
            <div class="card-body">

                <form id="form">
                    @csrf

                    <div class="mb-3">
                        <label>Nama Mapel</label>
                        <input type="text" name="nama_mapel" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label>Kode Mapel</label>
                        <input type="text" name="kode_mapel" class="form-control" required>
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
            fetch('/mapel/' + id)
                .then(res => res.json())
                .then(data => {
                    document.querySelector('[name=nama_mapel]').value = data.nama_mapel
                    document.querySelector('[name=kode_mapel]').value = data.kode_mapel
                })
        }

        document.getElementById('form').addEventListener('submit', function(e) {
            e.preventDefault()

            const formData = new FormData(this)

            let url = '/mapel'

            if (id) {
                url = '/mapel/' + id
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
                    window.location.href = '/mapel'
                })
        })
    </script>
@endpush
