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

        $fontname1 = TCPDF_FONTS::addTTFfont( $_SERVER['DOCUMENT_ROOT'].'web/fonts/PTC75F.ttf', 'TrueTypeUnicode', '', 96);

        $pdf->SetFont($fontname1, '', 14, '', false);

//Коммерческое предложение заголовок (1 стр.)
        $pdf->AddPage();
        $bMargin = $pdf->getBreakMargin();
        $auto_page_break = $pdf->getAutoPageBreak();
        $pdf->SetAutoPageBreak(false, 0);
        $img_file = $_SERVER['DOCUMENT_ROOT'].'web/images/commerce_sugestion_1_page.jpg';
        $pdf->Image($img_file, 0, 0, 297, 210, '', '', '', false, 1300, '', false, false, 0);
        $pdf->SetAutoPageBreak($auto_page_break, $bMargin);
        $pdf->writeHTMLCell( 100, $h = 0, 50, 116, '<p style="font-weight: bold;font-size: 13px;color: #222222;">по разработке сайта</p>', $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = '', $autopadding = true);
        $pdf->writeHTMLCell( 100, $h = 0, 100, 116, '<p style="font-weight: bold;font-size: 13px;color: #222222;">'.$companyName.'</p>', $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = '', $autopadding = true);

//Приветствие клиента (2 стр.)
        $pdf->AddPage();
        $bMargin = $pdf->getBreakMargin();
        $auto_page_break = $pdf->getAutoPageBreak();
        $pdf->SetAutoPageBreak(false, 0);
        $img_file = $_SERVER['DOCUMENT_ROOT'].'web/images/commerce_sugestion_2_page.jpg';
        $pdf->Image($img_file, 0, 0, 297, 210, '', '', '', false, 1300, '', false, false, 0);
        $pdf->SetAutoPageBreak($auto_page_break, $bMargin);
        $pdf->writeHTMLCell( 100, $h = 0, 220, 40, '<a href="https://www.youtu.be/7NB4_TBkGkc" style="display: block;font-size: 5px;text-decoration: none"><p style="font-weight: bold;font-size: 13px;color: #222222;line-height: 0.95">УЗНАЙТЕ О НАС БОЛЬШЕ<br/>ВСЕГО ЗА 2 МИНУТЫ</p></a>', $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = '', $autopadding = true);
        $pdf->writeHTMLCell( 100, 50, 220, 55, '<a href="https://www.youtu.be/7NB4_TBkGkc" style="display: block;font-size: 7px; padding: 25px;"><div style="width: 250px;height: 250px; padding: 250px;">https://www.youtu.be/7NB4_TBkGkc</div></a>', $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = '', $autopadding = true);
        $pdf->writeHTMLCell( 100, 50, 199, 38, '<a href="https://www.youtu.be/7NB4_TBkGkc" style="display: block;font-size: 5px; padding: 25px;"><img src="'.$_SERVER['DOCUMENT_ROOT'].'web/images/watch.png" alt=""></a>', $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = '', $autopadding = true);
        $pdf->writeHTMLCell( '' , $h = 0, 25, 20, '<p style="font-weight: bold;font-size: 28px;color: #F59D0C;text-transform: uppercase">ЗДРАВСТВУЙТЕ, '.$name.'</p>', $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = '', $autopadding = true);

//Страница о нас с ссылками (3 стр.)
        $pdf->AddPage();
        $bMargin = $pdf->getBreakMargin();
        $auto_page_break = $pdf->getAutoPageBreak();
        $pdf->SetAutoPageBreak(false, 0);
        $img_file = $_SERVER['DOCUMENT_ROOT'].'web/images/diagonal_links.jpg';
        $pdf->Image($img_file, 0, 0, 297, 210, '', '', '', false, 1300, '', false, false, 0);

        $pdf->SetAutoPageBreak($auto_page_break, $bMargin);
        $pdf->Link(0, 0, 149, 210, 'https://youtu.be/7NB4_TBkGkc');
        $pdf->Link(149, 0, 149, 210, 'https://youtu.be/Fh9TsoCk7DY');
        $pdf->Link(142, 79, 52, 52, 'http://fresh-d.biz/');

