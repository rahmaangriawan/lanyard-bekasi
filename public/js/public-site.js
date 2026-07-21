/* Public interactions shared by Blade pages. Keep this file deferred. */

(function() {
    var header = document.getElementById('site-header');
    var sentinel = document.getElementById('site-header-sentinel');
    var toggle = document.getElementById('menu-toggle');
    var menu = document.getElementById('mobile-menu');
    var backdrop = document.getElementById('menu-backdrop');
    var isOpen = false;

    if (!header || !toggle || !menu) return;

    // ── Scroll: add floating pill state ──
    if (sentinel && 'IntersectionObserver' in window) {
      var headerObserver = new IntersectionObserver(function(entries) {
        header.classList.toggle('is-scrolled', !entries[0].isIntersecting);
      }, { rootMargin: '-12px 0px 0px 0px', threshold: 0 });
      headerObserver.observe(sentinel);
    }

    // ── Mobile toggle ──
    function closeMenu(restoreFocus) {
      isOpen = false;
      toggle.setAttribute('aria-expanded', 'false');
      toggle.setAttribute('aria-label', 'Buka menu navigasi');
      menu.classList.remove('is-open');
      menu.setAttribute('aria-hidden', 'true');
      document.body.style.overflow = '';
      if (restoreFocus) toggle.focus();
    }

    function openMenu() {
      isOpen = true;
      toggle.setAttribute('aria-expanded', 'true');
      toggle.setAttribute('aria-label', 'Tutup menu navigasi');
      menu.classList.add('is-open');
      menu.setAttribute('aria-hidden', 'false');
      document.body.style.overflow = 'hidden';
      var activeLink = menu.querySelector('[aria-current="page"]');
      var firstLink = menu.querySelector('a');
      requestAnimationFrame(function() {
        (activeLink || firstLink)?.focus();
      });
    }

    toggle.addEventListener('click', function() {
      if (isOpen) closeMenu(false);
      else openMenu();
    });

    // Close on link click
    menu.querySelectorAll('a').forEach(function(link) {
      link.addEventListener('click', function() { closeMenu(false); });
    });

    if (backdrop) {
      backdrop.addEventListener('click', function() { closeMenu(true); });
    }

    // Close on Escape
    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape' && isOpen) closeMenu(true);
    });

    var desktopQuery = window.matchMedia('(min-width: 1024px)');
    desktopQuery.addEventListener('change', function(event) {
      if (event.matches && isOpen) closeMenu(false);
    });

    // ── Smooth scroll for anchor links ──
    document.querySelectorAll('a[href^="#"]').forEach(function(anchor) {
      anchor.addEventListener('click', function(e) {
        var target = document.querySelector(this.getAttribute('href'));
        if (target) {
          e.preventDefault();
          target.scrollIntoView({ behavior: 'smooth' });
        }
      });
    });
  })();

