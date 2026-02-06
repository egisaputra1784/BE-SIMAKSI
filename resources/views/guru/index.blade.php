@extends('layouts.master')

@section('content')
    <div class="content-wrapper pb-4">

        {{-- HEADER --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold mb-0">Data Guru</h3>
                <small class="text-muted">Kelola akun guru & pengajar</small>
            </div>

            <a href="/guru/form" class="btn btn-success">
                <i class="mdi mdi-plus"></i> Tambah Guru
            </a>
        </div>


        {{-- CARD --}}
        <div class="card shadow-sm">
            <div class="card-body">

                {{-- SEARCH --}}
                <div class="row mb-3">
                    <div class="col-md-4">
                        <input type="text" class="form-control" placeholder="Cari nama / email / NIP...">
                    </div>
                </div>


                {{-- TABLE --}}
                <div class="table-responsive">
                    <table class="table table-hover align-middle">

                        <thead class="table-light">
                            <tr>
                                <th width="5%">#</th>
                                <th>Guru</th>
                                <th>Email</th>
                                <th>NIP</th>
                                <th>Role</th>
                                <th width="15%" class="text-center">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>

                            {{-- DUMMY DATA --}}
                            @php
                                $gurus = [
                                    ['name' => 'Pak Budi', 'email' => 'budi@school.com', 'nip' => '1989001'],
                                    ['name' => 'Bu Sinta', 'email' => 'sinta@school.com', 'nip' => '1989002'],
                                    ['name' => 'Pak Andi', 'email' => 'andi@school.com', 'nip' => '1989003'],
                                    ['name' => 'Bu Rina', 'email' => 'rina@school.com', 'nip' => '1989004'],
                                ];
                            @endphp

                            @foreach ($gurus as $i => $guru)
                                <tr>
                                    <td>{{ $i + 1 }}</td>

                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="rounded-circle bg-success text-white d-flex justify-content-center align-items-center"
                                                style="width:35px;height:35px;">
                                                <i class="mdi mdi-account-tie"></i>
                                            </div>
                                            <span class="fw-semibold">{{ $guru['name'] }}</span>
                                        </div>
                                    </td>

                                    <td>{{ $guru['email'] }}</td>
                                    <td>{{ $guru['nip'] }}</td>

                                    <td>
                                        <span class="badge bg-success">guru</span>
                                    </td>

                                    <td class="text-center">
                                        <a href="/guru/form" class="btn btn-sm btn-warning">
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
