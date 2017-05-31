<?php

namespace Fresh\CalcBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use \TCPDF_FONTS;
use TFox\MpdfPortBundle\TFoxMpdfPortBundle;

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
        //echo '<pre>';var_dump($request);die;
        $companyName = $request->request->get('company');
        $name = $request->request->get('name');
        $surname = $request->request->get('surname');
        $secondname = $request->request->get('secondname');
        $email = $request->request->get('email');
        $tel = $request->request->get('tel');
        $site_type = $request->request->get('site_type');
        $notificatons = $request->request->get('notificatons');

        $design = $request->request->get('$design');
        $adaptiveDesign = $request->request->get('adaptiveDesign');
        $programming = $request->request->get('programming');
        $markUp = $request->request->get('markUp');
        $programmingFinal = $request->request->get('programmingFinal');
        $totalTime = $request->request->get('totalTime');
        $totalPrice = $request->request->get('totalPrice');
        $pricePerOnePayment = $request->request->get('pricePerOnePayment');
        $markUpPrice = $request->request->get('markUpPrice');
        $programmingPrice = $request->request->get('programmingPrice');
        $designPrice = $request->request->get('designPrice');



//        $companyName= 'hello Привет медвед';
//        return $this->render('FreshCalcBundle:PDF:pdfdoc.html.twig',
//            array(
//                'companyName'  => $companyName
//            )
//        );

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

        $filename = $this->returnPDFResponseFromHTML($html, $companyName, $name);
//echo '<pre>';var_dump($_SERVER['DOCUMENT_ROOT']);die;
        return new JsonResponse($filename);


        // MPDF case

