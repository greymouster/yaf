<?php
/**
 * Autoloader class file
 *
 * @package Vint\Loader
 * @author vincent <piaoqingbin@yeah.net> 
 * @final 2013-07-01
 */

namespace Vint\Loader;

class Autoloader {

    /**
     * Namespace paths
     *
     * @var array
     */
    protected $namespaces = array();

    /**
     * Register a namespace
     *
     * @param string $namespace
     * @param array|string $paths
     * @return void
     */
    public function registerNamespace($namespace, $paths) {
        $this->namespaces[$namespace] = (array) $paths; 
        return $this;
    }

    /**
     * Autoload function
     *
     * @param string $class
     * @return void
     */
    public function autoload($class) {

        if ('\\' == $class{0})
            $class = substr($class, 1);
    	
        if (false === $pos = strrpos($class, '\\'))
            return;

        $namespace = substr($class, 0, $pos);
        $className = substr($class, $pos + 1);

        foreach ($this->namespaces as $nsKey => $directories) { 
            if (0 !== strpos($namespace, $nsKey)) 
                continue;

            $childNamespace = DIRECTORY_SEPARATOR;
            if (false !== strpos($namespace, '\\')) {
                $childNamespace .= str_replace(
                    '\\', 
                    DIRECTORY_SEPARATOR, 
                    substr($namespace, strlen($nsKey) + 1)
                ).DIRECTORY_SEPARATOR;
            }

            foreach ($directories as $directory) {

                $fullPath = $directory.$childNamespace.$className;
                if (is_file($file = $fullPath.'.php')) {
                    include $file;
                } else if (is_dir($fullPath)) {
                    if (is_file($file = $fullPath.DIRECTORY_SEPARATOR.$className.'.php')) {
                        include $file;
                    }
                }
            } 

        } 

    }

    /**
     * SPL register autoload function
     *
     * @return void
     */
    public function register() {
        spl_autoload_register(array($this, 'autoload'), true); 
    }

}

