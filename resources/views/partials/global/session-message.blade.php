@php
    $alertType = null;
    $alertMessage = null;

    if (session()->has('success')) {
        $alertType = 'success';
        $alertMessage = session('success');
    } elseif (session()->has('danger')) {
        $alertType = 'danger';
        $alertMessage = session('danger');
    } elseif (session()->has('warning')) {
        $alertType = 'warning';
        $alertMessage = session('warning');
    } elseif (session()->has('info')) {
        $alertType = 'info';
        $alertMessage = session('info');
    } elseif (session()->has('secondary')) {
        $alertType = 'secondary';
        $alertMessage = session('secondary');
    } elseif (session()->has('error')) {
        $alertType = 'danger';
        $alertMessage = session('error');
    }
@endphp

@if ($alertType && $alertMessage)
    <div x-data="{ show: true }" x-show="show"
        x-init="setTimeout(() => show = false, 3000)"
        class="alert alert-{{ $alertType }} alert-dismissible show fade mb-4">
        <div class="alert-body">
            <button class="close" @click="show = false"><span>&times;</span></button>
            {{ $alertMessage }}
        </div>
    </div>
@endif
