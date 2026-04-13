@extends('layouts.master')

@section('content')
    <div class="content-wrapper pb-4">

        <div class="d-flex justify-content-between mb-4">
            <h3>{{ request('id') ? 'Edit Guru Mapel' : 'Tambah Guru Mapel' }}</h3>
            <a href="/guru-mapel" class="btn btn-light">Kembali</a>
        </div>

        <div class="card">
            <div class="card-body">

                <form id="form">
                    @csrf

                    <div class="mb-3">
                        <label>Guru</label>
                        <select name="guru_id" class="form-control"></select>
                    </div>

                    <div class="mb-3">
                        <label>Mapel</label>
                        <select name="mapel_id" class="form-control"></select>
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

        function loadDropdown() {
            fetch('/guru/data')
                .then(res => res.json())
                .then(data => {
                    let html = '<option value="">Pilih Guru</option>'
                    data.forEach(d => {
                        html += `<option value="${d.id}">${d.name}</option>`
                    })
                    document.querySelector('[name=guru_id]').innerHTML = html
                })

            fetch('/mapel/data')
                .then(res => res.json())
                .then(data => {
                    let html = '<option value="">Pilih Mapel</option>'
                    data.forEach(d => {
                        html += `<option value="${d.id}">${d.nama_mapel}</option>`
                    })
                    document.querySelector('[name=mapel_id]').innerHTML = html
                })
        }

        if (id) {
            fetch('/guru-mapel/' + id)
                .then(res => res.json())
                .then(data => {
                    document.querySelector('[name=guru_id]').value = data.guru_id
                    document.querySelector('[name=mapel_id]').value = data.mapel_id
                })
        }

        document.getElementById('form').addEventListener('submit', function(e) {
            e.preventDefault()

            const formData = new FormData(this)
            let url = '/guru-mapel'

            if (id) {
                url = '/guru-mapel/' + id
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
                    window.location.href = '/guru-mapel'
                })
        })

        loadDropdown()
    </script>
@endpush
