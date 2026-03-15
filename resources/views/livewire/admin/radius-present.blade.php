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

            <div class="col-lg-12 col-md-12 col-12 col-sm-12">
                <div class="card card-primary">
                    <div class="card-header">
                        <h4>{{ $content }}</h4>
                        <div class="card-header-action">
                            <div class="btn-group">
                                <a href="{{ route('admin.check-qr') }}" class="btn btn-warning">
                                    <i class="fas fa-qrcode mr-1"></i> Check
                                </a>
                                <a href="{{ route('admin.generate-shift') }}" class="btn btn-info">
                                    <i class="fas fa-clock mr-1"></i> Shift
                                </a>
                                <a href="{{ route('admin.generate-qr') }}" class="btn btn-danger">
                                    <i class="fas fa-qrcode mr-1"></i> Generate
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            {{-- KOLOM KIRI: FORM INPUT --}}
                            <div class="col-md-6">
                                <div class="section-title mt-0">{{ $editingId ? 'Edit Location' : 'Maps Location' }}</div>
                                <form wire:submit.prevent="{{ $editingId ? 'update' : 'store' }}">
                                    <div class="row">
                                        {{-- Nama Lokasi --}}
                                        <div class="form-group col-md-6">
                                            <label>Nama Lokasi</label>
                                            <input type="text" class="form-control @error('location_name') is-invalid @enderror" 
                                                wire:model.live="location_name" placeholder="Contoh: Kantor Pusat">
                                            @error('location_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>

                                        {{-- Radius Lokasi --}}
                                        <div class="form-group col-md-6">
                                            <label>Radius Lokasi</label>
                                            <select name="location_radius" id="location_radius" class="form-control @error('location_radius') is-invalid @enderror" wire:model.live="location_radius">
                                                <option value="">Pilih Radius</option>
                                                <option value="50">50 meter</option>
                                                <option value="100">100 meter</option>
                                                <option value="200">200 meter</option>
                                            </select>
                                            @error('location_radius') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>


                                        {{-- Set Latitude --}}
                                        <div class="form-group col-md-6">
                                            <label>Latitude</label>
                                            <input
                                                id="latitude"
                                                type="number"
                                                step="any"
                                                class="form-control @error('latitude') is-invalid @enderror"
                                                wire:model.live="latitude"
                                                placeholder="Contoh: -6.200000"
                                            >
                                            @error('latitude') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>

                                        {{-- Set Longitude --}}
                                        <div class="form-group col-md-6">
                                            <label>Longitude</label>
                                            <input
                                                id="longitude"
                                                type="number"
                                                step="any"
                                                class="form-control @error('longitude') is-invalid @enderror"
                                                wire:model.live="longitude"
                                                placeholder="Contoh: 106.816666"
                                            >
                                            @error('longitude') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>

                                    {{-- Action Buttons --}}
                                    <div class="justify-content-start d-flex">
                                        @if ($editingId)
                                            <button type="submit" class="btn btn-info shadow-sm mb-4 mr-2">
                                                <i class="fas fa-sync mr-1"></i> Update
                                            </button>
                                            <button type="button" class="btn btn-danger shadow-sm mb-4" wire:click="cancelEdit">
                                                <i class="fas fa-times mr-1"></i> Batal
                                            </button>
                                        @else
                                            <button type="submit" class="btn btn-primary shadow-sm mb-4">
                                                <i class="fas fa-save mr-1"></i> Simpan
                                            </button>
                                        @endif
                                    </div>
                                </form>
                            </div>

                            {{-- KOLOM KANAN: MAPS PREVIEW --}}
                            <div class="col-md-6 border-left">
                                <div class="section-title mt-0">Maps Preview</div>
                                <div class="card shadow-sm" wire:ignore>
                                    <div class="card-body p-2">
                                        <div id="maps-preview" style="height: 320px; border-radius: 8px;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- TABLE: LIST LOCATION --}}
                        <div class="row">
                            <div class="col-md-12">
                                <div class="section-title mt-0">List Location</div>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover table-md">
                                        <thead class="thead-light text-center">
                                            <tr>
                                                <th width="35">#</th>
                                                <th>Nama Lokasi</th>
                                                <th>Radius</th> 
                                                <th>Latitude</th>
                                                <th>Longitude</th>
                                                <th width="100">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($locations as $location)
                                                <tr>
                                                    <td class="text-center">{{ $loop->iteration }}</td>
                                                    <td>{{ $location->name }}</td>
                                                    <td class="text-center">{{ $location->radius }} m</td>
                                                    <td>{{ $location->lat }}</td>
                                                    <td>{{ $location->lng }}</td>
                                                    <td class="text-center">
                                                        <button class="btn btn-sm btn-outline-warning mr-1" wire:click="edit({{ $location->id }})" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-outline-danger" wire:click="delete({{ $location->id }})" wire:confirm="Yakin hapus lokasi '{{ $location->name }}'?" title="Hapus">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="text-center text-muted">Belum ada data lokasi.</td>
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

@push('scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    window.radiusMapState = window.radiusMapState || { map: null, marker: null };

    document.addEventListener('livewire:navigated', initRadiusMapPreview);
    document.addEventListener('DOMContentLoaded', initRadiusMapPreview);
    document.addEventListener('livewire:initialized', () => {
        if (window.Livewire && !window.__radiusMapHooked) {
            window.__radiusMapHooked = true;
            Livewire.hook('morphed', () => {
                initRadiusMapPreview();
            });
        }
    });

    function initRadiusMapPreview() {
        const latInput = document.getElementById('latitude');
        const lngInput = document.getElementById('longitude');
        const mapContainer = document.getElementById('maps-preview');

        if (!latInput || !lngInput || !mapContainer || typeof L === 'undefined') {
            return;
        }

        if (window.radiusMapState.map) {
            window.radiusMapState.map.remove();
            window.radiusMapState.map = null;
            window.radiusMapState.marker = null;
        }

        const parsedLat = Number.parseFloat(latInput.value);
        const parsedLng = Number.parseFloat(lngInput.value);
        const hasExisting = Number.isFinite(parsedLat) && Number.isFinite(parsedLng);

        // Inisialisasi map dengan view default agar DOM container langsung valid
        const defaultLat = hasExisting ? parsedLat : -6.200000;
        const defaultLng = hasExisting ? parsedLng : 106.816666;
        const map = L.map('maps-preview', { center: [defaultLat, defaultLng], zoom: 15 });

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        const svgIcon = L.divIcon({
            className: '',
            html: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 36" width="25" height="41">
                <path fill="#0061f2" stroke="#00439e" stroke-width="1.2"
                    d="M12 1C7.03 1 3 5.03 3 10c0 7.25 9 24 9 24s9-16.75 9-24c0-4.97-4.03-9-9-9z"/>
                <circle cx="12" cy="10" r="3.5" fill="white"/>
            </svg>`,
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
        });

        const marker = L.marker([defaultLat, defaultLng], { draggable: true, icon: svgIcon }).addTo(map);
        window.radiusMapState.map = map;
        window.radiusMapState.marker = marker;

        const placeAt = (lat, lng) => {
            // Guard: pastikan instance map ini masih aktif sebelum memanggil setView
            if (window.radiusMapState.map !== map) return;
            marker.setLatLng([lat, lng]);
            map.setView([lat, lng], 15);
        };

        if (!hasExisting && navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (pos) => placeAt(pos.coords.latitude, pos.coords.longitude),
                () => { /* fallback sudah di-set sebagai default view di atas */ },
                { timeout: 8000 }
            );
        }

        const syncInputToMap = () => {
            const lat = Number.parseFloat(latInput.value);
            const lng = Number.parseFloat(lngInput.value);

            if (!Number.isFinite(lat) || !Number.isFinite(lng)) {
                return;
            }

            marker.setLatLng([lat, lng]);
            map.panTo([lat, lng]);
        };

        const syncMapToInput = (latlng) => {
            const lat = latlng.lat.toFixed(6);
            const lng = latlng.lng.toFixed(6);

            latInput.value = lat;
            lngInput.value = lng;
            latInput.dispatchEvent(new Event('input', { bubbles: true }));
            lngInput.dispatchEvent(new Event('input', { bubbles: true }));
        };

        map.on('click', function (event) {
            marker.setLatLng(event.latlng);
            map.panTo(event.latlng);
            syncMapToInput(event.latlng);
        });

        marker.on('dragend', function () {
            syncMapToInput(marker.getLatLng());
        });

        latInput.oninput = syncInputToMap;
        lngInput.oninput = syncInputToMap;
        syncInputToMap();
    }
</script>
@endpush
