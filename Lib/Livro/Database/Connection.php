<?php
namespace Livro\Database;

use PDO;
use Exception;

final class Connection
{
    private function __construct() {}
    
    public static function open($name)
    {
        if (file_exists("App/Config/{$name}.ini"))
        {
            $db = parse_ini_file("App/Config/{$name}.ini");
        }
        else if (file_exists("App/Config/{$name}.php"))
        {
            $db = require "App/Config/{$name}.php";
        }
        else
        {
            throw new Exception("Arquivo '$name' não encontrado");
        }
        
        $user = isset($db['user']) ? $db['user'] : NULL;
        $pass = isset($db['pass']) ? $db['pass'] : NULL;
        $name = isset($db['name']) ? $db['name'] : NULL;
        $host = isset($db['host']) ? $db['host'] : NULL;
        $type = isset($db['type']) ? $db['type'] : NULL;
        $port = isset($db['port']) ? $db['port'] : NULL;
        
        switch ($type)
        {
            case 'pgsql':
                $port = $port ? $port : '5432';
                $conn = new PDO("pgsql:dbname={$name}; user={$user}; password={$pass};
                        host=$host;port={$port}");
                break;
            case 'mysql':
                $port = $port ? $port : '3306';
                $conn = new PDO("mysql:host={$host};port={$port};dbname={$name}", $user, $pass);
                break;
            case 'sqlite':
                $conn = new PDO("sqlite:{$name}");
                $conn->query('PRAGMA foreign_keys = ON');
                break;
            case 'ibase':
                $conn = new PDO("firebird:dbname={$name}", $user, $pass);
                break;
            case 'oci8':
                $conn = new PDO("oci:dbname={$name}", $user, $pass);
                break;
            case 'mssql':
                $conn = new PDO("dblib:host={$host}:{$port};dbname={$name}", $user, $pass);
                break;
        }
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    }
}
