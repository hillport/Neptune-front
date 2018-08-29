<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 28/08/2018
 * Time: 09:14
 */

namespace App\Form\Type;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReCaptchaType extends AbstractType
{

    public function configureOptions(OptionsResolver $resolver){
        $resolver->setDefaults(array(
            'data-sitekey' =>  '6Le41GwUAAAAANSBGml8877-JtZB5TGaymzuG6sT'
        ));
    }
    public function getParent()
    {
        return HiddenType::class;
    }
}