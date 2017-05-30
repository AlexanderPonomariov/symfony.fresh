<?php

namespace Fresh\CalcBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class IndexController extends Controller
{
    public function indexAction($pdfLocation = null)
    {
        $name = 'Hello Alex!!!! I am you firt frase!!!';

        $em = $this->getDoctrine()->getManager();

        $sitesTypes = $em->getRepository('FreshCalcBundle:SitesTypes')->findAll();

        return $this->render('FreshCalcBundle:Index:index.html.twig',
            array(
                'name' => $name,
                'sitesTypes' => $sitesTypes,
                'pdfLocation' => $pdfLocation
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
//        $companyName = $request->request->get('company');
        $companyName= 'hello Привет медвед';

        $html = $this->renderView('FreshCalcBundle:PDF:pdfdoc.html.twig', array(
            'companyName'  => $companyName
        ));

//        return new Response(
//            $this->get('knp_snappy.pdf')->getOutputFromHtml($html),
//            200,
//            array(
//                'Content-Type'          => 'application/pdf',
//                'Content-Disposition'   => 'attachment; filename="file.pdf"'
//            )
//        );



//        $this->get('knp_snappy.pdf')->generateFromHtml(
//            $this->renderView(
//                'FreshCalcBundle:PDF:pdfdoc.html.twig',
//                array(
//                    'companyName'  => $companyName
//                )
//            ),
//            'c:\openserver/hello/file.pdf'
//        );

        $filename = $this->returnPDFResponseFromHTML($html);
//echo '<pre>';var_dump($_SERVER['DOCUMENT_ROOT']);die;
        return new JsonResponse($filename);

    }

    public function returnPDFResponseFromHTML($html){
        //set_time_limit(30); uncomment this line according to your needs
        // If you are not in a controller, retrieve of some way the service container and then retrieve it
        //$pdf = $this->container->get("white_october.tcpdf")->create('vertical', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        //if you are in a controlller use :
        //echo '<pre>';var_dump($this->get("white_october.tcpdf")->create('vertical', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false));die;
        $pdf = $this->get("white_october.tcpdf")->create('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetAuthor('Our Code World');
        $pdf->SetTitle(('Our Code World Title'));
        $pdf->SetSubject('Our Code World Subject');
        $pdf->setFontSubsetting(true);
        $pdf->SetFont('dejavusans', '', 11, '', true);
        //$pdf->SetMargins(20,20,40, true);
        $pdf->AddPage();

        $filename = $_SERVER['DOCUMENT_ROOT'].'pdf_files/'.'commerce_sugestion_'.time().'.pdf';

        $pdf->writeHTMLCell($w = 0, $h = 0, $x = '', $y = '', $html, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = '', $autopadding = true);
        $pdf->Output( $filename,'F');

        return $filename;
    }




}
