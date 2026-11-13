@extends('frontend.layouts.master')

@section('metas')
@endsection

@section('body_class', 'shop-page search-page')

@section('css')
<style>
.search-hero h1{font-family:'Barlow Condensed',sans-serif;font-size:clamp(42px,6vw,68px);font-weight:800;line-height:.95}.search-hero h1 span{display:block}.search-hero-form{display:flex!important;align-items:center;padding:0 10px 0 6px}.search-icon-button{display:grid!important;place-items:center!important;flex:0 0 42px;width:42px!important;height:42px!important;padding:0!important;border:0!important;background:transparent!important;color:#171717!important;font-size:16px;cursor:pointer}.search-icon-button:hover{color:#b87900!important}.search-hero-form input{flex:1;padding:0 8px}.search-empty-state{grid-column:1/-1;display:grid;place-items:center;text-align:center;min-height:350px;padding:45px 25px;background:#fff;border:1px solid #e5e8ec;border-radius:14px;box-shadow:0 8px 25px rgba(18,25,38,.05)}.search-empty-icon{width:72px;height:72px;border-radius:50%;display:grid;place-items:center;background:#fff1cf;color:#9b6700;font-size:25px;margin:0 auto 16px}.search-empty-state h2{font-size:25px;margin:0 0 8px}.search-empty-state p{max-width:460px;margin:0 auto;color:#737b87;font-size:12px;line-height:1.6}.search-empty-actions{display:flex;justify-content:center;gap:9px;margin-top:18px;flex-wrap:wrap}.search-empty-actions a{padding:10px 15px;border-radius:9px;text-decoration:none;font-size:11px;font-weight:900}.search-empty-actions .primary{background:#e9a611;color:#111}.search-empty-actions .secondary{border:1px solid #dfe4e9;color:#333}.category-empty{padding:10px 0;color:#8a919c;font-size:11px;line-height:1.5}.search-sidebar .price-range-control input[type=range]{padding:0;border:0}.search-sidebar .filter-fields input{margin:0;width:100%;height:42px}.price-input-wrap{position:relative;display:block!important;margin:0!important;padding:0!important}.price-input-wrap span{position:absolute;left:10px;top:12px;color:#8b929d;font-size:12px;font-weight:800}.price-input-wrap input{padding-left:25px!important}@media(max-width:560px){.search-empty-state{min-height:290px}.search-empty-state h2{font-size:21px}}
</style>
@endsection

@section('body')
    @php
      $query = $filters['q'] ?: 'all products';
    @endphp

    <section class="shop-hero" aria-labelledby="searchHeroTitle">
      <div class="shop-hero-grid" aria-hidden="true"></div>
      <div class="shop-hero-backdrop-text" aria-hidden="true"><span>SEARCH</span></div>
      <div class="container shop-hero-inner">
        <div class="shop-hero-copy reveal-up">
          <p class="shop-hero-eyebrow"><span></span> The complete Majix collection</p>
          <h1 id="searchHeroTitle" class="shop-title-word" aria-label="Search"><span>S</span><span>E</span><span>A</span><span>R</span><span>C</span><span>H</span></h1>
          <p>Search premium research compounds, clearly organised in one collection.</p>
          @if($products->total() > 0)
            <form class="search-hero-product-form" method="GET" action="{{ route('frontend.search') }}">
              <label class="visually-hidden" for="heroSearchQuery">Search products</label>
              <i class="fa-solid fa-magnifying-glass"></i>
              <input id="heroSearchQuery" name="q" type="search" value="{{ $filters['q'] }}" placeholder="Search products or categories">
              <button type="submit">Search <i class="fa-solid fa-arrow-right"></i></button>
            </form>
          @endif
        </div>
        <div class="shop-hero-benefits" aria-label="Majix quality standards">
          <div><i class="fa-solid fa-flask-vial"></i><span><strong>Research grade</strong><small>Compounds</small></span></div>
          <div><i class="fa-solid fa-shield-halved"></i><span><strong>Verified purity</strong><small>Quality assured</small></span></div>
          <div><i class="fa-solid fa-layer-group"></i><span><strong>Consistent</strong><small>Quality</small></span></div>
        </div>
      </div>
    </section>

    <section class="search-results-page section-space">
      <div class="container">
        <header class="search-catalog-intro reveal-on-scroll">
          <div><p><span></span> Search catalogue</p><h2>Results for <em>"{{ $query }}"</em></h2><small>Use the filters to quickly narrow down the Majix collection.</small></div>
          <div class="search-result-count"><strong>{{ $products->total() }}</strong><span>Products found</span></div>
        </header>
        <div class="shop-layout search-layout">
          <aside class="shop-sidebar search-sidebar reveal-on-scroll" aria-label="Search filters">
            <form method="GET" action="{{ route('frontend.search') }}">
              <input type="hidden" name="q" value="{{ $filters['q'] }}">

              <div class="filter-rail-head">
                <span><i class="fa-solid fa-sliders"></i></span>
                <div><small>Refine results</small><strong>Search filters</strong></div>
              </div>

              <div class="filter-block collapsible-filter">
                <h2>Categories</h2>
                <a class="{{ empty($filters['category']) ? 'active' : '' }}" href="{{ route('frontend.search', array_filter(['q' => $filters['q'] ?? null])) }}"><span>All Products</span><strong>({{ $products->total() }})</strong></a>
                @forelse ($categories as $category)
                  <a class="{{ ($filters['category'] ?? '') === $category->slug ? 'active' : '' }}" href="{{ route('frontend.search', array_filter(['q' => $filters['q'] ?? null, 'category' => $category->slug])) }}"><span>{{ $category->name }}</span><strong>({{ $category->products_count }})</strong></a>
                @empty
                  <div class="category-empty"><i class="fa-regular fa-folder-open"></i> No product categories are available.</div>
                @endforelse
              </div>

              <div class="filter-block collapsible-filter">
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
                  <label class="price-input-wrap"><span>£</span><input type="number" name="min_price" min="{{ $minBound }}" max="{{ $maxBound }}" placeholder="Min" value="{{ $filters['min_price'] }}" data-price-min-input aria-label="Minimum price"></label>
                  <label class="price-input-wrap"><span>£</span><input type="number" name="max_price" min="{{ $minBound }}" max="{{ $maxBound }}" placeholder="Max" value="{{ $filters['max_price'] }}" data-price-max-input aria-label="Maximum price"></label>
                </div>
              </div>

              <button class="clear-filters" type="submit">Apply filters <i class="fa-solid fa-filter"></i></button>
              <a class="clear-filters filter-reset" href="{{ route('frontend.search') }}">Clear filters <i class="fa-solid fa-rotate-right"></i></a>
            </form>
          </aside>

          <div class="shop-products">
            @if($products->total() > 0)
            <div class="shop-toolbar search-results-toolbar reveal-on-scroll">
              <div class="search-results-summary"><i class="fa-solid fa-magnifying-glass"></i><div class="shop-toolbar-main"><span>{{ $products->total() }} products found</span><p>@if($filters['q'])Matching “{{ $filters['q'] }}”@else Explore the complete Majix collection @endif</p></div></div>
              <div class="search-results-mark"><span></span> Majix verified collection</div>
            </div>
            @endif

            <div class="shop-grid reveal-group">
              @forelse ($products as $product)
                <article class="shop-product-card onyx-product-card product-card {{ $product->stock <= 0 ? 'is-out-of-stock' : '' }}" data-product-id="{{ $product->id }}" data-product-name="{{ $product->name }}" data-product-url="{{ route('frontend.product-details', $product->slug) }}">
                  <div class="onyx-product-badges"><span class="onyx-product-status {{ $product->stock <= 0 ? 'is-empty' : '' }}">{{ $product->stock <= 0 ? 'Out of stock' : 'In stock' }}</span>@if($product->is_bestseller || $product->is_featured)<span class="onyx-product-featured"><i class="fa-solid fa-bolt"></i> {{ $product->is_bestseller ? 'Bestseller' : 'Popular' }}</span>@endif</div>
                  <a class="onyx-product-image" href="{{ route('frontend.product-details', $product->slug) }}"><img src="{{ url($product->image ?: 'frontend/assets/images/product-bottle.png') }}" alt="{{ $product->name }}"></a>
                  <div class="onyx-product-content">
                    <div class="onyx-product-category">{{ $product->category?->name ?: 'Research compound' }}</div>
                    <a class="onyx-product-title" href="{{ route('frontend.product-details', $product->slug) }}">{{ $product->name }}</a>
                    <div class="onyx-product-summary"><div class="onyx-product-rating"><span><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i></span><small>{{ $product->reviews_count > 0 ? $product->reviews_count.' reviews' : 'New arrival' }}</small></div><div class="onyx-product-details"><span><strong>&pound;{{ number_format((float) ($product->sale_price ?: $product->price), 2) }}</strong>@if($product->sale_price)<del>&pound;{{ number_format((float) $product->price, 2) }}</del>@endif</span>@if($product->test_report_image)<button class="onyx-report-btn test-report-icon" type="button" data-test-report="{{ url($product->test_report_image) }}" data-test-report-title="{{ $product->name }} test report"><i class="fa-solid fa-file-shield"></i></button>@endif</div></div>
                    @if($product->stock > 0)<button class="onyx-add-cart" type="button" data-cart-add="{{ $product->id }}"><span>Add to cart</span><i class="fa-solid fa-cart-shopping"></i></button>@else<button class="onyx-add-cart notify-stock-btn" type="button" data-stock-notify="{{ $product->id }}" data-product-name="{{ $product->name }}"><span>Notify me</span><i class="fa-regular fa-bell"></i></button>@endif
                  </div>
                  @if ($product->stock <= 0)<span class="tag stock-tag">Out of Stock</span>@elseif ($product->is_featured)<span class="tag">Popular</span>@endif
                  <img src="{{ url($product->image ?: 'frontend/assets/images/product-bottle.png') }}" alt="{{ $product->name }}">
                  <small>{{ $product->category?->name }}</small>
                  <h3>{{ $product->name }}</h3>
                  <div class="rating">★★★★★ <span>({{ number_format($product->reviews_count) }} reviews)</span></div>
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
                <section class="search-empty-state">
                  <div>
                    <span class="search-empty-icon"><i class="fa-solid fa-magnifying-glass"></i></span>
                    <p class="search-empty-eyebrow">No exact match</p>
                    <h2>Let’s find the right compound.</h2>
                    <p>We could not find a product matching “{{ $query }}”. Try another keyword or explore a research category below.</p>
                    <form class="search-empty-form" method="GET" action="{{ route('frontend.search') }}"><input name="q" type="search" placeholder="Try another product name"><button type="submit"><i class="fa-solid fa-magnifying-glass"></i> Search again</button></form>
                    <div class="search-category-links">
                      @foreach($categories as $category)<a href="{{ route('frontend.search', ['category' => $category->slug]) }}">{{ $category->name }} <i class="fa-solid fa-arrow-right"></i></a>@endforeach
                    </div>
                    <div class="search-empty-actions"><a class="primary" href="{{ route('frontend.shop') }}">Browse full collection</a><a class="secondary" href="{{ route('frontend.search') }}">Reset search</a></div>
                  </div>
                </section>
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
