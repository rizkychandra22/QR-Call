<?php

namespace App\Livewire\Admin;

use App\Models\RadiusPresent as RadiusPresentModel;
use Livewire\Component;

class RadiusPresent extends Component
{
    public $title = 'Dashboard';
    public $subpage = 'Overview Admin';
    public $content = 'Radius QR';
    public $linkTitle;
    public $linkSubpage;
    public $location_name = '';
    public $location_radius = '';
    public $latitude = '';
    public $longitude = '';
    public $editingId = null;

    public function store()
    {
        $validated = $this->validate([
            'location_name' => ['required', 'string', 'max:255'],
            'location_radius' => ['required', 'numeric', 'gt:0'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
        ]);

        RadiusPresentModel::create([
            'name' => $validated['location_name'],
            'radius' => $validated['location_radius'],
            'lat' => $validated['latitude'],
            'lng' => $validated['longitude'],
        ]);

        $this->reset('location_name', 'location_radius', 'latitude', 'longitude', 'editingId');
        session()->flash('success', 'Lokasi berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        $location = RadiusPresentModel::findOrFail($id);
        $this->editingId = $location->id;
        $this->location_name = $location->name;
        $this->location_radius = $location->radius;
        $this->latitude = $location->lat;
        $this->longitude = $location->lng;
    }

    public function update()
    {
        $validated = $this->validate([
            'location_name' => ['required', 'string', 'max:255'],
            'location_radius' => ['required', 'numeric', 'gt:0'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
        ]);

        RadiusPresentModel::findOrFail($this->editingId)->update([
            'name' => $validated['location_name'],
            'radius' => $validated['location_radius'],
            'lat' => $validated['latitude'],
            'lng' => $validated['longitude'],
        ]);

        $this->reset('location_name', 'location_radius', 'latitude', 'longitude', 'editingId');
        session()->flash('success', 'Lokasi berhasil diperbarui.');
    }

    public function cancelEdit()
    {
        $this->reset('location_name', 'location_radius', 'latitude', 'longitude', 'editingId');
    }

    public function delete(int $id)
    {
        RadiusPresentModel::findOrFail($id)->delete();
        session()->flash('success', 'Lokasi berhasil dihapus.');
    }

    public function mount()
    {
        $this->linkTitle = route('admin.dashboard');
        $this->linkSubpage = route('admin.generate-qr');
    }

    public function render()
    {
        $locations = RadiusPresentModel::latest()->get();

        return view('livewire.admin.radius-present', [
            'locations' => $locations,
        ])->layout('layouts.app', [
            'subpage' => $this->subpage,
            'content' => $this->content,
        ]);
    }
}
