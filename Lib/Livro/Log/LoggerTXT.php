<?php
namespace Livro\Database;

class LoggerTXT extends Logger
{
    public function write($message)
    {
        date_default_timezone_set('America/Sao_Paulo');
        $time = date("Y-m-d H:i:s");
        
        $text = "$time :: $message\n";
        
        $handler = fopen($this->filename, 'a');
        fwrite($handler, $text);
        fclose($handler);
    }
}
