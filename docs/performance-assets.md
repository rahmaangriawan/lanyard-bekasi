# Performance Assets

## Build aset publik

Jalankan:

```powershell
npm run build:public
```

Perintah tersebut menghasilkan empat file:

- `public/build/public/site.min.css`
- `public/build/public/site.min.js`
- `public/build/public/home.min.css`
- `public/build/public/home.min.js`

`npm run build` otomatis menjalankan build aset publik sebelum build Vite.
Daftar input dan urutan bundling berada di `scripts/build-public-assets.mjs`.

## Kebijakan gambar

- Hero, logo header, dan gambar LCP memakai `loading="eager"`.
- Gambar di luar area awal memakai `loading="lazy"` dan `decoding="async"`.
- Upload JPG/PNG dari Media Library dikonversi ke WebP kualitas 82.
- Upload WebP, GIF, dan PDF tetap disimpan dalam format sumber.
- Media gambar tetap menghasilkan thumbnail WebP dan varian responsif 320/640.

## Font

Geist dan Inter disimpan lokal, memakai WOFF2 serta `font-display: swap`.
Material Symbols juga dinormalisasi menjadi `font-display: swap` saat build.
