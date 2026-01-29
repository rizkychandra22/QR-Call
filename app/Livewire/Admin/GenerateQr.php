<?php

namespace App\Livewire\Admin;

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
        $this->linkSubpage = route('admin.dashboard.generate-qr');

        $this->date = Carbon::now()->toDateString();
        $this->start_time = Carbon::now()->format('H:i');
        $this->end_time = Carbon::now()->addMinutes(5)->format('H:i');
    }

    public function generate()
    {
        $this->validate([
            'present' => 'required',
            'shift_id' => 'required',
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
            'activeQrs' => $activeQrs,
        ])->layout('layouts.app', [
            'subpage' => $this->subpage,
            'content' => $this->content,
        ]);
    }
}