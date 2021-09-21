const mix = require('laravel-mix')
const path = require('path')

const { exec } = require('child_process')

mix.extend(
  'ziggy',
  new (class {
    register (config = {}) {
      this.watch = config.watch ?? ['routes/**/*.php']
      this.path = config.path ?? ''
      // eslint-disable-next-line no-undef
      this.enabled = config.enabled ?? !Mix.inProduction()
    }

    boot () {
      if (!this.enabled) return

      const command = () =>
        exec(
          `php artisan ziggy:generate ${this.path}`,
          (_error, stdout, stderr) => console.log(stdout)
        )

      command()

      // eslint-disable-next-line no-undef
      if (Mix.isWatching() && this.watch) {
        require('chokidar')
          .watch(this.watch)
          .on('change', path => {
            console.log(`${path} changed...`)
            command()
          })
      }
    }
  })()
)

require('dotenv').config()
require('vuetifyjs-mix-extension')
require('laravel-mix-eslint')
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
mix
  .alias({
    '@': path.resolve('resources/js'),
    ziggy: path.resolve('vendor/tightenco/ziggy/dist/vue')
  })
  .js('resources/js/app.js', 'public/js')
  // .sass('resources/sass/app.scss', 'public/css')
  .eslint({
    // fix: true,
    extensions: ['js', 'vue'],
    exclude: ['/node_modules/*', '/resources/js/ziggy.js']
    // ...
  })
  .vuetify('vuetify-loader')
  .vue()
  .disableNotifications()

if (mix.inProduction()) {
  mix.version()
}
