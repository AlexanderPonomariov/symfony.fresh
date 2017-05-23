<?php

namespace Fresh\CalcBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class IndexController extends Controller
{
    public function indexAction()
    {
        $name = 'Hello Alex!!!! I am you firt frase!!!';

        $em = $this->getDoctrine()->getManager();

        $sitesTypes = $em->getRepository('FreshCalcBundle:SitesTypes')->findAll();

        return $this->render('FreshCalcBundle:Index:index.html.twig',
            array(
                'name' => $name,
                'sitesTypes' => $sitesTypes
            )
        );
    }

    public function showParametersAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $siteType = $em->getRepository('FreshCalcBundle:SitesTypes')->find($id);

        $siteTypeParameters = $em->getRepository('FreshCalcBundle:Parameters')->getParametersForSiteType($siteType->getId());



//        return $this->render('FreshCalcBundle:Index:index.html.twig',
//            array(
//
//                'sitesTypes' => $siteTypeParameters
//            )
//        );
        for ( $i=0; $i < count($siteTypeParameters); $i++ ) {
            $siteTypeParameters[$i]['workType']['id'] = $em->getRepository('FreshCalcBundle:Parameters')->find($siteTypeParameters[$i]['id'])->getWorkTypes()->getId();
           // echo '<pre>';var_dump($em->getRepository('FreshCalcBundle:WorkTypes')->find($siteTypeParameters[$i]['id'])->getWorkType());die;
            $siteTypeParameters[$i]['workType']['workType'] = $em->getRepository('FreshCalcBundle:Parameters')->find($siteTypeParameters[$i]['id'])->getWorkTypes()->getWorkType();
        }
        //echo '<pre>';var_dump($siteTypeParameters);die;
        return new JsonResponse(array('sitesTypes' => $siteTypeParameters));

    }
}
