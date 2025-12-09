const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

// Main app assets
mix.js('resources/js/app.js', 'public/js')
    .postCss('resources/css/app.css', 'public/css', [
        //
    ]);

// QR Scanner with ZXing
mix.js('resources/js/scan.js', 'public/js')
    .sourceMaps();

// Disable Mix success notifications
mix.disableNotifications();

// Version assets in production
if (mix.inProduction()) {
    mix.version();
}
