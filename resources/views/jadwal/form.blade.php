@extends('layouts.master')

@section('content')
    <div class="content-wrapper pb-4">

        <div class="d-flex justify-content-between mb-4">
            <div>
                <h3 class="fw-bold mb-0">
                    {{ request('id') ? 'Edit Jadwal' : 'Tambah Jadwal' }}
                </h3>
            </div>

            <a href="/jadwal" class="btn btn-light">Kembali</a>
        </div>

        <div class="card">
            <div class="card-body">

                <form id="form">
                    @csrf

                    <div class="mb-3">
                        <label>Kelas</label>
                        <select name="kelas_id" class="form-control" required id="kelas"></select>
                    </div>

                    <div class="mb-3">
                        <label>Mapel</label>
                        <select name="mapel_id" class="form-control" required id="mapel"></select>
                    </div>

                    <div class="mb-3">
                        <label>Guru</label>
                        <select name="guru_id" class="form-control" required id="guru"></select>
                    </div>

                    <div class="mb-3">
                        <label>Hari</label>
                        <select name="hari" class="form-control" required>
                            <option value="">-- pilih --</option>
                            <option value="senin">Senin</option>
                            <option value="selasa">Selasa</option>
                            <option value="rabu">Rabu</option>
                            <option value="kamis">Kamis</option>
                            <option value="jumat">Jumat</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>Jam Mulai</label>
                        <input type="time" name="jam_mulai" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label>Jam Selesai</label>
                        <input type="time" name="jam_selesai" class="form-control" required>
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

        // 🔥 load dropdown
        function loadDropdowns(selected = {}) {

            fetch('/kelas/data')
                .then(res => res.json())
                .then(data => {
                    let html = '<option value="">-- pilih kelas --</option>'
                    data.forEach(d => {
                        html +=
                            `<option value="${d.id}" ${selected.kelas_id == d.id ? 'selected' : ''}>${d.nama_kelas}</option>`
                    })
                    document.getElementById('kelas').innerHTML = html
                })

            fetch('/mapel/data')
                .then(res => res.json())
                .then(data => {
                    let html = '<option value="">-- pilih mapel --</option>'
                    data.forEach(d => {
                        html +=
                            `<option value="${d.id}" ${selected.mapel_id == d.id ? 'selected' : ''}>${d.nama_mapel}</option>`
                    })
                    document.getElementById('mapel').innerHTML = html
                })

            fetch('/guru/data')
                .then(res => res.json())
                .then(data => {
                    let html = '<option value="">-- pilih guru --</option>'
                    data.forEach(d => {
                        html +=
                            `<option value="${d.id}" ${selected.guru_id == d.id ? 'selected' : ''}>${d.name}</option>`
                    })
                    document.getElementById('guru').innerHTML = html
                })
        }


        // 🔥 kalau edit
        if (id) {
            fetch('/jadwal/' + id)
                .then(res => res.json())
                .then(data => {

                    loadDropdowns(data)

                    document.querySelector('[name=hari]').value = data.hari
                    document.querySelector('[name=jam_mulai]').value = data.jam_mulai
                    document.querySelector('[name=jam_selesai]').value = data.jam_selesai
                })
        } else {
            loadDropdowns()
        }


        // 🔥 submit
        document.getElementById('form').addEventListener('submit', function(e) {
            e.preventDefault()

            const formData = new FormData(this)

            let url = '/jadwal'

            if (id) {
                url = '/jadwal/' + id
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
                    window.location.href = '/jadwal'
                })
        })
    </script>
@endpush
