@extends('layouts.master')

@section('content')
    <div class="content-wrapper pb-4">

        <div class="d-flex justify-content-between mb-4">
            <h3 class="fw-bold">
                {{ request('id') ? 'Edit Point Rule' : 'Tambah Point Rule' }}
            </h3>

            <a href="/point-rules" class="btn btn-light">Kembali</a>
        </div>

        <div class="card">
            <div class="card-body">

                <form id="form">
                    @csrf

                    <div class="mb-3">
                        <label>Rule Name</label>
                        <input type="text" name="rule_name" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label>Target Role</label>
                        <select name="target_role" class="form-control">
                            <option value="murid">Murid</option>
                            <option value="guru">Guru</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>Condition Type</label>
                        <select name="condition_type" id="condition_type" class="form-control">
                            <option value="TIME">Keterlambatan (Menit)</option>
                            <option value="ALPHA">Alpha</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>Min Value</label>
                        <input type="number" name="min_value" class="form-control" placeholder="Minimal nilai (opsional)">
                    </div>

                    <div class="mb-3">
                        <label>Max Value</label>
                        <input type="number" name="max_value" class="form-control" placeholder="Maksimal nilai (opsional)">
                    </div>

                    <div class="mb-3">
                        <label>Point Modifier</label>
                        <input type="number" name="point_modifier" class="form-control" required>
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
            fetch('/point-rules/' + id)
                .then(res => res.json())
                .then(data => {
                    document.querySelector('[name=rule_name]').value = data.rule_name
                    document.querySelector('[name=target_role]').value = data.target_role
                    document.querySelector('[name=condition_type]').value = data.condition_type
                    document.querySelector('[name=min_value]').value = data.min_value ?? ''
                    document.querySelector('[name=max_value]').value = data.max_value ?? ''
                    document.querySelector('[name=point_modifier]').value = data.point_modifier
                })
        }

        document.getElementById('form').addEventListener('submit', function (e) {
            e.preventDefault()

            const formData = new FormData(this)

            let url = '/point-rules'

            if (id) {
                url = '/point-rules/' + id
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
                    window.location.href = '/point-rules'
                })
        })
    </script>
@endpush