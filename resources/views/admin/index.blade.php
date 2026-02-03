@extends('layouts.master')

@section('content')
    <div class="content-wrapper pb-4">

        {{-- HEADER --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold mb-0">Data Admin</h3>
                <small class="text-muted">Kelola akun administrator sistem</small>
            </div>

            <a href="/admin/form" class="btn btn-primary">
                <i class="mdi mdi-plus"></i> Tambah Admin
            </a>
        </div>


        {{-- CARD --}}
        <div class="card shadow-sm">
            <div class="card-body">

                {{-- SEARCH + FILTER --}}
                <div class="row mb-3">
                    <div class="col-md-4">
                        <input type="text" class="form-control" placeholder="Cari nama / email...">
                    </div>
                </div>


                {{-- TABLE --}}
                <div class="table-responsive">
                    <table class="table table-hover align-middle">

                        <thead class="table-light">
                            <tr>
                                <th width="5%">#</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th width="15%" class="text-center">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>

                            {{-- DUMMY DATA --}}
                            @php
                                $admins = [
                                    ['name' => 'Egi Noviani Saputra', 'email' => 'egi@school.com'],
                                    ['name' => 'Rizky Pratama', 'email' => 'rizky@school.com'],
                                    ['name' => 'Sinta Aulia', 'email' => 'sinta@school.com'],
                                    ['name' => 'Budi Santoso', 'email' => 'budi@school.com'],
                                ];
                            @endphp

                            @foreach ($admins as $i => $admin)
                                <tr>
                                    <td>{{ $i + 1 }}</td>

                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="rounded-circle bg-primary text-white d-flex justify-content-center align-items-center"
                                                style="width:35px;height:35px;">
                                                <i class="mdi mdi-account"></i>
                                            </div>
                                            <span class="fw-semibold">{{ $admin['name'] }}</span>
                                        </div>
                                    </td>

                                    <td>{{ $admin['email'] }}</td>

                                    <td>
                                        <span class="badge bg-danger">admin</span>
                                    </td>

                                    <td class="text-center">

                                        <a href="/admin/form" class="btn btn-sm btn-warning">
                                            <i class="mdi mdi-pencil"></i>
                                        </a>

                                        <a href="#" class="btn btn-sm btn-danger">
                                            <i class="mdi mdi-delete"></i>
                                        </a>

                                    </td>
                                </tr>
                            @endforeach

                        </tbody>

                    </table>
                </div>
            </div>
        </div>

    </div>
@endsection
