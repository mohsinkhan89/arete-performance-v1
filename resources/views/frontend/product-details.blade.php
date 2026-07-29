@extends('frontend.layouts.master')

@section('metas')
  <meta name="description" content="{{ $product->short_description ?: strip_tags($product->description ?? '') }}">
@endsection

@section('body')
@php
  $price = (float) ($product->sale_price ?: $product->price);
  $productImage = url($product->image ?: 'frontend/assets/images/product-bottle.png');
  $reportImage = $product->test_report_image ? url($product->test_report_image) : null;
  $descriptionText = trim(preg_replace('/\s+/', ' ', str_replace('&nbsp;', ' ', strip_tags($product->description ?? ''))));
  $hasDescription = $descriptionText !== '';
  $descriptionHtml = $product->description;
  $descriptionGridClass = $reportImage ? '' : 'has-no-report';
  $reviewCount = $reviews->count();
  $reviewAverage = $reviewCount ? round((float) $reviews->avg('rating'), 1) : 0;
  $reviewCounts = collect(range(5, 1))->mapWithKeys(fn ($rating) => [$rating => $reviews->where('rating', $rating)->count()]);
@endphp

<div class="product-detail-main">
  <section class="product-detail-top">
    <div class="container">
      <nav class="product-breadcrumb" aria-label="Breadcrumb">
        <a href="{{ route('frontend.index') }}">Home</a><i class="fa-solid fa-chevron-right"></i>
        <a href="{{ route('frontend.shop', array_filter(['category' => $product->category?->slug])) }}">{{ $product->category?->name ?? 'Shop' }}</a><i class="fa-solid fa-chevron-right"></i>
        <span>{{ $product->name }}</span>
      </nav>

      <div class="product-detail-layout">
        <div class="product-gallery">
          <div class="product-thumbs" aria-label="Product media">
            <button class="active" type="button" data-product-image="{{ $productImage }}" data-product-alt="{{ $product->name }}">
              <img src="{{ $productImage }}" alt="{{ $product->name }}">
            </button>
            @if ($reportImage)
              <button type="button" data-product-image="{{ $reportImage }}" data-product-alt="{{ $product->name }} test report">
                <img src="{{ $reportImage }}" alt="{{ $product->name }} test report">
              </button>
            @endif
          </div>
          <figure class="product-main-image" data-product-zoom role="button" tabindex="0" aria-label="Zoom product image" aria-pressed="false">
            @if ($product->is_featured)<span class="tag">Popular</span>@endif
            <img src="{{ $productImage }}" alt="{{ $product->name }}" data-product-main-image>
            <span class="product-zoom-lens" data-product-zoom-lens aria-hidden="true"></span>
            <button class="product-image-expand" type="button" data-product-expand aria-label="Open product image">
              <i class="fa-solid fa-plus"></i>
            </button>
            <figcaption><span><i class="fa-solid fa-magnifying-glass-plus"></i> Click to zoom</span><span>{{ $reportImage ? '2 images' : '1 image' }}</span></figcaption>
          </figure>
        </div>

        <aside class="product-purchase">
          <span class="product-category-pill">{{ $product->category?->name ?? 'Product' }}</span>
          <h1>{{ $product->name }}</h1>
          <div class="product-rating"><span>&#9733;&#9733;&#9733;&#9733;&#9733;</span><small>({{ number_format($product->reviews_count) }} reviews)</small></div>
          <strong class="product-price">&pound;{{ number_format($price, 2) }}</strong>
          <p class="product-detail-summary">{{ $product->short_description }}</p>
          <div class="product-assurance">
            <div><i class="fa-regular fa-circle-check"></i> {{ $product->stock > 0 ? 'In Stock' : 'Out of Stock' }}</div>
            <div><i class="fa-solid fa-prescription-bottle-medical"></i> Pharma Grade</div>
            <div><i class="fa-solid fa-flask-vial"></i> Lab Tested</div>
            <div><i class="fa-solid fa-sterling-sign"></i> &pound;4.99 Shipping</div>
          </div>
          @if ($product->stock > 0)
            <div class="product-quantity">
              <span>Quantity</span>
              <div class="cart-stepper"><button type="button" data-product-qty-dec aria-label="Decrease quantity">-</button><span data-product-qty>1</span><button type="button" data-product-qty-inc aria-label="Increase quantity">+</button></div>
            </div>
          @endif
          <div class="product-actions">
            @if ($product->stock > 0)
              <button class="btn btn-gold" type="button" data-product-add="{{ $product->id }}">Add to cart <i class="fa-solid fa-cart-plus"></i></button>
              <button class="btn buy-now-btn" type="button" data-buy-now="{{ $product->id }}">Buy now <i class="fa-solid fa-bolt"></i></button>
            @else
              <button class="btn btn-gold" type="button" data-stock-notify="{{ $product->id }}" data-product-name="{{ $product->name }}">Inform Me When Available <i class="fa-solid fa-bell"></i></button>
            @endif
          </div>
          <div class="secure-payment">
            <span>Secure Checkout With</span>
            <div class="payment-badges cart-payments" aria-label="Accepted payment methods">
              <span class="payment-image"><img src="{{ url('frontend/assets/images/google-pay.webp') }}" alt="Google Pay"></span>
              <span class="payment-image"><img src="{{ url('frontend/assets/images/apple-pay.webp') }}" alt="Apple Pay"></span>
            </div>
          </div>
        </aside>
      </div>
    </div>
  </section>

  @if ($hasDescription)
  <section class="product-info-section">
    <div class="container">
      <div data-product-description-source hidden>{!! $descriptionHtml !!}</div>

      <article class="product-info-card tab-content-card" id="description" data-tab-card="description">
        <nav class="product-tabs" aria-label="Description tab">
          <a class="active" href="#description">Description</a><a href="#benefits">Benefits</a><a href="#dosage">Dosage</a><a href="#ingredients">Ingredients</a><a href="#reviews">Reviews</a><a href="#faq">FAQ</a>
        </nav>
        <div class="product-info-grid {{ $descriptionGridClass }}">
          <div class="product-description" data-description-section="description">
            <h2 data-section-title="description"></h2>
          </div>
          <dl class="product-specs" data-description-section="specs"></dl>
          @if ($reportImage)
            <aside class="lab-report" data-product-report="{{ $reportImage }}" data-product-report-title="{{ $product->name }} test report">
              <h3>Lab Tested &amp; Certified</h3>
              <div class="report-preview"><img src="{{ $reportImage }}" alt="{{ $product->name }} test report"></div>
              <button type="button" data-lab-report>View full report <i class="fa-solid fa-download"></i></button>
            </aside>
          @endif
        </div>
      </article>

      <article class="product-info-card tab-content-card" id="benefits" data-tab-card="benefits">
        <nav class="product-tabs" aria-label="Benefits tab">
          <a href="#description">Description</a><a class="active" href="#benefits">Benefits</a><a href="#dosage">Dosage</a><a href="#ingredients">Ingredients</a><a href="#reviews">Reviews</a><a href="#faq">FAQ</a>
        </nav>
        <div class="benefits-tab-grid">
          <div><h2 data-section-title="benefits"></h2><div class="benefit-list dynamic-rich-content" data-description-section="benefits"></div></div>
          <figure class="benefits-product-shot"><img src="{{ $productImage }}" alt="{{ $product->name }}"></figure>
        </div>
      </article>

      <article class="product-info-card tab-content-card" id="dosage" data-tab-card="dosage">
        <nav class="product-tabs" aria-label="Dosage tab">
          <a href="#description">Description</a><a href="#benefits">Benefits</a><a class="active" href="#dosage">Dosage</a><a href="#ingredients">Ingredients</a><a href="#reviews">Reviews</a><a href="#faq">FAQ</a>
        </nav>
        <div class="dosage-tab-grid">
          <div class="dosage-copy"><h2 data-section-title="dosage"></h2><div class="dynamic-rich-content" data-description-section="dosage"></div></div>
          <div class="dosage-steps" data-description-section="dosage-steps"></div>
        </div>
        <div class="dosage-note" data-description-section="dosage-note"></div>
      </article>

      <article class="product-info-card tab-content-card" id="ingredients" data-tab-card="ingredients">
        <nav class="product-tabs" aria-label="Ingredients tab">
          <a href="#description">Description</a><a href="#benefits">Benefits</a><a href="#dosage">Dosage</a><a class="active" href="#ingredients">Ingredients</a><a href="#reviews">Reviews</a><a href="#faq">FAQ</a>
        </nav>
        <div class="ingredients-tab-grid">
          <div><h2 data-section-title="ingredients"></h2><div class="dynamic-rich-content" data-description-section="ingredients"></div></div>
          <aside class="ingredient-certifications" data-description-section="ingredient-cards"></aside>
        </div>
      </article>

      <article class="product-info-card tab-content-card" id="reviews" data-tab-card="reviews" data-server-content="{{ $reviews->isNotEmpty() ? '1' : '0' }}">
        <nav class="product-tabs" aria-label="Reviews tab">
          <a href="#description">Description</a><a href="#benefits">Benefits</a><a href="#dosage">Dosage</a><a href="#ingredients">Ingredients</a><a class="active" href="#reviews">Reviews</a><a href="#faq">FAQ</a>
        </nav>
        <div class="reviews-tab-content">
          <h2>Customer Reviews</h2>
          <div class="reviews-grid">
            <div class="review-score"><strong>{{ number_format($reviewAverage, 1) }}</strong><span>{!! str_repeat('&#9733;', (int) round($reviewAverage)) !!}</span><small>{{ $reviewCount }} reviews</small></div>
            <div class="review-bars">
              @foreach ($reviewCounts as $rating => $count)
                <div><span>{{ $rating }}</span><b><i style="width: {{ $reviewCount ? round(($count / $reviewCount) * 100) : 0 }}%"></i></b><em>{{ $count }}</em></div>
              @endforeach
            </div>
            @foreach ($reviews as $review)
              <article class="review-card"><span>{!! str_repeat('&#9733;', (int) $review->rating) !!}</span><p>"{{ $review->comment }}"</p><div><img src="{{ url($review->avatar ?: 'frontend/assets/images/testimonials/miker.png') }}" alt="{{ $review->customer_name }}"><strong>{{ $review->customer_name }}<small>{{ $review->customer_title ?: 'Verified Buyer' }}</small></strong></div></article>
            @endforeach
          </div>
          <div class="review-dots" aria-hidden="true"><span class="active"></span><span></span><span></span></div>
        </div>
      </article>

      <article class="product-info-card tab-content-card" id="faq" data-tab-card="faq">
        <nav class="product-tabs" aria-label="FAQ tab">
          <a href="#description">Description</a><a href="#benefits">Benefits</a><a href="#dosage">Dosage</a><a href="#ingredients">Ingredients</a><a href="#reviews">Reviews</a><a class="active" href="#faq">FAQ</a>
        </nav>
        <div class="faq-tab-grid">
          <div><h2 data-section-title="faq"></h2><div class="faq-list" data-description-section="faq"></div></div>
        </div>
      </article>

      @if ($relatedProducts->isNotEmpty())
        <section class="related-products">
          <h2>You May Also Like</h2>
          <div class="related-grid">
            @foreach ($relatedProducts as $related)
              <article class="related-card {{ $related->stock <= 0 ? 'is-out-of-stock' : '' }}" data-product-id="{{ $related->id }}" data-product-name="{{ $related->name }}" data-product-url="{{ route('frontend.product-details', $related->slug) }}">
                @if ($related->stock <= 0)<span class="tag stock-tag">Out of Stock</span>@endif
                <img src="{{ url($related->image ?: 'frontend/assets/images/product-bottle.png') }}" alt="{{ $related->name }}">
                <h3>{{ $related->name }}<br><span>{{ $related->category?->name }}</span></h3>
                <strong>&pound;{{ number_format((float) ($related->sale_price ?: $related->price), 2) }}</strong>
                @if ($related->stock > 0)<button type="button" aria-label="Add {{ $related->name }} to cart"><i class="fa-solid fa-cart-plus"></i></button>@else<button class="notify-stock-btn" type="button" data-stock-notify="{{ $related->id }}" data-product-name="{{ $related->name }}">Inform Me</button>@endif
              </article>
            @endforeach
          </div>
        </section>
      @endif
    </div>
  </section>
  @endif

  @include('frontend.inc.delivery-trusted')
</div>
@endsection
