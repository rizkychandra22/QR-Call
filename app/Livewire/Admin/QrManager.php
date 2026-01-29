<?php

namespace App\Livewire\Admin;

use App\Models\QrCode;
use Livewire\Component;
use Carbon\Carbon;

class QrManager extends Component
{
    public $title = 'Dashboard';
    public $subpage = 'Overview Admin';
    public $content = 'Monitoring QR Code';
    public $linkTitle;
    public $linkSubpage;

    public function mount()
    {
        $this->linkTitle = route('admin.dashboard');
        $this->linkSubpage = route('admin.dashboard.generate-qr');
    }

    public function render()
    {
        $qrs = QrCode::with('shift')
            ->whereDate('date', Carbon::today())->orderBy('created_at', 'DESC')
            ->get()->all();

        return view('livewire.admin.qr-manager', [
            'qrs' => $qrs
        ])->layout('layouts.app', [
            'subpage' => $this->subpage,
            'content' => $this->content,
        ]);
    }
}