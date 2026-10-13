/** @type {import('tailwindcss').Config} */

// Frequency Strategy Group palette — sampled from the logo
// (public/images/logo/logo.webp): brushed gold on near-black.
const gold = {
  '50': '#fbf7f1',
  '100': '#f5ecdd',
  '200': '#eadbbf',
  '300': '#e0c190', // logo highlight
  '400': '#d5ad7c',
  '500': '#ca9c6a', // logo base gold
  '600': '#b2804f',
  '700': '#8e6a45', // logo shadow
  '800': '#5d4327', // logo deep bronze
  '900': '#3f2d1a',
  '950': '#241a0f',
};

// Warm near-black for dark surfaces and text (the logo's background)
const ink = {
  '50': '#f6f5f3',
  '100': '#e9e6e1',
  '200': '#d4cfc6',
  '300': '#b9b1a4',
  '400': '#958b7c',
  '500': '#766d60',
  '600': '#5c544a',
  '700': '#423c35',
  '800': '#2a2622',
  '900': '#171512',
  '950': '#0b0a08', // logo background
};

module.exports = {
  content: [
    './resources/views/**/*.php',
    './resources/js/**/*.js',
    './src/**/*.php',
    './index.php',
  ],
  safelist: [
    // General utility classes that might be dynamically generated or used in the app
    'flex', 'hidden', 'block', 'inline-block', 'grid', 'gap-4', 'p-4', 'm-4',
    // Backgrounds
    'bg-blue-500/10',
    'bg-emerald-500/10',
    'bg-orange-500/10',
    'bg-amber-500/10',
    // Text colors
    'text-blue-500',
    'text-emerald-500',
    'text-orange-500',
    'text-amber-500',
  ],
  theme: {
    extend: {
      fontFamily: {
        'sans': ['Manrope', 'ui-sans-serif', 'system-ui', 'sans-serif'],
        // Display serif for headlines on the public site
        'serif': ['"Cormorant Garamond"', 'ui-serif', 'Georgia', 'serif'],
      },
      // App-wide type scale — each size sits about half a step above
      // Tailwind's default (e.g. text-sm 14px -> 15px, text-base 16px ->
      // 17px) so text reads a little larger on every page, while keeping
      // Tailwind's line-height ratios. Tune these to resize text globally.
      fontSize: {
        'xs':   ['0.8125rem', { lineHeight: '1.125rem' }],  // 13px (default 12)
        'sm':   ['0.9375rem', { lineHeight: '1.375rem' }],  // 15px (default 14)
        'base': ['1.0625rem', { lineHeight: '1.625rem' }],  // 17px (default 16)
        'lg':   ['1.1875rem', { lineHeight: '1.75rem' }],   // 19px (default 18)
        'xl':   ['1.375rem',  { lineHeight: '1.875rem' }],  // 22px (default 20)
        '2xl':  ['1.6875rem', { lineHeight: '2.125rem' }],  // 27px (default 24)
        '3xl':  ['2.0625rem', { lineHeight: '2.5rem' }],    // 33px (default 30)
        '4xl':  ['2.5rem',    { lineHeight: '2.75rem' }],   // 40px (default 36)
        '5xl':  ['3.25rem',   { lineHeight: '1' }],         // 52px (default 48)
      },
      colors: {
        // PRIMARY: brand gold. SECONDARY: brand ink. The admin screens use
        // primary-* / secondary-* throughout, so they pick up the brand too.
        primary: gold,
        secondary: ink,
        gold,
        ink,
        // Paper tone for light sections of the public site
        ivory: {
          DEFAULT: '#faf7f2',
          deep: '#f3ede3',
        },
      },
      letterSpacing: {
        'brand': '0.28em', // the logo's tracked "STRATEGY GROUP"
      },
    },
  },
  plugins: [],
  darkMode: 'class',
}
