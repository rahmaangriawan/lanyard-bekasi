(function () {
  function initGallery() {
    var gallery = document.querySelector('[data-gallery]');
    if (!gallery) return;

    var track = gallery.querySelector('[data-gallery-track]');
    var items = Array.prototype.slice.call(gallery.querySelectorAll('[data-gallery-item]'));
    var previous = gallery.querySelector('[data-gallery-prev]');
    var next = gallery.querySelector('[data-gallery-next]');
    var current = gallery.querySelector('[data-gallery-current]');
    var mobile = window.matchMedia('(max-width: 767px)');
    var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    var activeIndex = 0;

    if (!track || !items.length || !previous || !next || !current) return;

    function update(index) {
      activeIndex = Math.max(0, Math.min(index, items.length - 1));
      current.textContent = String(activeIndex + 1);
      previous.disabled = activeIndex === 0;
      next.disabled = activeIndex === items.length - 1;
    }

    function goTo(index) {
      if (!mobile.matches) return;
      var targetIndex = Math.max(0, Math.min(index, items.length - 1));
      track.scrollTo({
        left: items[targetIndex].offsetLeft - items[0].offsetLeft,
        behavior: reduceMotion.matches ? 'auto' : 'smooth'
      });
      update(targetIndex);
    }

    previous.addEventListener('click', function () {
      goTo(activeIndex - 1);
    });

    next.addEventListener('click', function () {
      goTo(activeIndex + 1);
    });

    track.addEventListener('keydown', function (event) {
      if (!mobile.matches) return;
      if (event.key === 'ArrowLeft') {
        event.preventDefault();
        goTo(activeIndex - 1);
      }
      if (event.key === 'ArrowRight') {
        event.preventDefault();
        goTo(activeIndex + 1);
      }
    });

    if ('IntersectionObserver' in window) {
      var observer = new IntersectionObserver(function (entries) {
        if (!mobile.matches) return;
        var visible = entries
          .filter(function (entry) { return entry.isIntersecting; })
          .sort(function (a, b) { return b.intersectionRatio - a.intersectionRatio; })[0];

        if (visible) update(items.indexOf(visible.target));
      }, { root: track, threshold: [0.55, 0.75] });

      items.forEach(function (item) { observer.observe(item); });
    }

    update(0);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initGallery, { once: true });
  } else {
    initGallery();
  }
})();
