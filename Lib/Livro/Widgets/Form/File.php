<?php
namespace Livro\Widgets\Form;

use Livro\Widgets\Base\Element;

class File extends Field implements FormElementInterface
{
    public function show()
    {
        $tag = new Element('input');
        $tag->class = 'field';		  
        $tag->name = $this->name;
        $tag->value = $this->value;  
        $tag->type = 'file';        
        $tag->style = "width:{$this->size}";
        
        if (!parent::getEditable())
        {
            $tag->readonly = "1";
        }
        
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