//Сайт Агро-Союз http://agroritet.com (4 стр.)
        $pdf->AddPage();
        $bMargin = $pdf->getBreakMargin();
        $auto_page_break = $pdf->getAutoPageBreak();
        $pdf->SetAutoPageBreak(false, 0);
        $pdf->SetMargins( 0, 0 , 0 );
        $img_file = $_SERVER['DOCUMENT_ROOT'].'web/images/agroritet_com.jpg';
        $pdf->Image($img_file, 0, 0, 297, 210, '', '', '', false, 1300, '', false, false, 0);
        $pdf->SetAutoPageBreak($auto_page_break, $bMargin);
        $pdf->writeHTMLCell( 75, 10, 10, 173, '<a href="http://agroritet.com" style="display: block;font-size: 12px; padding: 25px;"><div style="width: 250px;height: 250px; padding: 250px;">http://agroritet.com</div></a>', $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = '', $autopadding = false);

//Сайт Liberta http://www.liberta.com.ua (5 стр.)
        $pdf->AddPage();
        $bMargin = $pdf->getBreakMargin();
        $auto_page_break = $pdf->getAutoPageBreak();
        $pdf->SetAutoPageBreak(false, 0);
        $pdf->SetMargins( 0, 0 , 0 );
        $img_file = $_SERVER['DOCUMENT_ROOT'].'web/images/liberta_com.jpg';
        $pdf->Image($img_file, 0, 0, 297, 210, '', '', '', false, 1300, '', false, false, 0);
        $pdf->SetAutoPageBreak($auto_page_break, $bMargin);
        $pdf->writeHTMLCell( 75, 10, 10, 173, '<a href="http://www.liberta.com.ua" style="display: block;font-size: 12px; padding: 25px;"><div style="width: 250px;height: 250px; padding: 250px;">http://www.liberta.com.ua</div></a>', $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = '', $autopadding = false);

//Сайт Auremo http://www.auremo.org (6 стр.)
        $pdf->AddPage();
        $bMargin = $pdf->getBreakMargin();
        $auto_page_break = $pdf->getAutoPageBreak();
        $pdf->SetAutoPageBreak(false, 0);
        $pdf->SetMargins( 0, 0 , 0 );
        $img_file = $_SERVER['DOCUMENT_ROOT'].'web/images/auremo_org.jpg';
        $pdf->Image($img_file, 0, 0, 297, 210, '', '', '', false, 1300, '', false, false, 0);
        $pdf->SetAutoPageBreak($auto_page_break, $bMargin);
        $pdf->writeHTMLCell( 75, 10, 10, 173, '<a href="http://www.auremo.org" style="display: block;font-size: 12px; padding: 25px;"><div style="width: 250px;height: 250px; padding: 250px;">http://www.auremo.org</div></a>', $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = '', $autopadding = false);
        $pdf->SetMargins( 0, 0 , 0 );

//Сайт Агро-Союз (7 стр.)
        $pdf->AddPage();
        $bMargin = $pdf->getBreakMargin();
        $auto_page_break = $pdf->getAutoPageBreak();
        $pdf->SetMargins( 0, 0 , 0 );
        $pdf->SetAutoPageBreak(false, 0);
        $img_file = $_SERVER['DOCUMENT_ROOT'].'web/images/agrounion.jpg';
        $pdf->Image($img_file, 0, 0, 297, 210, '', '', '', false, 1300, '', false, false, 0);
        $pdf->SetAutoPageBreak($auto_page_break, $bMargin);
        //$pdf->writeHTMLCell( 75, 10, 10, 180, '<a href="http://www.auremo.org" style="display: block;font-size: 12px; padding: 25px;"><div style="width: 250px;height: 250px; padding: 250px;">http://www.auremo.org</div></a>', $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = '', $autopadding = true);

//Клиентский лист (8 стр.)
        $pdf->AddPage();
        $bMargin = $pdf->getBreakMargin();
        $auto_page_break = $pdf->getAutoPageBreak();
        $pdf->SetAutoPageBreak(false, 0);
        $img_file = $_SERVER['DOCUMENT_ROOT'].'web/images/client_list.jpg';
        $pdf->Image($img_file, 0, 0, 297, 210, '', '', '', false, 1300, '', false, false, 0);
        $pdf->SetAutoPageBreak($auto_page_break, $bMargin);
        $pdf->Link(200, 40, 65, 20, 'http://www.fresh-d.net/studio/regals.html');
        $pdf->writeHTMLCell( 180, 40, 222.5, 60, '<a href="https://youtu.be/YhNySK73C6o" style="display: block;font-size: 7px; padding: 25px;"><div style="width: 250px;height: 250px; padding: 250px;">http://www.fresh-d.net/studio/regals.html</div></a>', $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = '', $autopadding = true);

