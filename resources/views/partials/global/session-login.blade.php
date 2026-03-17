@if (session('info'))
    <div class="col-12" id="alert-container">
        <div x-data="{ show: true }" x-show="show" 
            x-init="setTimeout(() => show = false, 3000)" 
            class="alert alert-info alert-dismissible show fade mb-4">
            <div class="alert-body">
                <button class="close" @click="show = false"><span>&times;</span></button>
                {{ session('info') }}
            </div>
        </div>
    </div>
@endif