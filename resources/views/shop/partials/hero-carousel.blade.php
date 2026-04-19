<section class="hero-section">
    @if(isset($banners) && $banners->count() > 0)
        <div class="hero-carousel" id="heroCarousel">

            @foreach($banners as $index => $banner)
                <div class="hero-slide {{ $index === 0 ? 'active' : '' }}" data-index="{{ $index }}">

                    @if($banner->video)
                        <video class="hero-bg-video" muted loop playsinline preload="metadata">
                            <source src="{{ asset('storage/'.$banner->video) }}" type="video/mp4">
                        </video>
                    @else
                        <div class="hero-bg" style="
                            background-image: url('{{ $banner->image ? asset('storage/'.$banner->image) : asset('images/banner-default.jpg') }}');
                            background-color: {{ $banner->bg_color ?? '#0a0a1a' }};
                            background-position: {{ $banner->image_position ?? 'center center' }};
                        "></div>
                    @endif

                    <div class="hero-banner-content">
                        @if($banner->badge_text)
                            <div class="hero-badge">{{ $banner->badge_text }}</div>
                        @endif

                        <h1 class="hero-title">{!! nl2br(e($banner->title)) !!}</h1>

                        @if($banner->subtitle)
                            <p class="hero-desc">{{ $banner->subtitle }}</p>
                        @endif

                        @if($banner->price)
                            <div class="hero-price">
                                Nga <strong>{{ number_format($banner->price, 2) }} €</strong>
                                &nbsp;·&nbsp; Transport falas
                            </div>
                        @endif

                        <div class="hero-actions">
                            @if($banner->btn_primary_text)
                              <a href="{{ $banner->product?->uuid 
    ? route('shop.product', $banner->product->uuid) 
    : ($banner->btn_primary_url ?? url('/shop')) }}"
   class="hero-btn hero-btn-primary">
    {{ $banner->btn_primary_text }}
</a>
                            @endif

                            @if($banner->btn_secondary_text)
                            <a href="{{ $banner->product?->uuid
    ? route('shop.product', $banner->product->uuid)
    : ($banner->btn_secondary_url ?? url('/shop')) }}"
   class="hero-btn hero-btn-ghost">
    {{ $banner->btn_secondary_text }}
</a>
                            @endif
                        </div>
                    </div>

                </div>
            @endforeach

            <div class="hero-dots" id="heroDots">
                @foreach($banners as $index => $banner)
                    <button class="hero-dot {{ $index === 0 ? 'active' : '' }}"
                            data-slide="{{ $index }}"
                            aria-label="Slide {{ $index + 1 }}">
                    </button>
                @endforeach
            </div>

            @if($banners->count() > 1)
                <div class="hero-arrows">
                    <button class="hero-arrow" id="heroPrev" aria-label="Para">&#8592;</button>
                    <button class="hero-arrow" id="heroNext" aria-label="Pas">&#8594;</button>
                </div>
            @endif

            <div class="hero-progress" id="heroProgress"></div>
        </div>
    @else
        <div class="hero-empty">
            <p>Nuk ka oferta aktive.</p>
        </div>
    @endif
</section>
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/shop/partials.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('js/shop/hero-carousel.js') }}"></script>
@endpush
