@extends('layouts.master')

@section('content')
    <div class="content-wrapper pb-4">

        <div class="d-flex justify-content-between mb-4">
            <h3>{{ request('id') ? 'Edit Kelas' : 'Tambah Kelas' }}</h3>
            <a href="/kelas" class="btn btn-light">Kembali</a>
        </div>

        <div class="card">
            <div class="card-body">

                <form id="form">
                    @csrf

                    <div class="mb-3">
                        <label>Nama Kelas</label>
                        <input type="text" name="nama_kelas" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label>Tahun Ajar</label>
                        <select name="tahun_ajar_id" class="form-control"></select>
                    </div>

                    <div class="mb-3">
                        <label>Wali Kelas</label>
                        <select name="wali_guru_id" class="form-control"></select>
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

        // init biar urutannya waras
        async function init() {
            await loadDropdown()

            if (id) {
                const res = await fetch('/kelas/' + id)
                const data = await res.json()

                document.querySelector('[name=nama_kelas]').value = data.nama_kelas
                document.querySelector('[name=tahun_ajar_id]').value = data.tahun_ajar_id
                document.querySelector('[name=wali_guru_id]').value = data.wali_guru_id
            }
        }

        // load dropdown
        async function loadDropdown() {
            // tahun ajar
            const ta = await fetch('/tahun-ajar/data').then(res => res.json())
            let htmlTA = '<option value="">Pilih</option>'
            ta.forEach(d => {
                htmlTA += `<option value="${d.id}">${d.nama}</option>`
            })
            document.querySelector('[name=tahun_ajar_id]').innerHTML = htmlTA

            // guru
            const guru = await fetch('/guru/data').then(res => res.json())
            let htmlGuru = '<option value="">Pilih</option>'
            guru.forEach(d => {
                htmlGuru += `<option value="${d.id}">${d.name}</option>`
            })
            document.querySelector('[name=wali_guru_id]').innerHTML = htmlGuru
        }

        // submit tetap sama
        document.getElementById('form').addEventListener('submit', function(e) {
            e.preventDefault()

            const formData = new FormData(this)

            let url = '/kelas'

            if (id) {
                url = '/kelas/' + id
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
                    window.location.href = '/kelas'
                })
        })

        // jalankan
        init()
    </script>
@endpush
