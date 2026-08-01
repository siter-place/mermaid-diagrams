const defaultConfig = require('@wordpress/scripts/config/webpack.config');
const path = require('path');

module.exports = {
  ...defaultConfig,
  entry: {
    ...defaultConfig.entry(),
    'admin/library/index': './assets/src/apps/diagram-library/index.tsx',
    'admin/settings/index': './assets/src/apps/settings/index.tsx',
  },
  output: {
    ...defaultConfig.output,
    path: path.resolve(__dirname, 'build'),
    filename: '[name].js',
  },
};