(() => {
  const data = document.getElementById('service-decks-data');
  if (!data) return;
  let decks = [];
  try { decks = JSON.parse(data.textContent || '[]'); } catch (_) { return; }
  const navRoot = document.getElementById('deck-nav');
  const display = document.getElementById('deck-display');
  if(!navRoot || !display) return;

  let active = 0;
  let fadeTimer = null;
  const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  function buildDetail(idx){
    const d = decks[idx];
    return `
      <div class="flex flex-col">
        <div class="flex items-start justify-between gap-6">
          <span class="deck-detail-badge">${d.badge}</span>
          <span class="deck-detail-icon-box">
            <span class="material-symbols-outlined deck-detail-icon-glyph">${d.icon}</span>
          </span>
        </div>
        <div class="mt-8">
          <h3 class="text-[26px] md:text-[30px] font-bold leading-[1.1] tracking-tight text-slate-900">${d.title}</h3>
          <p class="mt-4 max-w-[46ch] text-[15px] leading-[1.75] text-slate-600">${d.detail}</p>
        </div>
        <div class="mt-8 pt-8 border-t border-slate-100">
          <div class="deck-features-grid">
            ${d.features.map(f=>`
              <div class="deck-feature-item">
                <span class="deck-feature-box"><span class="material-symbols-outlined deck-feature-glyph">check</span></span>
                <span class="text-[14px] leading-6 font-medium text-slate-700">${f}</span>
              </div>`).join('')}
          </div>
        </div>
        <div class="mt-10 rounded-2xl bg-slate-50 border border-slate-200/60 p-5 flex flex-wrap items-center justify-between gap-4 deck-cta" data-cta>
          <div>
            <div class="text-[20px] font-extrabold leading-none tracking-tight text-slate-900">${d.stats}</div>
            <div class="mt-1.5 text-[11px] font-bold uppercase tracking-widest text-slate-400">${d.stats_label}</div>
          </div>
          <a href="${d.cta_url}" class="deck-cta-btn group/btn relative overflow-hidden inline-flex items-center gap-2 rounded-xl bg-slate-800 px-6 py-3.5 text-[14px] font-semibold text-white transition-all before:absolute before:top-1/2 before:left-1/2 before:h-0 before:w-0 before:-translate-x-1/2 before:-translate-y-1/2 before:rounded-full before:bg-white/15 before:duration-700 before:ease-out hover:before:h-[800px] hover:before:w-[800px] active:scale-[0.98]">
            <span class="relative z-10">${d.cta}</span>
            <span class="material-symbols-outlined relative z-10 text-[18px] deck-cta-arrow group-hover/btn:translate-x-0.5 transition-transform duration-300" data-arrow>arrow_forward</span>
          </a>
        </div>
      </div>`;
  }

  function setActive(newIdx){
    if(newIdx===active && display.innerHTML.trim()!=='') return;
    const prevActiveBtn = navRoot.querySelector(`[data-index="${active}"]`);
    const nextBtn = navRoot.querySelector(`[data-index="${newIdx}"]`);
    active = newIdx;

    // nav states + press scale
    navRoot.querySelectorAll('.deck-nav').forEach((el,idx)=>{
      el.classList.remove('is-active','is-sweeping');
      el.setAttribute('aria-selected', idx===newIdx ? 'true' : 'false');
    });
    if(nextBtn){
      nextBtn.classList.add('is-active');
      // trigger reflow for sweep
      void nextBtn.offsetWidth;
      nextBtn.classList.add('is-sweeping');
      // responsive press scale 0.995 -> 1
      if(!prefersReduced && nextBtn.animate){
        nextBtn.animate([{transform:'scale(0.995)'},{transform:'scale(1)'}],{duration:220,easing:'ease-out'});
      }
      setTimeout(()=>{ nextBtn.classList.remove('is-sweeping'); }, 600);
    }

    // panel fade out -> update -> fade in
    if(fadeTimer) clearTimeout(fadeTimer);
    const FADE_OUT = prefersReduced ? 0 : 160;
    const FADE_IN = 300;

    display.style.transition = `opacity ${FADE_OUT}ms ease-out`;
    display.style.opacity = '0';

    fadeTimer = setTimeout(()=>{
      display.innerHTML = buildDetail(newIdx);
      display.style.transform = 'translateY(12px)';
      display.style.opacity = '0';
      display.style.transition = `opacity ${FADE_IN}ms ease-out, transform ${FADE_IN}ms cubic-bezier(0.22,1,0.36,1)`;

      requestAnimationFrame(()=>{
        requestAnimationFrame(()=>{
          display.style.opacity = '1';
          display.style.transform = 'translateY(0)';

          // checklist stagger 0,80,160,240
          const items = display.querySelectorAll('.deck-feature-item');
          items.forEach((it,i)=>{
            it.style.transitionDelay = `${i*80}ms`;
            setTimeout(()=>{ it.classList.add('is-visible'); }, 20 + i*80);
          });

          // CTA scale + arrow
          const cta = display.querySelector('[data-cta]');
          const arrow = display.querySelector('[data-arrow]');
          setTimeout(()=>{
            if(cta) cta.classList.add('is-visible');
            if(arrow && !prefersReduced){
              arrow.style.transform = 'translateX(4px)';
              setTimeout(()=>{ arrow.style.transform = 'translateX(0)'; }, 180);
            }
          }, 260);
        });
      });
    }, FADE_OUT);
  }

  navRoot.addEventListener('click', e=>{
    const btn = e.target.closest('[data-index]');
    if(!btn) return;
    setActive(Number(btn.dataset.index));
  });

  navRoot.addEventListener('keydown', e=>{
    if(e.key==='ArrowDown' || e.key==='ArrowUp'){
      e.preventDefault();
      const dir = e.key==='ArrowDown' ? 1 : -1;
      const next = (active + dir + decks.length) % decks.length;
      setActive(next);
      navRoot.querySelector(`[data-index="${next}"]`)?.focus();
    }
  });

  // init
  display.innerHTML = buildDetail(0);
  display.style.opacity='1';
  display.style.transform='translateY(0)';
  const firstItems = display.querySelectorAll('.deck-feature-item');
  firstItems.forEach((it,i)=>{
    it.style.transitionDelay=`${i*80}ms`;
    setTimeout(()=>it.classList.add('is-visible'), 20 + i*80);
  });
  const firstCta = display.querySelector('[data-cta]');
  setTimeout(()=>{ if(firstCta) firstCta.classList.add('is-visible'); }, 320);
  navRoot.querySelector('[data-index="0"]')?.classList.add('is-active');
})();

