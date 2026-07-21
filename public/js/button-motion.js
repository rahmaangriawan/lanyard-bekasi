/* Apply one motion language to public CTA buttons without changing their visual style. */
(function () {
  var selector = [
    'button:not(.site-header__hamburger):not(.site-header__backdrop):not(.deck-nav)',
    'a.site-header__btn',
    'a.ptl-hero__cta',
    'a.deck-cta-btn',
    'a.public-not-found__action',
    'a.btn-whatsapp',
    'a.button',
    'a[data-button-motion]',
    'a[class*="before:"]',
    'a[class*="bg-slate-9"][class*="px-"]',
    'a[class*="border-slate-8"][class*="px-"]'
  ].join(',');

  function isIcon(element) {
    return element.classList.contains('material-symbols-outlined')
      || element.getAttribute('aria-hidden') === 'true';
  }

  function findLabel(element) {
    var preferred = element.querySelector('.hero-btn-text, [data-button-label]');
    if (preferred) return preferred;

    var directText = Array.prototype.find.call(element.childNodes, function (node) {
      return node.nodeType === Node.TEXT_NODE && node.textContent.trim() !== '';
    });

    if (directText) {
      var wrapper = document.createElement('span');
      wrapper.textContent = directText.textContent.trim();
      directText.replaceWith(wrapper);
      return wrapper;
    }

    return Array.prototype.find.call(element.querySelectorAll('span'), function (span) {
      return !isIcon(span)
        && span.children.length === 0
        && span.textContent.trim() !== '';
    }) || null;
  }

  function enhance(element) {
    if (!(element instanceof HTMLElement)
      || element.dataset.buttonMotionReady === 'true') return;

    var label = findLabel(element);
    if (!label) return;

    element.classList.add('ptl-button-motion');
    element.dataset.buttonMotionReady = 'true';
    label.classList.add('ptl-button-motion__label');
  }

  function scan(root) {
    if (root instanceof HTMLElement && root.matches(selector)) enhance(root);
    root.querySelectorAll(selector).forEach(enhance);
  }

  scan(document);

  new MutationObserver(function (mutations) {
    mutations.forEach(function (mutation) {
      mutation.addedNodes.forEach(function (node) {
        if (node.nodeType === Node.ELEMENT_NODE) scan(node);
      });
    });
  }).observe(document.body, { childList: true, subtree: true });
})();
