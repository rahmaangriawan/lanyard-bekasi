/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/views/**/*.blade.php",
  ],
  darkMode: "class",
  theme: {
    fontFamily: {
      sans: ['Geist', 'ui-sans-serif', 'system-ui', '-apple-system', 'sans-serif'],
    },
    extend: {
      colors: {
        slate: {
          '50':  '#FAFAFA',  '100': '#EFF0F1', '200': '#DDE0E3',
          '300': '#CBD0D7',  '400': '#98A8BA', '500': '#6E819A',
          '600': '#536B86',  '700': '#576E8F', '800': '#3D4E67',
          '900': '#253141',  '950': '#161D27',
        },
        primary: {
          DEFAULT:  '#26374f',
          container: '#3d4e67',
          fixed:     '#d4e3ff',
        },
        secondary: {
          DEFAULT:  '#4e6078',
          container: '#cfe1fd',
        },
        surface: {
          DEFAULT:           '#f8f9ff',
          container:         '#e6eeff',
          'container-low':   '#eff3ff',
          'container-high':  '#dde9ff',
          'container-highest':'#d3e3ff',
        },
        outline: {
          DEFAULT: '#74777e',
          variant: '#c4c6ce',
        },
      },
      borderRadius: {
        'DEFAULT': '0.5rem',
        'lg': '0.75rem',
        'xl': '1rem',
        '2xl': '1.25rem',
        '3xl': '1.5rem',
        'full': '9999px',
      },
      spacing: {
        'gutter': '24px',
        'container-max': '1280px',
      },
      fontSize: {
        'display-lg':  ['48px', { lineHeight: '56px', letterSpacing: '-0.02em', fontWeight: '700' }],
        'display-lg-mobile': ['32px', { lineHeight: '40px', letterSpacing: '-0.01em', fontWeight: '700' }],
        'headline-md': ['24px', { lineHeight: '32px', fontWeight: '600' }],
        'body-lg':     ['18px', { lineHeight: '28px', fontWeight: '400' }],
        'body-md':     ['16px', { lineHeight: '24px', fontWeight: '400' }],
        'label-bold':  ['14px', { lineHeight: '20px', letterSpacing: '0.02em', fontWeight: '600' }],
        'label-sm':    ['12px', { lineHeight: '16px', fontWeight: '500' }],
      },
    },
  },
  plugins: [],
}
