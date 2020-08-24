var Encore = require('@symfony/webpack-encore');

Encore
    .setOutputPath('src/Resources/public/js/')
    .addEntry('contao-alert-reminder-bundle', './src/Resources/assets/js/contao-alert-reminder-bundle.js')
    .setPublicPath('/public/js/')
    .disableSingleRuntimeChunk()
    .enableSassLoader()
    .configureBabel(function (babelConfig) {
    }, {
        // include to babel processing
        includeNodeModules: ['@hundh/contao-alert-reminder-bundle']
    })
    .enableSourceMaps(!Encore.isProduction())
;

module.exports = Encore.getWebpackConfig();
