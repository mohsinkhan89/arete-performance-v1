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
            <div class="filter-block">
              <h2>Categories</h2>
              <a class="active" href="{{ route('frontend.shop') }}"><span>All Products</span><strong>({{ $products->total() }})</strong></a>
              @foreach ($categories as $category)
                <a href="#{{ $category->slug }}"><span>{{ $category->name }}</span><strong>({{ $category->products_count }})</strong></a>
              @endforeach
            </div>
            <div class="filter-block">
              <h2>Price Range</h2>
              <div class="price-range"><span></span></div>
              <div class="price-values"><strong>£0</strong><strong>£400</strong></div>
            </div>
            <div class="filter-block">
              <h2>Lab Tested</h2>
              <label><input type="checkbox" checked> Yes <strong>({{ $products->total() }})</strong></label>
            </div>
            <button class="clear-filters" type="button">Clear filters <i class="fa-solid fa-rotate-right"></i></button>
          </aside>

          <div class="shop-products">
            <div class="shop-toolbar reveal-on-scroll">
              <p>Showing {{ $products->firstItem() ?? 0 }}-{{ $products->lastItem() ?? 0 }} of {{ $products->total() }} results</p>
              <button type="button">Sort by: Latest <i class="fa-solid fa-chevron-down"></i></button>
            </div>
            <div class="shop-grid reveal-group">
              @forelse ($products as $product)
                <article class="shop-product-card product-card" id="{{ $product->category?->slug }}" data-product-id="{{ $product->slug }}">
                  @if ($product->is_featured)<span class="tag">Popular</span>@endif
                  <img src="{{ url($product->image ?: 'frontend/assets/images/product-bottle.png') }}" alt="{{ $product->name }}">
                  <small>{{ $product->category?->name }}</small>
                  <h3>{{ $product->name }}</h3>
                  <div class="rating">★★★★★ <span>({{ $product->stock }})</span></div>
                  <div><strong>£{{ number_format((float) ($product->sale_price ?: $product->price), 2) }}</strong><button aria-label="Add {{ $product->name }} to cart"><i class="fa-solid fa-cart-plus"></i></button></div>
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
