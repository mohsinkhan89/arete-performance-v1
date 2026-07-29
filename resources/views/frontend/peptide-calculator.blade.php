@extends('frontend.layouts.master')

@section('body')
<section class="peptide-calculator-page">
  <div class="container">
    <header class="peptide-calculator-hero">
      <div>
        <a class="peptide-back-link" href="{{ route('frontend.product-details', $product->slug) }}"><i class="fa-solid fa-arrow-left"></i> Back to product</a>
        <p class="eyebrow">Peptide Calculator</p>
        <h1>{{ $product->name }}</h1>
        <p>Choose your syringe, vial quantity, bacteriostatic water amount, and dose to calculate the syringe pull point.</p>
      </div>
      <div class="peptide-calculator-product">
        <img src="{{ $productImage }}" alt="{{ $product->name }}">
        <div><span>{{ $product->category?->name ?? 'Peptides' }}</span><strong>{{ $product->name }}</strong></div>
      </div>
    </header>

    <div class="peptide-calculator-shell" data-peptide-calculator data-conversion="1000" data-dose-unit="mcg">
      <div class="peptide-calculator-main">
        <section class="peptide-field peptide-field-full">
          <h2>What is the total volume of your syringe?</h2>
          <div class="peptide-syringe-options" data-peptide-group="syringe">
            @foreach ([[.3, 30, '0.3 ml'], [.5, 50, '0.5 ml'], [1, 100, '1.0 ml']] as [$ml, $units, $label])
              <button type="button" class="peptide-syringe-option {{ $loop->first ? 'active' : '' }}" data-value="{{ $ml }}" data-units="{{ $units }}" data-label="{{ $label }} syringe">
                <i class="fa-solid fa-syringe"></i><strong>{{ $label }}</strong><small>{{ $units }} units</small>
              </button>
            @endforeach
          </div>
          <div class="peptide-syringe-meta">
            <div><span>Selected Syringe</span><strong data-syringe-label>0.3 ml syringe</strong></div>
            <div><span>Total Capacity</span><strong data-syringe-capacity>0.3 ml / 30 units</strong></div>
            <div><span>Unit Scale</span><strong>1 unit = 0.01 ml</strong></div>
            <div><span>Markings</span><strong data-syringe-marking>30 unit markings</strong></div>
          </div>
        </section>

        <div class="peptide-fields-grid">
          <section class="peptide-field">
            <h2>Select Peptide Vial Quantity</h2>
            <div class="peptide-choice-row" data-peptide-group="vial">
              @foreach ([5, 10, 15] as $amount)<button type="button" class="peptide-choice {{ $vialAmount == $amount ? 'active' : '' }}" data-value="{{ $amount }}">{{ $amount }} mg</button>@endforeach
              <button type="button" class="peptide-choice {{ ! in_array($vialAmount, [5, 10, 15]) ? 'active' : '' }}" data-peptide-other="vial">Other</button>
            </div>
            <input class="peptide-custom-input {{ ! in_array($vialAmount, [5, 10, 15]) ? 'active' : '' }}" type="number" min=".1" step=".1" value="{{ $vialAmount }}" data-peptide-custom="vial" aria-label="Custom vial quantity">
          </section>

          <section class="peptide-field">
            <h2>How much bacteriostatic water are you adding?</h2>
            <div class="peptide-choice-row" data-peptide-group="water">
              @foreach ([1, 2, 3, 5] as $amount)<button type="button" class="peptide-choice {{ $loop->first ? 'active' : '' }}" data-value="{{ $amount }}">{{ $amount }} ml</button>@endforeach
              <button type="button" class="peptide-choice" data-peptide-other="water">Other</button>
            </div>
            <input class="peptide-custom-input" type="number" min=".1" step=".1" data-peptide-custom="water" aria-label="Custom water amount">
          </section>

          <section class="peptide-field">
            <h2>How much peptide do you want in each dose?</h2>
            <div class="peptide-choice-row" data-peptide-group="dose">
              @foreach ([50, 100, 250, 500] as $amount)<button type="button" class="peptide-choice {{ $loop->first ? 'active' : '' }}" data-value="{{ $amount }}">{{ $amount }} mcg</button>@endforeach
              <button type="button" class="peptide-choice" data-peptide-other="dose">Other</button>
            </div>
            <input class="peptide-custom-input" type="number" min=".1" step=".1" data-peptide-custom="dose" aria-label="Custom dose amount">
          </section>

          <section class="peptide-field peptide-field-summary">
            <span>Pull Syringe To</span><strong data-peptide-summary-result>0 units</strong>
            <p data-peptide-summary-note>Result updates from your selected syringe size.</p>
          </section>
        </div>
      </div>

      <aside class="peptide-calculator-result">
        <div class="peptide-result-card"><span>Pull Syringe To</span><strong><b data-peptide-result>0</b> units</strong></div>
        <div class="peptide-syringe-visual"><span class="peptide-syringe-fill" data-peptide-fill></span><b data-peptide-units>0 units</b></div>
        <p>For a dose of <strong data-peptide-dose-inline>50 mcg</strong>, pull the syringe to <strong data-peptide-result-inline>0</strong> units.</p>
        <div class="peptide-scale"><span data-syringe-scale-marker></span><div data-syringe-scale-ticks></div></div>
        <div class="peptide-result-meta">
          <div><span>Pull Volume</span><strong data-peptide-ml>0.000 ml</strong></div>
          <div><span>Syringe Usage</span><strong data-peptide-percent>0%</strong></div>
        </div>
        <div class="peptide-warning" data-peptide-warning><i class="fa-solid fa-triangle-exclamation"></i> Selected syringe is not sufficient for this amount.</div>
        <small class="peptide-disclaimer">Calculation aid only. This is not dosing or medical advice. Confirm all inputs and follow guidance from a qualified healthcare professional.</small>
      </aside>
    </div>
  </div>
</section>
@endsection
