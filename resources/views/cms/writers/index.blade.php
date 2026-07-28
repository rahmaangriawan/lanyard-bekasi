@extends('layouts.meridian')

@section('title', 'Penulis')

@push('head')
  <style>
    .writer-avatar {
      display: grid;
      place-items: center;
      width: 2.75rem;
      aspect-ratio: 1;
      flex: 0 0 2.75rem;
      overflow: hidden;
      border: 1px solid var(--color-border);
      border-radius: 50%;
      background: var(--color-muted);
      color: var(--color-primary);
      font-weight: 800;
    }
    .writer-avatar img { width: 100%; height: 100%; object-fit: cover; }
    .writer-delete-form { display: inline-flex; align-items: center; gap: .5rem; }
    .writer-delete-form .input { width: auto; min-width: 12rem; }
  </style>
@endpush

@section('content')
  <header class="page__header">
    <div class="page__headline">
      <h1 class="page__title">Penulis</h1>
      <p class="page__description">Kelola profil dan akses Writer untuk artikel serta halaman.</p>
    </div>
    <div class="page__actions">
      <a class="button button--primary" href="{{ route('cms.writers.create') }}">Tambah Writer</a>
    </div>
  </header>

  <div class="page__body">
    <section class="page__section">
      <div class="card">
        <div class="table-responsive">
          <table class="table table--hover table--align-middle">
            <thead class="table__head--alt">
              <tr>
                <th>Profil</th>
                <th>Email</th>
                <th>URL Publik</th>
                <th>Artikel</th>
                <th class="text-end">Aksi</th>
              </tr>
            </thead>
            <tbody>
              @forelse($writers as $writer)
                <tr>
                  <td>
                    <div class="flex items-center gap-3">
                      <div class="writer-avatar">
                        @if($writer->avatar)
                          <img src="{{ $writer->avatar }}" alt="" loading="lazy" />
                        @else
                          <span>{{ strtoupper(substr($writer->name, 0, 1)) }}</span>
                        @endif
                      </div>
                      <div>
                        <div class="font-bold">{{ $writer->name }}</div>
                        <span class="badge badge--light">WRITER</span>
                      </div>
                    </div>
                  </td>
                  <td>{{ $writer->email }}</td>
                  <td>
                    <a href="{{ route('blog.author', ['slug' => $writer->slug]) }}" target="_blank" rel="noopener">
                      /blog/penulis/{{ $writer->slug }}
                    </a>
                  </td>
                  <td>{{ number_format($writer->posts_count) }}</td>
                  <td class="text-end">
                    <div class="inline-flex flex-wrap justify-end gap-2">
                      <a class="button button--sm button--outline button--neutral" href="{{ route('cms.writers.edit', $writer) }}">Edit</a>
                      <form class="writer-delete-form" method="post" action="{{ route('cms.writers.destroy', $writer) }}" onsubmit="return confirm('Hapus Writer dan alihkan semua artikelnya?')">
                        @csrf
                        @method('DELETE')
                        <select class="input" name="replacement_author_id" required aria-label="Alihkan artikel ke">
                          @foreach($replacementAuthors->where('id', '!=', $writer->id) as $author)
                            <option value="{{ $author->id }}">{{ $author->name }} ({{ $author->role }})</option>
                          @endforeach
                        </select>
                        <button class="button button--sm button--outline button--danger" type="submit">Hapus</button>
                      </form>
                    </div>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="5" class="text-center text-muted-foreground">Belum ada akun Writer.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
        @if($writers->hasPages())
          <div class="card__footer">{{ $writers->links() }}</div>
        @endif
      </div>
    </section>
  </div>
@endsection
