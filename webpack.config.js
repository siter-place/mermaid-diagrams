const defaultConfig = require('@wordpress/scripts/config/webpack.config');
const path = require('path');
const CopyPlugin = require('copy-webpack-plugin');

const plugins = defaultConfig.plugins.map( ( plugin ) => {
  if (
    plugin &&
    plugin.constructor &&
    plugin.constructor.name === 'DependencyExtractionWebpackPlugin'
  ) {
    const DependencyExtractionWebpackPlugin = plugin.constructor;

    return new DependencyExtractionWebpackPlugin( {
      ...plugin.options,
      requestToExternal( request ) {
        if (
          request === 'lodash' ||
          request.startsWith( 'lodash/' ) ||
          request.startsWith( 'lodash-es' ) ||
          request.includes( 'lodash' )
        ) {
          return null;
        }
        if ( request.endsWith( '.css' ) ) {
          return null;
        }
        return undefined;
      },
    } );
  }
  return plugin;
} );

module.exports = {
  ...defaultConfig,
  plugins: [
    ...plugins,
    new CopyPlugin( {
      patterns: [
        {
          from: path.resolve( __dirname, 'node_modules/@wordpress/dataviews/build-style/style.css' ),
          to: path.resolve( __dirname, 'build/vendor/dataviews.css' ),
        },
      ],
    } ),
  ],
  entry: {
    ...defaultConfig.entry(),
    'admin/library/index': './assets/src/apps/diagram-library/index.tsx',
    'admin/settings/index': './assets/src/apps/settings/index.tsx',
  },
  resolve: {
    ...defaultConfig.resolve,
    alias: {
      ...( defaultConfig.resolve?.alias || {} ),
      'lodash-es': path.resolve( __dirname, 'node_modules/lodash' ),
      '@mdm/mermaid-runtime': path.resolve( __dirname, 'packages/mermaid-runtime/src' ),
      [ path.resolve( __dirname, 'packages/mermaid-runtime/src/init' ) ]: path.resolve(
        __dirname,
        'packages/mermaid-runtime/src/init-browser'
      ),
      [ path.resolve( __dirname, 'packages/mermaid-runtime/src/init.ts' ) ]: path.resolve(
        __dirname,
        'packages/mermaid-runtime/src/init-browser.ts'
      ),
    },
    fallback: {
      ...( defaultConfig.resolve?.fallback || {} ),
      fs: false,
      net: false,
      tls: false,
      child_process: false,
      http: false,
      https: false,
      zlib: false,
      stream: false,
      crypto: false,
      os: false,
      vm: false,
      url: false,
      canvas: false,
    },
  },
  output: {
    ...defaultConfig.output,
    path: path.resolve(__dirname, 'build'),
    filename: '[name].js',
  },
};
