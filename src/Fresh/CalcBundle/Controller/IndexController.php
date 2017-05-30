<?php

namespace Fresh\CalcBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Knp\Bundle\SnappyBundle\KnpSnappyBundle;

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

    public function generatePdfAction(Request $request)
    {
        //echo '<pre>';var_dump($request->request->get('company'));die;
        $companyName = $request->request->get('company');


        $html = $this->renderView('FreshCalcBundle:PDF:pdfdoc.html.twig', array(
            'companyName'  => $companyName
        ));

        return new Response(
            $this->get('knp_snappy.pdf')->getOutputFromHtml($html),
            200,
            array(
                'Content-Type'          => 'application/pdf',
                'Content-Disposition'   => 'attachment; filename="file.pdf"'
            )
        );



//        $this->get('knp_snappy.pdf')->generateFromHtml(
//            $this->renderView(
//                'FreshCalcBundle:PDF:pdfdoc.html.twig',
//                array(
//                    'companyName'  => $companyName
//                )
//            ),
//            'c:\openserver/hello/file.pdf'
//        );


    }
}
