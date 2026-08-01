/* Reveal the global WhatsApp control after the first painted frame. */
(function () {
  var button = document.getElementById('floating-whatsapp');
  if (!button) return;

  requestAnimationFrame(function () {
    button.classList.add('is-ready');
  });
})();
