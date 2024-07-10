<?php
namespace Livro\Widgets\Base;

class Image extends Element
{
    private $source;
    
    public function __construct($source)
    {
        parent::__construct('img');
        
        $this->src = $source;
        $this->border = 0;
    }
}
