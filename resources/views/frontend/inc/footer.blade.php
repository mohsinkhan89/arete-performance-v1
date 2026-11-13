<div class="home-page shared-footer-shell">
<footer class="site-footer" id="contact">
    <div class="container">
        <!-- <div class="footer-feature-strip">
            <div><i class="fa-solid fa-flask-vial"></i><strong>Research Grade</strong><span>Purpose-built compounds</span></div>
            <div><i class="fa-solid fa-shield-halved"></i><strong>Quality Assured</strong><span>Consistent standards</span></div>
            <div><i class="fa-solid fa-truck-fast"></i><strong>Reliable Delivery</strong><span>Fast and discreet</span></div>
            <div><i class="fa-solid fa-lock"></i><strong>Secure Checkout</strong><span>Protected payments</span></div>
        </div> -->

        <div class="footer-main">
            <div class="footer-brand-block">
                <p class="footer-kicker"><span></span>Majix Research</p>
                <a class="brand footer-brand" href="{{ route('frontend.index') }}#home" aria-label="Majix home">
                    <img src="{{ url($siteSettings['footer_logo'] ?? 'frontend/assets/images/logo/logo.png') }}" alt="Majix">
                </a>
                <p class="footer-copy">Premium research products supplied with quality, consistency and confidence.</p>
                <a class="footer-shop-link" href="{{ route('frontend.shop') }}">Explore the collection <i class="fa-solid fa-arrow-right"></i></a>
            </div>

            <div class="footer-category-block">
                <h3>Categories</h3>
                <div class="footer-links footer-category-links">
                    @forelse ($footerCategories ?? collect() as $category)
                        <a href="{{ route('frontend.shop', ['category' => $category->slug]) }}">
                            {{ $category->name }}
                            {{-- @if (isset($category->products_count))
                                <span>{{ $category->products_count }}</span>
                            @endif --}}
                        </a>
                    @empty
                        <a href="{{ route('frontend.shop') }}">Shop Products</a>
                    @endforelse
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <span>&copy; {{ date('Y') }} MAJIX. Manufactured in <b>Germany</b>.</span>
            <div class="payment-badges" aria-label="Accepted payment methods">
                <span class="payment-image"><img src="{{ url('frontend/assets/images/google-pay.webp') }}" alt="Google Pay"></span>
                <span class="payment-image"><img src="{{ url('frontend/assets/images/apple-pay.webp') }}" alt="Apple Pay"></span>
            </div>
        </div>
    </div>
</footer>
</div>
