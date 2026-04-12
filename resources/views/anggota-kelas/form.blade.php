@extends('layouts.master')

@section('content')
    <div class="content-wrapper pb-4">

        <div class="d-flex justify-content-between mb-4">
            <h3>Tambah Anggota Kelas</h3>
            <a href="/anggota-kelas" class="btn btn-light">Kembali</a>
        </div>

        <div class="card">
            <div class="card-body">

                <form id="form">
                    @csrf

                    <div class="mb-3">
                        <label>Kelas</label>
                        <select name="kelas_id" class="form-control"></select>
                    </div>

                    <div class="mb-3">
                        <label>Murid</label>
                        <select name="murid_id" class="form-control"></select>
                    </div>

                    <button class="btn btn-info">Simpan</button>
                </form>

            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script>
        async function loadDropdown() {
            // kelas
            const kelas = await fetch('/kelas/data').then(res => res.json())
            let htmlKelas = '<option value="">Pilih</option>'
            kelas.forEach(k => {
                htmlKelas += `<option value="${k.id}">${k.nama_kelas}</option>`
            })
            document.querySelector('[name=kelas_id]').innerHTML = htmlKelas

            // murid
            const murid = await fetch('/murid/data').then(res => res.json())
            let htmlMurid = '<option value="">Pilih</option>'
            murid.forEach(m => {
                htmlMurid += `<option value="${m.id}">${m.name}</option>`
            })
            document.querySelector('[name=murid_id]').innerHTML = htmlMurid
        }

        document.getElementById('form').addEventListener('submit', function(e) {
            e.preventDefault()

            const formData = new FormData(this)

            fetch('/anggota-kelas', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: formData
                })
                .then(res => res.json())
                .then(res => {
                    if (!res.success) {
                        alert(res.message)
                        return
                    }

                    alert('Berhasil ditambah')
                    window.location.href = '/anggota-kelas'
                })
        })

        loadDropdown()
    </script>
@endpush
