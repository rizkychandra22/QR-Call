<div>
    <section class="section">
        <div class="section-header">
            <h1>{{ $subpage }}</h1>
            @include('partials.breadcrumb')
        </div>

        <div class="row">
            {{-- Alert Message --}}
            <div class="col-12">
                @if (session()->has('success') || session()->has('danger'))
                    <div x-data="{ show: true }" 
                         x-show="show" 
                         x-init="setTimeout(() => show = false, 3000)"
                         class="alert alert-{{ session()->has('success') ? 'success' : 'danger' }} alert-dismissible show fade mb-4">
                        <div class="alert-body">
                            <button class="close" @click="show = false"><span>&times;</span></button>
                            {{ session('success') ?? session('danger') }}
                        </div>
                    </div>
                @endif
            </div>

            <div class="col-lg-12 col-md-12 col-12 col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h4>{{ $content }}</h4>
                        <div class="card-header-action">
                            <div class="btn-group">
                                <a href="{{ route('admin.dashboard.generate-qr') }}" class="btn btn-warning">
                                    <i class="fas fa-qrcode mr-1"></i> Presensi QR
                                </a>
                                <a href="{{ route('admin.dashboard.check-qr') }}" class="btn btn-danger">
                                    <i class="fas fa-qrcode mr-1"></i> Check QR
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            {{-- KOLOM KIRI: FORM INPUT --}}
                            <div class="col-md-4">
                                <div class="section-title mt-0">{{ $isEdit ? 'Edit Shift' : 'Tambah Shift Baru' }}</div>
                                <form wire:submit.prevent="{{ $isEdit ? 'update' : 'store' }}">
                                    {{-- Nama Shift --}}
                                    <div class="form-group">
                                        <label>Nama Shift</label>
                                        <input type="text" class="form-control @error('shift_name') is-invalid @enderror" 
                                            wire:model.live="shift_name" placeholder="Contoh: Pagi Grup A">
                                        @error('shift_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    {{-- Kode Shift --}}
                                    <div class="form-group">
                                        <label>Kode Shift (Otomatis)</label>
                                        <input type="text" class="form-control @error('shift_code') is-invalid @enderror" 
                                            wire:model="shift_code" readonly placeholder="PGA01">
                                        
                                        @if($isEdit)
                                            <small class="text-danger font-italic">*Shift code cannot be changed while in edit mode.</small>
                                        @else
                                            <small class="text-danger">*Generated automatically based on your shift name.</small>
                                        @endif

                                        @error('shift_code') 
                                            <div class="invalid-feedback d-block">{{ $message }}</div> 
                                        @enderror
                                    </div>

                                    {{-- Jam Kerja (Kiri-Kanan) --}}
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Jam Masuk</label>
                                                <input type="time" class="form-control @error('in_time') is-invalid @enderror" 
                                                    wire:model="in_time">
                                                @error('in_time') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Jam Pulang</label>
                                                <input type="time" class="form-control @error('out_time') is-invalid @enderror" 
                                                    wire:model="out_time">
                                                @error('out_time') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Action Buttons --}}
                                    <div class="justify-content-start d-flex">
                                        @if($isEdit)
                                            <button type="button" wire:click="resetInput" class="btn btn-danger shadow-sm mr-2 mb-4">
                                                <i class="fas fa-times mr-1"></i> Batal
                                            </button>
                                        @endif
                                        <button type="submit" class="btn btn-primary shadow-sm mb-4">
                                            <i class="fas fa-save mr-1"></i> {{ $isEdit ? 'Update' : 'Simpan' }}
                                        </button>
                                    </div>
                                </form>
                            </div>

                            {{-- KOLOM KANAN: TABEL DATA --}}
                            <div class="col-md-8 border-left">
                                <div class="section-title mt-0">Daftar Jam Kerja Karyawan</div>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover table-md">
                                        <thead class="thead-light text-center">
                                            <tr>
                                                <th width="50">#</th>
                                                <th>Shift</th>
                                                <th>Code</th>
                                                <th>Jam Masuk</th>
                                                <th>Jam Pulang</th>
                                                <th width="120">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($shifts as $shift)
                                                <tr>
                                                    <td class="text-center">{{ $loop->iteration }}</td>
                                                    <td class="font-weight-bold">{{ $shift->shift_name }}</td>
                                                    <td class="font-weight-bold">{{ $shift->shift_code }}</td>
                                                    <td class="text-center">{{ \Carbon\Carbon::parse($shift->in_time)->format('H:i') }}</td>
                                                    <td class="text-center">{{ \Carbon\Carbon::parse($shift->out_time)->format('H:i') }}</td>
                                                    <td class="text-center">
                                                        <button wire:click="edit({{ $shift->id }})" class="btn btn-sm btn-outline-warning mr-1" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button wire:click="delete({{ $shift->id }})" 
                                                                wire:confirm="Hapus Shift {{ $shift->shift_name }} Dengan Code {{ $shift->shift_code }}?"
                                                                class="btn btn-sm btn-outline-danger" title="Hapus">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="text-center text-muted">Belum ada data shift.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>