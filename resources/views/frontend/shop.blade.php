@extends('frontend.layouts.master')

@section('body')
    <section class="shop-hero" aria-labelledby="shopHeroTitle">
      <div class="shop-hero-grid" aria-hidden="true"></div>
      <div class="container shop-hero-inner">
        <div class="shop-hero-copy reveal-up">
          <p class="shop-hero-eyebrow"><span></span> The complete Majix collection</p>
          <h1 id="shopHeroTitle" class="shop-title-word" aria-label="Shop"><span>S</span><span>H</span><span>O</span><span>P</span></h1>
          <p>Premium research compounds, clearly organised in one collection.</p>
        </div>
        <div class="shop-hero-benefits">
          <div><i class="fa-solid fa-flask-vial"></i><span><strong>Research grade</strong><small>Compounds</small></span></div>
          <div><i class="fa-solid fa-shield-halved"></i><span><strong>Verified purity</strong><small>Quality assured</small></span></div>
          <div><i class="fa-solid fa-layer-group"></i><span><strong>Consistent</strong><small>Quality</small></span></div>
        </div>
      </div>
    </section>

    <section class="shop-catalog section-space" id="shopCollection">
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
                <article class="shop-product-card onyx-product-card product-card {{ $product->stock <= 0 ? 'is-out-of-stock' : '' }}" id="{{ $product->category?->slug }}" data-product-id="{{ $product->id }}" data-product-name="{{ $product->name }}" data-product-url="{{ route('frontend.product-details', $product->slug) }}">
                  <div class="onyx-product-badges">
                    <span class="onyx-product-status {{ $product->stock <= 0 ? 'is-empty' : '' }}">{{ $product->stock <= 0 ? 'Out of stock' : 'In stock' }}</span>
                    @if($product->is_bestseller || $product->is_featured)<span class="onyx-product-featured"><i class="fa-solid fa-bolt"></i> {{ $product->is_bestseller ? 'Bestseller' : 'Popular' }}</span>@endif
                  </div>
                  <a class="onyx-product-image" href="{{ route('frontend.product-details', $product->slug) }}"><img src="{{ url($product->image ?: 'frontend/assets/images/product-bottle.png') }}" alt="{{ $product->name }}"></a>
                  <div class="onyx-product-content">
                    <div class="onyx-product-category">{{ $product->category?->name ?: 'Research compound' }}</div>
                    <a class="onyx-product-title" href="{{ route('frontend.product-details', $product->slug) }}">{{ $product->name }}</a>
                    <div class="onyx-product-summary">
                      <div class="onyx-product-rating"><span aria-label="Customer reviews"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i></span><small>{{ $product->reviews_count > 0 ? $product->reviews_count.' reviews' : 'New arrival' }}</small></div>
                      <div class="onyx-product-details"><span><strong>&pound;{{ number_format((float) ($product->sale_price ?: $product->price), 2) }}</strong>@if($product->sale_price)<del>&pound;{{ number_format((float) $product->price, 2) }}</del>@endif</span>@if($product->test_report_image)<button class="onyx-report-btn test-report-icon" type="button" data-test-report="{{ url($product->test_report_image) }}" data-test-report-title="{{ $product->name }} test report" aria-label="View {{ $product->name }} test report"><i class="fa-solid fa-file-shield"></i></button>@endif</div>
                    </div>
                    @if($product->stock > 0)<button class="onyx-add-cart" type="button" data-cart-add="{{ $product->id }}"><span>Add to cart</span><i class="fa-solid fa-cart-shopping"></i></button>@else<button class="onyx-add-cart notify-stock-btn" type="button" data-stock-notify="{{ $product->id }}" data-product-name="{{ $product->name }}"><span>Notify me</span><i class="fa-regular fa-bell"></i></button>@endif
                  </div>
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

        {{-- <section class="bundle-banner reveal-on-scroll" aria-label="Bundle offer">
          <div></div>
          <div>
            <p class="eyebrow">Bundle &amp; save</p>
            <h2>Buy More. Save More.</h2>
            <p>Stack your results with premium bundles and exclusive savings.</p>
            <a class="btn btn-gold" href="{{ route('frontend.shop') }}">View bundles <i class="fa-solid fa-arrow-right"></i></a>
          </div>
        </section> --}}
      </div>
    </section>

    @include('frontend.inc.delivery-trusted')
@endsection
