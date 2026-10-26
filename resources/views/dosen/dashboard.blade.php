@extends('layouts.app')

@section('title', 'Dashboard Dosen')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Selamat datang, ' . $user->name . '!')

@section('content')
    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card primary animate-in">
            <div class="stat-value">{{ $mataKuliah->count() }}</div>
            <div class="stat-label">Mata Kuliah {{ $user->isAdmin() ? 'Total' : 'Diampu' }}</div>
        </div>
        <div class="stat-card secondary animate-in">
            <div class="stat-value">{{ $totalMahasiswa }}</div>
            <div class="stat-label">Total Mahasiswa</div>
        </div>
        <div class="stat-card success animate-in">
            <div class="stat-value">{{ $persentaseHadir }}%</div>
            <div class="stat-label">Tingkat Kehadiran</div>
        </div>
        <div class="stat-card info animate-in">
            <div class="stat-value">{{ $statsAbsensi->total ?? 0 }}</div>
            <div class="stat-label">Total Record Absensi</div>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
        <!-- Statistik Kehadiran -->
        <div class="card animate-in">
            <div class="card-header">
                <h3>Statistik Kehadiran Global</h3>
            </div>
            <div class="card-body">
                @php
                    $total = $statsAbsensi->total ?? 0;
                    $items = [
                        ['label' => 'Hadir', 'value' => $statsAbsensi->hadir ?? 0, 'color' => '#10b981', 'bg' => '#ecfdf5'],
                        ['label' => 'Izin', 'value' => $statsAbsensi->izin ?? 0, 'color' => '#0ea5e9', 'bg' => '#f0f9ff'],
                        ['label' => 'Sakit', 'value' => $statsAbsensi->sakit ?? 0, 'color' => '#f59e0b', 'bg' => '#fffbeb'],
                        ['label' => 'Alpa', 'value' => $statsAbsensi->alpa ?? 0, 'color' => '#ef4444', 'bg' => '#fef2f2'],
                    ];
                @endphp
                @foreach($items as $item)
                    <div style="display: flex; align-items: center; gap: 14px; padding: 12px 0; {{ !$loop->last ? 'border-bottom: 1px solid #f1f5f9;' : '' }}">
                        <div style="width: 40px; height: 40px; border-radius: 8px; background: {{ $item['bg'] }}; display: flex; align-items: center; justify-content: center;">
                            <span style="font-size: 16px; font-weight: 800; color: {{ $item['color'] }};">{{ $item['value'] }}</span>
                        </div>
                        <div style="flex: 1;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                                <span style="font-size: 13px; font-weight: 600; color: #334155;">{{ $item['label'] }}</span>
                                <span style="font-size: 12px; color: #94a3b8; font-weight: 600;">{{ $total > 0 ? round(($item['value'] / $total) * 100, 1) : 0 }}%</span>
                            </div>
                            <div class="progress-bar-wrapper" style="height: 6px;">
                                <div style="height: 100%; border-radius: 10px; width: {{ $total > 0 ? ($item['value'] / $total) * 100 : 0 }}%; background: {{ $item['color'] }}; transition: width 0.6s ease;"></div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Daftar Mata Kuliah -->
        <div class="card animate-in">
            <div class="card-header">
                <h3>Mata Kuliah {{ $user->isAdmin() ? '' : 'yang Diampu' }}</h3>
            </div>
            <div class="card-body" style="padding: 0;">
                @if($mataKuliah->count() > 0)
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>Kode MK</th>
                                    <th>Nama MK</th>
                                    <th>Jadwal</th>
                                    @if($user->isAdmin())
                                        <th>Dosen</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($mataKuliah as $mk)
                                    <tr>
                                        <td><span style="font-weight: 600; color: #6366f1;">{{ $mk->kode_mk }}</span></td>
                                        <td style="font-weight: 600;">{{ $mk->nama_mk }}</td>
                                        <td>
                                            <span style="background: #f1f5f9; padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 500;">
                                                {{ $mk->hari }}, {{ substr($mk->jam_mulai, 0, 5) }} - {{ substr($mk->jam_selesai, 0, 5) }}
                                            </span>
                                        </td>
                                        @if($user->isAdmin())
                                            <td>{{ $mk->dosen->name }}</td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="empty-state">
                        <h4>Belum ada mata kuliah</h4>
                        <p>Data mata kuliah belum tersedia.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
