@extends('layouts.master')

@section('content')
    <div class="content-wrapper pb-4">

        <div class="d-flex justify-content-between mb-4">
            <h3 class="fw-bold">
                {{ request('id') ? 'Edit Item' : 'Tambah Item' }}
            </h3>

            <a href="/flexibility-items" class="btn btn-light">Kembali</a>
        </div>

        <div class="card">
            <div class="card-body">

                <form id="form">
                    @csrf

                    <div class="mb-3">
                        <label>Nama Item</label>
                        <input type="text" name="item_name" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label>Point Cost</label>
                        <input type="number" name="point_cost" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label>Stock Limit</label>
                        <input type="number" name="stock_limit" class="form-control">
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
            fetch('/flexibility-items/' + id)
                .then(res => res.json())
                .then(data => {
                    document.querySelector('[name=item_name]').value = data.item_name
                    document.querySelector('[name=point_cost]').value = data.point_cost
                    document.querySelector('[name=stock_limit]').value = data.stock_limit
                })
        }

        document.getElementById('form').addEventListener('submit', function(e) {
            e.preventDefault()

            const formData = new FormData(this)

            let url = '/flexibility-items'

            if (id) {
                url = '/flexibility-items/' + id
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
                    window.location.href = '/flexibility-items'
                })
        })
    </script>
@endpush
