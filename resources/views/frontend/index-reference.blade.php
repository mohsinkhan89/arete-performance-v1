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

@if($categories->isNotEmpty())
<section class="onyx-categories section-pad" aria-labelledby="categoryHeading">
  <div class="container">
    <header class="onyx-category-heading">
      <div><p class="onyx-eyebrow">Browse by category</p><h2 id="categoryHeading">Find your research focus</h2><p>Explore the MAJIX collection by compound category.</p></div>
    </header>
    <div class="onyx-category-grid">
      @foreach($categories as $category)
        <a class="onyx-category-card" href="{{ route('frontend.shop', ['category' => $category->slug]) }}">
          <span class="onyx-category-image"><img src="{{ url($category->image ?: 'frontend/assets/images/product-bottle.png') }}" alt="{{ $category->name }}"></span>
          <span class="onyx-category-content"><small>{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</small><strong>{{ $category->name }}</strong><em>{{ $category->products_count }} {{ \Illuminate\Support\Str::plural('product', $category->products_count) }}</em></span>
          <i class="fa-solid fa-arrow-up-right-from-square"></i>
        </a>
      @endforeach
    </div>
  </div>
</section>
@endif

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

<section class="onyx-standard section-pad" id="about">
  <div class="container onyx-standard-grid">
    <div class="onyx-lab-visual">
      <div class="onyx-lab-frame"><img src="{{ url('frontend/assets/images/banner/trusted-research-banner.png') }}" alt="Laboratory research vial with precision dropper"></div>
      <div class="onyx-lab-seal"><i class="fa-solid fa-flask-vial"></i><span><strong>MAJIX Standard</strong><small>Precision in every detail</small></span></div>
    </div>
    <div class="onyx-standard-copy">
      <p class="onyx-eyebrow"><span></span>The Majix standard</p>
      <h2>Trusted Research.<br><em>Proven Quality.</em></h2>
      <p>Formulated and supplied to meet exacting standards of purity, consistency and reliability for professional research applications.</p>
      <div class="onyx-quality-row"><div><i class="fa-solid fa-microscope"></i><span><strong>Lab Verified</strong><small>Quality checks and documented standards.</small></span></div><div><i class="fa-solid fa-layer-group"></i><span><strong>High Purity</strong><small>Consistent compounds across every batch.</small></span></div><div><i class="fa-solid fa-shield-halved"></i><span><strong>Research Use Only</strong><small>Purpose-built for laboratory applications.</small></span></div></div>
    </div>
  </div>
</section>

@if($reviews->isNotEmpty())
<section class="onyx-reviews section-pad" aria-labelledby="reviewHeading">
  <div class="container">
    <header class="onyx-review-heading"><p class="onyx-eyebrow">Trusted by our customers</p><h2 id="reviewHeading">Confidence, backed by experience.</h2><p>Real feedback from customers who choose MAJIX for quality and consistency.</p></header>
    <div class="onyx-review-grid">
      @foreach($reviews->take(3) as $review)
        <article class="onyx-review-card {{ $review->is_featured ? 'is-featured' : '' }}">
          <div class="onyx-review-top"><span aria-label="{{ $review->rating }} out of 5 stars">{!! str_repeat('&#9733;', max(1, min(5, (int) $review->rating))) !!}</span><i class="fa-solid fa-quote-right"></i></div>
          <p>&ldquo;{{ $review->comment }}&rdquo;</p>
          <footer><img src="{{ url($review->avatar ?: 'frontend/assets/images/testimonials/miker.png') }}" alt="{{ $review->customer_name }}"><span><strong>{{ $review->customer_name }}</strong><small>{{ $review->customer_title ?: 'Verified customer' }}</small></span><em><i class="fa-solid fa-circle-check"></i> Verified</em></footer>
        </article>
      @endforeach
    </div>
    <div class="onyx-review-metrics"><span><strong>4.9/5</strong><small>Average rating</small></span><span><strong>Quality</strong><small>Focused standards</small></span><span><strong>Secure</strong><small>Protected checkout</small></span></div>
  </div>
</section>
@endif

<section class="onyx-process">
  <div class="container">
    <div class="onyx-process-copy"><p class="onyx-eyebrow">How it works</p><h2>A Simple, Secure Process</h2><p>Browse, order and receive your research compounds with confidence.</p><div class="onyx-steps"><div><b>1</b><i class="fa-regular fa-calendar-check"></i><span><strong>Browse &amp; Select</strong><small>Explore our research compounds and choose what you need.</small></span></div><div><b>2</b><i class="fa-solid fa-lock"></i><span><strong>Secure Checkout</strong><small>A fast, encrypted and seamless process.</small></span></div><div><b>3</b><i class="fa-solid fa-truck-fast"></i><span><strong>Fast Delivery</strong><small>Discreet, reliable and tracked to your door.</small></span></div></div></div>
    <div class="onyx-newsletter"><div class="onyx-newsletter-copy"><i class="fa-regular fa-envelope"></i><div><h2>Stay Updated with MAJIX</h2><p>Be the first to know about new research compounds, restocks and updates.</p></div></div><span class="onyx-newsletter-divider" aria-hidden="true"></span><form onsubmit="event.preventDefault()"><label><i class="fa-regular fa-envelope"></i><input type="email" placeholder="Enter your email address" aria-label="Email address" required></label><button type="submit">Subscribe <i class="fa-solid fa-arrow-right"></i></button></form></div>
  </div>
</section>

@endsection
