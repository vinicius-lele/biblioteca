<?php
namespace Livro\Control;

use Livro\Widgets\Base\Element;

class Page extends Element
{
    public function __construct()
    {
        parent::__construct('div');
    }
    
    public function show()
    {
        if ($_GET)
        {
            $class  = isset($_GET['class'])  ? $_GET['class']  : '';
            $method = isset($_GET['method']) ? $_GET['method'] : '';
            
            if ($class)
            {
                $object = $class == get_class($this) ? $this : new $class;
                if (method_exists($object, $method))
                {
                    call_user_func(array($object, $method), $_GET);
                }
            }
        }
        
        parent::show();
    }
}
