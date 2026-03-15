<?php

use App\Livewire\Admin\GenerateQr;
use App\Livewire\Admin\QrManager;
use App\Livewire\Admin\RadiusPresent;
use App\Livewire\Admin\ShiftManager;
use App\Livewire\Auth\Login;
use App\Livewire\Dashboard\Admin;
use App\Livewire\Dashboard\Karyawan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Route Login Full Livewire
Route::redirect('/', '/login');
Route::get('/login', Login::class)->name('login')->middleware('guest');
Route::get('/logout', Login::class)->name('logout');   

// Route Pintu Masuk Utama (/user) setelah login
Route::middleware(['RoleUser:Admin,Karyawan'])->get('/user', function () {
    $user = Auth::user();
    if ($user->role === 'Admin') {
        return redirect()->route('admin.dashboard');
    } 
    if ($user->role === 'Karyawan') {
        return redirect()->route('karyawan.dashboard');
    }
    
    Auth::logout();
    return redirect()->route('login')->withErrors([
        'loginAkses' => 'Sesi login anda telah berakhir, silahkan login kembali.'
    ]);
});

// Route Grup Role Admin
Route::middleware(['RoleUser:Admin'])->prefix('dashboard')->group(function () {
    Route::get('/admin', Admin::class)->name('admin.dashboard');
    Route::get('/admin/present/generate/shift', ShiftManager::class)->name('admin.generate-shift');
    Route::get('/admin/present/generate/qr-code/', GenerateQr::class)->name('admin.generate-qr');
    Route::get('/admin/present/set-location/', RadiusPresent::class)->name('admin.set-location');
    Route::get('/admin/present/check/generate/qr-code/', QrManager::class)->name('admin.check-qr');
});

// Route Grup Role Karyawan
Route::middleware(['RoleUser:Karyawan'])->prefix('dashboard')->group(function () {
    Route::get('/karyawan', Karyawan::class)->name('karyawan.dashboard');
});

// Route Download Struk PDF