//Смета по разработке (9 стр.)
//        $pdf->AddPage();
//        $bMargin = $pdf->getBreakMargin();
//        $auto_page_break = $pdf->getAutoPageBreak();
//        $pdf->SetAutoPageBreak(false, 0);
//        $img_file = $_SERVER['DOCUMENT_ROOT'].'web/images/why_we.jpg';
//        $pdf->Image($img_file, 0, 0, 297, 210, '', '', '', false, 1300, '', false, false, 0);
//        $pdf->SetAutoPageBreak($auto_page_break, $bMargin);
//        $pdf->Link(200, 40, 65, 20, 'http://www.fresh-d.net/studio/regals.html');
//        $pdf->writeHTMLCell( 180, 40, 223, 60, '<a href="https://youtu.be/YhNySK73C6o" style="display: block;font-size: 7px; padding: 25px;"><div style="width: 250px;height: 250px; padding: 250px;">http://www.fresh-d.net/studio/regals.html</div></a>', $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = '', $autopadding = true);

//Почему мы? (10 стр.)
        $pdf->AddPage();
        $bMargin = $pdf->getBreakMargin();
        $auto_page_break = $pdf->getAutoPageBreak();
        $pdf->SetAutoPageBreak(false, 0);
        $img_file = $_SERVER['DOCUMENT_ROOT'].'web/images/why_we.jpg';
        $pdf->Image($img_file, 0, 0, 297, 210, '', '', '', false, 1300, '', false, false, 0);
        $pdf->SetAutoPageBreak($auto_page_break, $bMargin);
        $pdf->writeHTMLCell( 180, 40, 111, 139, '<a href="http://www.fresh-d.net/portfolio" style="display: block;font-size: 11px; padding: 25px;"><div style="width: 250px;height: 250px; padding: 250px;">www.fresh-d.net/portfolio</div></a>', $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = '', $autopadding = true);

//Доп. услуги (11 стр.)
//        $pdf->AddPage();
//        $bMargin = $pdf->getBreakMargin();
//        $auto_page_break = $pdf->getAutoPageBreak();
//        $pdf->SetAutoPageBreak(false, 0);
//        $img_file = $_SERVER['DOCUMENT_ROOT'].'web/images/client_list.jpg';
//        $pdf->Image($img_file, 0, 0, 297, 210, '', '', '', false, 1300, '', false, false, 0);
//        $pdf->SetAutoPageBreak($auto_page_break, $bMargin);
//        $pdf->Link(200, 40, 65, 20, 'http://www.fresh-d.net/studio/regals.html');
//        $pdf->writeHTMLCell( 180, 40, 223, 60, '<a href="https://youtu.be/YhNySK73C6o" style="display: block;font-size: 7px; padding: 25px;"><div style="width: 250px;height: 250px; padding: 250px;">http://www.fresh-d.net/studio/regals.html</div></a>', $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = '', $autopadding = true);

//Почему стоимость именно такая (12 стр.)
        $pdf->AddPage();
        $bMargin = $pdf->getBreakMargin();
        $auto_page_break = $pdf->getAutoPageBreak();
        $pdf->SetAutoPageBreak(false, 0);
        $img_file = $_SERVER['DOCUMENT_ROOT'].'web/images/why_price_like_this.jpg';
        $pdf->Image($img_file, 0, 0, 297, 210, '', '', '', false, 1300, '', false, false, 0);
        $pdf->SetAutoPageBreak($auto_page_break, $bMargin);
//        $pdf->Link(200, 40, 65, 20, 'http://www.fresh-d.net/studio/regals.html');
//        $pdf->writeHTMLCell( 180, 40, 223, 60, '<a href="https://youtu.be/YhNySK73C6o" style="display: block;font-size: 7px; padding: 25px;"><div style="width: 250px;height: 250px; padding: 250px;">http://www.fresh-d.net/studio/regals.html</div></a>', $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = '', $autopadding = true);


