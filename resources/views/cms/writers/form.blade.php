@extends('layouts.meridian')

@section('title', $writer ? 'Edit Writer' : 'Tambah Writer')

@section('content')
  <header class="page__header">
    <div class="page__headline">
      <h1 class="page__title">{{ $writer ? 'Edit Writer' : 'Tambah Writer' }}</h1>
      <p class="page__description">Akun Writer hanya dapat mengelola post, halaman, dan melihat Media Library.</p>
    </div>
  </header>

  <div class="page__body">
    <section class="page__section">
      <form class="card" method="post" action="{{ $writer ? route('cms.writers.update', $writer) : route('cms.writers.store') }}">
        @csrf
        @if($writer) @method('PATCH') @endif
        <div class="card__header">
          <span class="card__title">Profil Writer</span>
          <span class="badge badge--primary">WRITER</span>
        </div>
        <div class="card__body grid grid-cols-12 gap-4">
          <div class="form-group col-span-12 md:col-span-6">
            <label class="form-label" for="name">Nama</label>
            <input id="name" class="input" name="name" value="{{ old('name', $writer?->name) }}" required />
            @error('name')<p class="form-error">{{ $message }}</p>@enderror
          </div>
          <div class="form-group col-span-12 md:col-span-6">
            <label class="form-label" for="email">Email Login</label>
            <input id="email" class="input" type="email" name="email" value="{{ old('email', $writer?->email) }}" required />
            @error('email')<p class="form-error">{{ $message }}</p>@enderror
          </div>
          <div class="form-group col-span-12">
            <label class="form-label" for="bio">Bio Penulis</label>
            <textarea id="bio" class="input min-h-32" name="bio" maxlength="600">{{ old('bio', $writer?->bio) }}</textarea>
            @error('bio')<p class="form-error">{{ $message }}</p>@enderror
          </div>
          <div class="form-group col-span-12">
            <label class="form-label" for="avatar">URL Avatar dari Media Library</label>
            <div class="flex flex-wrap items-center gap-3">
              <input id="avatar" class="input flex-1" name="avatar" value="{{ old('avatar', $writer?->avatar) }}" placeholder="/storage/uploads/avatar.webp" data-writer-avatar />
              <button class="button button--outline button--neutral" type="button" data-open-writer-media>Pilih Media</button>
            </div>
            @error('avatar')<p class="form-error">{{ $message }}</p>@enderror
          </div>
          <div class="form-group col-span-12 md:col-span-6">
            <label class="form-label" for="password">{{ $writer ? 'Password Baru (opsional)' : 'Password' }}</label>
            <input id="password" class="input" type="password" name="password" autocomplete="new-password" @required(!$writer) />
            @error('password')<p class="form-error">{{ $message }}</p>@enderror
          </div>
          <div class="form-group col-span-12 md:col-span-6">
            <label class="form-label" for="password_confirmation">Konfirmasi Password</label>
            <input id="password_confirmation" class="input" type="password" name="password_confirmation" autocomplete="new-password" @required(!$writer) />
          </div>
        </div>
        <div class="card__footer">
          <a class="button button--outline button--neutral" href="{{ route('cms.writers.index') }}">Batal</a>
          <button class="button button--primary ms-auto" type="submit">Simpan Writer</button>
        </div>
      </form>
    </section>
  </div>

  <dialog class="card" data-writer-media-dialog style="width:min(52rem,calc(100% - 2rem));max-height:85vh">
    <div class="card__header flex items-center justify-between">
      <span class="card__title">Pilih Avatar</span>
      <button class="button button--outline button--neutral" type="button" data-close-writer-media>Tutup</button>
    </div>
    <div class="card__body">
      <p class="text-sm text-muted-foreground" data-writer-media-status>Memuat gambar...</p>
      <div class="grid grid-cols-2 gap-3 md:grid-cols-4" data-writer-media-grid></div>
    </div>
  </dialog>
@endsection

@push('scripts')
  <script>
    (function () {
      var dialog = document.querySelector('[data-writer-media-dialog]');
      var grid = document.querySelector('[data-writer-media-grid]');
      var status = document.querySelector('[data-writer-media-status]');
      var avatar = document.querySelector('[data-writer-avatar]');
      var loaded = false;

      document.querySelector('[data-open-writer-media]')?.addEventListener('click', function () {
        dialog.showModal();
        if (loaded) return;
        fetch(@json(route('cms.media-picker')) + '?per_page=48', { headers: { Accept: 'application/json' } })
          .then(function (response) { if (!response.ok) throw new Error(); return response.json(); })
          .then(function (payload) {
            loaded = true;
            status.textContent = payload.media.length ? 'Pilih gambar untuk avatar.' : 'Belum ada gambar.';
            payload.media.forEach(function (item) {
              var button = document.createElement('button');
              button.type = 'button';
              button.className = 'card overflow-hidden text-left';
              button.innerHTML = '<img style="width:100%;aspect-ratio:1;object-fit:cover" alt=""><span class="block truncate p-2 text-xs"></span>';
              button.querySelector('img').src = item.preview_url || item.url;
              button.querySelector('span').textContent = item.filename;
              button.addEventListener('click', function () {
                avatar.value = item.url;
                dialog.close();
              });
              grid.appendChild(button);
            });
          })
          .catch(function () { status.textContent = 'Media Library gagal dimuat.'; });
      });
      document.querySelector('[data-close-writer-media]')?.addEventListener('click', function () { dialog.close(); });
    })();
  </script>
@endpush
