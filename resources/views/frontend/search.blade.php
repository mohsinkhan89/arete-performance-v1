@extends('frontend.layouts.master')

@section('metas')
@endsection

@section('css')
@endsection

@section('body')

    <section class="search-hero">
      <div class="container">
        <div class="search-hero-copy reveal-up">
          <p class="eyebrow">Search results</p>
          <h1>Results for <span>&ldquo;Whey&rdquo;</span></h1>
          <p>Showing 1-12 of 24 results for "Whey Protein"</p>
          <form class="search-hero-form">
            <label class="visually-hidden" for="searchQuery">Search query</label>
            <i class="fa-solid fa-magnifying-glass"></i>
            <input id="searchQuery" type="search" value="Whey" aria-label="Search query">
            <button type="button" aria-label="Clear search"><i class="fa-solid fa-xmark"></i></button>
          </form>
        </div>
      </div>
    </section>

    <section class="search-results-page section-space">
      <div class="container">
        <nav class="search-breadcrumb" aria-label="Breadcrumb"><a href="{{ route('frontend.index') }}">Home</a><i class="fa-solid fa-chevron-right"></i><span>Search</span></nav>
        <div class="shop-layout search-layout">
          <aside class="shop-sidebar search-sidebar reveal-on-scroll" aria-label="Search filters">
            <div class="filter-block collapsible-filter">
              <h2>Categories <i class="fa-solid fa-minus"></i></h2>
              <label><input type="checkbox" checked> All Products <strong>(24)</strong></label>
              <label><input type="checkbox"> Protein <strong>(12)</strong></label>
              <label><input type="checkbox"> Mass Gainers <strong>(4)</strong></label>
              <label><input type="checkbox"> Pre-Workout <strong>(3)</strong></label>
              <label><input type="checkbox"> Recovery <strong>(2)</strong></label>
              <label><input type="checkbox"> Vitamins <strong>(2)</strong></label>
              <label><input type="checkbox"> Accessories <strong>(1)</strong></label>
            </div>
            <div class="filter-block collapsible-filter">
              <h2>Price Range <i class="fa-solid fa-minus"></i></h2>
              <div class="price-range"><span></span></div>
              <div class="price-values"><strong>$0</strong><strong>$200</strong></div>
            </div>
            <div class="filter-block collapsible-filter">
              <h2>Brand <i class="fa-solid fa-minus"></i></h2>
              <label><input type="checkbox" checked> Arete Performance <strong>(18)</strong></label>
              <label><input type="checkbox"> Premium Labs <strong>(4)</strong></label>
              <label><input type="checkbox"> Elite Formulations <strong>(2)</strong></label>
            </div>
            <div class="filter-block collapsible-filter">
              <h2>Form <i class="fa-solid fa-minus"></i></h2>
              <label><input type="checkbox"> Powder <strong>(16)</strong></label>
              <label><input type="checkbox"> Capsule <strong>(4)</strong></label>
              <label><input type="checkbox"> Tablet <strong>(2)</strong></label>
              <label><input type="checkbox"> Liquid <strong>(2)</strong></label>
            </div>
            <div class="filter-block collapsible-filter">
              <h2>Flavor <i class="fa-solid fa-minus"></i></h2>
              <label><input type="checkbox"> Chocolate <strong>(8)</strong></label>
              <label><input type="checkbox"> Vanilla <strong>(6)</strong></label>
              <label><input type="checkbox"> Strawberry <strong>(4)</strong></label>
              <label><input type="checkbox"> Unflavored <strong>(2)</strong></label>
            </div>
            <button class="clear-filters light-clear" type="button">Clear filters <i class="fa-solid fa-rotate-right"></i></button>
          </aside>

          <div class="shop-products">
            <div class="shop-toolbar reveal-on-scroll">
              <p>Showing 1-12 of 24 results</p>
              <button type="button">Sort by: Relevance <i class="fa-solid fa-chevron-down"></i></button>
            </div>
            <div class="shop-grid reveal-group">
              <article class="shop-product-card product-card" data-product-id="whey-protein-isolate"><span class="tag">Best seller</span><img src="{{ url('frontend/assets/images/product-bottle.png') }}" alt="Whey Protein Isolate"><small>Protein</small><h3>Whey Protein Isolate</h3><div class="rating">&#9733;&#9733;&#9733;&#9733;&#9733; <span>(128)</span></div><div><strong>$59.99</strong><button aria-label="Add Whey Protein Isolate to cart"><i class="fa-solid fa-cart-plus"></i></button></div></article>
              <article class="shop-product-card product-card bottle-silver" data-product-id="whey-protein-concentrate"><span class="tag tag-green">New</span><img src="{{ url('frontend/assets/images/categories-imgs/peptides.png') }}" alt="Whey Protein Concentrate"><small>Protein</small><h3>Whey Protein Concentrate</h3><div class="rating">&#9733;&#9733;&#9733;&#9733;&#9733; <span>(84)</span></div><div><strong>$49.99</strong><button aria-label="Add Whey Protein Concentrate to cart"><i class="fa-solid fa-cart-plus"></i></button></div></article>
              <article class="shop-product-card product-card" data-product-id="whey-hydrolysate"><img src="{{ url('frontend/assets/images/product-bottle.png') }}" alt="Whey Hydrolysate"><small>Protein</small><h3>Whey Hydrolysate</h3><div class="rating">&#9733;&#9733;&#9733;&#9733;&#9734; <span>(72)</span></div><div><strong>$64.99</strong><button aria-label="Add Whey Hydrolysate to cart"><i class="fa-solid fa-cart-plus"></i></button></div></article>
              <article class="shop-product-card product-card bottle-gold" data-product-id="whey-protein-blend"><img src="{{ url('frontend/assets/images/categories-imgs/orals') }}.png" alt="Whey Protein Blend"><small>Protein</small><h3>Whey Protein Blend</h3><div class="rating">&#9733;&#9733;&#9733;&#9733;&#9733; <span>(68)</span></div><div><strong>$54.99</strong><button aria-label="Add Whey Protein Blend to cart"><i class="fa-solid fa-cart-plus"></i></button></div></article>
              <article class="shop-product-card product-card" data-product-id="whey-isolate-unflavored"><img src="{{ url('frontend/assets/images/product-bottle.png') }}" alt="Whey Isolate Unflavored"><small>Protein</small><h3>Whey Isolate (Unflavored)</h3><div class="rating">&#9733;&#9733;&#9733;&#9733;&#9733; <span>(56)</span></div><div><strong>$59.99</strong><button aria-label="Add Whey Isolate Unflavored to cart"><i class="fa-solid fa-cart-plus"></i></button></div></article>
              <article class="shop-product-card product-card" data-product-id="whey-protein-shaker"><img src="{{ url('frontend/') }}assets/images/category-boxes.svg" alt="Whey Protein and Shaker"><small>Protein</small><h3>Whey Protein + Shaker</h3><div class="rating">&#9733;&#9733;&#9733;&#9733;&#9733; <span>(51)</span></div><div><strong>$69.99</strong><button aria-label="Add Whey Protein and Shaker to cart"><i class="fa-solid fa-cart-plus"></i></button></div></article>
              <article class="shop-product-card product-card" data-product-id="whey-isolate-chocolate"><img src="{{ url('frontend/assets/images/product-bottle.png') }}" alt="Whey Isolate Chocolate"><small>Protein</small><h3>Whey Isolate (Chocolate)</h3><div class="rating">&#9733;&#9733;&#9733;&#9733;&#9734; <span>(47)</span></div><div><strong>$59.99</strong><button aria-label="Add Whey Isolate Chocolate to cart"><i class="fa-solid fa-cart-plus"></i></button></div></article>
              <article class="shop-product-card product-card" data-product-id="whey-concentrate"><img src="{{ url('frontend/') }}assets/images/categories-imgs/sexual-health.png" alt="Whey Concentrate"><small>Protein</small><h3>Whey Concentrate</h3><div class="rating">&#9733;&#9733;&#9733;&#9733;&#9734; <span>(42)</span></div><div><strong>$44.99</strong><button aria-label="Add Whey Concentrate to cart"><i class="fa-solid fa-cart-plus"></i></button></div></article>
              <article class="shop-product-card product-card" data-product-id="whey-isolate-vanilla"><img src="{{ url('frontend/assets/images/product-bottle.png') }}" alt="Whey Isolate Vanilla"><small>Protein</small><h3>Whey Isolate (Vanilla)</h3><div class="rating">&#9733;&#9733;&#9733;&#9733;&#9734; <span>(38)</span></div><div><strong>$59.99</strong><button aria-label="Add Whey Isolate Vanilla to cart"><i class="fa-solid fa-cart-plus"></i></button></div></article>
              <article class="shop-product-card product-card" data-product-id="whey-mass-gainer"><img src="{{ url('frontend/assets/images/product-bottle.png') }}" alt="Whey Mass Gainer"><small>Protein</small><h3>Whey Mass Gainer</h3><div class="rating">&#9733;&#9733;&#9733;&#9733;&#9733; <span>(35)</span></div><div><strong>$69.99</strong><button aria-label="Add Whey Mass Gainer to cart"><i class="fa-solid fa-cart-plus"></i></button></div></article>
              <article class="shop-product-card product-card bottle-silver" data-product-id="whey-protein-strawberry"><img src="{{ url('frontend/assets/images/categories-imgs/orals') }}.png" alt="Whey Protein Strawberry"><small>Protein</small><h3>Whey Protein (Strawberry)</h3><div class="rating">&#9733;&#9733;&#9733;&#9733;&#9734; <span>(31)</span></div><div><strong>$54.99</strong><button aria-label="Add Whey Protein Strawberry to cart"><i class="fa-solid fa-cart-plus"></i></button></div></article>
              <article class="shop-product-card product-card" data-product-id="whey-blend-cookies"><img src="{{ url('frontend/assets/images/product-bottle.png') }}" alt="Whey Blend Cookies and Cream"><small>Protein</small><h3>Whey Blend (Cookies &amp; Cream)</h3><div class="rating">&#9733;&#9733;&#9733;&#9733;&#9733; <span>(28)</span></div><div><strong>$54.99</strong><button aria-label="Add Whey Blend Cookies and Cream to cart"><i class="fa-solid fa-cart-plus"></i></button></div></article>
            </div>

            <nav class="search-pagination" aria-label="Search results pages">
              <a href="{{ route('frontend.search') }}" aria-label="Previous page"><i class="fa-solid fa-arrow-left"></i></a>
              <a class="active" href="{{ route('frontend.search') }}">1</a>
              <a href="{{ route('frontend.search', ['page' => 2]) }}">2</a>
              <a href="{{ route('frontend.search', ['page' => 3]) }}">3</a>
              <a href="{{ route('frontend.search', ['page' => 2]) }}" aria-label="Next page"><i class="fa-solid fa-arrow-right"></i></a>
            </nav>
          </div>
        </div>
      </div>
    </section>

    <section class="shop-trust">
      <div class="container">
        <div class="shop-trust-grid">
          <div><i class="fa-solid fa-shield-halved"></i><strong>Premium Quality</strong><span>Top-tier products you can trust.</span></div>
          <div><i class="fa-solid fa-flask-vial"></i><strong>Lab Tested</strong><span>Every batch is lab verified.</span></div>
          <div><i class="fa-solid fa-truck-fast"></i><strong>Discreet Shipping</strong><span>Private and secure worldwide delivery.</span></div>
          <div><i class="fa-solid fa-headset"></i><strong>24/7 Support</strong><span>We're here to help you succeed.</span></div>
        </div>
      </div>
    </section>

    <section class="shop-newsletter">
      <div class="container">
        <div class="shop-newsletter-inner">
          <div>
            <p class="eyebrow">Stay in the loop</p>
            <h2>Exclusive deals. Expert tips. Straight to your inbox.</h2>
            <p>Join the Arete Performance community.</p>
          </div>
          <form class="newsletter">
            <label class="visually-hidden" for="searchNewsletter">Email</label>
            <input id="searchNewsletter" type="email" placeholder="Enter your email" required>
            <button type="submit">Subscribe</button>
          </form>
        </div>
      </div>
    </section>

    @include('frontend.inc.delivery-trusted')

@endsection

@section('js')
@endsection
