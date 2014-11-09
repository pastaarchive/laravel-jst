<?php namespace Lukaswhite\Jst;

use Config;
use Symfony\Component\Finder\Finder;


/**
 * JST Generator class.
 *
 * Generates a JST file from separate template files.  Designed for use with Backbone.
 */
class JstGenerator {

	/**
	 * Run the generate process.
	 */
	public static function run()
	{
		$finder = new Finder;

		$dir = base_path() . Config::get('jst::source_dir');

		if (!file_exists($dir)) {
			throw new \Exception('The source directory does not exist. Please check your configuration.');
		}

		//JST
		$files = iterator_to_array($finder->files()->in($dir.'/jst'), false);

		$template_func = '_.template';
		$js = '';
		$js .= "var JST = JST || {};\n";

		if (count($files)) {
			foreach ($files as $file) {
				$contents = str_replace(array("\n","'"), array('',"\'"), $file->getContents());
				
				$contents = preg_replace('!\s+!', ' ', $contents);
				
				$ext = pathinfo($file->getRelativePathname(), PATHINFO_EXTENSION);
				
				$topath = preg_replace("/\\.[^.\\s]{3,4}$/", "", $file->getRelativePathname());
				$topath = str_replace('jst/', '', $topath);
				$js .= sprintf("JST['%s%s'] = %s('%s');\n", Config::get('jst::source_prefix'), $topath, $template_func, $contents);
			}
		}

		$output_filename = base_path() . Config::get('jst::dest_dir') . '/jst.js';
		
		if (!file_put_contents($output_filename, $js)) {
			throw new \Exception("Could not write JST file to $output_filename. Check the permissions, perhaps?");
		}
		
		//BN
		$files = iterator_to_array($finder->files()->in($dir.'/bm'), false);

		$js = '';
		$js .= "var CAFBN = {};\n";
		
		if (count($files)) {
			foreach ($files as $file) {
				$contents = str_replace(array("\n","'"), array('',"\'"), $file->getContents());
				
				$contents = preg_replace('!\s+!', ' ', $contents);
				
				$ext = pathinfo($file->getRelativePathname(), PATHINFO_EXTENSION);
				
				$topath = preg_replace("/\\.[^.\\s]{3,4}$/", "", $file->getRelativePathname());
				$topath = str_replace('bm/', '', $topath);
				if($ext == 'html'){
					$js .= sprintf("CAFBN.%s = '%s');\n", $topath, $contents);
				}elseif($text == 'css'){
					$js .= sprintf("CAFBN.style = '%s');\n", $contents);
				}
			}
		}

		$output_filename = base_path() . Config::get('jst::dest_dir') . '/bn.js';
		
		if (!file_put_contents($output_filename, $js)) {
			throw new \Exception("Could not write JST file to $output_filename. Check the permissions, perhaps?");
		}

	}

}
