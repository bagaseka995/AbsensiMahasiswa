@extends('layouts.app')

@section('title', 'Sesi Pertemuan Perkuliahan')
@section('page-title', 'Pertemuan & Sesi Absensi')
@section('page-subtitle', 'Buka pertemuan baru agar mahasiswa dapat mengisi absensi')

@section('content')
    <div class="sesi-container" style="display: grid; grid-template-columns: 0.9fr 1.1fr; gap: 24px; align-items: start;">
        <!-- Form Buka Pertemuan Baru -->
        <div class="card animate-in">
            <div class="card-header">
                <h3>Buka Pertemuan Baru</h3>
                <span style="font-size: 12px; color: #64748b; font-weight: 500;">
                    {{ now()->translatedFormat('l, d F Y') }}
                </span>
            </div>
            <div class="card-body">
                <form action="{{ route('dosen.sesi-absensi.buka') }}" method="POST">
                    @csrf
                    <!-- Pilih Mata Kuliah -->
                    <div class="form-group">
                        <label for="mata_kuliah_id" style="font-size: 13.5px; font-weight: 600; color: #1e293b;">
                            1. Mata Kuliah
                        </label>
                        <select name="mata_kuliah_id" id="mata_kuliah_id" class="form-control" required>
                            <option value="">-- Pilih Mata Kuliah --</option>
                            @foreach($daftarMK as $mk)
                                <option value="{{ $mk->id }}" data-next-pertemuan="{{ $mk->next_pertemuan }}" {{ old('mata_kuliah_id') == $mk->id ? 'selected' : '' }}>
                                    {{ $mk->kode_mk }} - {{ $mk->nama_mk }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Nomor Pertemuan (Otomatis) -->
                    <div class="form-group">
                        <label style="font-size: 13.5px; font-weight: 600; color: #1e293b;">
                            2. Nomor Pertemuan (Otomatis)
                        </label>
                        <div style="display: flex; align-items: center; gap: 12px; padding: 12px 16px; background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 8px;">
                            <span class="badge" id="badge_pertemuan_ke" style="background: linear-gradient(135deg, #6366f1, #4f46e5); color: #ffffff; font-size: 13.5px; padding: 6px 14px; font-weight: 700; border-radius: 6px;">
                                Pertemuan ke-1
                            </span>
                            <span id="text_pertemuan_info" style="font-size: 12px; color: #64748b;">
                                Nomor pertemuan dihitung otomatis oleh sistem (1, 2, 3, dst).
                            </span>
                        </div>
                    </div>

                    <!-- Topik / Pokok Bahasan -->
                    <div class="form-group">
                        <label for="topik" style="font-size: 13.5px; font-weight: 600; color: #1e293b;">
                            3. Topik / Materi Kuliah (Opsional)
                        </label>
                        <input type="text" name="topik" id="topik" class="form-control" value="{{ old('topik') }}" placeholder="Contoh: Pengenalan HTML & CSS Dasar">
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px; font-weight: 600; font-size: 14px; margin-top: 10px;">
                        <i class="fas fa-door-open"></i> Buka Pertemuan Baru
                    </button>
                </form>

                <div style="margin-top: 18px; padding: 14px; background: #f8fafc; border-radius: 10px; border-left: 3.5px solid #10b981;">
                    <p style="font-size: 12.5px; color: #475569; margin: 0; line-height: 1.6;">
                        <strong>Alur Absensi:</strong> Setelah Anda mengklik <em>"Buka Pertemuan Baru"</em>, mahasiswa yang terdaftar di kelas dapat langsung mengisi absensi (Hadir, Izin, atau Sakit). Untuk status Izin dan Sakit, mahasiswa diwajibkan melampirkan foto surat dokter/izin.
                    </p>
                </div>
            </div>
        </div>

        <!-- Sesi Pertemuan Yang Sedang Aktif -->
        <div style="display: flex; flex-direction: column; gap: 20px;">
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <h3 style="font-size: 16px; font-weight: 700; color: #1e293b; margin: 0;">
                    Pertemuan Yang Sedang Aktif ({{ $sesiAktif->count() }})
                </h3>
            </div>

            @forelse($sesiAktif as $sesi)
                <div class="card animate-in" style="border: 2px solid #10b981; box-shadow: 0 4px 15px rgba(16, 185, 129, 0.12);">
                    <div class="card-header" style="background: linear-gradient(135deg, #ecfdf5, #f0fdf4); padding: 16px 20px;">
                        <div>
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <h3 style="font-size: 16px; margin: 0; color: #065f46;">
                                    {{ $sesi->mataKuliah->nama_mk }}
                                </h3>
                                <span class="badge" style="background: #10b981; color: #ffffff; font-size: 11px; font-weight: 700;">
                                    Pertemuan ke-{{ $sesi->pertemuan_ke }}
                                </span>
                            </div>
                            <div style="font-size: 12px; color: #047857; margin-top: 4px;">
                                {{ $sesi->mataKuliah->kode_mk }} &middot; Dibuka {{ $sesi->started_at->format('H:i') }} ({{ $sesi->started_at->diffForHumans() }})
                                @if($sesi->topik)
                                    &middot; <strong>Materi:</strong> {{ $sesi->topik }}
                                @endif
                            </div>
                        </div>
                        <div style="display: flex; align-items: center; gap: 6px;">
                            <span style="width: 8px; height: 8px; border-radius: 50%; background: #10b981; display: inline-block; animation: pulse-dot 2s infinite;"></span>
                            <span style="font-size: 11.5px; color: #059669; font-weight: 700; letter-spacing: 0.5px;">DIBUKA</span>
                        </div>
                    </div>

                    <div class="card-body" style="padding: 18px 20px;">
                        <!-- Ringkasan Mahasiswa Yang Hadir di Sesi Ini -->
                        @php
                            $totalMasuk = $sesi->absensi->count();
                            $hadirCount = $sesi->absensi->where('status', 'Hadir')->count();
                            $izinCount = $sesi->absensi->where('status', 'Izin')->count();
                            $sakitCount = $sesi->absensi->where('status', 'Sakit')->count();
                        @endphp
                        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; margin-bottom: 16px; text-align: center;">
                            <div style="padding: 10px; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0;">
                                <div style="font-size: 18px; font-weight: 800; color: #1e293b;">{{ $totalMasuk }}</div>
                                <div style="font-size: 11px; color: #64748b; font-weight: 600;">Total Absen</div>
                            </div>
                            <div style="padding: 10px; background: #ecfdf5; border-radius: 8px; border: 1px solid #a7f3d0;">
                                <div style="font-size: 18px; font-weight: 800; color: #059669;">{{ $hadirCount }}</div>
                                <div style="font-size: 11px; color: #059669; font-weight: 600;">Hadir</div>
                            </div>
                            <div style="padding: 10px; background: #f0f9ff; border-radius: 8px; border: 1px solid #bae6fd;">
                                <div style="font-size: 18px; font-weight: 800; color: #0284c7;">{{ $izinCount }}</div>
                                <div style="font-size: 11px; color: #0284c7; font-weight: 600;">Izin</div>
                            </div>
                            <div style="padding: 10px; background: #fffbeb; border-radius: 8px; border: 1px solid #fde68a;">
                                <div style="font-size: 18px; font-weight: 800; color: #d97706;">{{ $sakitCount }}</div>
                                <div style="font-size: 11px; color: #d97706; font-weight: 600;">Sakit</div>
                            </div>
                        </div>

                        <!-- Daftar Mahasiswa Yang Telah Absen di Pertemuan Ini -->
                        <div style="font-size: 13px; font-weight: 700; color: #1e293b; margin-bottom: 8px; display: flex; align-items: center; justify-content: space-between;">
                            <span>Daftar Mahasiswa Masuk:</span>
                            <span style="font-size: 11px; font-weight: 500; color: #64748b;">Update otomatis saat mahasiswa absen</span>
                        </div>

                        @if($totalMasuk > 0)
                            <div class="table-responsive" style="max-height: 220px; overflow-y: auto; border: 1px solid #e2e8f0; border-radius: 8px;">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Mahasiswa</th>
                                            <th>Waktu</th>
                                            <th>Status</th>
                                            <th style="text-align: center;">Bukti Surat</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($sesi->absensi as $ab)
                                            <tr>
                                                <td>
                                                    <div style="font-weight: 600; font-size: 12.5px; color: #1e293b;">
                                                        {{ $ab->mahasiswa->name ?? 'Mahasiswa' }}
                                                    </div>
                                                    <div style="font-size: 11px; color: #64748b;">
                                                        NIM: {{ $ab->mahasiswa->nim ?? '-' }}
                                                    </div>
                                                </td>
                                                <td style="font-size: 12px; color: #64748b;">
                                                    {{ substr($ab->waktu, 0, 5) }}
                                                </td>
                                                <td>
                                                    @php
                                                        $badgeClass = match($ab->status) {
                                                            'Hadir' => 'badge-hadir',
                                                            'Izin' => 'badge-izin',
                                                            'Sakit' => 'badge-sakit',
                                                            default => '',
                                                        };
                                                    @endphp
                                                    <span class="badge {{ $badgeClass }}" style="font-size: 11px;">
                                                        {{ $ab->status }}
                                                    </span>
                                                </td>
                                                <td style="text-align: center;">
                                                    @if($ab->bukti_foto)
                                                        <button type="button" onclick="showBuktiModal('{{ asset($ab->bukti_foto) }}', '{{ $ab->status }}', '{{ $ab->mahasiswa->name }}')" style="background: #eef2ff; color: #4f46e5; border: 1px solid #c7d2fe; padding: 4px 10px; border-radius: 6px; font-size: 11.5px; font-weight: 600; cursor: pointer;">
                                                            Lihat
                                                        </button>
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
                            <div style="text-align: center; padding: 20px; background: #f8fafc; border-radius: 8px; border: 1px dashed #cbd5e1; color: #64748b; font-size: 12.5px;">
                                Menunggu mahasiswa mengisi absensi pada pertemuan ini...
                            </div>
                        @endif

                        <!-- Tombol Tutup Sesi -->
                        <div style="margin-top: 16px; padding-top: 14px; border-top: 1px solid #e2e8f0; display: flex; justify-content: flex-end;">
                            <form action="{{ route('dosen.sesi-absensi.tutup', $sesi->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn" style="background: #fef2f2; color: #ef4444; border: 1px solid #fecaca; padding: 8px 18px; border-radius: 8px; font-weight: 600; font-size: 13px; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;"
                                    onclick="return confirm('Apakah Anda yakin ingin menutup pertemuan ke-{{ $sesi->pertemuan_ke }} ini? Mahasiswa tidak akan dapat mengisi absensi lagi.')">
                                    Tutup Pertemuan
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="card animate-in" style="border: 2px dashed #cbd5e1; background: #f8fafc; text-align: center; padding: 48px 24px;">
                    <h3 style="font-size: 16px; font-weight: 700; color: #334155; margin-bottom: 6px;">Tidak Ada Pertemuan Aktif</h3>
                    <p style="font-size: 13px; color: #64748b; margin: 0 auto; max-width: 360px; line-height: 1.5;">
                        Silakan pilih mata kuliah di formulir samping dan klik <strong>"Buka Pertemuan Baru"</strong> agar mahasiswa dapat mulai absen.
                    </p>
                </div>
            @endforelse

            <!-- Riwayat Pertemuan Yang Telah Selesai -->
            @if(isset($riwayatSesi) && $riwayatSesi->count() > 0)
                <div class="card animate-in" style="margin-top: 10px;">
                    <div class="card-header">
                        <h3>Riwayat Pertemuan Yang Telah Ditutup</h3>
                    </div>
                    <div class="card-body" style="padding: 0;">
                        <div class="table-responsive">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Mata Kuliah & Pertemuan</th>
                                        <th>Tanggal & Topik</th>
                                        <th style="text-align: center;">Total Mahasiswa</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($riwayatSesi as $rs)
                                        <tr>
                                            <td>
                                                <div style="font-weight: 600; font-size: 13.5px; color: #1e293b;">
                                                    {{ $rs->mataKuliah->nama_mk }}
                                                </div>
                                                <div style="font-size: 11.5px; color: #4f46e5; font-weight: 600;">
                                                    Pertemuan ke-{{ $rs->pertemuan_ke }}
                                                </div>
                                            </td>
                                            <td>
                                                <div style="font-size: 12.5px; color: #334155;">
                                                    {{ $rs->tanggal->format('d M Y') }}
                                                </div>
                                                <div style="font-size: 11.5px; color: #64748b;">
                                                    {{ $rs->topik ? 'Materi: ' . $rs->topik : '-' }}
                                                </div>
                                            </td>
                                            <td style="text-align: center; font-weight: 700; color: #1e293b;">
                                                <span class="badge" style="background: #eef2ff; color: #4f46e5; font-size: 12px;">
                                                    {{ $rs->absensi_count }} Mahasiswa
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge" style="background: #f1f5f9; color: #64748b; font-size: 11px;">
                                                    Selesai
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

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
        </div>
    </div>
@endsection

@push('styles')
<style>
    @keyframes pulse-dot {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.5; transform: scale(1.4); }
    }

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

    @media (max-width: 900px) {
        .sesi-container {
            grid-template-columns: 1fr !important;
        }
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

    document.addEventListener('DOMContentLoaded', function() {
        const mkSelect = document.getElementById('mata_kuliah_id');
        const badgePertemuan = document.getElementById('badge_pertemuan_ke');
        const textInfo = document.getElementById('text_pertemuan_info');

        function updatePertemuanDisplay() {
            if (!mkSelect || !badgePertemuan) return;
            const selectedOpt = mkSelect.options[mkSelect.selectedIndex];
            const nextPertemuan = selectedOpt ? selectedOpt.getAttribute('data-next-pertemuan') : null;

            if (nextPertemuan && selectedOpt.value) {
                badgePertemuan.textContent = 'Pertemuan ke-' + nextPertemuan;
                badgePertemuan.style.background = 'linear-gradient(135deg, #6366f1, #4f46e5)';
                textInfo.textContent = 'Nomor pertemuan berikutnya untuk kelas ini (otomatis).';
            } else {
                badgePertemuan.textContent = 'Pertemuan ke-...';
                badgePertemuan.style.background = '#94a3b8';
                textInfo.textContent = 'Pilih mata kuliah terlebih dahulu untuk melihat nomor pertemuan.';
            }
        }

        if (mkSelect) {
            mkSelect.addEventListener('change', updatePertemuanDisplay);
            updatePertemuanDisplay();
        }
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeBuktiModal();
        }
    });
</script>
@endpush
