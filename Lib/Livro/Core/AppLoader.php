<?php
namespace Livro\Core;

use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

class AppLoader
{
    protected $directories;
    
    public function addDirectory($directory)
    {
        $this->directories[] = $directory;
    }
    
    public function register()
    {
        spl_autoload_register(array($this, 'loadClass'));
    }
    
    public function loadClass($class)
    {
        $folders = $this->directories;
        
        foreach ($folders as $folder)
        {
            if (file_exists("{$folder}/{$class}.php"))
            {
                require_once "{$folder}/{$class}.php";
                return TRUE;
            }
            else
            {
                if (file_exists($folder))
                {
                    foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($folder),
                                                           RecursiveIteratorIterator::SELF_FIRST) as $entry)
                    {
                        if (is_dir($entry))
                        {
                            if (file_exists("{$entry}/{$class}.php"))
                            {
                                require_once "{$entry}/{$class}.php";
                                return TRUE;
                            }
                        }
                    }
                }
            }
        }
    }
}
