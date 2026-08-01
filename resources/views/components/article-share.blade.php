<nav class="article-share {{ $class ?? '' }}" aria-label="Bagikan artikel">
  <span>Bagikan artikel</span>
  <div>
    <a class="share-button share-button--whatsapp" href="https://api.whatsapp.com/send?text={{ rawurlencode($post->title . ' ' . url()->current()) }}" target="_blank" rel="noopener noreferrer" aria-label="Bagikan ke WhatsApp" title="Bagikan ke WhatsApp">
      <img src="/images/share-whatsapp.svg" alt="" width="18" height="18" aria-hidden="true">
      <span>WhatsApp</span>
    </a>
    <a class="share-button share-button--facebook" href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank" rel="noopener noreferrer" aria-label="Bagikan ke Facebook" title="Bagikan ke Facebook">
      <img src="/images/share-facebook.svg" alt="" width="18" height="18" aria-hidden="true">
      <span>Facebook</span>
    </a>
    <a class="share-button share-button--x" href="https://twitter.com/intent/tweet?text={{ urlencode($post->title) }}&url={{ urlencode(url()->current()) }}" target="_blank" rel="noopener noreferrer" aria-label="Bagikan ke X" title="Bagikan ke X">
      <img src="/images/share-x.svg" alt="" width="18" height="18" aria-hidden="true">
      <span>X</span>
    </a>
    <a class="share-button share-button--threads" href="https://www.threads.net/intent/post?text={{ urlencode($post->title . ' ' . url()->current()) }}" target="_blank" rel="noopener noreferrer" aria-label="Bagikan ke Threads" title="Bagikan ke Threads">
      <img src="/images/share-threads.svg" alt="" width="18" height="18" aria-hidden="true">
      <span>Threads</span>
    </a>
  </div>
</nav>
