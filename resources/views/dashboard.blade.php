@extends('layouts.master')

@section('content')
    <div class="content-wrapper pb-4">

        {{-- HEADER --}}
        <div class="page-header mb-4">
            <h3 class="fw-bold">Dashboard Sekolah</h3>
            <p class="text-muted mb-0">Panel kontrol sistem akademik & absensi</p>
        </div>


        {{-- ===================== STAT CARDS ===================== --}}
        <div class="row">

            {{-- Murid --}}
            <div class="col-md-3 grid-margin stretch-card">
                <div class="card shadow-sm">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Total Murid</p>
                            <h3 class="fw-bold mb-0">512</h3>
                        </div>
                        <i class="mdi mdi-account-group text-primary" style="font-size:32px;"></i>
                    </div>
                </div>
            </div>

            {{-- Guru --}}
            <div class="col-md-3 grid-margin stretch-card">
                <div class="card shadow-sm">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Total Guru</p>
                            <h3 class="fw-bold mb-0">32</h3>
                        </div>
                        <i class="mdi mdi-teach text-success" style="font-size:32px;"></i>
                    </div>
                </div>
            </div>

            {{-- Kelas --}}
            <div class="col-md-3 grid-margin stretch-card">
                <div class="card shadow-sm">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Total Kelas</p>
                            <h3 class="fw-bold mb-0">18</h3>
                        </div>
                        <i class="mdi mdi-google-classroom text-warning" style="font-size:32px;"></i>
                    </div>
                </div>
            </div>

            {{-- Absen hari ini --}}
            <div class="col-md-3 grid-margin stretch-card">
                <div class="card shadow-sm">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Sesi Absen Hari Ini</p>
                            <h3 class="fw-bold mb-0">9</h3>
                        </div>
                        <i class="mdi mdi-qrcode-scan text-danger" style="font-size:32px;"></i>
                    </div>
                </div>
            </div>

        </div>



        {{-- ===================== ROW 2 ===================== --}}
        <div class="row">

            {{-- Tahun ajar aktif --}}
            <div class="col-lg-4 grid-margin stretch-card">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3">Tahun Ajar Aktif</h5>

                        <h3 class="text-primary fw-bold">2025 / 2026</h3>
                        <p class="text-muted">Status: Aktif</p>

                        <hr>

                        <p class="mb-1">Jumlah Kelas : <b>18</b></p>
                        <p class="mb-1">Jumlah Murid : <b>512</b></p>
                        <p class="mb-0">Jumlah Guru : <b>32</b></p>
                    </div>
                </div>
            </div>


            {{-- Jadwal hari ini --}}
            <div class="col-lg-8 grid-margin stretch-card">
                <div class="card shadow-sm">
                    <div class="card-body">

                        <h5 class="fw-bold mb-3">Jadwal Hari Ini</h5>

                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Kelas</th>
                                        <th>Mapel</th>
                                        <th>Guru</th>
                                        <th>Jam</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>X RPL 1</td>
                                        <td>Pemrograman Web</td>
                                        <td>Pak Budi</td>
                                        <td>07:00 - 08:30</td>
                                    </tr>
                                    <tr>
                                        <td>X RPL 2</td>
                                        <td>Basis Data</td>
                                        <td>Bu Sinta</td>
                                        <td>08:30 - 10:00</td>
                                    </tr>
                                    <tr>
                                        <td>XI TKJ</td>
                                        <td>Jaringan</td>
                                        <td>Pak Andi</td>
                                        <td>10:15 - 11:45</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>

        </div>



        {{-- ===================== ROW 3 ===================== --}}
        <div class="row">

            {{-- Rekap absensi --}}
            <div class="col-lg-8 grid-margin stretch-card">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3">Rekap Absensi Hari Ini</h5>

                        <div class="d-flex gap-4">
                            <span class="badge bg-success p-2">Hadir : 480</span>
                            <span class="badge bg-warning p-2">Izin : 20</span>
                            <span class="badge bg-danger p-2">Alpha : 12</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Quick actions --}}
            <div class="col-lg-4 grid-margin stretch-card">
                <div class="card shadow-sm">
                    <div class="card-body">

                        <h5 class="fw-bold mb-3">Aksi Cepat</h5>

                        <div class="d-grid gap-2">
                            <a href="#" class="btn btn-primary">
                                <i class="mdi mdi-plus"></i> Tambah Kelas
                            </a>

                            <a href="#" class="btn btn-success">
                                <i class="mdi mdi-book-plus"></i> Tambah Mapel
                            </a>

                            <a href="#" class="btn btn-warning">
                                <i class="mdi mdi-qrcode"></i> Buka Sesi Absen
                            </a>
                        </div>

                    </div>
                </div>
            </div>

        </div>

    </div>
@endsection
