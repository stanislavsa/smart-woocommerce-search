const path = require('path')
const webpack = require('webpack')

module.exports = {
  entry: { element: './smartSearch/index.js' },
  resolve: {
    fallback: {
      util: require.resolve('util/'),
      process: require.resolve('process/browser')
    }
  },
  optimization: {
    moduleIds: 'named',
    chunkIds: 'named'
  },
  plugins: [
    new webpack.ProvidePlugin({ process: 'process/browser' })
  ],
  output: {
    path: path.resolve(__dirname, 'public/dist'),
    filename: 'element.bundle.js',
    // VCWB loads element bundles through its shared JSONP runtime. A
    // standalone IIFE executes before that runtime has registered vcCake's
    // `api` service, resulting in `elementComponent` being undefined.
    chunkLoadingGlobal: 'vcvWebpackJsonp4x',
    chunkFormat: 'array-push'
  },
  module: {
    rules: [
      {
        test: /\.js$/,
        exclude: /node_modules/,
        use: {
          loader: 'babel-loader',
          options: { presets: ['@babel/preset-env', '@babel/preset-react'] }
        }
      },
      { test: /\.css$/, use: ['style-loader', 'css-loader'] }
    ]
  }
}
