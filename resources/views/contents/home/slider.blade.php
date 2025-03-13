<div class="slider-content mt-3 pt-lg-2">
    <div class="row m-0">
        <div class="col-lg-8 col-md-12 col-12">
            <div id="carouselExampleIndicators" class="carousel slide" data-bs-ride="carousel" data-bs-interval="3000"
                data-bs-wrap="true">
                <div class="carousel-indicators">
                    @if ($sliders->isEmpty())
                        <!-- If no sliders, show a single indicator -->
                        <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0"
                            class="active" aria-current="true" aria-label="Slide 1"></button>
                    @else
                        <!-- If sliders exist, loop through them -->
                        @foreach ($sliders as $index => $slider)
                            <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="{{ $index }}"
                                class="{{ $index === 0 ? 'active' : '' }}" aria-current="true" aria-label="Slide {{ $index + 1 }}"></button>
                        @endforeach
                    @endif
                </div>
                <div class="carousel-inner">
                    @if ($sliders->isEmpty())
                        <!-- If no sliders, show a single fallback image -->
                        <div class="carousel-item active">
                            <img src="{{ asset('assets/images/home/banner1.jpg') }}" class="d-block w-100 img-fluid p-2 rounded-1"
                                alt="fallback_slider_image">
                        </div>
                    @else
                        <!-- If sliders exist, loop through them -->
                        @foreach ($sliders as $index => $slider)
                            <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                <img src="{{ asset($slider->image_path ?? 'assets/images/home/banner1.jpg') }}" class="d-block w-100 img-fluid p-2 rounded-1"
                                    alt="slider_image{{ $index + 1 }}">
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-12 col-12 p-0 d-flex justify-content-center">
            <img src="{{ asset('assets/images/home/secondaryImg.jpg') }}" alt="secondary_image" class="img-fluid p-2 rounded-1">
        </div>
    </div>
</div>