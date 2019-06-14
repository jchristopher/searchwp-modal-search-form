/**
 * This is the Parcel bundler for the PRODUCTION version
 */

const Bundler = require('parcel-bundler');
const Path = require('path');

const file = Path.join(__dirname, './src/searchwp-modal-form.js');

const options = {
	outDir: Path.join(__dirname, './dist'),
	outFile: 'searchwp-modal-form.min.js',
	watch: true,
	cache: false,
	minify: true,
	hmr: false
};

const bundler = new Bundler(file, options);

bundler.bundle();
