@extends('frontend.layouts.master')

@section('body')
    <section class="shop-hero">
      <div class="container">
        <div class="shop-hero-inner">
          <div class="shop-hero-copy reveal-up">
            <p class="eyebrow">Premium performance products</p>
            <h1>Shop</h1>
            <p>Explore premium, science-backed supplements designed to help you perform, recover and grow.</p>
          </div>
          <div class="shop-hero-features reveal-up delay-2">
            <div><i class="fa-solid fa-truck-fast"></i><span>Fast &amp; Secure<br>Delivery</span></div>
            <div><i class="fa-solid fa-globe"></i><span>Delivery All<br>Over UK</span></div>
            <div><i class="fa-solid fa-sterling-sign"></i><span>Flat Delivery<br>&pound;4.99</span></div>
          </div>
        </div>
      </div>
    </section>

    <section class="shop-catalog section-space">
      <div class="container">
        <div class="shop-layout">
          <aside class="shop-sidebar reveal-on-scroll" aria-label="Shop filters">
            <form method="GET" action="{{ route('frontend.shop') }}">
              @if (! empty($filters['q']))
                <input type="hidden" name="q" value="{{ $filters['q'] }}">
              @endif
              @if (! empty($filters['sort']))
                <input type="hidden" name="sort" value="{{ $filters['sort'] }}" data-price-sort-field>
              @endif

              <div class="filter-block">
                <h2>Categories</h2>
                <a class="{{ empty($filters['category']) ? 'active' : '' }}" href="{{ route('frontend.shop', array_filter(['q' => $filters['q'] ?? null, 'sort' => $filters['sort'] ?? null, 'min_price' => $filters['min_price'] ?? null, 'max_price' => $filters['max_price'] ?? null])) }}"><span>All Products</span><strong>({{ $totalProducts }})</strong></a>
                @foreach ($categories as $category)
                  <a class="{{ ($filters['category'] ?? '') === $category->slug ? 'active' : '' }}" href="{{ route('frontend.shop', array_filter(['category' => $category->slug, 'q' => $filters['q'] ?? null, 'sort' => $filters['sort'] ?? null, 'min_price' => $filters['min_price'] ?? null, 'max_price' => $filters['max_price'] ?? null])) }}"><span>{{ $category->name }}</span><strong>({{ $category->products_count }})</strong></a>
                @endforeach
              </div>

              <div class="filter-block">
                <h2>Price Range</h2>
                @php
                  $minBound = $priceBounds['min'] ?? 0;
                  $maxBound = $priceBounds['max'] ?? 400;
                  $selectedMin = is_numeric($filters['min_price']) ? (int) $filters['min_price'] : $minBound;
                  $selectedMax = is_numeric($filters['max_price']) ? (int) $filters['max_price'] : $maxBound;
                @endphp
                <div class="price-range-control" data-price-range>
                  <input type="range" min="{{ $minBound }}" max="{{ $maxBound }}" value="{{ $selectedMin }}" data-price-min-range aria-label="Minimum price">
                  <input type="range" min="{{ $minBound }}" max="{{ $maxBound }}" value="{{ $selectedMax }}" data-price-max-range aria-label="Maximum price">
                  <div class="price-range"><span data-price-range-fill></span></div>
                </div>
                <div class="price-values"><strong>£<span data-price-min-label>{{ $selectedMin }}</span></strong><strong>£<span data-price-max-label>{{ $selectedMax }}</span></strong></div>
                <div class="filter-fields">
                  <input type="number" name="min_price" min="{{ $minBound }}" max="{{ $maxBound }}" placeholder="Min" value="{{ $filters['min_price'] }}" data-price-min-input>
                  <input type="number" name="max_price" min="{{ $minBound }}" max="{{ $maxBound }}" placeholder="Max" value="{{ $filters['max_price'] }}" data-price-max-input>
                </div>
              </div>

              <div class="filter-block">
                <h2>Lab Tested</h2>
                <label><input type="checkbox" checked> Yes <strong>({{ $totalProducts }})</strong></label>
              </div>

              <button class="clear-filters" type="submit">Apply filters <i class="fa-solid fa-filter"></i></button>
              <a class="clear-filters filter-reset" href="{{ route('frontend.shop') }}">Clear filters <i class="fa-solid fa-rotate-right"></i></a>
            </form>
          </aside>

          <div class="shop-products">
            <div class="shop-toolbar reveal-on-scroll">
              <p>Showing {{ $products->firstItem() ?? 0 }}-{{ $products->lastItem() ?? 0 }} of {{ $products->total() }} results</p>
              <form method="GET" action="{{ route('frontend.shop') }}" class="sort-form">
                @foreach (['q', 'category', 'min_price', 'max_price'] as $filterKey)
                  @if (! empty($filters[$filterKey]))
                    <input type="hidden" name="{{ $filterKey }}" value="{{ $filters[$filterKey] }}">
                  @endif
                @endforeach
                <label class="visually-hidden" for="shopSort">Sort products</label>
                <select id="shopSort" name="sort" onchange="this.form.submit()">
                  <option value="latest" @selected(($filters['sort'] ?? 'latest') === 'latest')>Sort by: Latest</option>
                  <option value="name" @selected(($filters['sort'] ?? '') === 'name')>Sort by: Name</option>
                  <option value="price_asc" @selected(($filters['sort'] ?? '') === 'price_asc')>Price: Low to high</option>
                  <option value="price_desc" @selected(($filters['sort'] ?? '') === 'price_desc')>Price: High to low</option>
                </select>
              </form>
            </div>

            <div class="shop-grid reveal-group">
              @forelse ($products as $product)
                <article class="shop-product-card product-card" id="{{ $product->category?->slug }}" data-product-id="{{ $product->id }}" data-product-url="{{ route('frontend.product-details', $product->slug) }}">
                  @if ($product->is_featured)<span class="tag">Popular</span>@endif
                  <img src="{{ url($product->image ?: 'frontend/assets/images/product-bottle.png') }}" alt="{{ $product->name }}">
                  <small>{{ $product->category?->name }}</small>
                  <h3>{{ $product->name }}</h3>
                  <div class="rating">★★★★★ <span>({{ $product->stock }})</span></div>
                  <div class="product-card-tools">
                    <div class="product-card-qty" aria-label="{{ $product->name }} quantity"><button type="button" data-card-qty-dec>-</button><span data-card-qty>1</span><button type="button" data-card-qty-inc>+</button></div>
                    @if ($product->test_report_image)
                      <button class="test-report-icon" type="button" data-test-report="{{ url($product->test_report_image) }}" data-test-report-title="{{ $product->name }} test report" aria-label="View {{ $product->name }} test report"><i class="fa-solid fa-flask-vial"></i></button>
                    @endif
                  </div>
                  <div><strong>£{{ number_format((float) ($product->sale_price ?: $product->price), 2) }}</strong><button type="button" data-cart-add="{{ $product->id }}" aria-label="Add {{ $product->name }} to cart"><i class="fa-solid fa-cart-plus"></i></button></div>
                </article>
              @empty
                <p>No products found.</p>
              @endforelse
            </div>

            @if ($products->hasPages())
              <div class="shop-pagination" aria-label="Shop pagination">
                {{ $products->onEachSide(1)->links('pagination::bootstrap-5') }}
              </div>
            @endif
          </div>
        </div>

        <section class="bundle-banner reveal-on-scroll" aria-label="Bundle offer">
          <div></div>
          <div>
            <p class="eyebrow">Bundle &amp; save</p>
            <h2>Buy More. Save More.</h2>
            <p>Stack your results with premium bundles and exclusive savings.</p>
            <a class="btn btn-gold" href="{{ route('frontend.shop') }}">View bundles <i class="fa-solid fa-arrow-right"></i></a>
          </div>
        </section>
      </div>
    </section>

    @include('frontend.inc.delivery-trusted')
@endsection
