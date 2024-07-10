<?php
namespace Livro\Database;

class Criteria
{
    private $properties;
    private $filters;
    
    function __construct()
    {
        $this->filters = array();
    }
    
    public function add($variable, $compare_operator, $value, $logic_operator = 'and')
    {
        if (empty($this->filters))
        {
            $logic_operator = NULL;
        }
        
        $this->filters[] = [$variable, $compare_operator, $this->transform($value), $logic_operator];
    }
    
    private function transform($value)
    {
        if (is_array($value))
        {
            foreach ($value as $x)
            {
                if (is_integer($x))
                {
                    $foo[]= $x;
                }
                else if (is_string($x))
                {
                    $foo[]= "'$x'";
                }
            }
            $result = '(' . implode(',', $foo) . ')';
        }
        else if (is_string($value))
        {
            $result = "'$value'";
        }
        else if (is_null($value))
        {
            $result = 'NULL';
        }
        
        else if (is_bool($value))
        {
            $result = $value ? 'TRUE' : 'FALSE';
        }
        else
        {
            $result = $value;
        }
        return $result;
    }
    
    public function dump()
    {
        if (is_array($this->filters) and count($this->filters) > 0)
        {
            $result = '';
            foreach ($this->filters as $filter)
            {
                $result .= $filter[3] . ' ' . $filter[0] . ' ' . $filter[1] . ' '. $filter[2] . ' ';
            }
            $result = trim($result);
            return "({$result})";
        }
    }
    
    public function setProperty($property, $value)
    {
        if (isset($value))
        {
            $this->properties[$property] = $value;
        }
        else
        {
            $this->properties[$property] = NULL;
        }
    }
    
    public function getProperty($property)
    {
        if (isset($this->properties[$property]))
        {
            return $this->properties[$property];
        }
    }
}
