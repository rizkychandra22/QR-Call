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
            
            <div class="col-lg-12">
                {{-- CARD FORM GENERATE --}}
                <div class="card card-primary">
                    <div class="card-header">
                        <h4>{{ $content }}</h4>
                        <div class="card-header-action">
                            <div class="btn-group">
                                <a href="{{ route('admin.dashboard.generate-shift') }}" class="btn btn-warning">
                                    <i class="fas fa-clock mr-1"></i> Check Shift
                                </a>
                                <a href="{{ route('admin.dashboard.check-qr') }}" class="btn btn-danger">
                                    <i class="fas fa-qrcode mr-1"></i> Check QR
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form wire:submit.prevent="generate">
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label>Pilih Jenis Absen</label>
                                    <select wire:model="present" class="form-control @error('present') is-invalid @enderror">
                                        <option value="">--- Pilih Tipe Absen ---</option>
                                        <option value="in_present">Absen Masuk</option>
                                        <option value="out_present">Absen Keluar</option>
                                    </select>
                                    @error('present') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Shift Kerja</label>
                                    <select wire:model="shift_id" class="form-control @error('shift_id') is-invalid @enderror">
                                        <option value="">--- Pilih Shift Kerja ---</option>
                                        @foreach($shifts as $shift)
                                            <option value="{{ $shift->id }}">
                                                Shift {{ $shift->shift_name }} 
                                                ({{ \Carbon\Carbon::parse($shift->in_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($shift->out_time)->format('H:i') }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('shift_id') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Tanggal Absen</label>
                                    <input type="date" wire:model="date" class="form-control @error('date') is-invalid @enderror">
                                    @error('date') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label>Mulai Aktif</label> 
                                    <input type="time" wire:model="start_time" class="form-control @error('start_time') is-invalid @enderror">
                                    @error('start_time') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Berakhir Aktif</label>
                                    <input type="time" wire:model="end_time" class="form-control @error('end_time') is-invalid @enderror">
                                    @error('end_time') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Tampilkan QR-Code Absensi</label>
                                    <button type="submit" class="btn btn-lg btn-primary btn-block">
                                        <i class="fas fa-qrcode mr-2"></i> Generate QR-Code
                                    </button>
                                </div>
                            </div>
                        </form>

                        {{-- RESULT QR TAMPIL DISINI --}}
                        @if ($qr_code_value)
                            <hr>
                            <div class="d-flex justify-content-center mb-4 mt-4" wire:transition>
                                <div class="text-center p-4 rounded bg-white shadow-sm" style="border: 1px solid #e3e3e3; width: fit-content;">
                                    <h6 class="font-weight-bold mb-2 text-primary">BARU DITERBITKAN</h6>
                                    <div class="mb-3">
                                        <span class="badge badge-success">{{ $qr_present_type === 'in_present' ? 'MASUK' : 'KELUAR' }}</span>
                                        <span class="badge badge-dark">SHIFT {{ strtoupper($qr_shift_name) }}</span>
                                    </div>
                                    <div class="d-flex justify-content-center mb-2">
                                        {!! QrCode::size(180)->generate($qr_code_value) !!}
                                    </div>
                                    <div class="text-muted small">{{ $qr_code_value }}</div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- CARD MONITORING QR AKTIF (REALTIME) --}}
                <div class="card" wire:poll.5s>
                    <div class="card-header">
                        <h4><i class="fas fa-broadcast-tower text-danger mr-2"></i> Live QR Code Aktif</h4>
                        <div class="card-header-action">
                            <span class="badge badge-success">Update: {{ now()->format('H:i:s') }}</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @forelse($activeQrs as $active)
                                <div class="col-md-3 col-sm-6 mb-4">
                                    <div class="text-center p-3 border rounded shadow-sm bg-white">
                                        <div class="mb-2">
                                            <span class="badge badge-{{ $active->present == 'in_present' ? 'info' : 'warning' }} text-small">
                                                {{ $active->present == 'in_present' ? 'Masuk' : 'Keluar' }}
                                            </span>
                                        </div>
                                        <h6>{{ $active->shift->shift_name }}</h6>
                                        <div class="d-flex justify-content-center my-2">
                                            {!! QrCode::size(120)->generate($active->qr_code_present) !!}
                                        </div>
                                        <div class="text-small font-weight-bold text-danger">
                                            Selesai: {{ \Carbon\Carbon::parse($active->end_time)->format('H:i') }}
                                        </div>
                                        <div class="text-muted" style="font-size: 10px;">{{ $active->qr_code_present }}</div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12 text-center py-4">
                                    <p class="text-muted">Tidak ada QR Code yang aktif saat ini.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>