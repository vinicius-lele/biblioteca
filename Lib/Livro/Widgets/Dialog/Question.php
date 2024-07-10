<?php
namespace Livro\Widgets\Dialog;

use Livro\Control\Action;
use Livro\Widgets\Base\Element;

class Question
{
    function __construct($message, Action $action_yes, Action $action_no = NULL)
    {
        $div = new Element('div');
        $div->class = 'alert alert-warning question';
        
        $url_yes = $action_yes->serialize();
        
        $link_yes = new Element('a');
        $link_yes->href = $url_yes;
        $link_yes->class = 'btn btn-default';
        $link_yes->style = 'float:right';
        $link_yes->add('Sim');
        
        $message .= '&nbsp;' . $link_yes;
        if ($action_no)
        {
            $url_no = $action_no->serialize();
            
            $link_no = new Element('a');
            $link_no->href = $url_no;
            $link_no->class = 'btn btn-default';
            $link_no->style = 'float:right';
            $link_no->add('Não');
            
            $message .= $link_no;
        }
        
        $div->add($message);
        $div->show();
    }
}
