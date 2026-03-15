<div>
    <section class="section">
        <div class="section-header">
            <h1>{{ $subpage }}</h1>
            @include('partials.templates.breadcrumb')
        </div>

        <div class="row">
            {{-- Alert Message --}}
            <div class="col-12">
                @include('partials.global.session-message')
            </div>
            
            <div class="col-lg-12">
                <div class="card card-primary">
                    <div class="card-header">
                        <h4>{{ $content }}</h4>
                        <div class="card-header-action">
                            <div class="btn-group">
                                <a href="{{ route('admin.generate-shift') }}" class="btn btn-warning">
                                    <i class="fas fa-clock mr-1"></i> Shift
                                </a>
                                <a href="{{ route('admin.set-location') }}" class="btn btn-info">
                                    <i class="fas fa-map-marker-alt mr-1"></i> Radius
                                </a>
                                <a href="{{ route('admin.generate-qr') }}" class="btn btn-danger">
                                    <i class="fas fa-qrcode mr-1"></i> Generate
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Wire:poll akan merefresh isi card-body setiap 5 detik --}}
                    <div class="card-body">
                       <div class="row mb-2">
                            <div class="col-md-12">
                                <div class="alert alert-light border shadow-sm d-flex justify-content-between align-items-center">
                                    <div class="spin">
                                        <i class="fas fa-sync fa-spin mr-2 text-primary"></i> 
                                        <span>Diperbarui otomatis setiap 5 detik.</span>
                                    </div>

                                    <div class="clock d-flex align-items-center" wire:poll.1s>
                                        <i class="fas fa-clock mr-2 text-primary"></i> 
                                        <span class="font-weight-bold">Waktu: {{ \Carbon\Carbon::now()->format('H:i:s') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive" wire:poll.5s>
                            <table class="table table-bordered table-md text-center">
                                <thead class="thead-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Tanggal</th>
                                        <th>Shift</th>
                                        <th>Tipe</th>
                                        <th>Batas Waktu</th>
                                        <th>Token</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($qrs as $qr)
                                        @php
                                            $isExpired = \Carbon\Carbon::now()->greaterThan($qr->end_time);
                                            $status = $isExpired ? 'expired' : $qr->status;
                                        @endphp
                                        <tr wire:key="qr-{{ $qr->id }}">
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ \Carbon\Carbon::parse($qr->date)->format('d M Y') }}</td>
                                            <td>
                                                <strong>{{ $qr->shift->shift_name }}</strong><br>
                                                <small class="badge badge-light">{{ $qr->shift->shift_code }}</small>
                                            </td>
                                            <td>
                                                <span class="badge badge-{{ $qr->present == 'in_present' ? 'info' : 'warning' }}">
                                                    {{ $qr->present == 'in_present' ? 'MASUK' : 'KELUAR' }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="font-weight-bold">{{ \Carbon\Carbon::parse($qr->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($qr->end_time)->format('H:i') }}</div>
                                            </td>
                                            <td>
                                                <code class="p-1 bg-light border rounded">{{ $qr->qr_code_present }}</code>
                                            </td>
                                            <td>
                                                <span class="badge badge-{{ $status == 'active' ? 'success' : 'danger' }}">
                                                    <i class="fas {{ $status == 'active' ? 'fa-check-circle' : 'fa-times-circle' }} mr-1"></i>
                                                    {{ strtoupper($status) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-4 text-muted">
                                                Tidak ada QR Code aktif untuk hari ini.
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