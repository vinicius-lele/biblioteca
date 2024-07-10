<?php
date_default_timezone_set('America/Sao_Paulo');

if (version_compare(PHP_VERSION, '7.0.0') == -1)
{
    die('A versão mínima do PHP para rodar esta aplicação é: 7.0.0');
}

require_once 'Lib/Livro/Core/ClassLoader.php';
$al= new Livro\Core\ClassLoader;
$al->addNamespace('Livro', 'Lib/Livro');
$al->register();

require_once 'Lib/Livro/Core/AppLoader.php';
$al= new Livro\Core\AppLoader;
$al->addDirectory('App/Control');
$al->addDirectory('App/Model');
$al->register();

$loader = require 'vendor/autoload.php';
$loader->register();

$template = file_get_contents('App/Templates/template.html');
$content = '<div class="simple-text"><h2>SISTEMA DE CONTROLE DA BIBLIOTECA</h2><p><h2>EMEF Profº Nelson Gabaldi</h2></p></div>';
$class   = 'Home';

if ($_GET)
{
    $class = $_GET['class'];
    if (class_exists($class))
    {
        try
        {
            $pagina = new $class;
            ob_start();
            $pagina->show();
            $content = ob_get_contents();
            ob_end_clean();
        }
        catch (Exception $e)
        {
            $content = $e->getMessage() . '<br>' .$e->getTraceAsString();
        }
    }
    else
    {
        $content = "<img src='App\Images\page_not_found.png' width=1000px height=900px>"; 
    }
}
$output = str_replace('{content}', $content, $template);
$output = str_replace('{class}',   $class, $output);
echo $output;
