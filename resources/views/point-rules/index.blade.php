@extends('layouts.master')

@section('content')
    <div class="content-wrapper pb-4">

        <div class="d-flex justify-content-between mb-4">
            <h3 class="fw-bold">Point Rules</h3>

            <a href="/point-rules/form" class="btn btn-primary">
                Tambah Rule
            </a>
        </div>

        <div class="card">
            <div class="card-body">

                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Rule</th>
                            <th>Role</th>
                            <th>Condition</th>
                            <th>Point</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody id="table"></tbody>
                </table>

            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script>
        function formatCondition(d) {
            if (d.condition_type === 'ALPHA') {
                return 'Alpha'
            }

            if (d.min_value !== null && d.max_value !== null) {
                return `${d.min_value} - ${d.max_value} menit`
            }

            if (d.min_value !== null && d.max_value === null) {
                return `> ${d.min_value} menit`
            }

            if (d.min_value === null && d.max_value !== null) {
                return `< ${d.max_value} menit`
            }

            return '-'
        }

        function loadData() {
            fetch('/point-rules/data')
                .then(res => res.json())
                .then(data => {
                    let html = ''

                    data.forEach((d, i) => {

                        html += `
                    <tr>
                        <td>${i + 1}</td>
                        <td>${d.rule_name}</td>
                        <td>${d.target_role}</td>
                        <td>${formatCondition(d)}</td>
                        <td>${d.point_modifier}</td>
                        <td>
                            <button onclick="edit(${d.id})" class="btn btn-warning btn-sm">Edit</button>
                            <button onclick="hapus(${d.id})" class="btn btn-danger btn-sm">Hapus</button>
                        </td>
                    </tr>`
                    })

                    document.getElementById('table').innerHTML = html
                })
        }

        function edit(id) {
            location.href = '/point-rules/form?id=' + id
        }

        function hapus(id) {
            if (!confirm('Hapus rule ini?')) return

            fetch('/point-rules/' + id, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }).then(() => loadData())
        }

        loadData()
    </script>
@endpush