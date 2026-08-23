import oklabFunction from '@csstools/postcss-oklab-function';
import colorMixFunction from '@csstools/postcss-color-mix-function';
import autoprefixer from 'autoprefixer';

// Tailwind v4 / DaisyUI compile colors as oklch()/oklab() and color-mix() by
// default, which assumes browsers no older than ~2023 (Chrome 111+, Firefox
// 113+, Safari 16.4+). These two plugins rewrite each declaration into a
// computed sRGB fallback *before* the modern one, so older browsers use the
// fallback (they simply don't understand the later line and skip it) while
// modern browsers still get the original wide-gamut value — see
// resources/css/app.css's top for context on why this file exists.
export default {
    plugins: [
        oklabFunction({ preserve: true, subFeatures: { displayP3: false } }),
        colorMixFunction({ preserve: true }),
        autoprefixer(),
    ],
};
