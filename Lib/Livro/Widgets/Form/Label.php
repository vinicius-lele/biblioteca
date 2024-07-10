<?php
namespace Livro\Widgets\Form;

use Livro\Widgets\Base\Element;

class Label extends Field implements FormElementInterface
{
    private $tag;
    
    public function __construct($value)
    {
        $this->setValue($value);
        
        $this->tag = new Element('label');
    }
    
    public function add($child)
    {
        $this->tag->add($child);
    }
    
    public function show()
    {
        $this->tag->add($this->value);
        $this->tag->show();
    }
}
