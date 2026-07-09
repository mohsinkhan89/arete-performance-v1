@extends('frontend.layouts.master')

@section('metas')
@endsection

@section('css')
@endsection

@section('body')
    @php
      $query = $filters['q'] ?: 'all products';
    @endphp

    <section class="search-hero">
      <div class="container">
        <div class="search-hero-copy reveal-up">
          <p class="eyebrow">Search results</p>
          <h1>Results for <span>"{{ $query }}"</span></h1>
          <p>Showing {{ $products->firstItem() ?? 0 }}-{{ $products->lastItem() ?? 0 }} of {{ $products->total() }} results</p>
          <form class="search-hero-form" method="GET" action="{{ route('frontend.search') }}">
            <label class="visually-hidden" for="searchQuery">Search query</label>
            <i class="fa-solid fa-magnifying-glass"></i>
            <input id="searchQuery" name="q" type="search" value="{{ $filters['q'] }}" aria-label="Search query" placeholder="Search products or categories">
            <button type="submit" aria-label="Search"><i class="fa-solid fa-arrow-right"></i></button>
          </form>
        </div>
      </div>
    </section>

    <section class="search-results-page section-space">
      <div class="container">
        <nav class="search-breadcrumb" aria-label="Breadcrumb"><a href="{{ route('frontend.index') }}">Home</a><i class="fa-solid fa-chevron-right"></i><span>Search</span></nav>
        <div class="shop-layout search-layout">
          <aside class="shop-sidebar search-sidebar reveal-on-scroll" aria-label="Search filters">
            <form method="GET" action="{{ route('frontend.search') }}">
              <input type="hidden" name="q" value="{{ $filters['q'] }}">

              <div class="filter-block collapsible-filter">
                <h2>Categories <i class="fa-solid fa-minus"></i></h2>
                <a class="{{ empty($filters['category']) ? 'active' : '' }}" href="{{ route('frontend.search', array_filter(['q' => $filters['q'] ?? null])) }}"><span>All Products</span><strong>({{ $products->total() }})</strong></a>
                @foreach ($categories as $category)
                  <a class="{{ ($filters['category'] ?? '') === $category->slug ? 'active' : '' }}" href="{{ route('frontend.search', array_filter(['q' => $filters['q'] ?? null, 'category' => $category->slug])) }}"><span>{{ $category->name }}</span><strong>({{ $category->products_count }})</strong></a>
                @endforeach
              </div>

              <div class="filter-block collapsible-filter">
                <h2>Price Range <i class="fa-solid fa-minus"></i></h2>
                <div class="price-range"><span></span></div>
                <div class="price-values"><strong>£0</strong><strong>£400</strong></div>
                <div class="filter-fields">
                  <input type="number" name="min_price" min="0" placeholder="Min" value="{{ $filters['min_price'] }}">
                  <input type="number" name="max_price" min="0" placeholder="Max" value="{{ $filters['max_price'] }}">
                </div>
              </div>

              <button class="clear-filters light-clear" type="submit">Apply filters <i class="fa-solid fa-filter"></i></button>
              <a class="clear-filters filter-reset" href="{{ route('frontend.search') }}">Clear filters <i class="fa-solid fa-rotate-right"></i></a>
            </form>
          </aside>

          <div class="shop-products">
            <div class="shop-toolbar reveal-on-scroll">
              <p>Showing {{ $products->firstItem() ?? 0 }}-{{ $products->lastItem() ?? 0 }} of {{ $products->total() }} results</p>
              <form method="GET" action="{{ route('frontend.search') }}" class="sort-form">
                @foreach (['q', 'category', 'min_price', 'max_price'] as $filterKey)
                  @if (! empty($filters[$filterKey]))
                    <input type="hidden" name="{{ $filterKey }}" value="{{ $filters[$filterKey] }}">
                  @endif
                @endforeach
                <label class="visually-hidden" for="searchSort">Sort products</label>
                <select id="searchSort" name="sort" onchange="this.form.submit()">
                  <option value="latest" @selected(($filters['sort'] ?? 'latest') === 'latest')>Sort by: Latest</option>
                  <option value="name" @selected(($filters['sort'] ?? '') === 'name')>Sort by: Name</option>
                  <option value="price_asc" @selected(($filters['sort'] ?? '') === 'price_asc')>Price: Low to high</option>
                  <option value="price_desc" @selected(($filters['sort'] ?? '') === 'price_desc')>Price: High to low</option>
                </select>
              </form>
            </div>

            <div class="shop-grid reveal-group">
              @forelse ($products as $product)
                <article class="shop-product-card product-card {{ $product->stock <= 0 ? 'is-out-of-stock' : '' }}" data-product-id="{{ $product->id }}" data-product-name="{{ $product->name }}" data-product-url="{{ route('frontend.product-details', $product->slug) }}">
                  @if ($product->stock <= 0)<span class="tag stock-tag">Out of Stock</span>@elseif ($product->is_featured)<span class="tag">Popular</span>@endif
                  <img src="{{ url($product->image ?: 'frontend/assets/images/product-bottle.png') }}" alt="{{ $product->name }}">
                  <small>{{ $product->category?->name }}</small>
                  <h3>{{ $product->name }}</h3>
                  <div class="rating">★★★★★ <span>({{ $product->stock }})</span></div>
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
              @empty
                <p>No products found. Try another product or category name.</p>
              @endforelse
            </div>

            @if ($products->hasPages())
              <div class="shop-pagination" aria-label="Search pagination">
                {{ $products->onEachSide(1)->links('pagination::bootstrap-5') }}
              </div>
            @endif
          </div>
        </div>
      </div>
    </section>

    @include('frontend.inc.delivery-trusted')
@endsection

@section('js')
@endsection
