@php
  $serviceAreas = ['Jakarta', 'Bogor', 'Depok', 'Tangerang', 'Bekasi'];
@endphp

<section class="home-locations" id="locations" aria-labelledby="home-locations-title">
  <div class="home-locations__container">
    <header class="home-locations__header">
      <h2 id="home-locations-title">Area Jangkauan Indonesia</h2>
      <p>Kami melayani pengiriman ke seluruh Indonesia, dengan cakupan layanan utama di wilayah Jabodetabek.</p>
    </header>

    <div class="home-locations__map">
      <iframe
        src="https://www.google.com/maps?q=Indonesia&amp;z=4&amp;output=embed"
        title="Peta jangkauan pengiriman Pusat Tali Lanyard di Indonesia"
        loading="lazy"
        allowfullscreen=""
        referrerpolicy="strict-origin-when-cross-origin"
      ></iframe>
    </div>

    <div class="home-locations__areas" role="list" aria-label="Area pengiriman utama Jabodetabek">
      @foreach ($serviceAreas as $area)
        <div class="home-locations__area" role="listitem">
          <span class="material-symbols-outlined" aria-hidden="true">location_on</span>
          <div>
            <h3>{{ $area }}</h3>
            <p>Area pengiriman utama</p>
          </div>
        </div>
      @endforeach
    </div>
  </div>
</section>
