import replace from '@rollup/plugin-replace';
import resolve from '@rollup/plugin-node-resolve';
import { terser } from 'rollup-plugin-terser';

let getConfig = function(handle, output, replace, terser) {
	return {
		input: 'assets/src/' + handle + '.js',
		output: {
			file: 'assets/dist/' + output + '.js',
			format: 'iife'
		},
		plugins: [
			resolve(),
			replace,
			terser
		]
	};
};

export default ['searchwp-modal-form'].map(function(bundle){
	return [
		// Development version.
		getConfig(bundle, bundle, replace({
			'process.env.NODE_ENV': JSON.stringify('development')
		}), null),
		// Production version.
		getConfig(bundle, bundle + '.min', replace({
			'process.env.NODE_ENV': JSON.stringify('production')
		}), terser())
	];
}).reduce((a, b) => a.concat(b), []);
