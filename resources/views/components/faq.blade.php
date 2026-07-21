@php($faqs = config('seo.faq', []))

<section class="home-faq" id="faq" aria-labelledby="home-faq-title">
  <div class="home-faq__container">
    <header class="home-faq__header">
      <h2 id="home-faq-title">Pertanyaan Paling Sering Diajukan</h2>
    </header>

    <div class="home-faq__accordion" id="faq-accordion" role="region" aria-label="Pertanyaan seputar lanyard custom">
      @foreach ($faqs as $index => $faq)
        <details class="home-faq__item faq-item">
          <summary id="faq-question-{{ $index }}" aria-controls="faq-answer-{{ $index }}">
            <span>{{ $faq['question'] }}</span>
            <span class="material-symbols-outlined home-faq__icon" aria-hidden="true">expand_more</span>
          </summary>
          <div class="home-faq__answer" id="faq-answer-{{ $index }}" aria-labelledby="faq-question-{{ $index }}">
            <p>{{ $faq['answer'] }}</p>
          </div>
        </details>
      @endforeach
    </div>
  </div>
</section>
