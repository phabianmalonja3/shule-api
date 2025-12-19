const mix = require('laravel-mix');
const purgecss = require('@fullhuman/postcss-purgecss');

mix.js('resources/js/app.js', 'public/js')
   .sass('resources/sass/app.scss', 'public/css')
   .options({
      postCss: [
        purgecss({
          content: [
            './resources/views/**/*.blade.php',  // Blade templates
            './resources/js/**/*.vue',           // Vue.js components (if any)
            './resources/js/**/*.js',            // JavaScript files
            './resources/css/**/*.css'           // CSS files
          ],
          defaultExtractor: content => content.match(/[A-Za-z0-9-_:/]+/g) || []
        })
      ]
   })
   .version();
