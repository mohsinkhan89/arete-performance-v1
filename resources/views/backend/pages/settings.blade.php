@extends('backend.layouts.master')

@section('title', $pageTitle ?? 'Site Settings')

@section('body')
    <div class="page-head compact-head">
        <div>
            <span class="eyebrow">Settings</span>
            <h1>Site Settings</h1>
            <p>Manage website branding from one place.</p>
        </div>
    </div>

    <div class="settings-tabs" role="tablist" aria-label="Settings tabs">
        <button class="active" type="button"><i class="fa-solid fa-globe"></i> Site</button>
    </div>

    <form class="admin-form site-settings-form" action="{{ route('backend.settings.site.update') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="site-settings-grid">
            <section class="settings-panel">
                <div class="settings-panel-head">
                    <div>
                        <span class="eyebrow">Logos</span>
                        <h2>Header &amp; Footer</h2>
                    </div>
                </div>

                <div class="logo-settings-grid">
                    <label class="logo-upload-card">
                        <span>Header Logo</span>
                        <img src="{{ url($settings['header_logo'] ?? 'frontend/assets/images/logo/logo-transperent.png') }}" alt="Current header logo">
                        <input type="file" name="header_logo_file" accept=".jpg,.jpeg,.png,.webp,.svg,image/*">
                        @error('header_logo_file') <small class="field-error">{{ $message }}</small> @enderror
                    </label>

                    <label class="logo-upload-card">
                        <span>Footer Logo</span>
                        <img src="{{ url($settings['footer_logo'] ?? 'frontend/assets/images/logo/logo.png') }}" alt="Current footer logo">
                        <input type="file" name="footer_logo_file" accept=".jpg,.jpeg,.png,.webp,.svg,image/*">
                        @error('footer_logo_file') <small class="field-error">{{ $message }}</small> @enderror
                    </label>
                </div>
            </section>

            <section class="settings-panel">
                <div class="settings-panel-head">
                    <div>
                        <span class="eyebrow">Theme</span>
                        <h2>Website Colors</h2>
                    </div>
                </div>

                <div class="color-settings-grid">
                    <label class="color-field">
                        <span>Primary Color</span>
                        <input type="color" name="primary_color" value="{{ old('primary_color', $settings['primary_color'] ?? '#f5a817') }}">
                        @error('primary_color') <small class="field-error">{{ $message }}</small> @enderror
                    </label>

                    <label class="color-field">
                        <span>Secondary Color</span>
                        <input type="color" name="secondary_color" value="{{ old('secondary_color', $settings['secondary_color'] ?? '#111111') }}">
                        @error('secondary_color') <small class="field-error">{{ $message }}</small> @enderror
                    </label>
                </div>

                <div class="brand-preview" style="--preview-primary: {{ old('primary_color', $settings['primary_color'] ?? '#f5a817') }}; --preview-secondary: {{ old('secondary_color', $settings['secondary_color'] ?? '#111111') }};">
                    <span></span>
                    <strong>Brand preview</strong>
                    <button type="button">Shop Now</button>
                </div>
            </section>
        </div>

        <div class="form-actions sticky-actions">
            <button class="btn-primary" type="submit"><i class="fa-solid fa-floppy-disk"></i> Save Site Settings</button>
        </div>
    </form>
@endsection
