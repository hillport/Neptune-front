<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 13/07/2018
 * Time: 14:34
 */

namespace App\Twig;


use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class FileExistExtension extends AbstractExtension
{

    public function getFunctions()
    {
        return array(
          new TwigFunction('file_exist',array($this,'fileExist'))
        );
    }
    public function fileExist($path){
        return file_exists($path);
    }
}