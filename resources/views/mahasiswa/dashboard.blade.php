@extends('layouts.app')

@section('title', 'Dashboard Mahasiswa')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Selamat datang, ' . $user->name . '!')

@section('content')
    @if(isset($sesiAktifHariIni) && $sesiAktifHariIni->count() > 0)
        <div class="alert alert-success animate-in" style="background: linear-gradient(135deg, #ecfdf5, #f0fdf4); border: 1.5px solid #10b981; border-radius: 12px; padding: 16px 20px; margin-bottom: 24px; display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap;">
            <div>
                <h4 style="font-size: 15px; font-weight: 700; color: #065f46; margin: 0 0 2px 0;">
                    Pertemuan Telah Dibuka Oleh Dosen!
                </h4>
                <p style="font-size: 13px; color: #047857; margin: 0;">
                    Ada {{ $sesiAktifHariIni->count() }} mata kuliah yang pertemuannya sedang dibuka untuk absensi hari ini.
                </p>
            </div>
            <a href="{{ route('mahasiswa.absensi') }}" class="btn btn-success" style="padding: 9px 18px; font-size: 13.5px; font-weight: 600; text-decoration: none; border-radius: 8px;">
                Isi Absensi Sekarang
            </a>
        </div>
    @endif

    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card primary animate-in">
            <div class="stat-value">{{ $mataKuliah->count() }}</div>
            <div class="stat-label">Mata Kuliah</div>
        </div>
        <div class="stat-card success animate-in">
            <div class="stat-value">{{ $hadir }}</div>
            <div class="stat-label">Hadir</div>
        </div>
        <div class="stat-card warning animate-in">
            <div class="stat-value">{{ $izin + $sakit }}</div>
            <div class="stat-label">Izin / Sakit</div>
        </div>
        <div class="stat-card danger animate-in">
            <div class="stat-value">{{ $alpa }}</div>
            <div class="stat-label">Alpa</div>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
        <!-- Persentase Kehadiran -->
        <div class="card animate-in">
            <div class="card-header">
                <h3>Persentase Kehadiran</h3>
            </div>
            <div class="card-body" style="text-align: center; padding: 32px 24px;">
                <div style="font-size: 48px; font-weight: 800; color: {{ $persentaseHadir >= 75 ? '#10b981' : ($persentaseHadir >= 50 ? '#f59e0b' : '#ef4444') }}; letter-spacing: -2px; margin-bottom: 8px;">
                    {{ $persentaseHadir }}%
                </div>
                <div style="font-size: 13px; color: #64748b; margin-bottom: 16px;">dari {{ $totalAbsensi }} total pertemuan</div>
                <div class="progress-bar-wrapper" style="max-width: 300px; margin: 0 auto; height: 10px;">
                    <div class="progress-bar-fill {{ $persentaseHadir >= 75 ? 'high' : ($persentaseHadir >= 50 ? 'medium' : 'low') }}" style="width: {{ $persentaseHadir }}%"></div>
                </div>
                <div style="display: flex; justify-content: center; gap: 20px; margin-top: 20px; font-size: 12px;">
                    <span><span class="badge badge-hadir">Hadir {{ $hadir }}</span></span>
                    <span><span class="badge badge-izin">Izin {{ $izin }}</span></span>
                    <span><span class="badge badge-sakit">Sakit {{ $sakit }}</span></span>
                    <span><span class="badge badge-alpa">Alpa {{ $alpa }}</span></span>
                </div>
            </div>
        </div>

        <!-- Jadwal Hari Ini -->
        <div class="card animate-in">
            <div class="card-header">
                <h3>Jadwal Hari Ini ({{ $hariIni }})</h3>
            </div>
            <div class="card-body">
                @if($jadwalHariIni->count() > 0)
                    @foreach($jadwalHariIni as $mk)
                        <div style="padding: 12px 14px; border-radius: 10px; background: #f8fafc; margin-bottom: 10px; border: 1px solid #e2e8f0;">
                            <div style="font-size: 14px; font-weight: 600; color: #0f172a;">{{ $mk->nama_mk }}</div>
                            <div style="font-size: 12px; color: #64748b; margin-top: 2px;">
                                {{ substr($mk->jam_mulai, 0, 5) }} - {{ substr($mk->jam_selesai, 0, 5) }} &middot;
                                {{ $mk->ruangan ?? '-' }} &middot;
                                {{ $mk->dosen->name }}
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="empty-state">
                        <h4>Tidak ada jadwal hari ini</h4>
                        <p>Nikmati hari bebas kuliah!</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Info Mahasiswa -->
    <div class="card animate-in" style="margin-top: 24px;">
        <div class="card-header">
            <h3>Informasi Mahasiswa</h3>
        </div>
        <div class="card-body">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
                <div>
                    <div style="font-size: 12px; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Nama</div>
                    <div style="font-size: 14px; font-weight: 600; color: #0f172a; margin-top: 2px;">{{ $user->name }}</div>
                </div>
                <div>
                    <div style="font-size: 12px; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">NIM</div>
                    <div style="font-size: 14px; font-weight: 600; color: #0f172a; margin-top: 2px;">{{ $user->nim ?? '-' }}</div>
                </div>
                <div>
                    <div style="font-size: 12px; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Program Studi</div>
                    <div style="font-size: 14px; font-weight: 600; color: #0f172a; margin-top: 2px;">{{ $user->program_studi ?? '-' }}</div>
                </div>
                <div>
                    <div style="font-size: 12px; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Kelas</div>
                    <div style="font-size: 14px; font-weight: 600; color: #0f172a; margin-top: 2px;">{{ $user->kelas ?? '-' }}</div>
                </div>
            </div>
        </div>
    </div>
@endsection
