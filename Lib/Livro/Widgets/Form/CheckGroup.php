<?php
namespace Livro\Widgets\Form;

use Livro\Widgets\Base\Element;

class CheckGroup extends Field implements FormElementInterface
{
    private $layout = 'vertical';
    private $items;
    
    public function setLayout($dir)
    {
        $this->layout = $dir;
    }
    
    public function addItems($items)
    {
        $this->items = $items;
    }
    
    public function show()
    {
        if ($this->items)
        {
            foreach ($this->items as $index => $label)
            {
                $button = new CheckButton("{$this->name}[]");
                $button->setValue($index);
                
                if (in_array($index, (array) $this->value))
                {
                    $button->setProperty('checked', '1');
                }
                
                $obj = new Label($label);
                $obj->add($button);
                $obj->show();
                if ($this->layout == 'vertical')
                {
                    $br = new Element('br');
                    $br->show();
                    echo "\n";
                }
            }
        }
    }
}