//        $arguments = array(
//            'constructorArgs' => array('','', 0, '', 15, 15, 16, 16, 9, 9, 'L'), //Constructor arguments. Numeric array. Don't forget about points 2 and 3 in Warning section!
//            'writeHtmlMode' => null, //$mode argument for WriteHTML method
//            'writeHtmlInitialise' => null, //$mode argument for WriteHTML method
//            'writeHtmlClose' => null, //$close argument for WriteHTML method
//            'outputFilename' => null, //$filename argument for Output method
//            'outputDest' => null //$dest argument for Output method
//        );
//
//        $mpdfService = $this->get('tfox.mpdfport');
//        $mPDF = $mpdfService->getMpdf(array('','A4', 15, '', 0, 0, 0, 0, 9, 9, 'L'));
//        $mPDF->WriteHTML($html);
//        $mPDF->Output($_SERVER['DOCUMENT_ROOT'].'/pdf_files/qwee1.pdf', 'F');
//
//        return new JsonResponse();

    }

    public function returnPDFResponseFromHTML($html, $companyName='', $name=''){

        $pdf = $this->get("white_october.tcpdf")->create('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        $fontname1 = TCPDF_FONTS::addTTFfont( $_SERVER['DOCUMENT_ROOT'].'fonts/PTC75F.ttf', 'TrueTypeUnicode', '', 96);

        $pdf->SetFont($fontname1, '', 14, '', false);


        $pdf->AddPage();
        $bMargin = $pdf->getBreakMargin();
        $auto_page_break = $pdf->getAutoPageBreak();
        $pdf->SetAutoPageBreak(false, 0);
        $img_file = $_SERVER['DOCUMENT_ROOT'].'images/commerce_sugestion_1_page.jpg';
        $pdf->Image($img_file, 0, 0, 297, 210, '', '', '', false, 1300, '', false, false, 0);
        $pdf->SetAutoPageBreak($auto_page_break, $bMargin);
        $pdf->writeHTMLCell( 100, $h = 0, 50, 116, '<p style="font-weight: bold;font-size: 13px;color: #222222;">по разработке сайта</p>', $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = '', $autopadding = true);
        $pdf->writeHTMLCell( 100, $h = 0, 100, 116, '<p style="font-weight: bold;font-size: 13px;color: #222222;">'.$companyName.'</p>', $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = '', $autopadding = true);


        $pdf->AddPage();
        $bMargin = $pdf->getBreakMargin();
        $auto_page_break = $pdf->getAutoPageBreak();
        $pdf->SetAutoPageBreak(false, 0);
        $img_file = $_SERVER['DOCUMENT_ROOT'].'images/commerce_sugestion_2_page.jpg';
        $pdf->Image($img_file, 0, 0, 297, 210, '', '', '', false, 1300, '', false, false, 0);
        $pdf->SetAutoPageBreak($auto_page_break, $bMargin);
        $pdf->writeHTMLCell( 100, $h = 0, 220, 40, '<a href="https://www.youtu.be/7NB4_TBkGkc" style="display: block;font-size: 5px;text-decoration: none"><p style="font-weight: bold;font-size: 13px;color: #222222;line-height: 0.95">УЗНАЙТЕ О НАС БОЛЬШЕ<br/>ВСЕГО ЗА 2 МИНУТЫ</p></a>', $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = '', $autopadding = true);
        $pdf->writeHTMLCell( 100, 50, 220, 55, '<a href="https://www.youtu.be/7NB4_TBkGkc" style="display: block;font-size: 5px; padding: 25px;"><div style="width: 250px;height: 250px; padding: 250px;">https://www.youtu.be/7NB4_TBkGkc</div></a>', $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = '', $autopadding = true);
        $pdf->writeHTMLCell( 100, 50, 199, 38, '<a href="https://www.youtu.be/7NB4_TBkGkc" style="display: block;font-size: 5px; padding: 25px;"><img src="'.$_SERVER['DOCUMENT_ROOT'].'images/watch.png" alt=""></a>', $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = '', $autopadding = true);
        $pdf->writeHTMLCell( '' , $h = 0, 25, 20, '<p style="font-weight: bold;font-size: 28px;color: #F59D0C;text-transform: uppercase">ЗДРАВСТВУЙТЕ, '.$name.'</p>', $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = '', $autopadding = true);


        $pdf->AddPage();
        $bMargin = $pdf->getBreakMargin();
        $auto_page_break = $pdf->getAutoPageBreak();
        $pdf->SetAutoPageBreak(false, 0);

        $pdf->SetAutoPageBreak($auto_page_break, $bMargin);
        $pdf->writeHTMLCell( '', 210, 0, 0, '<a href="https://www.youtu.be/7NB4_TBkGkc" style="display: block;font-size: 5px; padding: 25px;"><img src="'.$_SERVER['DOCUMENT_ROOT'].'images/diagonal_link_2.png" alt=""></a>', $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = '', $autopadding = true);




        $pdf->writeHTMLCell( 100, $h = 0, 220, 40, '<a href="https://www.youtu.be/7NB4_TBkGkc" style="display: block;font-size: 5px;text-decoration: none"><p style="font-weight: bold;font-size: 13px;color: #222222;line-height: 0.95">УЗНАЙТЕ О НАС БОЛЬШЕ<br/>ВСЕГО ЗА 2 МИНУТЫ</p></a>', $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = '', $autopadding = true);
        $pdf->writeHTMLCell( 100, 50, 220, 55, '<a href="https://www.youtu.be/7NB4_TBkGkc" style="display: block;font-size: 5px; padding: 25px;"><div style="width: 250px;height: 250px; padding: 250px;">https://www.youtu.be/7NB4_TBkGkc</div></a>', $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = '', $autopadding = true);
        $pdf->writeHTMLCell( 100, 50, 199, 38, '<a href="https://www.youtu.be/7NB4_TBkGkc" style="display: block;font-size: 5px; padding: 25px;"><img src="'.$_SERVER['DOCUMENT_ROOT'].'images/watch.png" alt=""></a>', $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = '', $autopadding = true);
        $pdf->writeHTMLCell( '' , $h = 0, 25, 20, '<p style="font-weight: bold;font-size: 28px;color: #F59D0C;text-transform: uppercase">ЗДРАВСТВУЙТЕ, '.$name.'</p>', $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = '', $autopadding = true);





//        $pdf->Cell(30, 0, 'Top-Center', 1, $ln=0, 'C', 0, '', 0, false, 'T', 'C');
//        $pdf->Write(10, 'Google', 'http://www.google.com/', false, 'L', true);


        $filename = 'pdf_files/commerce_sugestion_'.time().'.pdf';
        $pdf->Output( $_SERVER['DOCUMENT_ROOT'].''.$filename,'F');

        return $filename;
    }




}
