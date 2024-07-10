<?php
namespace Livro\Widgets\Container;

use Livro\Widgets\Base\Element;

class VBox extends Element
{
    public function __construct()
    {
        parent::__construct('div');
        $this->{'style'} = 'display: inline-block';
    }
    
    public function add($child)
    {
        $wrapper = new Element('div');
        $wrapper->{'style'} = 'clear:both';
        $wrapper->add($child);
        parent::add($wrapper);
        return $wrapper;
    }
}
