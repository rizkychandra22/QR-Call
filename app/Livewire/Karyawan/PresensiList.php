<?php

namespace App\Livewire\Karyawan;

use Livewire\Component;

class PresensiList extends Component
{
    public $title = 'Dashboard';
    public $subpage = 'Overview Karyawan';
    public $content = 'List Presensi';
    public $linkTitle;
    public $linkSubpage;

    public function mount()
    {
        $this->linkTitle = route('karyawan.dashboard');
        $this->linkSubpage = route('karyawan.present');
    }

    public function render()
    {
        return view('livewire.karyawan.presensi-list')->layout('layouts.app', [
            'title' => $this->title,
            'subpage' => $this->subpage,
            'content' => $this->content,
            'linkTitle' => $this->linkTitle,
            'linkSubpage' => $this->linkSubpage,
        ]);
    }
}
