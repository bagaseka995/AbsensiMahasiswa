@extends('layouts.app')

@section('title', 'Kelola Data Mahasiswa')
@section('page-title', 'Kelola Data Mahasiswa')
@section('page-subtitle', 'Manajemen akun mahasiswa, kelas, serta mata kuliah yang diikuti')

@section('content')
    <!-- Top Action & Search Bar -->
    <div class="card animate-in" style="margin-bottom: 20px;">
        <div class="card-body" style="padding: 16px 20px;">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 14px;">
                <!-- Filter & Search Form -->
                <form action="{{ route('admin.mahasiswa.index') }}" method="GET" style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap; flex: 1; max-width: 650px;">
                    <div style="flex: 1; min-width: 200px;">
                        <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari nama, NIM, atau email..." style="height: 40px; font-size: 13px;">
                    </div>
                    <div style="width: 140px;">
                        <select name="kelas" class="form-control" style="height: 40px; font-size: 13px;" onchange="this.form.submit()">
                            <option value="">Semua Kelas</option>
                            @foreach($daftarKelas as $k)
                                <option value="{{ $k }}" {{ request('kelas') == $k ? 'selected' : '' }}>{{ $k }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary" style="height: 40px; padding: 0 16px; font-size: 13px;">
                        Cari
                    </button>
                    @if(request('q') || request('kelas'))
                        <a href="{{ route('admin.mahasiswa.index') }}" class="btn" style="height: 40px; display: inline-flex; align-items: center; padding: 0 14px; background: #f1f5f9; color: #475569; font-size: 13px; text-decoration: none; border-radius: 8px;">
                            Reset
                        </a>
                    @endif
                </form>

                <!-- Tombol Tambah Mahasiswa -->
                <div>
                    <button type="button" onclick="openTambahModal()" class="btn btn-primary" style="height: 40px; padding: 0 18px; font-weight: 600; font-size: 13.5px; display: inline-flex; align-items: center; gap: 6px;">
                        + Tambah Mahasiswa
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Ringkasan Singkat -->
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px; padding: 0 4px;">
        <span style="font-size: 13px; font-weight: 600; color: #475569;">
            Total Mahasiswa Terdaftar: <strong style="color: #4f46e5;">{{ $totalMahasiswa }} Mahasiswa</strong>
        </span>
        @if(request('q') || request('kelas'))
            <span style="font-size: 12px; color: #64748b;">
                Ditemukan {{ $mahasiswa->total() }} hasil pencarian
            </span>
        @endif
    </div>

    <!-- Tabel Daftar Mahasiswa -->
    <div class="card animate-in">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
            <h3>Daftar Mahasiswa</h3>
            <span class="badge badge-role">{{ $mahasiswa->total() }} Data</span>
        </div>
        <div class="card-body" style="padding: 0;">
            @if($mahasiswa->count() > 0)
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th style="width: 50px;">No</th>
                                <th>NIM & Nama</th>
                                <th>Email</th>
                                <th>Kelas & Prodi</th>
                                <th>Mata Kuliah Diikuti</th>
                                <th style="text-align: center; width: 140px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($mahasiswa as $index => $mhs)
                                <tr>
                                    <td>{{ $mahasiswa->firstItem() + $index }}</td>
                                    <td>
                                        <div style="font-weight: 700; font-size: 13.5px; color: #1e293b;">
                                            {{ $mhs->name }}
                                        </div>
                                        <div style="font-size: 11.5px; color: #4f46e5; font-weight: 600;">
                                            NIM: {{ $mhs->nim ?? '-' }}
                                        </div>
                                    </td>
                                    <td style="font-size: 12.5px; color: #475569;">
                                        {{ $mhs->email }}
                                    </td>
                                    <td>
                                        <span class="badge" style="background: #eef2ff; color: #4f46e5; font-size: 11.5px; font-weight: 700;">
                                            {{ $mhs->kelas ?? '-' }}
                                        </span>
                                        <div style="font-size: 11px; color: #64748b; margin-top: 2px;">
                                            {{ $mhs->program_studi ?? '-' }}
                                        </div>
                                    </td>
                                    <td>
                                        <div style="display: flex; flex-wrap: wrap; gap: 4px; max-width: 320px;">
                                            @forelse($mhs->mataKuliah as $mk)
                                                <span class="badge" style="background: #f1f5f9; color: #334155; font-size: 11px; padding: 2px 7px;" title="{{ $mk->nama_mk }}">
                                                    {{ $mk->kode_mk }}
                                                </span>
                                            @empty
                                                <span style="font-size: 11px; color: #94a3b8; font-style: italic;">Belum ada MK</span>
                                            @endforelse
                                        </div>
                                    </td>
                                    <td style="text-align: center;">
                                        <div style="display: inline-flex; gap: 6px;">
                                            <button type="button" class="btn" style="background: #eef2ff; color: #4f46e5; border: 1px solid #c7d2fe; padding: 4px 10px; border-radius: 6px; font-size: 11.5px; font-weight: 600; cursor: pointer;"
                                                onclick='openEditModal(@json($mhs), @json($mhs->mataKuliah->pluck("id")))'
                                            >
                                                Edit
                                            </button>
                                            <form action="{{ route('admin.mahasiswa.destroy', $mhs->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Hapus mahasiswa {{ $mhs->name }}? Seluruh data absensi terkait mahasiswa ini juga akan terhapus.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn" style="background: #fef2f2; color: #ef4444; border: 1px solid #fecaca; padding: 4px 10px; border-radius: 6px; font-size: 11.5px; font-weight: 600; cursor: pointer;">
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Bersih Tanpa Ikon Raksasa -->
                @if($mahasiswa->hasPages())
                    <div style="padding: 14px 20px; border-top: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
                        <div style="font-size: 12.5px; color: #64748b;">
                            Menampilkan <strong>{{ $mahasiswa->firstItem() }}</strong> - <strong>{{ $mahasiswa->lastItem() }}</strong> dari <strong>{{ $mahasiswa->total() }}</strong> mahasiswa
                        </div>
                        <div style="display: flex; gap: 6px; align-items: center;">
                            @if($mahasiswa->onFirstPage())
                                <span style="padding: 6px 12px; font-size: 12px; font-weight: 600; color: #94a3b8; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; cursor: not-allowed;">
                                    Sebelumnya
                                </span>
                            @else
                                <a href="{{ $mahasiswa->previousPageUrl() }}" style="padding: 6px 12px; font-size: 12px; font-weight: 600; color: #4f46e5; background: #ffffff; border: 1px solid #c7d2fe; border-radius: 6px; text-decoration: none;">
                                    Sebelumnya
                                </a>
                            @endif

                            @foreach($mahasiswa->getUrlRange(1, $mahasiswa->lastPage()) as $page => $url)
                                @if($page == $mahasiswa->currentPage())
                                    <span style="padding: 6px 12px; font-size: 12px; font-weight: 700; color: #ffffff; background: #4f46e5; border-radius: 6px;">
                                        {{ $page }}
                                    </span>
                                @else
                                    <a href="{{ $url }}" style="padding: 6px 12px; font-size: 12px; font-weight: 600; color: #475569; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 6px; text-decoration: none;">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach

                            @if($mahasiswa->hasMorePages())
                                <a href="{{ $mahasiswa->nextPageUrl() }}" style="padding: 6px 12px; font-size: 12px; font-weight: 600; color: #4f46e5; background: #ffffff; border: 1px solid #c7d2fe; border-radius: 6px; text-decoration: none;">
                                    Selanjutnya
                                </a>
                            @else
                                <span style="padding: 6px 12px; font-size: 12px; font-weight: 600; color: #94a3b8; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; cursor: not-allowed;">
                                    Selanjutnya
                                </span>
                            @endif
                        </div>
                    </div>
                @endif
            @else
                <div class="empty-state">
                    <h4>Tidak ada data mahasiswa</h4>
                    <p>Silakan klik tombol "+ Tambah Mahasiswa" untuk menambahkan data baru.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Modal Tambah Mahasiswa -->
    <div id="modalTambah" class="custom-modal" style="display: none;">
        <div class="modal-backdrop" onclick="closeTambahModal()"></div>
        <div class="modal-dialog" style="position: relative; z-index: 1001; max-width: 580px; width: 92%; background: #ffffff; border-radius: 14px; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); animation: zoomIn 0.2s ease;">
            <div class="modal-header" style="padding: 16px 20px; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between;">
                <div>
                    <h4 style="font-size: 16px; font-weight: 700; color: #1e293b; margin: 0;">Tambah Mahasiswa Baru</h4>
                    <span style="font-size: 12px; color: #64748b;">Lengkapi formulir di bawah ini untuk membuat akun mahasiswa baru</span>
                </div>
                <button type="button" onclick="closeTambahModal()" style="background: transparent; border: none; font-size: 22px; line-height: 1; color: #64748b; cursor: pointer; padding: 4px 8px;">
                    &times;
                </button>
            </div>
            <form action="{{ route('admin.mahasiswa.store') }}" method="POST">
                @csrf
                <div class="modal-body" style="padding: 20px; max-height: 70vh; overflow-y: auto;">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                        <div class="form-group" style="margin-bottom: 12px;">
                            <label style="font-size: 13px; font-weight: 600;">NIM *</label>
                            <input type="text" name="nim" class="form-control" placeholder="Contoh: 2024021" required>
                        </div>
                        <div class="form-group" style="margin-bottom: 12px;">
                            <label style="font-size: 13px; font-weight: 600;">Kelas *</label>
                            <input type="text" name="kelas" class="form-control" placeholder="Contoh: TI-A" required>
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom: 12px;">
                        <label style="font-size: 13px; font-weight: 600;">Nama Lengkap *</label>
                        <input type="text" name="name" class="form-control" placeholder="Nama lengkap mahasiswa" required>
                    </div>

                    <div style="display: grid; grid-template-columns: 1.2fr 0.8fr; gap: 14px;">
                        <div class="form-group" style="margin-bottom: 12px;">
                            <label style="font-size: 13px; font-weight: 600;">Email Akun *</label>
                            <input type="email" name="email" class="form-control" placeholder="nama@student.ac.id" required>
                        </div>
                        <div class="form-group" style="margin-bottom: 12px;">
                            <label style="font-size: 13px; font-weight: 600;">Password</label>
                            <input type="password" name="password" class="form-control" placeholder="Default: password">
                            <small style="font-size: 11px; color: #94a3b8;">Kosongkan bila ingin memakai password default</small>
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom: 14px;">
                        <label style="font-size: 13px; font-weight: 600;">Program Studi *</label>
                        <input type="text" name="program_studi" class="form-control" value="Teknik Informatika" required>
                    </div>

                    <div class="form-group" style="margin-bottom: 6px;">
                        <label style="font-size: 13px; font-weight: 600; display: block; margin-bottom: 6px;">
                            Pilih Mata Kuliah Yang Diikuti:
                        </label>
                        <div style="display: grid; grid-template-columns: 1fr; gap: 8px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px 14px; max-height: 160px; overflow-y: auto;">
                            @foreach($daftarMK as $mk)
                                <label style="display: flex; align-items: center; gap: 8px; font-size: 12.5px; font-weight: 500; color: #334155; margin: 0; cursor: pointer;">
                                    <input type="checkbox" name="mata_kuliah_ids[]" value="{{ $mk->id }}" checked style="cursor: pointer;">
                                    <span><strong>{{ $mk->kode_mk }}</strong> - {{ $mk->nama_mk }} ({{ $mk->sks }} SKS)</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="padding: 14px 20px; border-top: 1px solid #e2e8f0; display: flex; justify-content: flex-end; gap: 10px;">
                    <button type="button" onclick="closeTambahModal()" class="btn" style="background: #f1f5f9; color: #475569; font-size: 13px; padding: 8px 16px;">
                        Batal
                    </button>
                    <button type="submit" class="btn btn-primary" style="font-size: 13px; padding: 8px 18px; font-weight: 600;">
                        Simpan Mahasiswa Baru
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Mahasiswa -->
    <div id="modalEdit" class="custom-modal" style="display: none;">
        <div class="modal-backdrop" onclick="closeEditModal()"></div>
        <div class="modal-dialog" style="position: relative; z-index: 1001; max-width: 580px; width: 92%; background: #ffffff; border-radius: 14px; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); animation: zoomIn 0.2s ease;">
            <div class="modal-header" style="padding: 16px 20px; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between;">
                <div>
                    <h4 style="font-size: 16px; font-weight: 700; color: #1e293b; margin: 0;">Edit Data Mahasiswa</h4>
                    <span style="font-size: 12px; color: #64748b;">Perbarui informasi identitas atau mata kuliah yang diikuti</span>
                </div>
                <button type="button" onclick="closeEditModal()" style="background: transparent; border: none; font-size: 22px; line-height: 1; color: #64748b; cursor: pointer; padding: 4px 8px;">
                    &times;
                </button>
            </div>
            <form id="formEditMahasiswa" action="" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body" style="padding: 20px; max-height: 70vh; overflow-y: auto;">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                        <div class="form-group" style="margin-bottom: 12px;">
                            <label style="font-size: 13px; font-weight: 600;">NIM *</label>
                            <input type="text" id="edit_nim" name="nim" class="form-control" required>
                        </div>
                        <div class="form-group" style="margin-bottom: 12px;">
                            <label style="font-size: 13px; font-weight: 600;">Kelas *</label>
                            <input type="text" id="edit_kelas" name="kelas" class="form-control" required>
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom: 12px;">
                        <label style="font-size: 13px; font-weight: 600;">Nama Lengkap *</label>
                        <input type="text" id="edit_name" name="name" class="form-control" required>
                    </div>

                    <div style="display: grid; grid-template-columns: 1.2fr 0.8fr; gap: 14px;">
                        <div class="form-group" style="margin-bottom: 12px;">
                            <label style="font-size: 13px; font-weight: 600;">Email Akun *</label>
                            <input type="email" id="edit_email" name="email" class="form-control" required>
                        </div>
                        <div class="form-group" style="margin-bottom: 12px;">
                            <label style="font-size: 13px; font-weight: 600;">Ganti Password</label>
                            <input type="password" name="password" class="form-control" placeholder="Biarkan kosong jika tidak diganti">
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom: 14px;">
                        <label style="font-size: 13px; font-weight: 600;">Program Studi *</label>
                        <input type="text" id="edit_program_studi" name="program_studi" class="form-control" required>
                    </div>

                    <div class="form-group" style="margin-bottom: 6px;">
                        <label style="font-size: 13px; font-weight: 600; display: block; margin-bottom: 6px;">
                            Pilih Mata Kuliah Yang Diikuti:
                        </label>
                        <div style="display: grid; grid-template-columns: 1fr; gap: 8px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px 14px; max-height: 160px; overflow-y: auto;">
                            @foreach($daftarMK as $mk)
                                <label style="display: flex; align-items: center; gap: 8px; font-size: 12.5px; font-weight: 500; color: #334155; margin: 0; cursor: pointer;">
                                    <input type="checkbox" name="mata_kuliah_ids[]" value="{{ $mk->id }}" class="edit_mk_checkbox" id="edit_mk_{{ $mk->id }}" style="cursor: pointer;">
                                    <span><strong>{{ $mk->kode_mk }}</strong> - {{ $mk->nama_mk }} ({{ $mk->sks }} SKS)</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="padding: 14px 20px; border-top: 1px solid #e2e8f0; display: flex; justify-content: flex-end; gap: 10px;">
                    <button type="button" onclick="closeEditModal()" class="btn" style="background: #f1f5f9; color: #475569; font-size: 13px; padding: 8px 16px;">
                        Batal
                    </button>
                    <button type="submit" class="btn btn-primary" style="font-size: 13px; padding: 8px 18px; font-weight: 600;">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
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
    function openTambahModal() {
        document.getElementById('modalTambah').style.display = 'flex';
    }

    function closeTambahModal() {
        document.getElementById('modalTambah').style.display = 'none';
    }

    function openEditModal(mhs, enrolledMkIds) {
        document.getElementById('formEditMahasiswa').action = '/admin/mahasiswa/' + mhs.id;
        document.getElementById('edit_name').value = mhs.name || '';
        document.getElementById('edit_email').value = mhs.email || '';
        document.getElementById('edit_nim').value = mhs.nim || '';
        document.getElementById('edit_kelas').value = mhs.kelas || '';
        document.getElementById('edit_program_studi').value = mhs.program_studi || '';

        // Reset and check enrolled mata kuliah
        document.querySelectorAll('.edit_mk_checkbox').forEach(cb => {
            const mkId = parseInt(cb.value);
            cb.checked = enrolledMkIds.includes(mkId);
        });

        document.getElementById('modalEdit').style.display = 'flex';
    }

    function closeEditModal() {
        document.getElementById('modalEdit').style.display = 'none';
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeTambahModal();
            closeEditModal();
        }
    });
</script>
@endpush
