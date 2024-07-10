<?php
namespace Livro\Widgets\Datagrid;

use Livro\Control\ActionInterface;

class Datagrid
{
    private $columns;
    private $items;
    private $actions;
    
    public function addColumn(DatagridColumn $object)
    {
        $this->columns[] = $object;
    }
    
    public function addAction($label, ActionInterface $action, $field, $image = null)
    {
        $this->actions[] = [ 'label'  => $label,
                             'action' => $action,
                             'field'  => $field,
                             'image'  => $image ];
    }
    
    public function addItem($object)
    {
        $this->items[] = $object;
        
        foreach ($this->columns as $column)
        {
            $name = $column->getName();
            if (!isset($object->$name))
            {
                $object->$name;
            }
        }
    }
    
    public function getColumns()
    {
        return $this->columns;
    }
    
    public function getItems()
    {
        return $this->items;
    }
    
    public function getActions()
    {
        return $this->actions;
    }
    
    function clear()
    {
        $this->items = [];
    }
}