//Сроки выполнения работ (13 стр.)
//        $pdf->AddPage();
//        $bMargin = $pdf->getBreakMargin();
//        $auto_page_break = $pdf->getAutoPageBreak();
//        $pdf->SetAutoPageBreak(false, 0);
//        $img_file = $_SERVER['DOCUMENT_ROOT'].'web/images/client_list.jpg';
//        $pdf->Image($img_file, 0, 0, 297, 210, '', '', '', false, 1300, '', false, false, 0);
//        $pdf->SetAutoPageBreak($auto_page_break, $bMargin);
//        $pdf->Link(200, 40, 65, 20, 'http://www.fresh-d.net/studio/regals.html');
//        $pdf->writeHTMLCell( 180, 40, 223, 60, '<a href="https://youtu.be/YhNySK73C6o" style="display: block;font-size: 7px; padding: 25px;"><div style="width: 250px;height: 250px; padding: 250px;">http://www.fresh-d.net/studio/regals.html</div></a>', $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = '', $autopadding = true);

//О системе управления сайтом (14 стр.)
        $pdf->AddPage();
        $bMargin = $pdf->getBreakMargin();
        $auto_page_break = $pdf->getAutoPageBreak();
        $pdf->SetAutoPageBreak(false, 0);
        $img_file = $_SERVER['DOCUMENT_ROOT'].'web/images/about_content_management_system-01.jpg';
        $pdf->Image($img_file, 0, 0, 297, 210, '', '', '', false, 1300, '', false, false, 0);
        $pdf->SetAutoPageBreak($auto_page_break, $bMargin);
//        $pdf->Link(200, 40, 65, 20, 'http://www.fresh-d.net/studio/regals.html');
//        $pdf->writeHTMLCell( 180, 40, 223, 60, '<a href="https://youtu.be/YhNySK73C6o" style="display: block;font-size: 7px; padding: 25px;"><div style="width: 250px;height: 250px; padding: 250px;">http://www.fresh-d.net/studio/regals.html</div></a>', $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = '', $autopadding = true);

//Контактная информация (15 стр.)
        $pdf->AddPage();
//        $bMargin = $pdf->getBreakMargin();
//        $auto_page_break = $pdf->getAutoPageBreak();
//        $pdf->SetAutoPageBreak(false, 0);
        $img_file = $_SERVER['DOCUMENT_ROOT'].'web/images/contact_information-01.jpg';
        $pdf->Image($img_file, 0, 0, 297, 210, '', '', '', false, 1300, '', false, false, 0);
        $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 058', PDF_HEADER_STRING, array(0,0,0), array(255,255,255));
        $pdf->SetPrintHeader(false);
        $pdf->SetPrintFooter(false);
        
//        $pdf->SetAutoPageBreak($auto_page_break, $bMargin);
        $pdf->SetMargins( -20 , -20 , -20 , true );
        $pdf->writeHTMLCell( 200, 10, 177, 116, '<a href="https://youtu.be/7NB4_TBkGkc" style="display: block;font-size: 9px; padding: 25px;"><div style="width: 250px;height: 250px; padding: 250px;">https://youtu.be/7NB4_TBkGkc</div></a>', $border = 0, $ln = 1, $fill = 0, false, $align = '', false);
        $pdf->writeHTMLCell( 200, 10, 177, 134, '<a href="http://www.fresh-d.net/" style="display: block;font-size: 9px; padding: 25px;"><div style="width: 250px;height: 250px; padding: 250px;">www.fresh-d.net</div></a>', $border = 0, $ln = 1, $fill = 0, false, $align = '', false);
        $pdf->writeHTMLCell( 180, 10, 177, 151, '<a href="http://www.fresh-d.net/studio/regals.html" style="display: block;font-size: 9px; padding: 25px;"><div style="width: 250px;height: 250px; padding: 250px;">www.fresh-d.net/studio/regals.html</div></a>', $border = 0, $ln = 1, $fill = 0, false, $align = '', false);
        $pdf->writeHTMLCell( 180, 10, 177, 167, '<a href="https://youtu.be/Fh9TsoCk7DY" style="display: block;font-size: 9px; padding: 25px;"><div style="width: 250px;height: 250px; padding: 250px;">https://youtu.be/Fh9TsoCk7DY</div></a>', $border = 0, $ln = 1, $fill = 0, false, $align = '', false);









//        $pdf->Cell(30, 0, 'Top-Center', 1, $ln=0, 'C', 0, '', 0, false, 'T', 'C');
//        $pdf->Write(10, 'Google', 'http://www.google.com/', false, 'L', true);


        $filename = 'web/pdf_files/commerce_sugestion_'.time().'.pdf';
        //var_dump($filename);die();
        $pdf->Output( $_SERVER['DOCUMENT_ROOT'].''.$filename,'F');

        return $filename;
    }




}
