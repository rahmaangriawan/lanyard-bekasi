/* Reveal the global WhatsApp control and remove it while footer CTA is visible. */
(function () {
  var button = document.getElementById('floating-whatsapp');
  if (!button) return;

  requestAnimationFrame(function () {
    button.classList.add('is-ready');
  });

  var footer = document.getElementById('site-footer');
  if (!footer || !('IntersectionObserver' in window)) return;

  var footerObserver = new IntersectionObserver(function (entries) {
    button.classList.toggle('is-footer-visible', entries[0].isIntersecting);
  }, { threshold: 0 });

  footerObserver.observe(footer);
})();
