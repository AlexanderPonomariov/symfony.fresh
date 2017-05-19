<?php

namespace Fresh\CalcBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class IndexController extends Controller
{
    public function indexAction()
    {
        $name = 'Hello Alex!!!! I am you firt frase!!!';

        return $this->render('FreshCalcBundle:Index:index.html.twig', array('name' => $name));
    }
}
