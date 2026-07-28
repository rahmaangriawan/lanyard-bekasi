import { mkdir, readFile, writeFile } from 'node:fs/promises';
import { dirname, resolve } from 'node:path';
import { transform as transformCss } from 'lightningcss';
import { transform as transformJs } from 'esbuild';

const root = resolve(import.meta.dirname, '..');
const bundles = {
  'public/build/public/site.min.css': [
    'public/css/app.min.css',
    'public/vendor/scroll/scroll-reveal.css',
    'public/css/header.css',
    'public/css/footer.css',
    'public/css/floating-whatsapp.css',
    'public/css/home-cta.css',
    'public/css/button-motion.css',
  ],
  'public/build/public/home.min.css': [
    'public/css/hero.css',
    'public/css/home-pricing.css',
    'public/css/homepage.css',
    'public/css/home-about.css',
    'public/css/home-benefits.css',
    'public/css/home-gallery.css',
    'public/css/home-testimonials.css',
    'public/css/home-faq.css',
    'public/css/home-products.css',
    'public/css/home-locations.css',
  ],
  'public/build/public/site.min.js': [
    'public/js/public-site.js',
    'public/js/button-motion.js',
    'public/js/floating-whatsapp.js',
    'public/vendor/scroll/scroll-reveal.js',
  ],
  'public/build/public/home.min.js': [
    'public/js/home-gallery.js',
    'public/js/home-testimonials.js',
  ],
};

async function concatenate(files) {
  const contents = await Promise.all(
    files.map(async (file) => `/* ${file} */\n${await readFile(resolve(root, file), 'utf8')}`),
  );

  return contents.join('\n');
}

for (const [output, inputs] of Object.entries(bundles)) {
  const target = resolve(root, output);
  const source = (await concatenate(inputs))
    .replace(/font-display\s*:\s*block/gi, 'font-display:swap');
  let result = output.endsWith('.css')
    ? transformCss({ filename: target, code: Buffer.from(source), minify: true }).code
    : Buffer.from((await transformJs(source, {
        loader: 'js',
        minify: true,
        target: 'es2019',
      })).code);

  if (output.endsWith('.css')) {
    result = Buffer.from(
      result.toString().replace(
        /-webkit-backdrop-filter:([^;}]+)/g,
        'backdrop-filter:$1;-webkit-backdrop-filter:$1',
      ),
    );
  }

  await mkdir(dirname(target), { recursive: true });
  await writeFile(target, result);
  console.log(`${output}: ${(result.length / 1024).toFixed(1)} KB`);
}
