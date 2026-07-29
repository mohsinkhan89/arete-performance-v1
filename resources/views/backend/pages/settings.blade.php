@extends('backend.layouts.master')

@section('title', $pageTitle ?? 'Site Settings')

@section('body')
    @php
        $headerLogo = $settings['header_logo'] ?? 'frontend/assets/images/logo/logo-transperent.png';
        $footerLogo = $settings['footer_logo'] ?? 'frontend/assets/images/logo/logo.png';
        $whatsappNumber = old('company_whatsapp_number', $settings['company_whatsapp_number'] ?? '');
        $adminOrderEmails = old('admin_order_emails', $settings['admin_order_emails'] ?? '');
        $cssVersion = old('css_version', $settings['css_version'] ?? '1.0.0');
        $jsVersion = old('js_version', $settings['js_version'] ?? '1.0.0');
    @endphp

    <div class="page-heading">
        <h1>Site Settings</h1>
        <p>Manage frontend branding, customer contact details and order notifications.</p>
    </div>

    <article class="panel form-panel form-panel-full settings-admin-panel">
        <div class="settings-native-grid">
            <form class="admin-form form-section" action="{{ route('backend.settings.site.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="setting" value="header_logo">
                <div class="form-section-head"><span>01</span><h2>Header Logo</h2></div>
                <div class="settings-logo-current"><img src="{{ url($headerLogo) }}" alt="Current header logo"></div>
                <label>Choose Header Logo
                    <input type="file" name="header_logo_file" accept=".jpg,.jpeg,.png,.webp,.svg,image/*">
                    <small>Used in the website header and checkout navigation.</small>
                    @error('header_logo_file') <span>{{ $message }}</span> @enderror
                </label>
                <div class="form-actions"><button type="submit"><i class="fa-solid fa-floppy-disk"></i> Save Header Logo</button></div>
            </form>

            <form class="admin-form form-section" action="{{ route('backend.settings.site.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="setting" value="footer_logo">
                <div class="form-section-head"><span>02</span><h2>Footer Logo</h2></div>
                <div class="settings-logo-current"><img src="{{ url($footerLogo) }}" alt="Current footer logo"></div>
                <label>Choose Footer Logo
                    <input type="file" name="footer_logo_file" accept=".jpg,.jpeg,.png,.webp,.svg,image/*">
                    <small>Displayed in footer branding and lower site sections.</small>
                    @error('footer_logo_file') <span>{{ $message }}</span> @enderror
                </label>
                <div class="form-actions"><button type="submit"><i class="fa-solid fa-floppy-disk"></i> Save Footer Logo</button></div>
            </form>

            <form class="admin-form form-section" action="{{ route('backend.settings.site.update') }}" method="POST">
                @csrf
                <input type="hidden" name="setting" value="company_whatsapp_number">
                <div class="form-section-head"><span>03</span><h2>WhatsApp Number</h2></div>
                <label>Company WhatsApp Number
                    <input type="text" name="company_whatsapp_number" value="{{ $whatsappNumber }}" placeholder="+44 7123 456789">
                    <small>Customers are redirected to this number after checkout.</small>
                    @error('company_whatsapp_number') <span>{{ $message }}</span> @enderror
                </label>
                <div class="form-actions"><button type="submit"><i class="fa-solid fa-floppy-disk"></i> Save WhatsApp Number</button></div>
            </form>

            <form class="admin-form form-section" action="{{ route('backend.settings.site.update') }}" method="POST">
                @csrf
                <input type="hidden" name="setting" value="admin_order_emails">
                <div class="form-section-head"><span>04</span><h2>Admin Order Emails</h2></div>
                <label>Admin Email Addresses
                    <input type="text" name="admin_order_emails" value="{{ $adminOrderEmails }}" placeholder="admin@example.com, orders@example.com">
                    <small>Separate multiple email addresses with commas.</small>
                    @error('admin_order_emails') <span>{{ $message }}</span> @enderror
                </label>
                <div class="form-actions"><button type="submit"><i class="fa-solid fa-floppy-disk"></i> Save Admin Emails</button></div>
            </form>

            <form class="admin-form form-section" action="{{ route('backend.settings.site.update') }}" method="POST">
                @csrf
                <input type="hidden" name="setting" value="asset_versions">
                <div class="form-section-head"><span>05</span><h2>CSS &amp; JS Versions</h2></div>
                <label>CSS Version
                    <input type="text" name="css_version" value="{{ $cssVersion }}" placeholder="1.0.0">
                    <small>Change this after updating the frontend stylesheet to force browsers to load the latest CSS.</small>
                    @error('css_version') <span>{{ $message }}</span> @enderror
                </label>
                <label>JS Version
                    <input type="text" name="js_version" value="{{ $jsVersion }}" placeholder="1.0.0">
                    <small>Change this after updating frontend JavaScript to force browsers to load the latest JS.</small>
                    @error('js_version') <span>{{ $message }}</span> @enderror
                </label>
                <div class="form-actions"><button type="submit"><i class="fa-solid fa-rotate"></i> Update Asset Versions</button></div>
            </form>
        </div>
    </article>
@endsection