(() => {
  const gallery = document.getElementById('product-gallery');
  if (!gallery || gallery.dataset.sliderReady === 'true') return;
  gallery.dataset.sliderReady = 'true';

  const wrap = gallery.closest('.product-gallery-wrap');
  const fadeLeft = wrap?.querySelector('.product-gallery-fade.is-left');
  const fadeRight = wrap?.querySelector('.product-gallery-fade.is-right');
  const list = gallery.querySelector('.product-gallery-list');
  const originalSlides = list ? Array.from(list.children) : [];
  const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
  let loopWidth = 0;
  let loopStart = 0;
  let frame = 0;
  let motionFrame = 0;
  let motionTarget = 0;
  let motionTime = 0;
  let normalizing = false;
  let dragging = false;
  let moved = false;
  let suppressClick = false;
  let startX = 0;
  let startScrollLeft = 0;
  let dragLastX = 0;
  let dragLastTime = 0;
  let dragVelocity = 0;
  let activePointerId = null;
  let wheelTimer = 0;

  const setScrollPosition = (left) => {
    const previousBehavior = gallery.style.scrollBehavior;
    gallery.style.scrollBehavior = 'auto';
    gallery.scrollLeft = left;
    if (previousBehavior) gallery.style.scrollBehavior = previousBehavior;
    else gallery.style.removeProperty('scroll-behavior');
  };

  const normalizeLoop = () => {
    if (normalizing || loopWidth <= 0) return 0;

    const minimum = loopStart;
    const maximum = loopStart + loopWidth;
    let target = gallery.scrollLeft;

    while (target < minimum) target += loopWidth;
    while (target >= maximum) target -= loopWidth;

    const correction = target - gallery.scrollLeft;
    if (Math.abs(correction) < 1) return 0;

    normalizing = true;
    setScrollPosition(target);
    normalizing = false;

    // Posisi acuan drag ikut dipindah agar gerakan tidak meloncat saat loop berulang.
    if (dragging) startScrollLeft += correction;
    if (motionFrame) motionTarget += correction;
    return correction;
  };

  const nearestSlideOffset = (position) => {
    if (!list) return position;
    const slides = Array.from(list.children);
    if (!slides.length) return position;

    return slides.reduce((nearest, slide) => {
      const offset = slide.offsetLeft;
      return Math.abs(offset - position) < Math.abs(nearest - position) ? offset : nearest;
    }, slides[0].offsetLeft);
  };

  const finishMotion = () => {
    motionFrame = 0;
    motionTime = 0;
    setScrollPosition(motionTarget);
    normalizeLoop();
    gallery.style.removeProperty('scroll-behavior');
    gallery.style.removeProperty('scroll-snap-type');
    scheduleFadeUpdate();
  };

  const animateMotion = (time) => {
    const elapsed = motionTime ? Math.min(time - motionTime, 40) : 16;
    motionTime = time;
    const distance = motionTarget - gallery.scrollLeft;

    if (Math.abs(distance) <= 0.35) {
      finishMotion();
      return;
    }

    // Frame-rate independent easing: responsif di awal, lembut saat mendekati kartu.
    const easing = 1 - Math.exp(-elapsed / 95);
    gallery.scrollLeft += distance * easing;
    normalizeLoop();
    scheduleFadeUpdate();
    motionFrame = requestAnimationFrame(animateMotion);
  };

  const glideTo = (target) => {
    motionTarget = target;
    gallery.style.scrollBehavior = 'auto';
    gallery.style.scrollSnapType = 'none';

    if (reducedMotion.matches) {
      setScrollPosition(motionTarget);
      normalizeLoop();
      gallery.style.removeProperty('scroll-behavior');
      gallery.style.removeProperty('scroll-snap-type');
      scheduleFadeUpdate();
      return;
    }

    if (!motionFrame) {
      motionTime = 0;
      motionFrame = requestAnimationFrame(animateMotion);
    }
  };

  const cancelMotion = () => {
    if (motionFrame) cancelAnimationFrame(motionFrame);
    motionFrame = 0;
    motionTime = 0;
  };

  const updateFades = () => {
    frame = 0;
    normalizeLoop();
    fadeLeft?.classList.remove('is-hidden');
    fadeRight?.classList.remove('is-hidden');
  };

  const scheduleFadeUpdate = () => {
    if (!frame) frame = requestAnimationFrame(updateFades);
  };

  const makeClone = (slide) => {
    const clone = slide.cloneNode(true);
    clone.setAttribute('aria-hidden', 'true');
    clone.querySelectorAll('a, button, input, select, textarea, [tabindex]').forEach((element) => element.setAttribute('tabindex', '-1'));
    return clone;
  };

  if (list && originalSlides.length > 1) {
    const nextClones = originalSlides.map(makeClone);
    nextClones.forEach((slide) => list.appendChild(slide));
    [...originalSlides].reverse().forEach((slide) => list.prepend(makeClone(slide)));
    requestAnimationFrame(() => {
      loopStart = originalSlides[0].offsetLeft;
      loopWidth = nextClones[0].offsetLeft - loopStart;
      setScrollPosition(loopStart);
    });
  }

  gallery.addEventListener('scroll', scheduleFadeUpdate, { passive: true });

  gallery.addEventListener('wheel', (event) => {
    if (event.ctrlKey || dragging) return;

    const rawDelta = Math.abs(event.deltaX) > Math.abs(event.deltaY)
      ? event.deltaX
      : event.deltaY;
    if (Math.abs(rawDelta) < 0.5) return;

    event.preventDefault();
    const unit = event.deltaMode === 1 ? 64 : event.deltaMode === 2 ? gallery.clientWidth : 1.9;
    const delta = Math.max(-420, Math.min(420, rawDelta * unit));
    const base = motionFrame ? motionTarget : gallery.scrollLeft;
    glideTo(base + delta);

    window.clearTimeout(wheelTimer);
    wheelTimer = window.setTimeout(() => {
      if (dragging) return;
      glideTo(nearestSlideOffset(motionTarget));
    }, 110);
  }, { passive: false });

  gallery.addEventListener('pointerdown', (event) => {
    if (event.pointerType === 'touch' || event.button !== 0) return;
    window.clearTimeout(wheelTimer);
    cancelMotion();
    dragging = true;
    moved = false;
    activePointerId = event.pointerId;
    startX = event.clientX;
    startScrollLeft = gallery.scrollLeft;
    dragLastX = event.clientX;
    dragLastTime = performance.now();
    dragVelocity = 0;
    gallery.classList.add('is-dragging');
    gallery.style.scrollBehavior = 'auto';
    gallery.style.scrollSnapType = 'none';
    gallery.setPointerCapture(event.pointerId);
  });

  gallery.addEventListener('pointermove', (event) => {
    if (!dragging || event.pointerId !== activePointerId) return;
    const distance = event.clientX - startX;
    if (Math.abs(distance) > 4) moved = true;
    gallery.scrollLeft = startScrollLeft - distance;
    normalizeLoop();
    if (moved) {
      const now = performance.now();
      const elapsed = Math.max(now - dragLastTime, 1);
      const instantVelocity = (event.clientX - dragLastX) / elapsed;
      dragVelocity = (dragVelocity * 0.65) + (instantVelocity * 0.35);
      dragLastX = event.clientX;
      dragLastTime = now;
      event.preventDefault();
    }
  });

  const stopDragging = (event) => {
    if (!dragging || (event.pointerId !== undefined && event.pointerId !== activePointerId)) return;
    const shouldGlide = moved;
    const idleTime = performance.now() - dragLastTime;
    const releaseVelocity = idleTime > 80 ? 0 : Math.max(-1.6, Math.min(1.6, dragVelocity));
    const projectedPosition = gallery.scrollLeft - (releaseVelocity * 190);
    dragging = false;
    gallery.classList.remove('is-dragging');
    if (activePointerId !== null && gallery.hasPointerCapture(activePointerId)) {
      gallery.releasePointerCapture(activePointerId);
    }
    activePointerId = null;
    if (moved) {
      suppressClick = true;
      setTimeout(() => { suppressClick = false; }, 250);
    }
    if (shouldGlide) glideTo(nearestSlideOffset(projectedPosition));
    else {
      gallery.style.removeProperty('scroll-behavior');
      gallery.style.removeProperty('scroll-snap-type');
    }
    scheduleFadeUpdate();
  };

  gallery.addEventListener('pointerup', stopDragging);
  gallery.addEventListener('pointercancel', stopDragging);
  gallery.addEventListener('lostpointercapture', (event) => {
    if (!dragging || event.pointerId !== activePointerId) return;
    stopDragging(event);
  });

  gallery.addEventListener('click', (event) => {
    if (!suppressClick) return;
    event.preventDefault();
    event.stopPropagation();
  }, true);

  gallery.addEventListener('keydown', (event) => {
    if (event.key !== 'ArrowLeft' && event.key !== 'ArrowRight') return;
    event.preventDefault();
    const card = gallery.querySelector('.product-slide');
    const step = (card?.getBoundingClientRect().width || 280) + 20;
    const direction = event.key === 'ArrowRight' ? 1 : -1;
    const base = motionFrame ? motionTarget : gallery.scrollLeft;
    glideTo(nearestSlideOffset(base + (direction * step)));
  });

  requestAnimationFrame(updateFades);
})();

(function () {
    const container = document.getElementById('faq-accordion');
    if (!container) return;
    const items = container.querySelectorAll('.faq-item');
    items.forEach(function (item) {
      item.addEventListener('toggle', function () {
        if (!this.open) return;
        items.forEach(function (other) {
          if (other !== item && other.open) {
            other.open = false;
          }
        });
      });
    });
  })();
