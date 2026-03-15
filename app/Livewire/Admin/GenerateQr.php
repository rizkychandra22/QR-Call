<?php

namespace App\Livewire\Admin;

use App\Models\RadiusPresent;
use App\Models\Shift;
use App\Models\QrCode as QrModel;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Livewire\Component;
use Illuminate\Support\Str;
use Carbon\Carbon;

class GenerateQr extends Component
{
    // Form Properties
    public $present;
    public $shift_id;
    public $radius_present_id;
    public $date;
    public $start_time;
    public $end_time;

    // Result Input
    public $qr_code_value;
    public $qr_present_type;
    public $qr_shift_name;

    public $title = 'Dashboard';
    public $subpage = 'Overview Admin';
    public $content = 'Generate QR';
    public $linkTitle;
    public $linkSubpage;

    public function mount()
    {
        $this->linkTitle = route('admin.dashboard');
        $this->linkSubpage = route('admin.generate-qr');

        $this->radius_present_id = RadiusPresent::query()->value('id');
        $this->date = Carbon::now()->toDateString();
        $this->start_time = Carbon::now()->format('H:i');
        $this->end_time = Carbon::now()->addMinutes(3)->format('H:i');
        // $this->end_time = Carbon::now()->addHours(1)->format('H:i');
    }

    public function generate()
    {
        if (! RadiusPresent::query()->exists()) {
            session()->flash('error', 'Tambahkan lokasi radius terlebih dahulu sebelum membuat QR-Code.');

            return;
        }

        $this->validate([
            'present' => 'required',
            'shift_id' => 'required|exists:shifts,id',
            'radius_present_id' => 'required|exists:radius_presents,id',
            'date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
        ]);

        // Generate Unique QR Code Value: shiftCode + date(dm) + random(9)
        // $uuid = (string) Str::uuid();
        $shift = Shift::find($this->shift_id);
        $sCode = strtoupper($shift->shift_code);
        $dCode = Carbon::parse($this->date)->format('dm');
        $randomCode = strtoupper(Str::random(9));
        $uniqueCode = $sCode . $dCode . $randomCode;

        // Simpan ke Database
        QrModel::create([
            'shift_id' => $this->shift_id,
            'radius_present_id' => $this->radius_present_id,
            'qr_code_present' => $uniqueCode,
            'present' => $this->present,
            'date' => $this->date,
            'start_time' => $this->date . ' ' . $this->start_time,
            'end_time' => $this->date . ' ' . $this->end_time,
            'status' => 'active',
        ]);

        // Kirim data ke tampilan
        $this->qr_code_value = $uniqueCode;
        $this->qr_present_type = $this->present;
        $this->qr_shift_name = $shift->shift_name;

        session()->flash('success', 'QR-Code ' . $this->qr_shift_name . ' Berhasil Dibuat!');
    }

    public function render()
    {
        $activeQrs = QrModel::with('shift')
            ->where('status', 'active')
            ->where('end_time', '>', Carbon::now())
            ->latest()
            ->get();

        return view('livewire.admin.generate-qr', [
            'shifts' => Shift::all(),
            'locations' => RadiusPresent::all(),
            'activeQrs' => $activeQrs,
        ])->layout('layouts.app', [
            'subpage' => $this->subpage,
            'content' => $this->content,
        ]);
    }
}