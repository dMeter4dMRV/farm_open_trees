const path = require("path");
const glob = require("glob");
const webpack = require('webpack');
const { merge } = require('webpack-merge')

const paths = {
  root: path.resolve(__dirname, './'),
  src: path.resolve(__dirname, './src'),
  build: path.resolve(__dirname, './dist'),
  public: path.resolve(__dirname, './public')
}

const prod = {
  mode: "production",
  devtool: false,
  performance: {
    hints: false
  }
}

const dev = {
  mode: "development",
  devtool: 'eval-cheap-module-source-map',
  devServer: {
    historyApiFallback: true,
    static: paths.public,
    open: true,
    compress: true,
    hot: true,
    port: 3002,
  }
}

const common = {
  entry: {
    "./scripts" : glob.sync("./src/*.js")
  },
  output: {
    path: paths.build,
    publicPath: "/dist/",
    filename: "[name].min.js",
    chunkFilename: "chunks/[name].min.js",
    clean: true
  },
  plugins: [
    new webpack.ProvidePlugin({
      process: 'process',
    }),
    new webpack.ProvidePlugin({
      Buffer: ['buffer', 'Buffer'],
    })
  ],
  resolve: {
    alias: {
      '@': paths.root
    },
    fallback: {
      assert: require.resolve('assert'),
      crypto: require.resolve('crypto-browserify'),
      http: require.resolve('stream-http'),
      https: require.resolve('https-browserify'),
      os: require.resolve('os-browserify/browser'),
      stream: require.resolve('stream-browserify'),
    }
  },
}

module.exports = (cmd) => {
  const production = cmd.production
  const config = production ? prod : dev
  return merge(common, config)
}
