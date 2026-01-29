<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;

class Karyawan extends Component
{
    public $subpage = 'Dashboard';
    public $content = 'Overview Karyawan';
    public $linkSubpage;

    public function mount()
    {
        $this->linkSubpage = route('karyawan.dashboard');
    }

    public function render()
    {
        return view('livewire.dashboard.karyawan')->layout('layouts.app', [
            'subpage' => 'Dashboard',    
            'content' => 'Overview Karyawan', 
        ]);
    }
}
