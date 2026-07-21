/* Scroll Reveal Engine — Mode A Vanilla Light — Native scroll, wheel up/down scrub, reversible */
/* < 4kb gz — no deps, no Lenis, no hijack. Respects .product-splide wheel hijack */
(() => {
  if (window.__SR_INIT__) return; window.__SR_INIT__ = true;
  const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  if (prefersReduced) return;

  const $ = (sel, root=document) => Array.from(root.querySelectorAll(sel));

  /* helpers */
  function clamp(v, a, b){ return Math.max(a, Math.min(b, v)); }
  function lerp(a,b,t){ return a + (b-a)*t; }

  /* --- 1) REVEAL — reversible IO --- */
  const revealEls = $('[data-sr]');
  if (revealEls.length){
    const io = new IntersectionObserver((entries)=>{
      entries.forEach(e=>{
        const el = e.target;
        const repeat = el.getAttribute('data-sr-repeat') !== 'false'; // default repeat true for Fase 1
        if (e.isIntersecting){
          const d = parseInt(el.getAttribute('data-sr-delay')||'0',10);
          const stagger = el.getAttribute('data-sr')==='stagger';
          if (stagger){
            const base = parseInt(el.getAttribute('data-sr-stagger')||'80',10);
            Array.from(el.children).forEach((ch,i)=>{
              ch.style.setProperty('--stagger', (i*base)+'ms');
            });
          }
          setTimeout(()=>{
            el.classList.add('is-in');
            el.classList.remove('is-out');
          }, d);
        } else {
          if (repeat){
            el.classList.remove('is-in');
            if (e.boundingClientRect.top > 0){
              el.classList.remove('is-out');
            } else {
              el.classList.add('is-out');
            }
          }
        }
      });
    }, { threshold: 0.18, rootMargin: '0px 0px -8% 0px' });
    revealEls.forEach(el=>{
      const del = el.getAttribute('data-sr-delay');
      if (del) el.style.setProperty('--sr-delay', del+'ms');
      io.observe(el);
    });
  }

  /* --- 2) PARALLAX — rAF, progress based, reversible wheel --- */
  const parallaxEls = $('[data-parallax]');
  const allowParallax = window.matchMedia('(min-width: 768px)').matches;
  if (parallaxEls.length && allowParallax){
    let ticking = false;
    let vh = window.innerHeight;
    const items = parallaxEls.map(el=>{
      const speed = parseFloat(el.getAttribute('data-parallax')||'0.1');
      const styleAttr = el.getAttribute('style')||'';
      // preserve hero translateX(-6.5rem) and rotate(...) if present in inline style
      const rotMatch = styleAttr.match(/--rot:\s*([^;"]+)/);
      const inlineRot = el.style.getPropertyValue('--rot') || (rotMatch?rotMatch[1]:'');
      const baseRotStr = inlineRot.trim();
      const baseRotNum = parseFloat(baseRotStr) || 0;
      // detect hero card translateX from inline transform
      const txMatch = styleAttr.match(/translateX\(([^)]+)\)/);
      const baseTx = txMatch ? txMatch[1] : '';
      const rotInlineMatch = styleAttr.match(/rotate\(([^)]+)\)/);
      const baseRotateInline = rotInlineMatch ? rotInlineMatch[1] : '';
      return { el, speed, baseRotStr, baseRotNum, baseTx, baseRotateInline };
    });
    const activeItems = new Set();
    const parallaxObserver = new IntersectionObserver((entries)=>{
      entries.forEach(entry=>{
        const item = items.find(candidate=>candidate.el === entry.target);
        if (!item) return;
        if (entry.isIntersecting) activeItems.add(item);
        else activeItems.delete(item);
      });
      onScroll();
    }, { rootMargin: '35% 0px 35% 0px' });
    items.forEach(item=>parallaxObserver.observe(item.el));

    function onScroll(){
      if (ticking) return;
      ticking = true;
      requestAnimationFrame(()=>{
        // Read all geometry first, then write transforms. Mixing reads and
        // writes per element forces repeated synchronous layout recalculation.
        const measurements = Array.from(activeItems, item => ({ item, rect: item.el.getBoundingClientRect() }));
        measurements.forEach(({item: {el, speed, baseRotStr, baseRotNum, baseTx, baseRotateInline}, rect})=>{
          if (el.closest && el.closest('.product-splide') && document.querySelector('.product-splide:hover')) return;
          const center = rect.top + rect.height*0.5;
          const delta = vh*0.5 - center;
          const y = delta * speed * -0.35;
          // build transform: preserve hero fan-out — order must be rotate -> translateX -> translateY
          let parts = [];
          // rotate first (hero: -6deg / 6deg, trust: var(--rot))
          if (baseRotateInline){
            parts.push(`rotate(${baseRotateInline})`);
          } else {
            const rot = baseRotStr || (baseRotNum?baseRotNum+'deg':'');
            if (rot) parts.push(`rotate(${rot})`);
          }
          if (baseTx) parts.push(`translateX(${baseTx})`);
          parts.push(`translateY(${y.toFixed(2)}px)`);
          let t = parts.join(' ');
          if (el.dataset.parallaxScale){
            const sc = lerp(1, parseFloat(el.dataset.parallaxScale), clamp(Math.abs(delta)/ (vh*0.8),0,1));
            t += ` scale(${sc})`;
          }
          el.style.transform = t;
        });
        ticking = false;
      });
    }
    window.addEventListener('scroll', onScroll, { passive:true });
    window.addEventListener('resize', ()=>{ vh = window.innerHeight; onScroll(); }, { passive:true });
    onScroll();
  }

  /* --- 3) CLIENT LOGOS — reversible width expand --- */
  /* Logo marquee motion is CSS-only; no scroll-linked behavior. */

  /* --- 4) SERVICE HIGHLIGHTS — scroll-spy DISABLED Fase 4 per request user: cukup container masuk aja, no auto deck switch --- */
  (()=>{ /* intentionally empty — deck manual click only */ })();

  /* --- 5) progress bar removed Fase 3 — user request hapus garis horizontal di Pilihan Produk --- */
  (()=>{
    const old = document.getElementById('sr-products-progress');
    if (old) old.remove();
  })();

})();
