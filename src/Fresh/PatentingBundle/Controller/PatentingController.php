<?php

namespace Fresh\PatentingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class PatentingController extends Controller
{
    public function indexAction()
    {
        return $this->render('FreshPatentingBundle:Patenting:calc.html.twig');
    }
}
