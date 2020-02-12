const externals = {
  wp: "wp",
  react: "React",
  "react-dom": "ReactDOM"
};

const isProduction = process.env.NODE_ENV === "production";
const mode = isProduction ? "production" : "development";

// const MiniCssExtractPlugin = require("mini-css-extract-plugin");

module.exports = {
  mode,
  entry: {
    block: "./assets/src/block.js"
  },
  output: {
    path: __dirname + "/assets/dist/",
    filename: "[name].build.js"
  },
  externals,
  module: {
    rules: [
      {
        test: /\.js$/,
        exclude: /node_modules/,
        use: {
          loader: "babel-loader",
          options: {
            cacheDirectory: true,
            babelrc: false,
            presets: ["@babel/preset-env", "@babel/preset-react"]
          }
        }
      } //,
      // {
      //   test: /\.scss$/,
      //   exclude: /node_modules/,
      //   use: [
      //     MiniCssExtractPlugin.loader,
      //     "css-loader",
      //     {
      //       loader: "sass-loader",
      //       options: {
      //         outputStyle: "compressed"
      //       }
      //     }
      //   ]
      // }
    ]
  } //,
  // plugins: [
  //   new MiniCssExtractPlugin({
  //     filename: "[name].build.css",
  //     chunkFilename: "[id].css"
  //   })
  // ]
};
