<?php
namespace Livro\Widgets\Form;

use Livro\Widgets\Base\Element;

abstract class Field implements FormElementInterface
{
    protected $name;
    protected $size;
    protected $value;
    protected $editable;
    protected $formLabel;
    protected $properties;
    
    public function __construct($name)
    {
        self::setEditable(true);
        self::setName($name);
    }
    
    public function __set($name, $value)
    {
        if (is_scalar($value))
        {              
            $this->setProperty($name, $value);
        }
    }
    
    public function __get($name)
    {
        return $this->getProperty($name);
    }
    
    public function setName($name)
    {
        $this->name = $name;
    }
    
    public function getName()
    {
        return $this->name;
    }
    
    public function setLabel($label)
    {
        $this->formLabel = $label;
    }
    
    public function getLabel()
    {
        return $this->formLabel;
    }


    public function setValue($value)
    {
        $this->value = $value;
    }
    
    public function getValue()
    {
        return $this->value;
    }
    
    public function setEditable($editable)
    {
        $this->editable= $editable;
    }
    
    public function getEditable()
    {
        return $this->editable;
    }
    
    public function setProperty($name, $value)
    {
        $this->properties[$name] = $value;
    }
    
    public function getProperty($name)
    {
        return $this->properties[$name];
    }
    
    public function setSize($width, $height = NULL)
    {
        $this->size = $width;
    }
}
