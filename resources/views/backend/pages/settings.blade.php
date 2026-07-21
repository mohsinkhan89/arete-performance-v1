@extends('backend.layouts.master')

@section('title', $pageTitle ?? 'Site Settings')

@section('body')
    @php
        $headerLogo = $settings['header_logo'] ?? 'frontend/assets/images/logo/logo-transperent.png';
        $footerLogo = $settings['footer_logo'] ?? 'frontend/assets/images/logo/logo.png';
        $whatsappNumber = old('company_whatsapp_number', $settings['company_whatsapp_number'] ?? '');
    @endphp

    <div class="settings-modern">
        <div class="settings-hero">
            <div>
                <span class="eyebrow">Control Center</span>
                <h1>Site Settings</h1>
                <p>Update frontend branding and customer contact details from one clean dashboard.</p>
            </div>
            <div class="settings-hero-badge">
                <i class="fa-solid fa-shield-halved"></i>
                <span>Brand assets</span>
            </div>
        </div>

        <form class="admin-form site-settings-form" action="{{ route('backend.settings.site.update') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="site-settings-grid">
                <section class="settings-panel settings-brand-panel">
                    <div class="settings-panel-head">
                        <div>
                            <span class="eyebrow">Identity</span>
                            <h2>Logo Management</h2>
                        </div>
                        <span class="settings-panel-chip">PNG, JPG, WEBP, SVG</span>
                    </div>

                    <div class="logo-settings-grid">
                        <label class="logo-upload-card">
                            <span class="logo-card-title">Header Logo</span>
                            <span class="logo-preview-frame">
                                <img src="{{ url($headerLogo) }}" alt="Current header logo">
                            </span>
                            <span class="logo-card-meta">Used in website header and checkout navigation.</span>
                            <span class="file-pick-row">
                                <i class="fa-solid fa-cloud-arrow-up"></i>
                                <strong>Replace header logo</strong>
                            </span>
                            <input type="file" name="header_logo_file" accept=".jpg,.jpeg,.png,.webp,.svg,image/*">
                            @error('header_logo_file') <small class="field-error">{{ $message }}</small> @enderror
                        </label>

                        <label class="logo-upload-card footer-logo-card">
                            <span class="logo-card-title">Footer Logo</span>
                            <span class="logo-preview-frame">
                                <img src="{{ url($footerLogo) }}" alt="Current footer logo">
                            </span>
                            <span class="logo-card-meta">Shown in footer branding and lower site sections.</span>
                            <span class="file-pick-row">
                                <i class="fa-solid fa-cloud-arrow-up"></i>
                                <strong>Replace footer logo</strong>
                            </span>
                            <input type="file" name="footer_logo_file" accept=".jpg,.jpeg,.png,.webp,.svg,image/*">
                            @error('footer_logo_file') <small class="field-error">{{ $message }}</small> @enderror
                        </label>
                    </div>
                </section>

                <aside class="settings-preview-panel">
                    <div class="settings-preview-head">
                        <span class="eyebrow">Preview</span>
                        <h2>Current Brand</h2>
                    </div>

                    <div class="brand-preview-window">
                        <div class="brand-preview-top">
                            <img src="{{ url($headerLogo) }}" alt="Header logo preview">
                            <span>Header</span>
                        </div>
                        <div class="brand-preview-body">
                            <span class="preview-line wide"></span>
                            <span class="preview-line"></span>
                            <span class="preview-button">Shop Now</span>
                        </div>
                        <div class="brand-preview-footer">
                            <img src="{{ url($footerLogo) }}" alt="Footer logo preview">
                        </div>
                    </div>

                    <div class="settings-status-list">
                        <div>
                            <i class="fa-solid fa-image"></i>
                            <span>Header Logo</span>
                            <strong>Active</strong>
                        </div>
                        <div>
                            <i class="fa-solid fa-layer-group"></i>
                            <span>Footer Logo</span>
                            <strong>Active</strong>
                        </div>
                        <div>
                            <i class="fa-brands fa-whatsapp"></i>
                            <span>WhatsApp</span>
                            <strong>{{ $whatsappNumber ?: 'Not set' }}</strong>
                        </div>
                    </div>
                </aside>

                <section class="settings-panel settings-contact-panel">
                    <div class="settings-panel-head">
                        <div>
                            <span class="eyebrow">Contact</span>
                            <h2>WhatsApp Number</h2>
                        </div>
                        <span class="settings-panel-chip">Order redirect</span>
                    </div>

                    <label class="settings-input-wrap">
                        <span>Company WhatsApp Number</span>
                        <div class="settings-input-icon">
                            <i class="fa-brands fa-whatsapp"></i>
                            <input type="text" name="company_whatsapp_number" value="{{ $whatsappNumber }}" placeholder="+44 7123 456789">
                        </div>
                        <small>Customers are redirected to this WhatsApp number 10 seconds after order success.</small>
                        @error('company_whatsapp_number') <small class="field-error">{{ $message }}</small> @enderror
                    </label>
                </section>

                <section class="settings-panel settings-help-panel">
                    <div>
                        <i class="fa-solid fa-circle-info"></i>
                        <strong>Quick Note</strong>
                    </div>
                    <p>Use clean transparent logos for best results. After saving, frontend pages will use the updated assets automatically.</p>
                </section>
            </div>

            <div class="form-actions sticky-actions settings-savebar">
                <div>
                    <strong>Ready to publish?</strong>
                    <span>Save changes to update the live site settings.</span>
                </div>
                <button class="btn-primary" type="submit"><i class="fa-solid fa-floppy-disk"></i> Save Settings</button>
            </div>
        </form>
    </div>
@endsection
