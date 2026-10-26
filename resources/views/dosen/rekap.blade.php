@extends('layouts.app')

@section('title', 'Rekap Absensi')
@section('page-title', 'Rekap Absensi')
@section('page-subtitle', 'Lihat rekap kehadiran dan verifikasi bukti surat izin/dokter mahasiswa')

@section('content')
    <!-- Filter -->
    <div class="card animate-in">
        <div class="card-header">
            <h3>Filter Mata Kuliah</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('dosen.rekap') }}" method="GET" style="display: flex; gap: 12px; align-items: flex-end; flex-wrap: wrap;">
                <div class="form-group" style="flex: 1; min-width: 250px; margin-bottom: 0;">
                    <label for="mata_kuliah_id">Pilih Mata Kuliah</label>
                    <select name="mata_kuliah_id" id="mata_kuliah_id" class="form-control" required>
                        <option value="">-- Pilih Mata Kuliah --</option>
                        @foreach($daftarMK as $mk)
                            <option value="{{ $mk->id }}" {{ request('mata_kuliah_id') == $mk->id ? 'selected' : '' }}>
                                {{ $mk->kode_mk }} - {{ $mk->nama_mk }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-primary" style="height: 42px;">
                    <i class="fas fa-search"></i> Tampilkan
                </button>
            </form>
        </div>
    </div>

    @if($selectedMK)
        <!-- Ringkasan Persentase Kehadiran Mahasiswa -->
        <div class="card animate-in">
            <div class="card-header">
                <h3>Rekap Persentase: {{ $selectedMK->kode_mk }} - {{ $selectedMK->nama_mk }}</h3>
                <span class="badge badge-role">{{ $rekapData->count() }} Mahasiswa</span>
            </div>
            <div class="card-body" style="padding: 0;">
                @if($rekapData->count() > 0)
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>NIM</th>
                                    <th>Nama Mahasiswa</th>
                                    <th style="text-align: center;">Hadir</th>
                                    <th style="text-align: center;">Izin</th>
                                    <th style="text-align: center;">Sakit</th>
                                    <th style="text-align: center;">Alpa</th>
                                    <th style="text-align: center;">Total</th>
                                    <th>Persentase</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($rekapData as $index => $data)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td><span style="font-weight: 600; color: #6366f1;">{{ $data->mahasiswa->nim ?? '-' }}</span></td>
                                        <td style="font-weight: 600;">{{ $data->mahasiswa->name }}</td>
                                        <td style="text-align: center;"><span class="badge badge-hadir">{{ $data->hadir }}</span></td>
                                        <td style="text-align: center;"><span class="badge badge-izin">{{ $data->izin }}</span></td>
                                        <td style="text-align: center;"><span class="badge badge-sakit">{{ $data->sakit }}</span></td>
                                        <td style="text-align: center;"><span class="badge badge-alpa">{{ $data->alpa }}</span></td>
                                        <td style="text-align: center; font-weight: 600;">{{ $data->total }}</td>
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
                @else
                    <div class="empty-state">
                        <h4>Belum ada data</h4>
                        <p>Belum ada mahasiswa terdaftar untuk mata kuliah ini.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Detail Catatan Absensi & Verifikasi Bukti Foto -->
        <div class="card animate-in">
            <div class="card-header">
                <h3>Riwayat Kehadiran & Bukti Surat (Izin / Sakit)</h3>
                <span style="font-size: 12px; color: #64748b;">
                    Total: {{ $riwayatAbsensi->count() }} Catatan
                </span>
            </div>
            <div class="card-body" style="padding: 0;">
                @if($riwayatAbsensi->count() > 0)
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>Pertemuan & Tanggal</th>
                                    <th>NIM & Mahasiswa</th>
                                    <th>Status</th>
                                    <th>Keterangan</th>
                                    <th style="text-align: center;">Bukti Foto Surat</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($riwayatAbsensi as $ab)
                                    <tr>
                                        <td>
                                            <div style="font-weight: 700; font-size: 13px; color: #4f46e5;">
                                                Pertemuan ke-{{ $ab->pertemuan_ke ?? 1 }}
                                            </div>
                                            <div style="font-size: 11.5px; color: #64748b;">
                                                {{ $ab->tanggal->format('d M Y') }} &middot; {{ substr($ab->waktu, 0, 5) }}
                                            </div>
                                        </td>
                                        <td>
                                            <div style="font-weight: 600; font-size: 13px; color: #1e293b;">
                                                {{ $ab->mahasiswa->name ?? 'Mahasiswa' }}
                                            </div>
                                            <div style="font-size: 11px; color: #64748b;">
                                                NIM: {{ $ab->mahasiswa->nim ?? '-' }}
                                            </div>
                                        </td>
                                        <td>
                                            @php
                                                $badgeClass = match($ab->status) {
                                                    'Hadir' => 'badge-hadir',
                                                    'Izin' => 'badge-izin',
                                                    'Sakit' => 'badge-sakit',
                                                    'Alpa' => 'badge-alpa',
                                                    default => '',
                                                };
                                            @endphp
                                            <span class="badge {{ $badgeClass }}">{{ $ab->status }}</span>
                                        </td>
                                        <td style="font-size: 12.5px; color: #475569; max-width: 250px;">
                                            {{ $ab->keterangan ? $ab->keterangan : '-' }}
                                        </td>
                                        <td style="text-align: center;">
                                            @if($ab->bukti_foto)
                                                <button type="button" class="btn btn-outline" onclick="showBuktiModal('{{ asset($ab->bukti_foto) }}', '{{ $ab->status }}', '{{ $ab->mahasiswa->name }}')" style="background: #eef2ff; color: #4f46e5; border: 1px solid #c7d2fe; padding: 4px 10px; border-radius: 6px; font-size: 11.5px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 4px;">
                                                    Lihat Surat
                                                </button>
                                            @elseif(in_array($ab->status, ['Izin', 'Sakit']))
                                                <span style="font-size: 11px; color: #94a3b8; font-style: italic;">Tidak ada lampiran</span>
                                            @else
                                                <span style="color: #cbd5e1; font-size: 12px;">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="empty-state">
                        <h4>Belum ada riwayat absensi</h4>
                        <p>Belum ada mahasiswa yang mengisi absensi untuk mata kuliah ini.</p>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- Modal Preview Bukti Foto Surat Dokter / Izin -->
    <div id="modalBukti" class="custom-modal" style="display: none;">
        <div class="modal-backdrop" onclick="closeBuktiModal()"></div>
        <div class="modal-dialog" style="position: relative; z-index: 1001; max-width: 500px; width: 90%; background: #ffffff; border-radius: 14px; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); animation: zoomIn 0.2s ease;">
            <div class="modal-header" style="padding: 16px 20px; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between;">
                <div>
                    <h4 id="modalBuktiTitle" style="font-size: 15px; font-weight: 700; color: #1e293b; margin: 0;">Bukti Surat</h4>
                    <span id="modalBuktiSub" style="font-size: 12px; color: #64748b;"></span>
                </div>
                <button type="button" onclick="closeBuktiModal()" style="background: transparent; border: none; font-size: 22px; line-height: 1; color: #64748b; cursor: pointer; padding: 4px 8px;">
                    &times;
                </button>
            </div>
            <div class="modal-body" style="padding: 20px; text-align: center; background: #f8fafc;">
                <img id="modalBuktiImg" src="" alt="Bukti Surat" style="max-width: 100%; max-height: 400px; border-radius: 8px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); object-fit: contain;">
            </div>
            <div class="modal-footer" style="padding: 12px 20px; border-top: 1px solid #e2e8f0; text-align: right;">
                <a id="modalBuktiLink" href="" target="_blank" class="btn btn-primary" style="font-size: 12.5px; padding: 6px 14px; text-decoration: none;">
                    Buka Gambar Asli
                </a>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    @keyframes zoomIn {
        from { opacity: 0; transform: scale(0.95); }
        to { opacity: 1; transform: scale(1); }
    }

    .custom-modal {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        z-index: 1100;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .modal-backdrop {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(15, 23, 42, 0.6);
        backdrop-filter: blur(4px);
    }
</style>
@endpush

@push('scripts')
<script>
    function showBuktiModal(imgUrl, status, mhsNama) {
        const modal = document.getElementById('modalBukti');
        const img = document.getElementById('modalBuktiImg');
        const title = document.getElementById('modalBuktiTitle');
        const sub = document.getElementById('modalBuktiSub');
        const link = document.getElementById('modalBuktiLink');

        img.src = imgUrl;
        title.textContent = 'Bukti Surat ' + status;
        sub.textContent = 'Mahasiswa: ' + mhsNama;
        link.href = imgUrl;

        modal.style.display = 'flex';
    }

    function closeBuktiModal() {
        const modal = document.getElementById('modalBukti');
        if (modal) modal.style.display = 'none';
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeBuktiModal();
        }
    });
</script>
@endpush
