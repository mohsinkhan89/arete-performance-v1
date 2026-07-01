@extends('frontend.layouts.master')

@section('metas')
@endsection

@section('css')
@endsection

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
              <a class="active" href="shop.html"><span>All Products</span><strong>(28)</strong></a>
              <a href="shop.html#orals"><span>Orals</span><strong>(6)</strong></a>
              <a href="shop.html#fat-burners"><span>Fat Burners</span><strong>(5)</strong></a>
              <a href="shop.html#post-cycle-therapy"><span>Post Cycle Therapy</span><strong>(4)</strong></a>
              <a href="shop.html#hormones"><span>Hormones</span><strong>(3)</strong></a>
              <a href="shop.html#peptides"><span>Peptides</span><strong>(4)</strong></a>
              <a href="shop.html#sexual-health"><span>Sexual Health</span><strong>(4)</strong></a>
              <a href="shop.html#syringes-needles"><span>Syringes &amp; Needles</span><strong>(2)</strong></a>
            </div>
            <div class="filter-block">
              <h2>Price Range</h2>
              <div class="price-range"><span></span></div>
              <div class="price-values"><strong>$0</strong><strong>$200</strong></div>
            </div>
            <div class="filter-block">
              <h2>Brand</h2>
              <label><input type="checkbox"> Arete Performance <strong>(24)</strong></label>
              <label><input type="checkbox"> Premium Labs <strong>(2)</strong></label>
              <label><input type="checkbox"> Elite Formulations <strong>(2)</strong></label>
            </div>
            <div class="filter-block">
              <h2>Form</h2>
              <label><input type="checkbox"> Capsule <strong>(12)</strong></label>
              <label><input type="checkbox"> Tablet <strong>(6)</strong></label>
              <label><input type="checkbox"> Liquid <strong>(4)</strong></label>
              <label><input type="checkbox"> Powder <strong>(4)</strong></label>
              <label><input type="checkbox"> Injection <strong>(2)</strong></label>
            </div>
            <div class="filter-block">
              <h2>Lab Tested</h2>
              <label><input type="checkbox"> Yes <strong>(28)</strong></label>
            </div>
            <button class="clear-filters" type="button">Clear filters <i class="fa-solid fa-rotate-right"></i></button>
          </aside>

          <div class="shop-products">
            <div class="shop-toolbar reveal-on-scroll">
              <p>Showing 1-12 of 28 results</p>
              <button type="button">Sort by: Best Selling <i class="fa-solid fa-chevron-down"></i></button>
            </div>
            <div class="shop-grid reveal-group">
              <article class="shop-product-card product-card" data-product-id="cardarine"><span class="tag">Best seller</span><img src="{{ url('frontend/assets/images/product-bottle.png') }}" alt="Cardarine"><small>Fat Burners</small><h3>Cardarine (GW-501516)</h3><div class="rating">&#9733;&#9733;&#9733;&#9733;&#9733; <span>(128)</span></div><div><strong>$69.99</strong><button aria-label="Add Cardarine to cart"><i class="fa-solid fa-cart-plus"></i></button></div></article>
              <article class="shop-product-card product-card bottle-gold" data-product-id="anavar-50"><span class="tag tag-green">New</span><img src="{{ url('frontend/assets/images/product-bottle.png') }}" alt="Anavar 50"><small>Orals</small><h3>Anavar 50 (Oxandrolone)</h3><div class="rating">&#9733;&#9733;&#9733;&#9733;&#9733; <span>(94)</span></div><div><strong>$59.99</strong><button aria-label="Add Anavar 50 to cart"><i class="fa-solid fa-cart-plus"></i></button></div></article>
              <article class="shop-product-card product-card" data-product-id="pct-complete-stack"><img src="{{ url('frontend/assets/images/category-boxes.svg') }}" alt="PCT complete stack"><small>Post Cycle Therapy</small><h3>PCT Complete Stack</h3><div class="rating">&#9733;&#9733;&#9733;&#9733;&#9733; <span>(72)</span></div><div><strong>$89.99</strong><button aria-label="Add PCT Complete Stack to cart"><i class="fa-solid fa-cart-plus"></i></button></div></article>
              <article class="shop-product-card product-card" data-product-id="testosterone-enanthate"><img src="{{ url('frontend/assets/images/product-bottle.png') }}" alt="Testosterone Enanthate"><small>Hormones</small><h3>Testosterone Enanthate</h3><div class="rating">&#9733;&#9733;&#9733;&#9733;&#9733; <span>(68)</span></div><div><strong>$49.99</strong><button aria-label="Add Testosterone Enanthate to cart"><i class="fa-solid fa-cart-plus"></i></button></div></article>
              <article class="shop-product-card product-card bottle-silver" data-product-id="bpc-157"><span class="tag tag-red">Save 15%</span><img src="{{ url('frontend/assets/images/categories-imgs/peptides.png') }}" alt="BPC-157"><small>Peptides</small><h3>BPC-157 5mg</h3><div class="rating">&#9733;&#9733;&#9733;&#9733;&#9734; <span>(60)</span></div><div><strong>$59.99 <del>$69.99</del></strong><button aria-label="Add BPC-157 to cart"><i class="fa-solid fa-cart-plus"></i></button></div></article>
              <article class="shop-product-card product-card" data-product-id="male-enhancement-stack"><img src="{{ url('frontend/assets/images/categories-imgs/sexual-health.png') }}" alt="Male enhancement stack"><small>Sexual Health</small><h3>Male Enhancement Stack</h3><div class="rating">&#9733;&#9733;&#9733;&#9733;&#9733; <span>(51)</span></div><div><strong>$79.99</strong><button aria-label="Add Male Enhancement Stack to cart"><i class="fa-solid fa-cart-plus"></i></button></div></article>
              <article class="shop-product-card product-card bottle-gold" data-product-id="clenbuterol"><img src="{{ url('frontend/assets/images/categories-imgs/fat-burrners.png') }}" alt="Clenbuterol"><small>Fat Burners</small><h3>Clenbuterol 40mcg</h3><div class="rating">&#9733;&#9733;&#9733;&#9733;&#9733; <span>(67)</span></div><div><strong>$49.99</strong><button aria-label="Add Clenbuterol to cart"><i class="fa-solid fa-cart-plus"></i></button></div></article>
              <article class="shop-product-card product-card" data-product-id="insulin-syringes"><img src="{{ url('frontend/assets/images/categories-imgs/injection.png') }}" alt="Insulin syringes"><small>Syringes &amp; Needles</small><h3>Insulin Syringes 1ml</h3><div class="rating">&#9733;&#9733;&#9733;&#9733;&#9734; <span>(33)</span></div><div><strong>$9.99</strong><button aria-label="Add Insulin Syringes to cart"><i class="fa-solid fa-cart-plus"></i></button></div></article>
              <article class="shop-product-card product-card" data-product-id="winstrol"><span class="tag">Lab tested</span><img src="{{ url('frontend/assets/images/categories-imgs/orals.png') }}" alt="Winstrol"><small>Orals</small><h3>Winstrol 10mg</h3><div class="rating">&#9733;&#9733;&#9733;&#9733;&#9734; <span>(46)</span></div><div><strong>$54.99</strong><button aria-label="Add Winstrol to cart"><i class="fa-solid fa-cart-plus"></i></button></div></article>
              <article class="shop-product-card product-card bottle-silver" data-product-id="cjc-1295"><img src="{{ url('frontend/assets/images/categories-imgs/peptides.png') }}" alt="CJC-1295"><small>Peptides</small><h3>CJC-1295 2mg</h3><div class="rating">&#9733;&#9733;&#9733;&#9733;&#9734; <span>(37)</span></div><div><strong>$74.99</strong><button aria-label="Add CJC-1295 to cart"><i class="fa-solid fa-cart-plus"></i></button></div></article>
              <article class="shop-product-card product-card bottle-gold" data-product-id="trenbolone-acetate"><img src="{{ url('frontend/assets/images/product-bottle.png') }}" alt="Trenbolone Acetate"><small>Hormones</small><h3>Trenbolone Acetate</h3><div class="rating">&#9733;&#9733;&#9733;&#9733;&#9733; <span>(42)</span></div><div><strong>$64.99</strong><button aria-label="Add Trenbolone Acetate to cart"><i class="fa-solid fa-cart-plus"></i></button></div></article>
              <article class="shop-product-card product-card bottle-silver" data-product-id="nolvadex-20"><img src="{{ url('frontend/assets/images/categories-imgs/post-cycle-therapy.png') }}" alt="Nolvadex 20"><small>Post Cycle Therapy</small><h3>Nolvadex 20 (Tamoxifen)</h3><div class="rating">&#9733;&#9733;&#9733;&#9733;&#9733; <span>(53)</span></div><div><strong>$49.99</strong><button aria-label="Add Nolvadex 20 to cart"><i class="fa-solid fa-cart-plus"></i></button></div></article>
            </div>
          </div>
        </div>

        <section class="bundle-banner reveal-on-scroll" aria-label="Bundle offer">
          <div>
          </div>
          <div>
            <p class="eyebrow">Bundle &amp; save</p>
            <h2>Buy More. Save More.</h2>
            <p>Stack your results with premium bundles and exclusive savings.</p>
            <a class="btn btn-gold" href="shop.html">View bundles <i class="fa-solid fa-arrow-right"></i></a>
          </div>
        </section>
      </div>
    </section>

    @include('frontend.inc.delivery-trusted')

@endsection

@section('js')
@endsection