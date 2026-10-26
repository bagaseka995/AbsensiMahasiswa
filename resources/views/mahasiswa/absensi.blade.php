@extends('layouts.app')

@section('title', 'Absensi Mahasiswa')
@section('page-title', 'Absensi Kehadiran')
@section('page-subtitle', 'Pilih mata kuliah yang pertemuannya telah dibuka oleh dosen')

@section('content')
    <div class="absensi-container" style="display: grid; grid-template-columns: 1.15fr 0.85fr; gap: 24px; align-items: start;">
        <!-- Kolom Form Absensi -->
        <div class="card animate-in">
            <div class="card-header">
                <h3>Form Absensi Mahasiswa</h3>
                <span style="font-size: 12px; color: #64748b; font-weight: 500;">
                    {{ now()->translatedFormat('l, d F Y') }}
                </span>
            </div>
            <div class="card-body">
                @if($sesiAktif->count() > 0)
                    <form action="{{ route('mahasiswa.absensi.store') }}" method="POST" enctype="multipart/form-data" id="formAbsensi">
                        @csrf

                        <!-- 1. Pilih Pertemuan / Mata Kuliah Aktif -->
                        <div class="form-group">
                            <label style="font-size: 13.5px; font-weight: 600; color: #1e293b; margin-bottom: 8px;">
                                1. Pertemuan Perkuliahan Aktif
                            </label>
                            <p style="font-size: 12px; color: #64748b; margin-top: -4px; margin-bottom: 12px;">
                                Dosen telah membuka absensi untuk perkuliahan berikut:
                            </p>

                            <div style="display: flex; flex-direction: column; gap: 10px;">
                                @foreach($sesiAktif as $index => $sesi)
                                    @php
                                        $sudah = in_array($sesi->id, $sudahAbsenSesiIds);
                                    @endphp
                                    <label class="sesi-card-label {{ $sudah ? 'disabled' : '' }}" id="sesi-label-{{ $sesi->id }}">
                                        <input type="radio" name="sesi_absensi_id" value="{{ $sesi->id }}" {{ $sudah ? 'disabled' : ($index === 0 && !$sudah ? 'checked' : '') }} required style="accent-color: #6366f1; width: 18px; height: 18px;">
                                        <div style="flex: 1;">
                                            <div style="display: flex; align-items: center; justify-content: space-between; gap: 8px; flex-wrap: wrap;">
                                                <span style="font-weight: 700; font-size: 14px; color: #1e293b;">
                                                    {{ $sesi->mataKuliah->nama_mk }}
                                                </span>
                                                <span class="badge" style="background: #eef2ff; color: #4f46e5; font-size: 11px;">
                                                    Pertemuan ke-{{ $sesi->pertemuan_ke }}
                                                </span>
                                            </div>
                                            <div style="font-size: 12px; color: #64748b; margin-top: 4px;">
                                                {{ $sesi->mataKuliah->dosen->name ?? 'Dosen' }}
                                                &middot; <span style="color: #475569;">{{ $sesi->mataKuliah->kode_mk }}</span>
                                            </div>
                                            @if($sesi->topik)
                                                <div style="font-size: 11.5px; color: #4f46e5; margin-top: 4px; background: #f5f3ff; display: inline-block; padding: 2px 8px; border-radius: 4px;">
                                                    Topik: {{ $sesi->topik }}
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            @if($sudah)
                                                <span class="badge badge-hadir" style="font-size: 11px;">
                                                    Sudah Absen
                                                </span>
                                            @else
                                                <span class="badge" style="background: #ecfdf5; color: #059669; font-size: 11px;">
                                                    <span style="width: 6px; height: 6px; border-radius: 50%; background: #10b981; display: inline-block; margin-right: 4px; animation: pulse-dot 2s infinite;"></span>
                                                    Terbuka
                                                </span>
                                            @endif
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- 2. Status Kehadiran (Hadir / Izin / Sakit) -->
                        <div class="form-group" style="margin-top: 22px;">
                            <label style="font-size: 13.5px; font-weight: 600; color: #1e293b; margin-bottom: 8px;">
                                2. Pilih Status Kehadiran
                            </label>
                            <div class="status-options" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; margin-top: 6px;">
                                <div class="status-option hadir">
                                    <input type="radio" name="status" id="status_hadir" value="Hadir" checked>
                                    <label for="status_hadir" style="padding: 14px 8px;">
                                        <span style="font-size: 14px; font-weight: 700;">Hadir</span>
                                    </label>
                                </div>
                                <div class="status-option izin">
                                    <input type="radio" name="status" id="status_izin" value="Izin">
                                    <label for="status_izin" style="padding: 14px 8px;">
                                        <span style="font-size: 14px; font-weight: 700;">Izin</span>
                                    </label>
                                </div>
                                <div class="status-option sakit">
                                    <input type="radio" name="status" id="status_sakit" value="Sakit">
                                    <label for="status_sakit" style="padding: 14px 8px;">
                                        <span style="font-size: 14px; font-weight: 700;">Sakit</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- 3. Upload Bukti Foto (Surat Dokter / Surat Izin) - Muncul jika Izin atau Sakit -->
                        <div id="section_bukti_foto" style="display: none; margin-top: 20px; padding: 18px; background: #f8fafc; border: 1.5px dashed #cbd5e1; border-radius: 12px; transition: all 0.3s ease;">
                            <label for="bukti_foto" style="display: block; font-size: 13.5px; font-weight: 700; color: #1e293b; margin-bottom: 4px;">
                                <span id="label_bukti_title">Upload Bukti Surat Dokter / Surat Izin</span>
                                <span style="color: #ef4444;">*</span>
                            </label>
                            <p id="label_bukti_desc" style="font-size: 12px; color: #64748b; margin-bottom: 12px; line-height: 1.5;">
                                Untuk status Izin atau Sakit, Anda wajib melampirkan foto dokumen pendukung.
                            </p>

                            <div class="custom-file-upload" id="upload_box" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 16px; text-align: center; cursor: pointer; transition: all 0.2s;">
                                <input type="file" name="bukti_foto" id="bukti_foto" accept="image/jpeg,image/png,image/jpg,image/webp" style="display: none;">
                                <div id="upload_prompt">
                                    <div style="font-weight: 600; font-size: 14px; color: #1e293b; margin-bottom: 4px;">Pilih foto atau ambil gambar</div>
                                    <div style="font-size: 12px; color: #64748b;">Klik di sini untuk upload surat izin / surat dokter</div>
                                    <div style="font-size: 11px; color: #94a3b8; margin-top: 4px;">Format JPG, PNG, atau WEBP (Maksimal 5 MB)</div>
                                </div>
                                <div id="upload_preview_container" style="display: none; align-items: center; justify-content: center; flex-direction: column; gap: 10px;">
                                    <img id="image_preview" src="" alt="Preview Bukti" style="max-height: 180px; max-width: 100%; border-radius: 8px; border: 1px solid #e2e8f0; object-fit: contain;">
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <span id="file_name_display" style="font-size: 12px; color: #475569; font-weight: 600;"></span>
                                        <button type="button" id="btn_remove_file" style="background: #fee2e2; color: #ef4444; border: none; padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 600; cursor: pointer;">
                                            Hapus
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 4. Keterangan Opsional -->
                        <div class="form-group" style="margin-top: 18px;">
                            <label for="keterangan" style="font-size: 13px; font-weight: 600; color: #1e293b; margin-bottom: 6px;">
                                Catatan / Keterangan (Opsional)
                            </label>
                            <textarea name="keterangan" id="keterangan" class="form-control" placeholder="Tuliskan keterangan jika ada alasan khusus..." rows="2"></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary" style="width: 100%; padding: 13px; font-size: 14.5px; font-weight: 600; margin-top: 12px; display: flex; align-items: center; justify-content: center; gap: 8px;">
                            <i class="fas fa-paper-plane"></i> Simpan Absensi
                        </button>
                    </form>
                @else
                    <!-- Tampilan ketika belum ada dosen yang membuka sesi -->
                    <div style="text-align: center; padding: 48px 20px;">
                        <h4 style="font-size: 17px; font-weight: 700; color: #1e293b; margin-bottom: 8px;">
                            Pertemuan Belum Dibuka Oleh Dosen
                        </h4>
                        <p style="font-size: 13px; color: #64748b; margin-bottom: 20px; line-height: 1.6; max-width: 440px; margin-left: auto; margin-right: auto;">
                            Saat ini belum ada dosen yang membuka pertemuan baru untuk mata kuliah Anda. Absensi hanya dapat diisi setelah dosen pengampu mengklik <strong>"Buka Pertemuan Baru"</strong>.
                        </p>
                        <a href="{{ route('mahasiswa.jadwal') }}" class="btn btn-outline" style="border: 1px solid #cbd5e1; background: #ffffff; color: #475569; padding: 8px 18px; border-radius: 8px; font-size: 13px; text-decoration: none;">
                            Lihat Jadwal Kuliah
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Kolom Riwayat Absensi -->
        <div class="card animate-in">
            <div class="card-header">
                <h3>Riwayat Absensi Anda</h3>
            </div>
            <div class="card-body" style="padding: 0;">
                @if($riwayatAbsensi->count() > 0)
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>Mata Kuliah & Tanggal</th>
                                    <th>Status</th>
                                    <th style="text-align: center;">Bukti</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($riwayatAbsensi as $ab)
                                    <tr>
                                        <td>
                                            <div style="font-weight: 600; font-size: 13.5px; color: #1e293b;">
                                                {{ $ab->mataKuliah->nama_mk }}
                                            </div>
                                            <div style="font-size: 11.5px; color: #64748b; margin-top: 2px;">
                                                @if($ab->pertemuan_ke)
                                                    <span style="font-weight: 600; color: #4f46e5;">P-{{ $ab->pertemuan_ke }}</span> &middot;
                                                @endif
                                                {{ $ab->tanggal->format('d M Y') }} &middot; {{ substr($ab->waktu, 0, 5) }}
                                            </div>
                                            @if($ab->keterangan)
                                                <div style="font-size: 11px; color: #64748b; font-style: italic; margin-top: 2px;">
                                                    "{{ $ab->keterangan }}"
                                                </div>
                                            @endif
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
                                        <td style="text-align: center;">
                                            @if($ab->bukti_foto)
                                                <button type="button" class="btn-view-bukti" onclick="showBuktiModal('{{ asset($ab->bukti_foto) }}', '{{ $ab->status }}', '{{ $ab->mataKuliah->nama_mk }}')" style="background: #eef2ff; color: #4f46e5; border: 1px solid #c7d2fe; padding: 4px 10px; border-radius: 6px; font-size: 11.5px; font-weight: 600; cursor: pointer;">
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
                    <div class="empty-state">
                        <h4>Belum ada riwayat absensi</h4>
                        <p>Catatan absensi Anda akan tampil di sini setelah melakukan absensi.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Modal Preview Bukti Foto -->
    <div id="modalBukti" class="custom-modal" style="display: none;">
        <div class="modal-backdrop" onclick="closeBuktiModal()"></div>
        <div class="modal-dialog" style="position: relative; z-index: 1001; max-width: 500px; width: 90%; background: #ffffff; border-radius: 14px; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); animation: zoomIn 0.2s ease;">
            <div class="modal-header" style="padding: 16px 20px; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between;">
                <div>
                    <h4 id="modalBuktiTitle" style="font-size: 15px; font-weight: 700; color: #1e293b; margin: 0;">Bukti Surat</h4>
                    <span id="modalBuktiSub" style="font-size: 12px; color: #64748b;"></span>
                </div>
                <button type="button" onclick="closeBuktiModal()" style="background: transparent; border: none; font-size: 20px; line-height: 1; color: #64748b; cursor: pointer; padding: 4px 8px;">
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
    @keyframes pulse-dot {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.5; transform: scale(1.4); }
    }

    @keyframes zoomIn {
        from { opacity: 0; transform: scale(0.95); }
        to { opacity: 1; transform: scale(1); }
    }

    .sesi-card-label {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 14px 16px;
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.2s ease;
        background: #ffffff;
    }

    .sesi-card-label:hover:not(.disabled) {
        border-color: #6366f1;
        background: #faf5ff;
        transform: translateY(-1px);
    }

    .sesi-card-label.disabled {
        opacity: 0.6;
        background: #f8fafc;
        cursor: not-allowed;
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
        .absensi-container {
            grid-template-columns: 1fr !important;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const statusRadios = document.querySelectorAll('input[name="status"]');
        const sectionBukti = document.getElementById('section_bukti_foto');
        const labelTitle = document.getElementById('label_bukti_title');
        const labelDesc = document.getElementById('label_bukti_desc');
        const inputBukti = document.getElementById('bukti_foto');
        const uploadBox = document.getElementById('upload_box');
        const uploadPrompt = document.getElementById('upload_prompt');
        const previewContainer = document.getElementById('upload_preview_container');
        const imagePreview = document.getElementById('image_preview');
        const fileNameDisplay = document.getElementById('file_name_display');
        const btnRemove = document.getElementById('btn_remove_file');

        function toggleBuktiFoto() {
            const selectedStatus = document.querySelector('input[name="status"]:checked')?.value;
            if (selectedStatus === 'Izin' || selectedStatus === 'Sakit') {
                if (sectionBukti) sectionBukti.style.display = 'block';
                if (inputBukti) inputBukti.required = true;

                if (selectedStatus === 'Izin') {
                    if (labelTitle) labelTitle.textContent = 'Upload Bukti Foto Surat Izin';
                    if (labelDesc) labelDesc.textContent = 'Untuk status Izin, wajib melampirkan foto dokumen surat izin yang sah.';
                } else {
                    if (labelTitle) labelTitle.textContent = 'Upload Bukti Foto Surat Dokter';
                    if (labelDesc) labelDesc.textContent = 'Untuk status Sakit, wajib melampirkan foto surat keterangan istirahat/dokter.';
                }
            } else {
                if (sectionBukti) sectionBukti.style.display = 'none';
                if (inputBukti) inputBukti.required = false;
            }
        }

        statusRadios.forEach(radio => {
            radio.addEventListener('change', toggleBuktiFoto);
        });

        // Trigger on load
        toggleBuktiFoto();

        // Custom upload trigger
        if (uploadBox && inputBukti) {
            uploadBox.addEventListener('click', function(e) {
                if (e.target.id !== 'btn_remove_file' && !e.target.closest('#btn_remove_file')) {
                    inputBukti.click();
                }
            });

            inputBukti.addEventListener('change', function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        imagePreview.src = e.target.result;
                        fileNameDisplay.textContent = file.name;
                        uploadPrompt.style.display = 'none';
                        previewContainer.style.display = 'flex';
                    };
                    reader.readAsDataURL(file);
                }
            });

            if (btnRemove) {
                btnRemove.addEventListener('click', function(e) {
                    e.stopPropagation();
                    inputBukti.value = '';
                    imagePreview.src = '';
                    fileNameDisplay.textContent = '';
                    uploadPrompt.style.display = 'block';
                    previewContainer.style.display = 'none';
                });
            }
        }
    });

    // Modal Bukti Preview
    function showBuktiModal(imgUrl, status, mkNama) {
        const modal = document.getElementById('modalBukti');
        const img = document.getElementById('modalBuktiImg');
        const title = document.getElementById('modalBuktiTitle');
        const sub = document.getElementById('modalBuktiSub');
        const link = document.getElementById('modalBuktiLink');

        img.src = imgUrl;
        title.textContent = 'Bukti Surat ' + status;
        sub.textContent = mkNama;
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
