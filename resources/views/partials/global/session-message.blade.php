@if (session()->has('success') || session()->has('danger'))
    <div x-data="{ show: true }" 
            x-show="show" x-init="setTimeout(() => show = false, 3000)"
            class="alert alert-{{ session()->has('success') ? 'success' : 'danger' }} alert-dismissible show fade mb-4">
        <div class="alert-body">
            <button class="close" @click="show = false"><span>&times;</span></button>
            {{ session('success') ?? session('danger') }}
        </div>
    </div>
@endif