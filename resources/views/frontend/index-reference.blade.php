@extends('frontend.layouts.master')

@section('body')
<section class="onyx-hero" id="home" data-home-hero-slider>
  <div class="onyx-hero-track">
    @forelse($heroSlides as $slide)
      <article class="onyx-hero-slide {{ $loop->first ? 'is-active' : '' }}" data-home-hero-slide aria-hidden="{{ $loop->first ? 'false' : 'true' }}">
        <div class="onyx-hero-bg" @if($slide->background_image) style="background-image:linear-gradient(90deg,rgba(4,9,13,.96) 0%,rgba(4,9,13,.7) 48%,rgba(4,9,13,.18) 100%),url('{{ url($slide->background_image) }}')" @endif></div>
        <div class="container onyx-hero-inner">
          <div class="onyx-hero-copy"><p class="onyx-eyebrow">{{ $slide->subtitle ?: 'Research peptides' }}</p><h1>{{ $slide->title ?: 'Precision compounds for advanced research.' }}</h1><a class="onyx-primary-btn" href="{{ $slide->button_url ?: route('frontend.shop') }}">{{ $slide->button_label ?: 'Explore the collection' }} <i class="fa-solid fa-arrow-right"></i></a><p>{{ $slide->paragraph ?: 'High-purity research products supplied for laboratory and research use only.' }}</p><div class="onyx-hero-benefits"><div><i class="fa-solid fa-flask-vial"></i><span><strong>Research grade</strong><small>Compounds</small></span></div><div><i class="fa-solid fa-shield-halved"></i><span><strong>Verified purity</strong><small>Quality assured</small></span></div><div><i class="fa-solid fa-layer-group"></i><span><strong>Consistent</strong><small>Quality</small></span></div></div></div>
          <div class="onyx-hero-product"><span></span><img src="{{ url($slide->product_image ?: 'frontend/assets/images/product-bottle.png') }}" alt="{{ $slide->title }}"></div>
        </div>
      </article>
    @empty
      <article class="onyx-hero-slide is-active" data-home-hero-slide aria-hidden="false"><div class="onyx-hero-bg"></div><div class="container onyx-hero-inner"><div class="onyx-hero-copy"><p class="onyx-eyebrow">Research peptides</p><h1>Precision compounds for advanced research.</h1><a class="onyx-primary-btn" href="{{ route('frontend.shop') }}">Explore the collection <i class="fa-solid fa-arrow-right"></i></a><p>High-purity research products supplied for laboratory and research use only.</p><div class="onyx-hero-benefits"><div><i class="fa-solid fa-flask-vial"></i><span><strong>Research grade</strong><small>Compounds</small></span></div><div><i class="fa-solid fa-shield-halved"></i><span><strong>Verified purity</strong><small>Quality assured</small></span></div><div><i class="fa-solid fa-layer-group"></i><span><strong>Consistent</strong><small>Quality</small></span></div></div></div><div class="onyx-hero-product"><span></span><img src="{{ url('frontend/assets/images/product-bottle.png') }}" alt="Majix research product"></div></div></article>
    @endforelse
  </div>
  @if($heroSlides->count() > 1)<div class="onyx-hero-controls"><button class="home-hero-arrow home-hero-prev" type="button" aria-label="Previous slide"><i class="fa-solid fa-arrow-left"></i></button><div class="home-hero-pagination"></div><button class="home-hero-arrow home-hero-next" type="button" aria-label="Next slide"><i class="fa-solid fa-arrow-right"></i></button></div>@endif
</section>

<section class="onyx-collection section-pad" id="products">
  <div class="container">
    <header class="onyx-section-heading"><p class="onyx-eyebrow">Our research collection</p><h2>Explore the Majix collection</h2><p>A focused range of premium research products, supplied with quality and consistency.</p></header>
    <div class="onyx-product-grid">
      @forelse($featuredProducts->take(4) as $product)
        <article class="onyx-product-card product-card {{ $product->stock <= 0 ? 'is-out-of-stock' : '' }}" data-product-id="{{ $product->id }}" data-product-name="{{ $product->name }}" data-product-url="{{ route('frontend.product-details', $product->slug) }}">
          <div class="onyx-product-badges"><span class="onyx-product-status {{ $product->stock <= 0 ? 'is-empty' : '' }}">{{ $product->stock <= 0 ? 'Out of stock' : 'In stock' }}</span>@if($product->is_bestseller)<span class="onyx-product-featured"><i class="fa-solid fa-bolt"></i> Bestseller</span>@endif</div>
          <a class="onyx-product-image" href="{{ route('frontend.product-details', $product->slug) }}"><img src="{{ url($product->image ?: 'frontend/assets/images/product-bottle.png') }}" alt="{{ $product->name }}"></a>
          <div class="onyx-product-content">
            <div class="onyx-product-category">{{ $product->category?->name ?: 'Research compound' }}</div>
            <a class="onyx-product-title" href="{{ route('frontend.product-details', $product->slug) }}">{{ $product->name }}</a>
            <div class="onyx-product-summary">
              <div class="onyx-product-rating"><span aria-label="Customer reviews"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i></span><small>{{ $product->reviews_count > 0 ? $product->reviews_count . ' reviews' : 'New arrival' }}</small></div>
              <div class="onyx-product-details"><span><strong>&pound;{{ number_format((float)($product->sale_price ?: $product->price), 2) }}</strong>@if($product->sale_price)<del>&pound;{{ number_format((float)$product->price, 2) }}</del>@endif</span>@if($product->test_report_image)<button class="onyx-report-btn test-report-icon" type="button" data-test-report="{{ url($product->test_report_image) }}" data-test-report-title="{{ $product->name }} test report" aria-label="View {{ $product->name }} test report"><i class="fa-solid fa-file-shield"></i></button>@endif</div>
            </div>
            @if($product->stock > 0)<button class="onyx-add-cart" type="button" data-cart-add="{{ $product->id }}"><span>Add to cart</span><i class="fa-solid fa-cart-shopping"></i></button>@else<button class="onyx-add-cart notify-stock-btn" type="button" data-stock-notify="{{ $product->id }}" data-product-name="{{ $product->name }}"><span>Notify me</span><i class="fa-regular fa-bell"></i></button>@endif
          </div>
        </article>
      @empty
        @for($i = 0; $i < 4; $i++)<article class="onyx-product-card"><a class="onyx-product-image" href="{{ route('frontend.shop') }}"><img src="{{ url('frontend/assets/images/product-bottle.png') }}" alt="Majix research product"></a><div class="onyx-product-content"><div class="onyx-product-category">Research compound</div><a class="onyx-product-title" href="{{ route('frontend.shop') }}">Majix Research</a><div class="onyx-product-rating"><span><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i></span><small>New arrival</small></div><a class="onyx-add-cart" href="{{ route('frontend.shop') }}"><span>View product</span><i class="fa-solid fa-arrow-right"></i></a></div></article>@endfor
      @endforelse
    </div>
    <div class="onyx-center-action"><a class="onyx-dark-btn" href="{{ route('frontend.shop') }}">View full collection <i class="fa-solid fa-arrow-right"></i></a></div>
  </div>
</section>

@endsection
