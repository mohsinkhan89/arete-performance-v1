<footer class="site-footer" id="contact">
    <div class="container">
        <div class="footer-main">
            <div class="footer-brand-block">
                <a class="brand footer-brand" href="{{ route('frontend.index') }}#home" aria-label="Arete Performance home">
                    <img src="{{ url($siteSettings['footer_logo'] ?? 'frontend/assets/images/logo/logo.png') }}" alt="Arete Performance">
                </a>
                <p class="footer-copy">Premium performance solutions designed to help you reach your full potential.</p>
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
            <span>&copy; {{ date('Y') }} Manufactured in <b style="color: #f8ae25;">Germany</b>.</span>
            <div class="payment-badges" aria-label="Accepted payment methods">
                <span class="payment-image"><img src="{{ url('frontend/assets/images/google-pay.webp') }}" alt="Google Pay"></span>
                <span class="payment-image"><img src="{{ url('frontend/assets/images/apple-pay.webp') }}" alt="Apple Pay"></span>
            </div>
        </div>
    </div>
</footer>
