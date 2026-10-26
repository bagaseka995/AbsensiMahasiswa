@extends('layouts.app')

@section('title', 'Jadwal Kuliah')
@section('page-title', 'Jadwal Kuliah')
@section('page-subtitle', 'Daftar jadwal mata kuliah Anda')

@section('content')
    @foreach($hariUrut as $hari)
        @if(isset($jadwalPerHari[$hari]))
            <div class="card animate-in" style="margin-bottom: 20px;">
                <div class="card-header">
                    <h3>
                        {{ $hari }}
                    </h3>
                    <span class="badge badge-role">{{ $jadwalPerHari[$hari]->count() }} Mata Kuliah</span>
                </div>
                <div class="card-body" style="padding: 0;">
                    <div class="table-responsive">
                        <table style="table-layout: fixed; width: 100%; min-width: 750px;">
                            <thead>
                                <tr>
                                    <th style="width: 14%;">Kode MK</th>
                                    <th style="width: 26%;">Mata Kuliah</th>
                                    <th style="width: 24%;">Dosen</th>
                                    <th style="width: 18%;">Jam</th>
                                    <th style="width: 12%;">Ruangan</th>
                                    <th style="width: 6%; text-align: center;">SKS</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($jadwalPerHari[$hari] as $mk)
                                    <tr>
                                        <td><span style="font-weight: 600; color: #6366f1;">{{ $mk->kode_mk }}</span></td>
                                        <td style="font-weight: 600;">{{ $mk->nama_mk }}</td>
                                        <td>{{ $mk->dosen->name }}</td>
                                        <td>
                                            <span style="background: #f1f5f9; padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 600; white-space: nowrap; display: inline-block;">
                                                {{ substr($mk->jam_mulai, 0, 5) }} - {{ substr($mk->jam_selesai, 0, 5) }}
                                            </span>
                                        </td>
                                        <td>{{ $mk->ruangan ?? '-' }}</td>
                                        <td style="text-align: center;">{{ $mk->sks }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    @endforeach

    @if($jadwalPerHari->isEmpty())
        <div class="card">
            <div class="card-body">
                <div class="empty-state">
                    <h4>Belum ada jadwal kuliah</h4>
                    <p>Anda belum terdaftar di mata kuliah manapun.</p>
                </div>
            </div>
        </div>
    @endif
@endsection
