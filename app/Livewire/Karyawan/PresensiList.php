<?php

namespace App\Livewire\Karyawan;

use App\Models\Present;
use App\Models\QrCode;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class PresensiList extends Component
{
    public $title = 'Dashboard';
    public $subpage = 'Overview Karyawan';
    public $content = 'Present QR';
    public $linkTitle;
    public $linkSubpage;
    public $qrCodeValue = '';
    public $latitude;
    public $longitude;
    public $locationAccuracy;

    public function mount()
    {
        $this->linkTitle = route('karyawan.dashboard');
        $this->linkSubpage = route('karyawan.present');
    }

    public function submitPresent(): void
    {
        $validated = $this->validate([
            'qrCodeValue' => 'required|string',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'locationAccuracy' => 'nullable|numeric|min:0',
        ], [
            'qrCodeValue.required' => 'QR absen wajib diisi atau dipindai.',
            'latitude.required' => 'Lokasi perangkat wajib diambil sebelum absen.',
            'longitude.required' => 'Lokasi perangkat wajib diambil sebelum absen.',
        ]);

        $now = Carbon::now();
        $qrCode = QrCode::with(['shift', 'radiusPresent'])
            ->where('qr_code_present', trim($validated['qrCodeValue']))
            ->first();

        if (! $qrCode) {
            $this->addError('qrCodeValue', 'QR absen tidak ditemukan atau sudah tidak berlaku.');

            return;
        }

        $attendanceCutoff = $this->resolveAttendanceCutoff($qrCode);

        if ($now->greaterThan($attendanceCutoff)) {
            if ($qrCode->status !== 'expired') {
                $qrCode->update(['status' => 'expired']);
            }

            $this->addError('qrCodeValue', 'QR absen ini sudah kedaluwarsa.');

            return;
        }

        if ($qrCode->status !== 'active' || $now->lessThan($qrCode->start_time)) {
            $this->addError('qrCodeValue', 'QR absen belum aktif atau sudah dinonaktifkan.');

            return;
        }

        if (! $qrCode->date->isSameDay($now)) {
            $this->addError('qrCodeValue', 'QR absen ini tidak berlaku untuk hari ini.');

            return;
        }

        $distance = $this->calculateDistanceInMeters(
            (float) $validated['latitude'],
            (float) $validated['longitude'],
            (float) $qrCode->radiusPresent->lat,
            (float) $qrCode->radiusPresent->lng,
        );

        if ($distance > (float) $qrCode->radiusPresent->radius) {
            $roundedDistance = number_format($distance, 1, ',', '.');

            $this->addError(
                'latitude',
                "Anda berada di luar radius presensi. Jarak saat ini {$roundedDistance} m dari titik absen."
            );

            return;
        }

        if ($this->hasExistingPresent($qrCode)) {
            $this->addError('qrCodeValue', 'Anda sudah melakukan presensi untuk QR ini. Gunakan QR lain jika ada sesi absen baru.');

            return;
        }

        $checkIn = $this->findCheckInRecord($qrCode);

        if ($qrCode->present === 'out_present' && ! $checkIn) {
            $this->addError('qrCodeValue', 'Absen keluar hanya bisa dilakukan setelah absen masuk tercatat.');

            return;
        }

        if ($qrCode->present === 'in_present') {
            $lateValidationMessage = $this->validateLateAttendance($qrCode, $now);

            if ($lateValidationMessage !== null) {
                $this->addError('qrCodeValue', $lateValidationMessage);

                return;
            }
        }

        $present = Present::create([
            'user_id' => Auth::id(),
            'shift_id' => $qrCode->shift_id,
            'qr_code_id' => $qrCode->id,
            'date' => $qrCode->date->toDateString(),
            'time' => $now->format('H:i:s'),
            'hours' => $this->resolveHours($qrCode, $now, $checkIn),
            'present_desc_system' => $this->buildSystemDescription($qrCode, $now, $distance, $checkIn),
            'present_user_desc' => null,
            'present_user_image' => null,
            'status' => 'Hadir',
            'status_desc' => null,
            'status_image' => null,
            'lat_location_present' => (float) $validated['latitude'],
            'lng_location_present' => (float) $validated['longitude'],
        ]);

        $this->reset('qrCodeValue');
        $this->resetValidation();
        $this->dispatch('attendance-recorded', id: $present->id);

        session()->flash(
            'success',
            'Presensi ' . ($qrCode->present === 'in_present' ? 'masuk' : 'keluar') . ' berhasil direkam.'
        );
    }

    private function hasExistingPresent(QrCode $qrCode): bool
    {
        return Present::query()
            ->where('user_id', Auth::id())
            ->where('qr_code_id', $qrCode->id)
            ->exists();
    }

    private function findCheckInRecord(QrCode $qrCode): ?Present
    {
        return Present::query()
            ->where('user_id', Auth::id())
            ->where('shift_id', $qrCode->shift_id)
            ->whereDate('date', $qrCode->date)
            ->whereHas('qrCode', function ($query) {
                $query->where('present', 'in_present');
            })
            ->latest('time')
            ->first();
    }

    private function validateLateAttendance(QrCode $qrCode, Carbon $now): ?string
    {
        if (! $qrCode->allow_late) {
            return null;
        }

        $toleranceEnd = $this->resolveAttendanceCutoff($qrCode);

        if ($now->greaterThan($toleranceEnd)) {
            return 'Absen anda ditolak karena melewati batas waktu ketentuan dan toleransi keterlambatan.';
        }

        return null;
    }

    private function resolveAttendanceCutoff(QrCode $qrCode): Carbon
    {
        $cutoff = $qrCode->end_time->copy();

        if ($qrCode->allow_late && $qrCode->late_tolerance_minutes) {
            $cutoff->addMinutes((int) $qrCode->late_tolerance_minutes);
        }

        return $cutoff;
    }

    private function resolveHours(QrCode $qrCode, Carbon $now, ?Present $checkIn): ?string
    {
        if ($qrCode->present !== 'out_present' || ! $checkIn) {
            return null;
        }

        $clockIn = Carbon::parse($qrCode->date->toDateString() . ' ' . $checkIn->time);
        $workedMinutes = max($clockIn->diffInMinutes($now), 0);
        $hours = intdiv($workedMinutes, 60);
        $minutes = $workedMinutes % 60;

        return sprintf('%d jam %d menit', $hours, $minutes);
    }

    private function buildSystemDescription(QrCode $qrCode, Carbon $now, float $distance, ?Present $checkIn): string
    {
        $distanceLabel = number_format($distance, 1, ',', '.');

        if ($qrCode->present === 'out_present') {
            $workLabel = $checkIn?->time
                ? ' setelah absen masuk pukul ' . Carbon::parse($checkIn->time)->format('H:i')
                : '';

            return 'Absen keluar berhasil pada radius ' . $distanceLabel . ' m' . $workLabel . '.';
        }

        if ($now->lessThanOrEqualTo($qrCode->end_time)) {
            return 'Hadir sesuai waktu. Jarak ke titik presensi ' . $distanceLabel . ' m.';
        }

        $lateMinutes = (int) floor($qrCode->end_time->diffInSeconds($now) / 60);

        $lateLabel = 'Hadir terlambat ' . $lateMinutes . ' menit';

        if ($qrCode->allow_late) {
            $lateLabel .= ', namun ada toleransi kehadiran';
        }

        return $lateLabel . '. Jarak ke titik presensi ' . $distanceLabel . ' m.';
    }

    private function calculateDistanceInMeters(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371000;
        $latDelta = deg2rad($lat2 - $lat1);
        $lngDelta = deg2rad($lng2 - $lng1);

        $a = sin($latDelta / 2) * sin($latDelta / 2)
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2))
            * sin($lngDelta / 2) * sin($lngDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    public function render()
    {
        $presents = Present::with(['shift', 'qrCode'])
            ->where('user_id', Auth::id())
            ->latest('date')
            ->latest('time')
            ->get();

        $activeQrs = QrCode::with(['shift', 'radiusPresent'])
            ->where('status', 'active')
            ->whereDate('date', now()->toDateString())
            ->where('start_time', '<=', now())
            ->latest('start_time')
            ->get()
            ->filter(fn (QrCode $qrCode) => now()->lessThanOrEqualTo($this->resolveAttendanceCutoff($qrCode)));

        return view('livewire.karyawan.presensi-list', [
            'presents' => $presents,
            'activeQrs' => $activeQrs,
        ])->layout('layouts.app', [
            'title' => $this->title,
            'subpage' => $this->subpage,
            'content' => $this->content,
            'linkTitle' => $this->linkTitle,
            'linkSubpage' => $this->linkSubpage,
        ]);
    }
}
