const mix = require('laravel-mix');
const webpackConfig = require('./webpack.config');

mix.js('resources/vue/main.js', 'public/js')
    .version()
    .sourceMaps()
    .webpackConfig(webpackConfig);