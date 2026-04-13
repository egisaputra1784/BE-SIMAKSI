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
                        <label>Condition Operator</label>
                        <select name="condition_operator" class="form-control">
                            <option value="<">Kurang dari</option>
                            <option value=">">Lebih dari</option>
                            <option value="between">Antara</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>Condition Value</label>
                        <input type="text" name="condition_value" class="form-control" placeholder="contoh: 3 atau 1-5"
                            required>
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
                    document.querySelector('[name=condition_operator]').value = data.condition_operator
                    document.querySelector('[name=condition_value]').value = data.condition_value
                    document.querySelector('[name=point_modifier]').value = data.point_modifier
                })
        }

        document.getElementById('form').addEventListener('submit', function(e) {
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
