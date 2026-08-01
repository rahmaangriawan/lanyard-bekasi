(function () {
  function initTestimonials() {
    var section = document.querySelector('[data-testimonials]');
    if (!section) return;

    var track = section.querySelector('[data-testimonials-track]');
    var items = Array.prototype.slice.call(section.querySelectorAll('[data-testimonial-item]'));
    var status = section.querySelector('[data-testimonials-status]');
    var mobile = window.matchMedia('(max-width: 767px)');
    var reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    var activeIndex = 0;
    var autoplayTimer = 0;
    var autoplayDelay = 4200;
    var isPointerActive = false;
    var isFocusWithin = false;

    if (!track || items.length < 2) return;

    function normalizeIndex(index) {
      return (index + items.length) % items.length;
    }

    function update(index, announce) {
      activeIndex = normalizeIndex(index);
      items.forEach(function (item, itemIndex) {
        item.classList.toggle('is-active', itemIndex === activeIndex);
      });

      if (announce !== false && status) {
        status.textContent = 'Testimoni ' + (activeIndex + 1) + ' dari ' + items.length + ': '
          + items[activeIndex].getAttribute('data-testimonial-name');
      }
    }

    function goTo(index, announce) {
      if (!mobile.matches) return;
      var targetIndex = normalizeIndex(index);
      track.scrollTo({
        left: items[targetIndex].offsetLeft - items[0].offsetLeft,
        behavior: reducedMotion.matches ? 'auto' : 'smooth'
      });
      update(targetIndex, announce);
    }

    function stopAutoplay() {
      window.clearTimeout(autoplayTimer);
      autoplayTimer = 0;
    }

    function startAutoplay() {
      stopAutoplay();
      if (
        !mobile.matches
        || reducedMotion.matches
        || document.hidden
        || isPointerActive
        || isFocusWithin
      ) return;

      autoplayTimer = window.setTimeout(function () {
        goTo(activeIndex + 1);
        startAutoplay();
      }, autoplayDelay);
    }

    track.addEventListener('keydown', function (event) {
      if (!mobile.matches) return;
      if (event.key === 'ArrowLeft') {
        event.preventDefault();
        stopAutoplay();
        goTo(activeIndex - 1);
        startAutoplay();
      }
      if (event.key === 'ArrowRight') {
        event.preventDefault();
        stopAutoplay();
        goTo(activeIndex + 1);
        startAutoplay();
      }
    });

    track.addEventListener('pointerdown', function () {
      isPointerActive = true;
      stopAutoplay();
    }, { passive: true });
    track.addEventListener('pointerup', function () {
      isPointerActive = false;
      startAutoplay();
    }, { passive: true });
    track.addEventListener('pointercancel', function () {
      isPointerActive = false;
      startAutoplay();
    }, { passive: true });
    track.addEventListener('focusin', function () {
      isFocusWithin = true;
      stopAutoplay();
    });
    track.addEventListener('focusout', function (event) {
      if (event.relatedTarget && track.contains(event.relatedTarget)) return;
      isFocusWithin = false;
      startAutoplay();
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

    document.addEventListener('visibilitychange', startAutoplay);
    mobile.addEventListener('change', function () {
      if (mobile.matches) {
        goTo(activeIndex, false);
      } else {
        track.scrollTo({ left: 0, behavior: 'auto' });
        update(0, false);
      }
      startAutoplay();
    });
    reducedMotion.addEventListener('change', function () {
      goTo(activeIndex, false);
      startAutoplay();
    });

    update(0, false);
    startAutoplay();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initTestimonials, { once: true });
  } else {
    initTestimonials();
  }
})();
