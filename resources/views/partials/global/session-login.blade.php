@if (session('info'))
    <div class="col-12" id="alert-container">
        <div id="alert" class="alert alert-info alert-dismissible show fade mb-4">
            <div class="alert-body">
                {{ session('info') }}
            </div>
        </div>
    </div>
@endif