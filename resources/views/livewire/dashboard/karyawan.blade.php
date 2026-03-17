<div>
    <section class="section">
        <div class="section-header">
            <h1>{{ $subpage }}</h1>
            @include('partials.templates.breadcrumb')
        </div>

        <div class="row">
            {{-- Alert Message Login --}}
            @include('partials.global.session-login')
        </div>

        <div class="row">
            <div class="col-lg-12 col-md-12 col-12 col-sm-12">
                <div class="card card-primary">
                    <div class="card-header">
                        <h4>{{ $content }}</h4>
                        <div class="card-header-action">
                            {{-- <div class="btn-group">
                                <a href="" class="btn btn-danger">Daftar Produk</a>
                                <a href="" class="btn btn-success">Input Produk</a>
                            </div> --}}
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="statistic-details mt-sm-4">
                            <div class="statistic-details-item">
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>