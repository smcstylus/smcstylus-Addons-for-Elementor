<?php
namespace SMCstylus_Elementor;
// No access of directly access.
defined( 'ABSPATH' ) ||	exit; 

/**
 * SMCstylus Addons For Elementor - Autoloader.
 * Description: Handles dynamically loading classes only when needed.
 *
 * @since 1.0.0
 * @package SMCstylus Addons For Elementor
 */
class File_Handler {
	/**
	 * Run autoloader.
	 * Register a function as `__autoload()` implementation.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	
	public static function run() {
		spl_autoload_register( [ __CLASS__, 'autoload' ] );
    }
    
  /**
	 * Autoload.
	 * For a given class, check if it exist and load it.
	 *
	 * @since 1.0.0
	 * @access private
	 * @param string $class Class name.
	 */
	private static function autoload( $class_name ) {
        // Check to include only classes starting with main __NAMESPACE__
        if ( 0 !== strpos( $class_name, __NAMESPACE__ ) ) {
            return;
        }
				 
				// Transform __Namespace__ in path and class name in file
        $file = SMC_ADDEL_PATH . strtolower(
            preg_replace(
                [ '/\b'.__NAMESPACE__.'\\\/', '/([0-9])([a-z])([A-Z])/', '/_/', '/\\\/' ],
                [ '', '$1-$2', '-', DIRECTORY_SEPARATOR],
                $class_name
            )
        ) . '.php';

        // Include the file if existI
       if ( file_exists( $file ) ) {
            include_once ( $file );
        }
    }
		
		/** 
		 *  Find files in given folder and subfolders 
		 *  
		 * @since 1.0.0
	 	 * @access public
	 	 * @param string $dir path to search
	 	 * @param string $extension file extension to search
	 	 * @param string $sufix end file name to match
	 	 */
		public static function include_filtered_file($dir, $extension='php', $sufix='-config') {
			$ls = new \DirectoryIterator($dir);  
			$ds = DIRECTORY_SEPARATOR; 
			
			// Director object list 
			foreach ($ls as $item) {
					if (!$item->isDot()) {
						if ($item->isDir()) {
							 // Search subfolders
							 File_Handler::include_filtered_file("$dir" . $ds . "$item");
						} else {
							 // Search files
							$ext = $item->getExtension();
							$basename = $item->getBasename(".$ext");
							// Check if the file is *.php and ending in -config
							if($ext === $extension && $sufix === substr($basename,-7)){
								require_once $dir . $ds.  $item->getFilename();
							}
						}
					}
			 }
		}
		
		/**
		 * Check if file exist and include_once
		 * 
		 * @since 1.0.0
	 	 * @access public
	 	 * @param string $file file path.
	 	 */
		public static function loadFile($file){
			if (file_exists($file)) {
				include_once $file;
			}
		}
		
		/**
     * Generate safe path
     *
     * @since v1.0.0
	 	 * @access public
	 	 * @param string $path file path.
     */
    public static function safe_path($path) {
        $path = str_replace(['//', '\\\\'], ['/', '\\'], $path);

        return str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
    }

    /**
     * Generate safe url
     *
     * @since v1.0.0
	 	 * @access public
	 	 * @param string $url file path.
     */
    public static function safe_url($url) {
        if (is_ssl()) {
            $url = wp_parse_url($url);

            if (!empty($url['host'])) {
                $url['scheme'] = 'https';
            }

            return self::unparse_url($url);
        }

        return $url;
    }

		/**
     * Unparse url
     *
     * @since v1.0.0
	 	 * @access public
	 	 * @param string $parsed_url file path.
     */
    public static function unparse_url($parsed_url){
        $scheme = isset($parsed_url['scheme']) ? $parsed_url['scheme'] . '://' : '';
        $host = isset($parsed_url['host']) ? $parsed_url['host'] : '';
        $port = isset($parsed_url['port']) ? ':' . $parsed_url['port'] : '';
        $user = isset($parsed_url['user']) ? $parsed_url['user'] : '';
        $pass = isset($parsed_url['pass']) ? ':' . $parsed_url['pass'] : '';
        $pass = ($user || $pass) ? "$pass@" : '';
        $path = isset($parsed_url['path']) ? $parsed_url['path'] : '';
        $query = isset($parsed_url['query']) ? '?' . $parsed_url['query'] : '';
        $fragment = isset($parsed_url['fragment']) ? '#' . $parsed_url['fragment'] : '';

        return "$scheme$user$pass$host$port$path$query$fragment";
    }
}
