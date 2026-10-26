@extends('layouts.app')

@section('title', 'Laporan Kehadiran')
@section('page-title', 'Laporan Kehadiran')
@section('page-subtitle', 'Ringkasan persentase kehadiran per mata kuliah')

@section('content')
    @if($laporanData->count() > 0)
        <!-- Summary Cards -->
        @php
            $avgPersentase = $laporanData->avg('persentase');
            $totalMhs = $laporanData->sum('total_mahasiswa');
            $totalRecord = $laporanData->sum('total_absensi');
        @endphp
        <div class="stats-grid">
            <div class="stat-card primary animate-in">
                <div class="stat-value">{{ $laporanData->count() }}</div>
                <div class="stat-label">Total Mata Kuliah</div>
            </div>
            <div class="stat-card success animate-in">
                <div class="stat-value">{{ round($avgPersentase, 1) }}%</div>
                <div class="stat-label">Rata-rata Kehadiran</div>
            </div>
            <div class="stat-card secondary animate-in">
                <div class="stat-value">{{ $totalMhs }}</div>
                <div class="stat-label">Total Peserta</div>
            </div>
            <div class="stat-card info animate-in">
                <div class="stat-value">{{ $totalRecord }}</div>
                <div class="stat-label">Total Record</div>
            </div>
        </div>

        <!-- Tabel Laporan -->
        <div class="card animate-in">
            <div class="card-header">
                <h3>Laporan Per Mata Kuliah</h3>
            </div>
            <div class="card-body" style="padding: 0;">
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kode MK</th>
                                <th>Nama Mata Kuliah</th>
                                <th>Dosen</th>
                                <th style="text-align: center;">Mahasiswa</th>
                                <th style="text-align: center;">Hadir</th>
                                <th style="text-align: center;">Izin</th>
                                <th style="text-align: center;">Sakit</th>
                                <th style="text-align: center;">Alpa</th>
                                <th>% Kehadiran</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($laporanData as $index => $data)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td><span style="font-weight: 600; color: #6366f1;">{{ $data->mata_kuliah->kode_mk }}</span></td>
                                    <td style="font-weight: 600;">{{ $data->mata_kuliah->nama_mk }}</td>
                                    <td>{{ $data->mata_kuliah->dosen->name }}</td>
                                    <td style="text-align: center;">{{ $data->total_mahasiswa }}</td>
                                    <td style="text-align: center;"><span class="badge badge-hadir">{{ $data->hadir }}</span></td>
                                    <td style="text-align: center;"><span class="badge badge-izin">{{ $data->izin }}</span></td>
                                    <td style="text-align: center;"><span class="badge badge-sakit">{{ $data->sakit }}</span></td>
                                    <td style="text-align: center;"><span class="badge badge-alpa">{{ $data->alpa }}</span></td>
                                    <td>
                                        <div style="display: flex; align-items: center; gap: 8px;">
                                            <div class="progress-bar-wrapper" style="width: 80px; height: 6px;">
                                                <div class="progress-bar-fill {{ $data->persentase >= 75 ? 'high' : ($data->persentase >= 50 ? 'medium' : 'low') }}" style="width: {{ $data->persentase }}%"></div>
                                            </div>
                                            <span style="font-size: 12px; font-weight: 700; color: {{ $data->persentase >= 75 ? '#10b981' : ($data->persentase >= 50 ? '#f59e0b' : '#ef4444') }};">
                                                {{ $data->persentase }}%
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @else
        <div class="card">
            <div class="card-body">
                <div class="empty-state">
                    <h4>Belum ada data laporan</h4>
                    <p>Data mata kuliah dan absensi belum tersedia.</p>
                </div>
            </div>
        </div>
    @endif
@endsection
