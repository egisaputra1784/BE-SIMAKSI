@extends('layouts.master')

@section('content')
    <div class="content-wrapper pb-4">

        {{-- HEADER --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold mb-0">Tambah Admin</h3>
                <small class="text-muted">Buat akun administrator baru</small>
            </div>

            <a href="/admin" class="btn btn-light">
                <i class="mdi mdi-arrow-left"></i> Kembali
            </a>
        </div>


        <div class="row">
            <div class="col-lg-6">

                <div class="card shadow-sm">
                    <div class="card-body">

                        <form action="#" method="POST">

                            {{-- Nama --}}
                            <div class="mb-3">
                                <label class="form-label">Nama Lengkap</label>
                                <input type="text" class="form-control" placeholder="Contoh: Egi Noviani Saputra">
                            </div>


                            {{-- Email --}}
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" placeholder="admin@email.com">
                            </div>


                            {{-- Password --}}
                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" class="form-control" placeholder="••••••••">
                            </div>


                            {{-- Confirm --}}
                            <div class="mb-3">
                                <label class="form-label">Konfirmasi Password</label>
                                <input type="password" class="form-control" placeholder="••••••••">
                            </div>


                            {{-- Role hidden (auto admin) --}}
                            <input type="hidden" value="admin">


                            {{-- BUTTON --}}
                            <div class="d-flex gap-2 mt-4">

                                <button type="submit" class="btn btn-primary">
                                    <i class="mdi mdi-content-save"></i> Simpan
                                </button>

                                <a href="/admin" class="btn btn-secondary">
                                    Batal
                                </a>

                            </div>

                        </form>

                    </div>
                </div>

            </div>
        </div>

    </div>
@endsection
