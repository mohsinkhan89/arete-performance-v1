@extends('frontend.layouts.master')

@section('metas')
  <meta name="description" content="{{ $product->short_description ?: strip_tags($product->description ?? '') }}">
@endsection

@section('body')
@php
  $price = (float) ($product->sale_price ?: $product->price);
  $productImage = url($product->image ?: 'frontend/assets/images/product-bottle.png');
  $reportImage = $product->test_report_image ? url($product->test_report_image) : null;
  $descriptionHtml = $product->description ?: '<p>' . e($product->short_description ?: 'Product details will be updated soon.') . '</p>';
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
            <figcaption><span><i class="fa-solid fa-magnifying-glass-plus"></i> Click to zoom</span><span>{{ $reportImage ? '2 images' : '1 image' }}</span></figcaption>
          </figure>
        </div>

        <aside class="product-purchase">
          <span class="product-category-pill">{{ $product->category?->name ?? 'Product' }}</span>
          <h1>{{ $product->name }}</h1>
          <div class="product-rating"><span>&#9733;&#9733;&#9733;&#9733;&#9733;</span><small>({{ $product->stock }} in stock)</small></div>
          <strong class="product-price">&pound;{{ number_format($price, 2) }}</strong>
          <p class="product-detail-summary">{{ $product->short_description }}</p>
          <div class="product-assurance">
            <div><i class="fa-regular fa-circle-check"></i> {{ $product->stock > 0 ? 'In Stock' : 'Out of Stock' }}</div>
            <div><i class="fa-solid fa-truck-fast"></i> Discreet Shipping</div>
            <div><i class="fa-solid fa-flask-vial"></i> Lab Tested</div>
            <div><i class="fa-solid fa-globe"></i> Worldwide Delivery</div>
          </div>
          <div class="product-quantity">
            <span>Quantity</span>
            <div class="cart-stepper"><button type="button" data-product-qty-dec aria-label="Decrease quantity">-</button><span data-product-qty>1</span><button type="button" data-product-qty-inc aria-label="Increase quantity">+</button></div>
          </div>
          <div class="product-actions">
            <button class="btn btn-gold" type="button" data-product-add="{{ $product->id }}">Add to cart <i class="fa-solid fa-cart-plus"></i></button>
            <button class="btn buy-now-btn" type="button" data-buy-now="{{ $product->id }}">Buy now <i class="fa-solid fa-bolt"></i></button>
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

      <div class="product-benefit-strip">
        <div><i class="fa-solid fa-shield-halved"></i><strong>Premium Quality</strong><span>Trusted product quality.</span></div>
        <div><i class="fa-solid fa-flask-vial"></i><strong>Lab Tested</strong><span>{{ $reportImage ? 'Report available below.' : 'Batch checked selections.' }}</span></div>
        <div><i class="fa-solid fa-truck-fast"></i><strong>Discreet Shipping</strong><span>Private and secure delivery.</span></div>
        <div><i class="fa-solid fa-headset"></i><strong>Support</strong><span>Help whenever you need it.</span></div>
      </div>
    </div>
  </section>

  <section class="product-info-section">
    <div class="container">
      <div data-product-description-source hidden>{!! $descriptionHtml !!}</div>

      <article class="product-info-card tab-content-card">
        <nav class="product-tabs" aria-label="Description tab">
          <a class="active" href="#description">Description</a><a href="#benefits">Benefits</a><a href="#dosage">Dosage</a><a href="#ingredients">Ingredients</a><a href="#reviews">Reviews</a><a href="#faq">FAQ</a>
        </nav>
        <div class="product-info-grid" id="description">
          <div class="product-description" data-description-section="description">
            <h2>About {{ $product->name }}</h2>
            <p>{{ $product->short_description ?: 'Product information is being prepared.' }}</p>
          </div>
          <dl class="product-specs">
            <div><dt>Product Name:</dt><dd>{{ $product->name }}</dd></div>
            <div><dt>Category:</dt><dd>{{ $product->category?->name ?? 'Product' }}</dd></div>
            <div><dt>SKU:</dt><dd>{{ $product->sku }}</dd></div>
            <div><dt>Price:</dt><dd>&pound;{{ number_format($price, 2) }}</dd></div>
            <div><dt>Stock:</dt><dd>{{ $product->stock }}</dd></div>
            <div><dt>Brand:</dt><dd>Arete Performance</dd></div>
          </dl>
          <aside class="lab-report" data-product-report="{{ $reportImage }}" data-product-report-title="{{ $product->name }} test report">
            <h3>Lab Tested &amp; Certified</h3>
            <div class="report-preview"><img src="{{ $reportImage ?: url('frontend/assets/images/logo/logo-transperent.png') }}" alt="{{ $product->name }} test report"></div>
            <button type="button" data-lab-report>{{ $reportImage ? 'View full report' : 'Report coming soon' }} <i class="fa-solid fa-download"></i></button>
          </aside>
        </div>
        <div class="description-assurance">
          <div><i class="fa-solid fa-shield-halved"></i><strong>In Stock</strong><span>Order now for fast delivery</span></div>
          <div><i class="fa-solid fa-flask-vial"></i><strong>Lab Tested</strong><span>Quality checked product</span></div>
          <div><i class="fa-solid fa-truck-fast"></i><strong>Discreet Shipping</strong><span>Private &amp; secure delivery</span></div>
          <div><i class="fa-solid fa-globe"></i><strong>Worldwide Delivery</strong><span>Supported regions available</span></div>
        </div>
      </article>

      <article class="product-info-card tab-content-card" id="benefits">
        <nav class="product-tabs" aria-label="Benefits tab">
          <a href="#description">Description</a><a class="active" href="#benefits">Benefits</a><a href="#dosage">Dosage</a><a href="#ingredients">Ingredients</a><a href="#reviews">Reviews</a><a href="#faq">FAQ</a>
        </nav>
        <div class="benefits-tab-grid">
          <div><h2>Key Benefits</h2><div class="benefit-list dynamic-rich-content" data-description-section="benefits"></div></div>
          <figure class="benefits-product-shot"><img src="{{ $productImage }}" alt="{{ $product->name }}"></figure>
        </div>
      </article>

      <article class="product-info-card tab-content-card" id="dosage">
        <nav class="product-tabs" aria-label="Dosage tab">
          <a href="#description">Description</a><a href="#benefits">Benefits</a><a class="active" href="#dosage">Dosage</a><a href="#ingredients">Ingredients</a><a href="#reviews">Reviews</a><a href="#faq">FAQ</a>
        </nav>
        <div class="dosage-tab-grid">
          <div class="dosage-copy"><h2>Recommended Dosage</h2><div class="dynamic-rich-content" data-description-section="dosage"><p>Dosage guidance will be updated soon.</p></div></div>
          <div class="dosage-steps">
            <div><i class="fa-solid fa-flask-vial"></i><strong>Step 1</strong><span>Read Label</span><small>Review instructions before use.</small></div>
            <div><i class="fa-solid fa-chart-line"></i><strong>Step 2</strong><span>Stay Consistent</span><small>Follow a steady routine.</small></div>
            <div><i class="fa-solid fa-bullseye"></i><strong>Step 3</strong><span>Track Progress</span><small>Monitor performance and recovery.</small></div>
            <div><i class="fa-solid fa-rotate"></i><strong>Step 4</strong><span>Review Cycle</span><small>Adjust only with proper guidance.</small></div>
          </div>
        </div>
        <div class="dosage-note"><i class="fa-solid fa-info"></i><p><strong>Note:</strong> Do not exceed the recommended dosage. Consult a qualified professional before use.</p></div>
      </article>

      <article class="product-info-card tab-content-card" id="ingredients">
        <nav class="product-tabs" aria-label="Ingredients tab">
          <a href="#description">Description</a><a href="#benefits">Benefits</a><a href="#dosage">Dosage</a><a class="active" href="#ingredients">Ingredients</a><a href="#reviews">Reviews</a><a href="#faq">FAQ</a>
        </nav>
        <div class="ingredients-tab-grid">
          <div><h2>Ingredients</h2><div class="dynamic-rich-content" data-description-section="ingredients"><p>Ingredient information will be updated soon.</p></div></div>
          <aside class="ingredient-certifications">
            <div><i class="fa-solid fa-leaf"></i><strong>Premium Selection</strong><span>Quality-focused product sourcing.</span></div>
            <div><i class="fa-solid fa-flask-vial"></i><strong>Lab Verified</strong><span>{{ $reportImage ? 'Test report available.' : 'Testing information coming soon.' }}</span></div>
            <div><i class="fa-solid fa-shield-halved"></i><strong>Trusted Quality</strong><span>Managed by Arete Performance.</span></div>
          </aside>
        </div>
      </article>

      <article class="product-info-card tab-content-card" id="reviews">
        <nav class="product-tabs" aria-label="Reviews tab">
          <a href="#description">Description</a><a href="#benefits">Benefits</a><a href="#dosage">Dosage</a><a href="#ingredients">Ingredients</a><a class="active" href="#reviews">Reviews</a><a href="#faq">FAQ</a>
        </nav>
        <div class="reviews-tab-content">
          <h2>Customer Reviews</h2>
          <div class="reviews-grid">
            <div class="review-score"><strong>4.9</strong><span>&#9733;&#9733;&#9733;&#9733;&#9733;</span><small>Customer feedback</small></div>
            <div class="review-bars"><div><span>5</span><b><i style="width: 88%"></i></b><em>Top</em></div><div><span>4</span><b><i style="width: 18%"></i></b><em>Good</em></div><div><span>3</span><b><i style="width: 8%"></i></b><em>Ok</em></div></div>
            @forelse ($reviews as $review)
              <article class="review-card"><span>{!! str_repeat('&#9733;', (int) $review->rating) !!}</span><p>"{{ $review->comment }}"</p><div><img src="{{ url($review->avatar ?: 'frontend/assets/images/testimonials/miker.png') }}" alt="{{ $review->customer_name }}"><strong>{{ $review->customer_name }}<small>{{ $review->customer_title ?: 'Verified Buyer' }}</small></strong></div></article>
            @empty
              <article class="review-card"><span>&#9733;&#9733;&#9733;&#9733;&#9733;</span><p>"Premium product quality and fast service."</p><div><img src="{{ url('frontend/assets/images/testimonials/miker.png') }}" alt="Customer"><strong>Arete Customer<small>Verified Buyer</small></strong></div></article>
            @endforelse
          </div>
          <div class="review-dots" aria-hidden="true"><span class="active"></span><span></span><span></span></div>
        </div>
      </article>

      <article class="product-info-card tab-content-card" id="faq">
        <nav class="product-tabs" aria-label="FAQ tab">
          <a href="#description">Description</a><a href="#benefits">Benefits</a><a href="#dosage">Dosage</a><a href="#ingredients">Ingredients</a><a href="#reviews">Reviews</a><a class="active" href="#faq">FAQ</a>
        </nav>
        <div class="faq-tab-grid">
          <div><h2>Frequently Asked Questions</h2><div class="faq-list" data-description-section="faq"><details open><summary>How do I use this product?</summary><p>Review the product description and dosage guidance, then consult a qualified professional before use.</p></details></div></div>
          <aside class="faq-support"><i class="fa-solid fa-headset"></i><h3>Still Have Questions?</h3><p>Our support team is here to help you.</p><a class="btn buy-now-btn" href="{{ route('frontend.index') }}#contact">Contact support</a></aside>
        </div>
      </article>

      @if ($relatedProducts->isNotEmpty())
        <section class="related-products">
          <h2>You May Also Like</h2>
          <div class="related-grid">
            @foreach ($relatedProducts as $related)
              <article class="related-card" data-product-id="{{ $related->id }}" data-product-url="{{ route('frontend.product-details', $related->slug) }}">
                <img src="{{ url($related->image ?: 'frontend/assets/images/product-bottle.png') }}" alt="{{ $related->name }}">
                <h3>{{ $related->name }}<br><span>{{ $related->category?->name }}</span></h3>
                <strong>&pound;{{ number_format((float) ($related->sale_price ?: $related->price), 2) }}</strong>
                <button type="button" aria-label="Add {{ $related->name }} to cart"><i class="fa-solid fa-cart-plus"></i></button>
              </article>
            @endforeach
          </div>
        </section>
      @endif
    </div>
  </section>

  @include('frontend.inc.delivery-trusted')
</div>
@endsection
