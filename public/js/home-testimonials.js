(function () {
  function initTestimonials() {
    var section = document.querySelector('[data-testimonials]');
    if (!section) return;

    var track = section.querySelector('[data-testimonials-track]');
    var items = Array.prototype.slice.call(section.querySelectorAll('[data-testimonial-item]'));
    var dots = Array.prototype.slice.call(section.querySelectorAll('[data-testimonial-dot]'));
    var mobile = window.matchMedia('(max-width: 767px)');
    var reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    var activeIndex = 0;

    if (!track || !items.length || dots.length !== items.length) return;

    function update(index) {
      activeIndex = Math.max(0, Math.min(index, items.length - 1));
      dots.forEach(function (dot, dotIndex) {
        var isActive = dotIndex === activeIndex;
        dot.classList.toggle('is-active', isActive);
        dot.setAttribute('aria-current', isActive ? 'true' : 'false');
      });
    }

    function goTo(index) {
      if (!mobile.matches) return;
      var targetIndex = Math.max(0, Math.min(index, items.length - 1));
      track.scrollTo({
        left: items[targetIndex].offsetLeft - items[0].offsetLeft,
        behavior: reducedMotion.matches ? 'auto' : 'smooth'
      });
      update(targetIndex);
    }

    dots.forEach(function (dot, index) {
      dot.addEventListener('click', function () {
        goTo(index);
      });
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

      items.forEach(function (item) {
        observer.observe(item);
      });
    }

    update(0);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initTestimonials, { once: true });
  } else {
    initTestimonials();
  }
})();
