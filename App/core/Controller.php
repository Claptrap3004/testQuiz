<?php
namespace quiz;

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Loader\FilesystemLoader;

class Controller
{
    protected Factory $factory;
    protected DBFactory $dbFactory;

    public function __construct()
    {
        $this->factory = Factory::getFactory();
        $this->dbFactory = DBFactory::getFactory();
    }

    public function view(string $viewname, array $data): void
    {
        $loader = new FilesystemLoader('../App/View');
        $twig = new Environment($loader);


        $viewFile = '../App/View/' .  ucfirst($viewname). '.html.twig';
        if (file_exists($viewFile)) {
            try {
                $page = $twig->render("$viewname.html.twig", $data);
                echo $page;
            } catch (LoaderError|RuntimeError|SyntaxError $e) {
                echo $e;
            }
        }
        else require '../App/View/PageNotFoundController.php';
    }

}