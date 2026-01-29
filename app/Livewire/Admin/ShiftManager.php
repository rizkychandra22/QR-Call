<?php

namespace App\Livewire\Admin;

use App\Models\Shift;
use Livewire\Component;

class ShiftManager extends Component
{
    public $shift_name, $shift_code, $in_time, $out_time, $selected_id;
    public $isEdit = false;

    public $title = 'Dashboard';
    public $subpage = 'Overview Admin';
    public $content = 'Check Shift';
    public $linkTitle;
    public $linkSubpage;

    public function mount()
    {
        $this->linkTitle = route('admin.dashboard');
        $this->linkSubpage = route('admin.dashboard.generate-qr');
    }

    public function updatedShiftName($value)
    {
        if (!$this->isEdit && !empty($value)) {
            $words = explode(' ', preg_replace('/\s+/', ' ', trim($value)));
            $code = '';
            if (count($words) >= 1) {
                foreach ($words as $word) {
                    $code .= mb_substr($word, 0, 1);
                }
                if (count($words) == 1 && strlen($words[0]) > 1) {
                    $code .= mb_substr($words[0], 1, 1);
                }
                $code = mb_substr($code, 0, 3);
                $code .= str_pad(rand(0, 99), 2, '0', STR_PAD_LEFT);
            }
            $this->shift_code = strtoupper(mb_substr($code, 0, 5));
        }
    }

    protected function rules()
    {
        return [
            'shift_name' => 'required|string|max:50',
            'shift_code' => 'required|unique:shifts,shift_code,' . $this->selected_id,
            'in_time'    => 'required',
            'out_time'   => 'required',
        ];
    }

    public function resetInput()
    {
        $this->shift_name = null;
        $this->shift_code = null;
        $this->in_time = null;
        $this->out_time = null;
        $this->isEdit = false;
        $this->selected_id = null;
    }

    public function store()
    {
        $this->validate();

        Shift::create([
            'shift_name' => $this->shift_name,
            'shift_code' => $this->shift_code,
            'in_time'    => $this->in_time,
            'out_time'   => $this->out_time,
        ]);

        session()->flash('success', 'Shift ' . $this->shift_code . ' berhasil ditambahkan.');
        $this->resetInput();
    }

    public function edit($id)
    {
        $shift = Shift::findOrFail($id);
        $this->selected_id = $id;
        $this->shift_name  = $shift->shift_name;
        $this->shift_code  = $shift->shift_code;
        $this->in_time     = $shift->in_time;
        $this->out_time    = $shift->out_time;
        $this->isEdit      = true;
    }

    public function update()
    {
        $this->validate();

        $shift = Shift::findOrFail($this->selected_id);
        $shift->update([
            'shift_name' => $this->shift_name,
            'shift_code' => $this->shift_code,
            'in_time'    => $this->in_time,
            'out_time'   => $this->out_time,
        ]);

        session()->flash('success', 'Shift ' . $this->shift_code . ' berhasil diperbarui.');
        $this->resetInput();
    }

    public function delete($id)
    {
        $shift = Shift::find($id);
        $code = $shift->shift_code;
        $shift->delete();
        
        session()->flash('danger', "Shift $code telah dihapus.");
    }

    public function render()
    {
        return view('livewire.admin.shift-manager', [
            'shifts' => Shift::latest()->get()
        ])->layout('layouts.app', [
            'subpage' => 'Overview Admin',    
            'content' => 'Check Shift',
        ]);
    }
}