<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 02/04/2019
 * Time: 11:57
 */

namespace App\Twig;


use ScyLabs\NeptuneBundle\AbstractEntity\AbstractDetail;
use ScyLabs\NeptuneBundle\AbstractEntity\AbstractElem;
use ScyLabs\NeptuneBundle\AbstractEntity\AbstractFileLink;
use Symfony\Component\HttpFoundation\Request;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use ScyLabs\NeptuneBundle\Entity\Page;

class BaseExtension extends AbstractExtension
{

    public function getFunctions()
    {
        return array(
            new TwigFunction('fileName',array($this,'fileName')),
            new TwigFunction('fileGetContents',array($this,'fileGetContents')),
            new TwigFunction('getFirstText',array($this,'getFirstText'))
        );
    }
    public function fileName(AbstractFileLink $link,$locale) : string {

        $fileName = ($link->getDetail($locale) instanceof AbstractDetail && $link->getDetail($locale)->getTitle() !== null) ? $link->getDetail($locale)->getTitle() : $link->getParent()->getDetail($locale)->getName();

        $fileName = str_replace(
            array(" ", "À", "Á", "Â", "Ã", "Ä", "Å", "à", "á", "â", "ã", "ä", "å", "Ò", "Ó", "Ô", "Õ", "Ö", "Ø", "ò", "ó", "ô", "õ", "ö", "ø", "È", "É", "Ê", "Ë", "è", "é", "ê", "ë", "Ç", "ç", "Ì", "Í", "Î", "Ï", "ì", "í", "î", "ï", "Ù", "Ú", "Û", "Ü", "ù", "ú", "û", "ü", "ÿ", "Ñ", "ñ", "(", ")", "[", "]", "'", "#", "~", "$", "&", "%", "*", "@", "ç", "!", "?", ";", ",", ":", "/", "^", "¨", "€", "{", "}", "|", "+", ".", "²"),
            array("-", "A", "A", "A", "A", "A", "A", "a", "a", "a", "a", "a", "a", "O", "O", "O", "O", "O", "O", "o", "o", "o", "o", "o", "o", "E", "E", "E", "E", "e", "e", "e", "e", "C", "c", "I", "I", "I", "I", "i", "i", "i", "i", "U", "U", "U", "U", "u", "u", "u", "u", "y", "N", "n", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "euro", "", "", "", "", "", "2"),
            $fileName
        );

        $fileName = preg_replace("#[^a-zA-Z_0-9.-]#","",$fileName) || '0';


        return $fileName.'.'.$link->getFile()->getExt();

    }

    public function fileGetContents($path){
        return file_get_contents($path);
    }
        
    public function getFirstText(AbstractElem $page,$locale)
    {
        if (null !== $text = $page->getDetail($locale)->getMetaDesc())
            return $text;
        else if (null !== $text = $page->getDetail($locale)->getDescription())
            return $text;

        foreach ($page->getZones() as $zone) {
            if (null !== $text = $zone->getDetail($locale)->getDescription()) {
                return $text;
            }
        }
    }

}
