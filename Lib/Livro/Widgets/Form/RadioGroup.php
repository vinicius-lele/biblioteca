<?php
namespace Livro\Widgets\Form;

use Livro\Widgets\Base\Element;

class RadioGroup extends Field implements FormElementInterface
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
                $button = new RadioButton($this->name);
                $button->setValue($index);
                if ($this->value == $index)
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
                }
                echo "\n";
            }
        }
    }
}
