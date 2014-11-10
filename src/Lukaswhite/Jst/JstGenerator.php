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
		$dir = base_path() . Config::get('jst::source_dir');

		if (!file_exists($dir)) {
			throw new \Exception('The source directory does not exist. Please check your configuration.');
		}

		//JST
		$jstdir = $dir.'/jst';
		$finder = new Finder;
		$files = iterator_to_array($finder->files()->in($jstdir), false);

		$template_func = '_.template';
		$js = '';
		$js .= "var JST = JST || {};\n";

		if (count($files)) {
			foreach ($files as $file) {
				$contents = str_replace(array("\n","'"), array('',"\'"), $file->getContents());
				
				$contents = preg_replace('!\s+!', ' ', $contents);
				
				$ext = pathinfo($file->getRelativePathname(), PATHINFO_EXTENSION);
				
				$topath = str_replace($jstdir, '', $file->getRelativePathname());
				$topath = preg_replace("/\\.[^.\\s]{3,4}$/", "", $topath);
				$js .= sprintf("JST['%s'] = %s('%s');\n", $topath, $template_func, $contents);
			}
		}

		$output_filename = base_path() . Config::get('jst::dest_dir') . '/jst.js'; 
		
		if (!file_put_contents($output_filename, $js)) {
			throw new \Exception("Could not write JST file to $output_filename. Check the permissions, perhaps?");
		}
		
		//BM
		$bmdir = $dir.'/bm';
		$finder = new Finder;
		$files = iterator_to_array($finder->files()->in($bmdir), false);

		$js = '';
		$js .= "var CAFBM = {};\n";
		
		if (count($files)) {
			foreach ($files as $file) {
				$contents = str_replace(array("\n","'"), array('',"\'"), $file->getContents());
				
				$contents = preg_replace('!\s+!', ' ', $contents);
				
				$ext = pathinfo($file->getRelativePathname(), PATHINFO_EXTENSION);
				
				$topath = str_replace($bmdir, '', $file->getRelativePathname());
				$topath = preg_replace("/\\.[^.\\s]{3,4}$/", "", $topath);
				if($ext == 'html'){
					$js .= sprintf("CAFBM.%s = '%s';\n", $topath, $contents);
				}elseif($ext == 'css'){
					$js .= sprintf("CAFBM.style = '%s';\n", $contents);
				}
			}
		}

		$output_filename = base_path() . Config::get('jst::dest_dir') . '/bm.js';
		
		if (!file_put_contents($output_filename, $js)) {
			throw new \Exception("Could not write JST file to $output_filename. Check the permissions, perhaps?");
		}

	}

}