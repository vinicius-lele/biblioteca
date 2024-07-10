<?php
namespace Livro\Widgets\Form;

use Livro\Widgets\Base\Element;

class Hidden extends Field implements FormElementInterface
{
    protected $properties;
    
    public function show()
    {
        $tag = new Element('input');
        $tag->class = 'field';		  
        $tag->name = $this->name;     
        $tag->value = $this->value;   
        $tag->type = 'hidden';        
        $tag->style = "width:{$this->size}";
        
        if ($this->properties)
        {
            foreach ($this->properties as $property => $value)
            {
                $tag->$property = $value;
            }
        }
        
        $tag->show();
    }
}
