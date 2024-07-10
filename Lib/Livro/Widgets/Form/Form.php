<?php
namespace Livro\Widgets\Form;

use Livro\Control\ActionInterface;

class Form
{
    protected $title;
    protected $name;
    protected $fields;
    protected $actions;
    
    public function __construct($name = 'my_form')
    {
        $this->setName($name);
    }
    
    public function setName($name)
    {
        $this->name = $name;
    }
    
    public function getName()
    {
        return $this->name;
    }
    
    public function setTitle($title)
    {
        $this->title = $title;
    }
    
    public function getTitle()
    {
        return $this->title;
    }
    
    public function addField($label, FormElementInterface $object, $size = '100%')
    {
        $object->setSize($size);
        $object->setLabel($label);
        $this->fields[$object->getName()] = $object;
    }
    
    public function addAction($label, ActionInterface $action)
    {
        $this->actions[$label] = $action;
    }
    
    public function getFields()
    {
        return $this->fields;
    }
    
    public function getActions()
    {
        return $this->actions;
    }
    
    public function setData($object)
    {
        foreach ($this->fields as $name => $field)
        {
            if ($name AND isset($object->$name))
            {
                $field->setValue($object->$name);
            }
        }
    }
    
    public function getData($class = 'stdClass')
    {
        $object = new $class;
        
        foreach ($this->fields as $key => $fieldObject)
        {
            $val = isset($_POST[$key]) ? $_POST[$key] : '';
            $object->$key = $val;
        }
        foreach ($_FILES as $key => $content)
        {
            $object->$key = $content['tmp_name'];
        }
        return $object;
    }
}
