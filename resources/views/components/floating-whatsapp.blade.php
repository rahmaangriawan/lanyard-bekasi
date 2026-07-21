@php
  $floatingWhatsappMessage = rawurlencode('Halo Pusat Tali Lanyard, saya mau konsultasi lanyard custom.');
  $floatingWhatsappUrl = "https://wa.me/6282210200700?text={$floatingWhatsappMessage}";
@endphp

<a
  class="floating-whatsapp"
  id="floating-whatsapp"
  href="{{ $floatingWhatsappUrl }}"
  target="_blank"
  rel="noopener noreferrer"
  aria-label="Chat Pusat Tali Lanyard melalui WhatsApp"
  title="Chat melalui WhatsApp"
>
  <img
    class="floating-whatsapp__icon"
    src="/images/whatsapp.svg"
    alt=""
    width="27"
    height="27"
    aria-hidden="true"
  />
</a>
