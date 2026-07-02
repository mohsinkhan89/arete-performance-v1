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
            <div><i class="fa-solid fa-shield-halved"></i><span>Premium<br>quality</span></div>
            <div><i class="fa-solid fa-flask-vial"></i><span>Lab<br>tested</span></div>
            <div><i class="fa-solid fa-truck-fast"></i><span>Discreet<br>shipping</span></div>
            <div><i class="fa-solid fa-headset"></i><span>24/7<br>support</span></div>
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
                <input type="hidden" name="sort" value="{{ $filters['sort'] }}">
              @endif

              <div class="filter-block">
                <h2>Categories</h2>
                <a class="{{ empty($filters['category']) ? 'active' : '' }}" href="{{ route('frontend.shop', array_filter(['q' => $filters['q'] ?? null, 'sort' => $filters['sort'] ?? null])) }}"><span>All Products</span><strong>({{ $products->total() }})</strong></a>
                @foreach ($categories as $category)
                  <a class="{{ ($filters['category'] ?? '') === $category->slug ? 'active' : '' }}" href="{{ route('frontend.shop', array_filter(['category' => $category->slug, 'q' => $filters['q'] ?? null, 'sort' => $filters['sort'] ?? null])) }}"><span>{{ $category->name }}</span><strong>({{ $category->products_count }})</strong></a>
                @endforeach
              </div>

              <div class="filter-block">
                <h2>Price Range</h2>
                <div class="price-range"><span></span></div>
                <div class="price-values"><strong>£0</strong><strong>£400</strong></div>
                <div class="filter-fields">
                  <input type="number" name="min_price" min="0" placeholder="Min" value="{{ $filters['min_price'] }}">
                  <input type="number" name="max_price" min="0" placeholder="Max" value="{{ $filters['max_price'] }}">
                </div>
              </div>

              <div class="filter-block">
                <h2>Lab Tested</h2>
                <label><input type="checkbox" checked> Yes <strong>({{ $products->total() }})</strong></label>
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
                <article class="shop-product-card product-card" id="{{ $product->category?->slug }}" data-product-id="{{ $product->id }}">
                  @if ($product->is_featured)<span class="tag">Popular</span>@endif
                  <img src="{{ url($product->image ?: 'frontend/assets/images/product-bottle.png') }}" alt="{{ $product->name }}">
                  <small>{{ $product->category?->name }}</small>
                  <h3>{{ $product->name }}</h3>
                  <div class="rating">★★★★★ <span>({{ $product->stock }})</span></div>
                  <div class="product-card-qty" aria-label="{{ $product->name }} quantity"><button type="button" data-card-qty-dec>-</button><span data-card-qty>1</span><button type="button" data-card-qty-inc>+</button></div>
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
