const isDev = (process.env.NODE_ENV !== "production");

const path = require("path");
const globImporter = require("node-sass-glob-importer");

const CleanWebpackPlugin = require("clean-webpack-plugin");
const CopyPlugin = require("copy-webpack-plugin");
const FriendlyErrorsWebpackPlugin = require("friendly-errors-webpack-plugin");
const MiniCssExtractPlugin = require("mini-css-extract-plugin");
const FixStyleOnlyEntriesPlugin = require("webpack-fix-style-only-entries");

module.exports = {
  entry: {
    styles: ["./src/scss/styles.scss"],
    bundle: ["./src/js/common.js"],
  },
  watchOptions: {
    aggregateTimeout: 300,
  },
  // Tell us only about the errors.
  stats: 'errors-only',
  // Suppress performance errors.
  performance: {
    hints: false,
    maxEntrypointSize: 512000,
    maxAssetSize: 512000
  }
};
