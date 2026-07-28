{{-- Client Logos — overlaps hero on scroll, expands from 1000px to 1280px --}}
<section class="relative z-10 py-12 border-y border-slate-200/50 bg-white overflow-hidden" id="client-logos-section">
  <div class="client-logos-inner px-6 lg:px-10 mx-auto">
    <p class="text-center text-label-bold text-slate-500 uppercase tracking-widest mb-10">
      Dipercaya oleh Perusahaan Terkemuka
    </p>

    @php
      $logos = [
        ['src' => '/images/logos/bank-indonesia.svg', 'alt' => 'Bank Indonesia - klien Lanyard custom Indonesia', 'w' => 112, 'h' => 32],
        ['src' => '/images/logos/telkomsel.svg', 'alt' => 'Telkomsel - klien Lanyard custom Indonesia', 'w' => 96, 'h' => 32],
        ['src' => '/images/logos/kemenkes.svg', 'alt' => 'Kemenkes RI - klien Lanyard custom Indonesia', 'w' => 96, 'h' => 32],
        ['src' => '/images/logos/bca.svg', 'alt' => 'BCA - klien Lanyard custom Indonesia', 'w' => 96, 'h' => 32],
        ['src' => '/images/logos/universitas-indonesia.png', 'alt' => 'Universitas Indonesia - klien Lanyard custom Indonesia', 'w' => 104, 'h' => 32],
        ['src' => '/images/logos/bumn.svg', 'alt' => 'BUMN - klien Lanyard custom Indonesia', 'w' => 80, 'h' => 32],
        ['src' => '/images/logos/biznet.svg', 'alt' => 'Biznet - klien Lanyard custom Indonesia', 'w' => 96, 'h' => 32],
        ['src' => '/images/logos/komdigi.svg', 'alt' => 'Komdigi - klien Lanyard custom Indonesia', 'w' => 96, 'h' => 32],
      ];
    @endphp

    {{-- Marquee container --}}
    <div class="logo-marquee" id="logo-marquee">
      <div class="logo-marquee__track" id="logo-track">
        {{-- Two identical sets keep the 40 second marquee seamless. --}}
        @foreach ([1, 2] as $set)
        <div class="logo-marquee__set">
          @foreach ($logos as $logo)
          <img
            src="{{ $logo['src'] }}"
            alt="{{ $logo['alt'] }}"
            width="{{ $logo['w'] }}"
            height="{{ $logo['h'] }}"
            style="aspect-ratio: {{ $logo['w'] }}/{{ $logo['h'] }}; width: auto; height: 32px;"
            class="logo-marquee__img"
            loading="lazy"
            decoding="async"
          />
          @endforeach
        </div>
        @endforeach
      </div>
    </div>
  </div>
</section>
