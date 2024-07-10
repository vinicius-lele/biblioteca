<?php
namespace Livro\Widgets\Form;

use Livro\Widgets\Base\Element;

class Combo extends Field implements FormElementInterface
{
    private $items;
    protected $properties;
    
    public function addItems($items)
    {
        $this->items = $items;
    }
    
    public function show()
    {
        $tag = new Element('select');
        $tag->class = 'combo';
        $tag->name = $this->name;
        $tag->style = "width:{$this->size}";
        
        $option = new Element('option');
        $option->add('');
        $option->value = '0';
        
        $tag->add($option);
        if ($this->items)
        {
            foreach ($this->items as $chave => $item)
            {
                $option = new Element('option');
                $option->value = $chave;
                $option->add($item);
                
                if ($chave == $this->value)
                {
                    $option->selected = 1;
                }
                $tag->add($option);
            }
        }
        
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
