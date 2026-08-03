@extends('frontend.layouts.master')

@section('body')
    <section class="hero" id="home">
      <div class="container hero-content">
        <div class="home-hero-slider" data-home-hero-slider aria-label="Featured products">
          <div class="home-hero-track">
            @forelse ($heroProducts as $product)
              <article class="home-hero-slide {{ $loop->first ? 'is-active' : '' }}" data-home-hero-slide aria-hidden="{{ $loop->first ? 'false' : 'true' }}">
                <div class="home-hero-copy">
                  <p class="eyebrow">{{ $product->stock > 0 ? 'Featured performance product' : 'Back by popular demand soon' }}</p>
                  <h1>{{ $product->name }}</h1>
                  <p class="hero-copy">{{ $product->short_description ?: 'Premium quality, performance-focused support for your goals.' }}</p>
                  <div class="home-hero-price"><strong>&pound;{{ number_format((float) ($product->sale_price ?: $product->price), 2) }}</strong><span>{{ $product->category?->name ?? 'Performance' }}</span></div>
                  <div class="d-flex flex-wrap gap-3 mt-4">
                    <a class="btn btn-gold" href="{{ route('frontend.product-details', $product->slug) }}">View product <i class="fa-solid fa-arrow-right"></i></a>
                    @if ($product->stock <= 0)
                      <button class="btn hero-notify-btn" type="button" data-stock-notify="{{ $product->id }}" data-product-name="{{ $product->name }}"><i class="fa-regular fa-bell"></i> Inform Me</button>
                    @else
                      <button class="btn btn-outline-light-custom" type="button" data-cart-add="{{ $product->id }}">Add to cart</button>
                    @endif
                  </div>
                </div>
                <a class="home-hero-product" href="{{ route('frontend.product-details', $product->slug) }}" aria-label="View {{ $product->name }}">
                  @if ($product->stock <= 0)<span class="home-hero-stock">Out of stock</span>@endif
                  <span class="home-hero-glow" aria-hidden="true"></span>
                  <img src="{{ url($product->image ?: 'frontend/assets/images/product-bottle.png') }}" alt="{{ $product->name }}">
                </a>
              </article>
            @empty
              <article class="home-hero-slide is-active" data-home-hero-slide aria-hidden="false"><div class="home-hero-copy"><p class="eyebrow">Premium performance solutions</p><h1>Reach your <span>potential</span></h1><p class="hero-copy">Science-backed products designed to help you perform, recover and grow.</p><div class="d-flex flex-wrap gap-3 mt-4"><a class="btn btn-gold" href="#products">Shop now <i class="fa-solid fa-arrow-right"></i></a><a class="btn btn-outline-light-custom" href="#about">Learn more</a></div></div></article>
            @endforelse
          </div>
          @if ($heroProducts->count() > 1)
            <button class="home-hero-arrow home-hero-prev" type="button" aria-label="Previous featured product"><i class="fa-solid fa-chevron-left"></i></button>
            <button class="home-hero-arrow home-hero-next" type="button" aria-label="Next featured product"><i class="fa-solid fa-chevron-right"></i></button>
            <div class="home-hero-pagination" aria-label="Featured product pagination"></div>
          @endif
        </div>
        <div class="hero-feature-slider reveal-up delay-4" data-hero-feature-slider>
          <div class="hero-features">
            <div class="hero-feature-slide"><div class="hero-feature"><i class="fa-solid fa-truck-fast"></i><span>Fast &amp; Secure Delivery</span></div></div>
            <div class="hero-feature-slide"><div class="hero-feature"><i class="fa-solid fa-globe"></i><span>Delivery All Over UK</span></div></div>
            <div class="hero-feature-slide"><div class="hero-feature border-0"><i class="fa-solid fa-sterling-sign"></i><span>Flat Delivery &pound;4.99</span></div></div>
          </div>
          <div class="hero-feature-pagination" aria-label="Delivery benefits pagination"></div>
        </div>
      </div>
    </section>

    <section class="section-space categories" id="products">
      <div class="container">
        <div class="section-heading d-flex flex-wrap justify-content-between align-items-end gap-3 reveal-on-scroll">
          <div><p class="eyebrow">Browse categories</p><h2>What are you looking for?</h2></div>
          <a class="btn btn-light-outline" href="{{ route('frontend.shop') }}">View all categories <i class="fa-solid fa-arrow-right"></i></a>
        </div>
        <div class="row g-3 mt-3 reveal-group">
          @foreach ($categories as $category)
            <div class="col-6 col-md-4 col-lg-3">
              <article class="category-card" data-category-url="{{ route('frontend.shop', ['category' => $category->slug]) }}">
                <img src="{{ url($category->image ?: 'frontend/assets/images/product-bottle.png') }}" alt="{{ $category->name }}">
                <div><h3>{{ $category->name }}</h3><i class="fa-solid fa-chevron-right"></i></div>
              </article>
            </div>
          @endforeach
        </div>
      </div>
    </section>

    <section class="why-section section-space" id="about">
      <div class="container">
        <div class="row align-items-center g-4 reveal-group">
          <div class="col-lg-3 why-intro">
            <p class="eyebrow">Why choose Arete?</p>
            <h2>Your Goals.<br>Our Mission.</h2>
            <p>Premium quality products, backed by science and trusted by athletes worldwide.</p>
          </div>
          <div class="col-6 col-lg"><div class="benefit"><i class="fa-solid fa-prescription-bottle-medical"></i><h3>Pharma<br>Grade</h3><p>Quality-focused product standards.</p></div></div>
          <div class="col-6 col-lg"><div class="benefit"><i class="fa-solid fa-flask-vial"></i><h3>Lab<br>Tested</h3><p>Batch testing and verification.</p></div></div>
          <div class="col-6 col-lg"><div class="benefit"><i class="fa-solid fa-sterling-sign"></i><h3>&pound;4.99<br>Shipping</h3><p>Flat delivery across the UK.</p></div></div>
          <div class="col-6 col-lg"><div class="benefit border-0"><i class="fa-solid fa-truck-fast"></i><h3>Fast &amp; Secure<br>Delivery</h3><p>Quick, protected order handling.</p></div></div>
        </div>
      </div>
    </section>

    <section class="bestsellers section-space" id="bestsellers">
      <div class="container">
        <div class="section-heading light d-flex justify-content-between align-items-end reveal-on-scroll">
          <div><p class="eyebrow">Bestsellers</p><h2>Our Top Picks</h2></div>
          <a href="{{ route('frontend.shop') }}">View all <i class="fa-solid fa-arrow-right"></i></a>
        </div>
        <div class="product-slider">
          <button class="slider-arrow slider-prev" type="button" aria-label="Previous product"><i class="fa-solid fa-chevron-left"></i></button>
          <div class="row g-3 mt-3 reveal-group product-track">
            @foreach ($featuredProducts as $product)
              <div class="col-6 col-md-4 col-lg bestseller-product-column">
                <article class="product-card {{ $product->stock <= 0 ? 'is-out-of-stock' : '' }}" data-product-id="{{ $product->id }}" data-product-name="{{ $product->name }}" data-product-url="{{ route('frontend.product-details', $product->slug) }}">
                  @if ($product->stock <= 0)<span class="tag stock-tag">Out of Stock</span>@elseif ($product->is_bestseller)<span class="tag">Bestseller</span>@endif
                  <img src="{{ url($product->image ?: 'frontend/assets/images/product-bottle.png') }}" alt="{{ $product->name }}">
                  <h3>{{ $product->name }}</h3>
                  <small>{{ $product->category?->name }}</small>
                  <div class="product-card-tools">
                    @if ($product->stock > 0)
                      <div class="product-card-qty" aria-label="{{ $product->name }} quantity"><button type="button" data-card-qty-dec>-</button><span data-card-qty>1</span><button type="button" data-card-qty-inc>+</button></div>
                    @endif
                    @if ($product->test_report_image)
                      <button class="test-report-icon" type="button" data-test-report="{{ url($product->test_report_image) }}" data-test-report-title="{{ $product->name }} test report" aria-label="View {{ $product->name }} test report"><i class="fa-solid fa-flask-vial"></i></button>
                    @endif
                  </div>
                  <div><strong>&pound;{{ number_format((float) ($product->sale_price ?: $product->price), 2) }}</strong>@if ($product->stock > 0)<button type="button" data-cart-add="{{ $product->id }}" aria-label="Add {{ $product->name }} to cart"><i class="fa-solid fa-cart-plus"></i></button>@else<button class="notify-stock-btn" type="button" data-stock-notify="{{ $product->id }}" data-product-name="{{ $product->name }}">Inform Me</button>@endif</div>
                </article>
              </div>
            @endforeach
          </div>
          <button class="slider-arrow slider-next" type="button" aria-label="Next product"><i class="fa-solid fa-chevron-right"></i></button>
        </div>
      </div>
    </section>

    <section class="testimonials section-space">
      <div class="container">
        <div class="text-center section-heading testimonial-heading reveal-on-scroll">
          <p class="eyebrow">Trusted by athletes</p>
          <h2>Real Results. Real People.</h2>
          <p>At Arete Performance, we don't just promise results - we deliver them.<br>Here's what athletes and professionals have to say about their journey with us.</p>
        </div>
        <div class="testimonial-carousel" data-testimonial-slider>
          <button class="testimonial-nav testimonial-prev" type="button" aria-label="Previous testimonial"><i class="fa-solid fa-chevron-left"></i></button>
          <div class="testimonial-viewport">
            <div class="testimonial-track reveal-group">
              @foreach ($reviews as $review)
                <article class="testimonial-slide {{ $review->is_featured ? 'is-featured' : '' }}">
                  <figure class="quote-card">
                    <i class="fa-solid fa-quote-left"></i>
                    <p>"{{ $review->comment }}"</p>
                    <figcaption>
                      <img src="{{ url($review->avatar ?: 'frontend/assets/images/testimonials/miker.png') }}" alt="{{ $review->customer_name }}">
                      <div><strong>{{ $review->customer_name }}</strong><small>{{ $review->customer_title }}</small><span class="rating" aria-label="{{ $review->rating }} star rating">{{ str_repeat('★', $review->rating) }}</span></div>
                    </figcaption>
                  </figure>
                </article>
              @endforeach
            </div>
          </div>
          <button class="testimonial-nav testimonial-next" type="button" aria-label="Next testimonial"><i class="fa-solid fa-chevron-right"></i></button>
          <div class="testimonial-pagination" aria-label="Testimonials pagination"></div>
        </div>
        <div class="testimonial-stats reveal-on-scroll">
          <div><i class="fa-solid fa-users"></i><strong>50K+</strong><span>Happy Customers</span></div>
          <div><i class="fa-solid fa-star"></i><strong>4.9/5</strong><span>Average Rating</span></div>
          <div><i class="fa-solid fa-shield-halved"></i><strong>99%</strong><span>Satisfaction Rate</span></div>
          <div><i class="fa-solid fa-award"></i><strong>100%</strong><span>Quality Assured</span></div>
        </div>
      </div>
    </section>
@endsection
