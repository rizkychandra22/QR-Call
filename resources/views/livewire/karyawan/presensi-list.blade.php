<div>
    <section class="section">
        <div class="section-header">
            <h1>{{ $subpage }}</h1>
            @include('partials.templates.breadcrumb')
        </div>

        <div class="row">
            <div class="col-12">
                @include('partials.global.session-message')
            </div>
        </div>

        @if ($errors->any())
            <div class="row">
                <div class="col-12">
                    <div class="alert alert-danger alert-dismissible show fade">
                        <div class="alert-body">
                            <button class="close" data-dismiss="alert">
                                <span>&times;</span>
                            </button>
                            <div class="font-weight-bold mb-2">Presensi gagal diproses.</div>
                            <ul class="mb-0 pl-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="row" data-qr-attendance-page>
            <div class="col-lg-5 col-md-12 col-12">
                <div class="card card-primary">
                    <div class="card-header">
                        <h4>{{ $content }}</h4>
                        <div class="card-header-action">
                            <div class="btn-group">
                                <button type="button" class="btn btn-primary" id="btn-start-scanner">
                                    <i class="fas fa-camera mr-1"></i> Aktifkan Kamera
                                </button>
                                <button type="button" class="btn btn-dark" id="btn-stop-scanner">
                                    <i class="fas fa-stop-circle mr-1"></i> Stop
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="section-title mt-0">Scan QR Kehadiran</div>
                        <p class="text-muted mb-3">
                            Gunakan kamera untuk memindai QR aktif, lalu kirim presensi dari lokasi perangkat Anda.
                        </p>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="font-weight-bold">Status Scanner</span>
                                <span class="badge badge-light" id="qr-scanner-status">Belum aktif</span>
                            </div>
                            <div id="qr-reader" wire:ignore style="min-height: 260px; border: 2px dashed #dfe4ea; border-radius: 12px; padding: 12px; background: #fafbff;"></div>
                            <small class="text-muted d-block mt-2">
                                Jika kamera tidak tersedia, Anda tetap bisa menempelkan kode QR secara manual.
                            </small>
                        </div>

                        <form wire:submit.prevent="submitPresent">
                            <div class="form-group">
                                <label for="qr-code-value">Kode QR</label>
                                <input
                                    id="qr-code-value"
                                    type="text"
                                    wire:model="qrCodeValue"
                                    class="form-control @error('qrCodeValue') is-invalid @enderror"
                                    placeholder="Scan kamera atau tempel token QR di sini">
                                @error('qrCodeValue') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            <div class="card bg-light border-0 shadow-none mb-3">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="font-weight-bold">Lokasi Perangkat</span>
                                        <button type="button" class="btn btn-sm btn-outline-primary" id="btn-capture-location">
                                            <i class="fas fa-crosshairs mr-1"></i> Ambil Lokasi
                                        </button>
                                    </div>
                                    <div class="small text-muted mb-2" id="attendance-location-status">Lokasi perangkat saat ini.</div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label class="small mb-1">Latitude</label>
                                            <input id="attendance-latitude" type="text" wire:model="latitude" class="form-control form-control-sm @error('latitude') is-invalid @enderror" readonly>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="small mb-1">Longitude</label>
                                            <input id="attendance-longitude" type="text" wire:model="longitude" class="form-control form-control-sm @error('longitude') is-invalid @enderror" readonly>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="small mb-1">Akurasi</label>
                                            <input id="attendance-accuracy" type="text" wire:model="locationAccuracy" class="form-control form-control-sm" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary btn-lg btn-block">
                                <i class="fas fa-paper-plane mr-2"></i> Kirim Presensi
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-7 col-md-12 col-12">
                <div class="card card-info" wire:poll.1s>
                    <div class="card-header">
                        <h4><i class="fas fa-broadcast-tower text-info mr-2"></i>QR Aktif Saat Ini</h4>
                        <div class="card-header-action">
                            <span class="badge badge-info">{{ now()->format('H:i:s') }}</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @forelse ($activeQrs as $active)
                                <div class="col-md-6 mb-3" wire:key="active-qr-{{ $active->id }}">
                                    <div class="border rounded p-3 h-100">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div>
                                                <div class="font-weight-bold">{{ $active->shift->shift_name }}</div>
                                                <small class="text-muted">{{ $active->shift->shift_code }}</small>
                                            </div>
                                            <span class="badge badge-{{ $active->present === 'in_present' ? 'primary' : 'warning' }}">
                                                {{ $active->present === 'in_present' ? 'Masuk' : 'Keluar' }}
                                            </span>
                                        </div>
                                        <div class="text-center my-3">
                                            <div class="d-inline-flex align-items-center justify-content-center bg-white border rounded p-2 shadow-sm">
                                                {!! QrCode::size(120)->generate($active->qr_code_present) !!}
                                            </div>
                                            <div class="small text-muted mt-2" style="word-break: break-all;">
                                                {{ $active->qr_code_present }}
                                            </div>
                                        </div>
                                        <div class="small text-muted mb-1">Aktif sampai {{ \Carbon\Carbon::parse($active->end_time)->format('H:i') }}</div>
                                        <div class="small text-muted mb-1">Lokasi {{ $active->radiusPresent->name }}</div>
                                        <div class="small text-muted">Radius {{ number_format($active->radiusPresent->radius, 0, ',', '.') }} meter</div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12">
                                    <div class="alert alert-light border mb-0">
                                        Tidak ada QR aktif saat ini. Tunggu admin menerbitkan QR absen untuk shift Anda.
                                    </div>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="card card-secondary">
                    <div class="card-header">
                        <h4 class="section-title mt-0">Riwayat Kehadiran</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover table-md">
                                <thead class="thead-light">
                                    <tr>
                                        <th width="35">#</th>
                                        <th>Tanggal</th>
                                        <th>Shift</th>
                                        <th>Jenis</th>
                                        <th>Waktu Hadir</th>
                                        <th>Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($presents as $present)
                                        <tr wire:key="present-{{ $present->id }}">
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ \Carbon\Carbon::parse($present->date)->translatedFormat('d M Y') }}</td>
                                            <td>
                                                <div class="font-weight-bold">{{ $present->shift->shift_name }}</div>
                                                <small class="text-muted">{{ $present->shift->shift_code }}</small>
                                            </td>
                                            <td>
                                                <span class="badge badge-{{ $present->qrCode->present === 'in_present' ? 'primary' : 'warning' }}">
                                                    {{ $present->qrCode->present === 'in_present' ? 'Absen Masuk' : 'Absen Keluar' }}
                                                </span>
                                                <span class="badge badge-success">{{ $present->status }}</span>
                                            </td>
                                            <td>
                                                <div>{{ \Carbon\Carbon::parse($present->time)->format('H:i:s') }}</div>
                                                @if ($present->hours)
                                                    <small class="text-muted">Durasi {{ $present->hours }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                <div>{{ $present->present_desc_system ?? '-' }}</div>
                                                @if ($present->lat_location_present && $present->lng_location_present)
                                                    <small class="text-muted">
                                                        {{ number_format($present->lat_location_present, 6, '.', '') }},
                                                        {{ number_format($present->lng_location_present, 6, '.', '') }}
                                                    </small>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-4">
                                                Belum ada riwayat presensi. Scan QR pertama Anda dari panel di sebelah kiri.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

@push('scripts')
<script>
    (() => {
        if (window.karyawanQrAttendance) {
            return;
        }

        window.karyawanQrAttendance = {
            scanner: null,
            starting: false,

            getStatusElement() {
                return document.getElementById('qr-scanner-status');
            },

            setScannerStatus(message, type = 'light') {
                const statusElement = this.getStatusElement();

                if (!statusElement) {
                    return;
                }

                statusElement.className = `badge badge-${type}`;
                statusElement.textContent = message;
            },

            setLocationStatus(message, type = 'muted') {
                const target = document.getElementById('attendance-location-status');

                if (!target) {
                    return;
                }

                target.className = type === 'danger' ? 'small text-danger mb-2' : 'small text-muted mb-2';
                target.textContent = message;
            },

            syncInput(id, value) {
                const input = document.getElementById(id);

                if (!input) {
                    return;
                }

                input.value = value;
                input.dispatchEvent(new Event('input', { bubbles: true }));
            },

            async stopScanner() {
                if (!this.scanner) {
                    return;
                }

                try {
                    if (this.scanner.isScanning) {
                        await this.scanner.stop();
                    }
                } catch (error) {
                    console.warn('Gagal menghentikan scanner QR.', error);
                }

                try {
                    await this.scanner.clear();
                } catch (error) {
                    console.warn('Gagal membersihkan scanner QR.', error);
                }

                this.scanner = null;
                this.setScannerStatus('Scanner berhenti', 'secondary');
            },

            async startScanner() {
                if (this.starting) {
                    return;
                }

                const target = document.getElementById('qr-reader');
                const qrInput = document.getElementById('qr-code-value');

                if (!target || !qrInput) {
                    return;
                }

                if (!window.Html5Qrcode) {
                    this.setScannerStatus('Library scanner belum dimuat', 'danger');
                    return;
                }

                this.starting = true;
                this.setScannerStatus('Menyiapkan kamera...', 'warning');

                try {
                    await this.stopScanner();

                    const cameras = await window.Html5Qrcode.getCameras();

                    if (!cameras.length) {
                        this.setScannerStatus('Kamera tidak ditemukan', 'danger');
                        return;
                    }

                    this.scanner = new window.Html5Qrcode('qr-reader');

                    await this.scanner.start(
                        { facingMode: 'environment' },
                        {
                            fps: 10,
                            qrbox: { width: 220, height: 220 },
                            rememberLastUsedCamera: true,
                        },
                        async (decodedText) => {
                            this.syncInput('qr-code-value', decodedText.trim());
                            this.setScannerStatus('QR berhasil dipindai', 'success');
                            await this.stopScanner();
                        },
                        () => {}
                    );

                    this.setScannerStatus('Scanner aktif', 'success');
                } catch (error) {
                    console.error(error);
                    this.setScannerStatus('Izin kamera ditolak atau kamera gagal dibuka', 'danger');
                } finally {
                    this.starting = false;
                }
            },

            captureLocation() {
                if (!navigator.geolocation) {
                    this.setLocationStatus('Browser ini tidak mendukung geolocation.', 'danger');
                    return;
                }

                this.setLocationStatus('Mengambil lokasi perangkat...');

                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        const latitude = position.coords.latitude.toFixed(6);
                        const longitude = position.coords.longitude.toFixed(6);
                        const accuracy = Math.round(position.coords.accuracy);

                        this.syncInput('attendance-latitude', latitude);
                        this.syncInput('attendance-longitude', longitude);
                        this.syncInput('attendance-accuracy', accuracy);
                        this.setLocationStatus(`Lokasi berhasil diambil. Akurasi sekitar ${accuracy} meter.`);
                    },
                    (error) => {
                        const messages = {
                            1: 'Izin lokasi ditolak. Aktifkan izin lokasi lalu coba lagi.',
                            2: 'Lokasi tidak tersedia. Pastikan GPS atau jaringan aktif.',
                            3: 'Pengambilan lokasi melebihi batas waktu.',
                        };

                        this.setLocationStatus(messages[error.code] ?? 'Gagal mengambil lokasi perangkat.', 'danger');
                    },
                    {
                        enableHighAccuracy: true,
                        timeout: 10000,
                        maximumAge: 0,
                    }
                );
            },

            initPage() {
                if (!document.querySelector('[data-qr-attendance-page]')) {
                    this.stopScanner();
                    return;
                }

                const startButton = document.getElementById('btn-start-scanner');
                const stopButton = document.getElementById('btn-stop-scanner');
                const locationButton = document.getElementById('btn-capture-location');

                if (startButton) {
                    startButton.onclick = () => this.startScanner();
                }

                if (stopButton) {
                    stopButton.onclick = () => this.stopScanner();
                }

                if (locationButton) {
                    locationButton.onclick = () => this.captureLocation();
                }

                if (!document.getElementById('attendance-latitude')?.value || !document.getElementById('attendance-longitude')?.value) {
                    this.captureLocation();
                }

                this.setScannerStatus('Siap digunakan', 'light');
            },
        };

        document.addEventListener('livewire:navigated', () => window.karyawanQrAttendance.initPage());
        document.addEventListener('livewire:navigating', () => window.karyawanQrAttendance.stopScanner());
        window.addEventListener('attendance-recorded', () => {
            window.karyawanQrAttendance.setScannerStatus('Presensi tersimpan. Scanner siap dipakai lagi.', 'success');
        });

        window.karyawanQrAttendance.initPage();
    })();
</script>
@endpush
