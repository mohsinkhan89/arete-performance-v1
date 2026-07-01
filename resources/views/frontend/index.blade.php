@extends('frontend.layouts.master')

@section('metas')
@endsection

@section('css')
@endsection

@section('body')

    <section class="hero" id="home">
      <div class="container hero-content">
        <div class="row align-items-center min-vh-hero">
          <div class="col-lg-6 col-xl-5">
            <p class="eyebrow reveal-up">Premium performance solutions</p>
            <h1 class="reveal-up delay-1">Reach your <span>potential</span></h1>
            <p class="hero-copy reveal-up delay-2">Science-backed products designed to help you perform, recover and grow.</p>
            <div class="d-flex flex-wrap gap-3 mt-4 reveal-up delay-3">
              <a class="btn btn-gold" href="#products">Shop now <i class="fa-solid fa-arrow-right"></i></a>
              <a class="btn btn-outline-light-custom" href="index.html#about">Learn more</a>
            </div>
          </div>
          <!-- <div class="col-lg-6 col-xl-7 d-none d-lg-block">
            <div class="hero-product-stage reveal-up delay-2" aria-hidden="true">
              <img class="hero-shirt-logo" src="{{ url('frontend/assets/images/logo/logo-transperent.png') }}" alt="">
            </div>
          </div> -->
        </div>
        <div class="hero-features row g-0 reveal-up delay-4">
          <div class="col-6 col-lg-3"><div class="hero-feature"><i class="fa-solid fa-medal"></i><span>Premium<br>quality</span></div></div>
          <div class="col-6 col-lg-3"><div class="hero-feature"><i class="fa-solid fa-flask-vial"></i><span>Lab<br>tested</span></div></div>
          <div class="col-6 col-lg-3"><div class="hero-feature"><i class="fa-solid fa-shield-halved"></i><span>Discreet<br>shipping</span></div></div>
          <div class="col-6 col-lg-3"><div class="hero-feature border-0"><i class="fa-solid fa-headset"></i><span>24/7<br>support</span></div></div>
        </div>
      </div>
    </section>

    <section class="section-space categories" id="products">
      <div class="container">
        <div class="section-heading d-flex flex-wrap justify-content-between align-items-end gap-3 reveal-on-scroll">
          <div><p class="eyebrow">Browse categories</p><h2>What are you looking for?</h2></div>
          <a class="btn btn-light-outline" href="#bestsellers">View all categories <i class="fa-solid fa-arrow-right"></i></a>
        </div>
        <div class="row g-3 mt-3 reveal-group">
          <div class="col-6 col-md-4 col-lg-3"><article class="category-card"><img src="{{ url('frontend/assets/images/categories-imgs/orals.png') }}" alt="Orals"><div><h3>Orals</h3><i class="fa-solid fa-chevron-right"></i></div></article></div>
          <div class="col-6 col-md-4 col-lg-3"><article class="category-card bottle-dark"><img src="{{ url('frontend/assets/images/categories-imgs/fat-burrners.png') }}" alt="Fat burners"><div><h3>Fat Burners</h3><i class="fa-solid fa-chevron-right"></i></div></article></div>
          <div class="col-6 col-md-4 col-lg-3"><article class="category-card bottle-blue"><img src="{{ url('frontend/assets/images/categories-imgs/post-cycle-therapy.png') }}" alt="Post cycle therapy"><div><h3>Post Cycle Therapy</h3><i class="fa-solid fa-chevron-right"></i></div></article></div>
          <div class="col-6 col-md-4 col-lg-3"><article class="category-card bottle-gold"><img src="{{ url('frontend/assets/images/categories-imgs/orals.png') }}" alt="Human growth hormone"><div><h3>Growth Hormone</h3><i class="fa-solid fa-chevron-right"></i></div></article></div>
          <div class="col-6 col-md-4 col-lg-3"><article class="category-card bottle-silver"><img src="{{ url('frontend/assets/images/categories-imgs/peptides.png') }}" alt="Peptides"><div><h3>Peptides</h3><i class="fa-solid fa-chevron-right"></i></div></article></div>
          <div class="col-6 col-md-4 col-lg-3"><article class="category-card"><img class="category-art" src="{{ url('frontend/assets/images/categories-imgs/sexual-health.png') }}" alt="Sexual health products"><div><h3>Sexual Health</h3><i class="fa-solid fa-chevron-right"></i></div></article></div>
          <div class="col-12 col-md-8 col-lg-6"><article class="category-card wide-card"><img class="category-art syringe-art" src="{{ url('frontend/assets/images/categories-imgs/injection.png') }}" alt="Syringes and needles"><div><h3>Syringes &amp; Needles</h3><i class="fa-solid fa-chevron-right"></i></div></article></div>
        </div>
      </div>
    </section>

    <section class="why-section section-space" id="about">
      <div class="container">
        <div class="row align-items-center g-4 reveal-group">
          <div class="col-lg-3 why-intro">
            <p class="eyebrow">Why choose Arete?</p>
            <h2>Your Goals.<br>Our Mission.</h2>
            <p>Premium quality products, backed by science and trusted by athletes worldwide.</p>
          </div>
          <div class="col-6 col-lg"><div class="benefit"><i class="fa-solid fa-shield-halved"></i><h3>Premium<br>Quality</h3><p>Top-tier products you can trust.</p></div></div>
          <div class="col-6 col-lg"><div class="benefit"><i class="fa-solid fa-flask-vial"></i><h3>Lab<br>Tested</h3><p>Every batch is lab verified.</p></div></div>
          <div class="col-6 col-lg"><div class="benefit"><i class="fa-solid fa-truck-fast"></i><h3>Discreet<br>Shipping</h3><p>Private and secure delivery.</p></div></div>
          <div class="col-6 col-lg"><div class="benefit border-0"><i class="fa-solid fa-headset"></i><h3>24/7<br>Support</h3><p>We're here to help you succeed.</p></div></div>
        </div>
      </div>
    </section>

    <section class="bestsellers section-space" id="bestsellers">
      <div class="container">
        <div class="section-heading light d-flex justify-content-between align-items-end reveal-on-scroll">
          <div><p class="eyebrow">Bestsellers</p><h2>Our Top Picks</h2></div>
          <a href="#products">View all <i class="fa-solid fa-arrow-right"></i></a>
        </div>
        <div class="product-slider">
          <button class="slider-arrow slider-prev" type="button" aria-label="Previous product"><i class="fa-solid fa-chevron-left"></i></button>
          <div class="row g-3 mt-3 reveal-group product-track">
            <div class="col-6 col-md-4 col-lg"><article class="product-card bottle-blue"><span class="tag">Popular</span><img src="{{ url('frontend/assets/images/product-bottle.png') }}" alt="Anavar 50"><h3>Anavar 50</h3><small>Oxandrolone</small><div><strong>$59.99</strong><button aria-label="Add Anavar 50 to cart"><i class="fa-solid fa-cart-plus"></i></button></div></article></div>
            <div class="col-6 col-md-4 col-lg"><article class="product-card bottle-silver"><img src="{{ url('frontend/assets/images/product-bottle.png') }}" alt="Cardarine"><h3>Cardarine</h3><small>GW-501516</small><div><strong>$69.99</strong><button aria-label="Add Cardarine to cart"><i class="fa-solid fa-cart-plus"></i></button></div></article></div>
            <div class="col-6 col-md-4 col-lg"><article class="product-card bottle-gold"><img src="{{ url('frontend/assets/images/product-bottle.png') }}" alt="Nolvadex 20"><h3>Nolvadex 20</h3><small>Tamoxifen</small><div><strong>$49.99</strong><button aria-label="Add Nolvadex to cart"><i class="fa-solid fa-cart-plus"></i></button></div></article></div>
            <div class="col-6 col-md-4 col-lg"><article class="product-card bottle-dark"><span class="tag">New</span><img src="{{ url('frontend/assets/images/product-bottle.png') }}" alt="HGH 191AA"><h3>HGH 191AA</h3><small>10 IU</small><div><strong>$149.99</strong><button aria-label="Add HGH to cart"><i class="fa-solid fa-cart-plus"></i></button></div></article></div>
            <div class="col-6 col-md-4 col-lg"><article class="product-card"><img src="{{ url('frontend/assets/images/product-bottle.png') }}" alt="BPC-157"><h3>BPC-157</h3><small>5mg</small><div><strong>$59.99</strong><button aria-label="Add BPC-157 to cart"><i class="fa-solid fa-cart-plus"></i></button></div></article></div>
          </div>
          <button class="slider-arrow slider-next" type="button" aria-label="Next product"><i class="fa-solid fa-chevron-right"></i></button>
        </div>
      </div>
    </section>

    <section class="testimonials section-space">
      <div class="container">
        <div class="text-center section-heading testimonial-heading reveal-on-scroll">
          <p class="eyebrow">Trusted by athletes</p>
          <h2>Real Results. Real People.</h2>
          <p>At Arete Performance, we don't just promise results - we deliver them.<br>Here's what athletes and professionals have to say about their journey with us.</p>
        </div>
        <div class="testimonial-carousel" data-testimonial-slider>
          <button class="testimonial-nav testimonial-prev" type="button" aria-label="Previous testimonial"><i class="fa-solid fa-chevron-left"></i></button>
          <div class="testimonial-viewport">
            <div class="testimonial-track reveal-group">
              <article class="testimonial-slide"><figure class="quote-card"><i class="fa-solid fa-quote-left"></i><p>"Daily multivitamin meri morning routine ka important part ban chuka hai. Easy to take, gentle, aur overall wellness ke liye perfect."</p><figcaption><img src="{{ url('frontend/assets/images/testimonials/oliviacarter.png') }}" alt="Olivia Carter."><div><strong>Olivia Carter.</strong><small>Bodybuilder</small><span class="rating" aria-label="5 star rating">★★★★★</span></div></figcaption></figure></article>
              <article class="testimonial-slide is-featured"><figure class="quote-card"><i class="fa-solid fa-quote-left"></i><p>"Quality you can trust and results you can see."</p><figcaption><img src="{{ url('frontend/assets/images/testimonials/miker.png') }}" alt="Mike R."><div><strong>Mike R.</strong><small>Fitness Coach</small><span class="rating" aria-label="5 star rating">★★★★★</span></div></figcaption></figure></article>
              <article class="testimonial-slide"><figure class="quote-card"><i class="fa-solid fa-quote-left"></i><p>"This supplement fits perfectly into my daily wellness lifestyle. Packaging looks premium and the capsules are very easy to take."</p><figcaption><img src="{{ url('frontend/assets/images/testimonials/sophiabennett.png') }}" alt="Sophia Bennett."><div><strong>Sophia Bennett.</strong><small>Athlete</small><span class="rating" aria-label="5 star rating">★★★★★</span></div></figcaption></figure></article>
              <article class="testimonial-slide"><figure class="quote-card"><i class="fa-solid fa-quote-left"></i><p>"The packaging was clean, private, and the whole order felt professional."</p><figcaption><img src="{{ url('frontend/assets/images/testimonials/danielk.png') }}" alt="Daniel K."><div><strong>Daniel K.</strong><small>Powerlifter</small><span class="rating" aria-label="5 star rating">★★★★★</span></div></figcaption></figure></article>
              <article class="testimonial-slide"><figure class="quote-card"><i class="fa-solid fa-quote-left"></i><p>"After workouts, I like keeping my routine healthy and balanced. These supplements are easy to use and feel great for daily support."</p><figcaption><img src="{{ url('frontend/assets/images/testimonials/avamitchell.png') }}" alt="Ava Mitchell."><div><strong>Ava Mitchell.</strong><small>Trainer</small><span class="rating" aria-label="5 star rating">★★★★★</span></div></figcaption></figure></article>
            </div>
          </div>
          <button class="testimonial-nav testimonial-next" type="button" aria-label="Next testimonial"><i class="fa-solid fa-chevron-right"></i></button>
          <div class="testimonial-pagination" aria-label="Testimonials pagination"></div>
        </div>
        <div class="testimonial-stats reveal-on-scroll">
          <div><i class="fa-solid fa-users"></i><strong>50K+</strong><span>Happy Customers</span></div>
          <div><i class="fa-solid fa-star"></i><strong>4.9/5</strong><span>Average Rating</span></div>
          <div><i class="fa-solid fa-shield-halved"></i><strong>99%</strong><span>Satisfaction Rate</span></div>
          <div><i class="fa-solid fa-award"></i><strong>100%</strong><span>Quality Assured</span></div>
        </div>
      </div>
    </section>

@endsection

@section('js')
@endsection