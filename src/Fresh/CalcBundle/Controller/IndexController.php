<?php

namespace Fresh\CalcBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use \TCPDF_FONTS;

class IndexController extends Controller
{

    protected $workStages = [
        'Написание технического задания;',
        'Разработка дизайна десктопной версии (разрешение экрана более или равно 1 200 px);',
        'Разработка дизайна планшетной версии (разрешения экрана более 768 px и менее 1 200 px);',
        'Разработка дизайна мобильной версии (разрешения экрана более 320 px и менее 768 px);',
        'Адаптивная верстка сайта (3 основных разрешения экрана);',
        'Программирование сайта;',
        'Тестирование сайта;',
        'Установка аналитики;',
    ];


    protected $executors = [
        ['технический директор','Сердюк Алексей'],
        ['арт- директор','Талдыкин Юрий'],
        ['иллюстратор','Мороз Антон'],
        ['ведущий дизайнер супервизор','Талдыкин Виталий'],
        ['менеджер проекта','Даниил Папуша'],
        ['верстальщик','Дудников Олег'],
        ['программист','Слончаков Сергей'],
    ];

    protected $for_complicate;

    protected $adaptive;

    public function indexAction($pdfLocation = null)
    {

        $em = $this->getDoctrine()->getManager();

        $sitesTypes = $em->getRepository('FreshCalcBundle:SitesTypes')->findAll();



        return $this->render('FreshCalcBundle:Index:calc.html.twig',
            array(
                'sitesTypes' => $sitesTypes,
                'pdfLocation' => $pdfLocation,
                'work_stages' => $this->workStages,
                'executors' => $this->executors,
            )
        );
    }

    public function showParametersAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $siteType = $em->getRepository('FreshCalcBundle:SitesTypes')->find($id);

        $siteTypeParameters = $em->getRepository('FreshCalcBundle:Parameters')->getParametersForSiteType($siteType->getId());

        //echo '<pre>';var_dump($siteTypeParameters1);die;

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

//        echo '<pre>';var_dump( $request->request );die;
        $companyName = $request->request->get('company');
        $name = $request->request->get('name');
        $surname = $request->request->get('surname');
        $secondname = $request->request->get('secondname');
        $email = $request->request->get('email');
        $tel = $request->request->get('tel');
        $site_type = $request->request->get('site_type');
        $notificatons = $request->request->get('notificatons');
        $choosedWorkStages = $request->request->get('work_stages');

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

        $this->for_complicate = 1+($request->request->get("for_complicate")/100);
        $this->adaptive = $request->request->get("adaptive")/100;

        $html = $this->renderView('FreshCalcBundle:PDF:pdfdoc.html.twig', array(
            'companyName'  => $companyName
        ));

        $filename = $this->returnPDFResponseFromHTML($html, $companyName, $name, $choosedWorkStages, $request);

        return new JsonResponse($filename);

    }

    public function returnPDFResponseFromHTML($html, $companyName='', $name='' , $choosedWorkStages='' , $request){
//        echo '<pre>';var_dump($request->request);die;

        $designArr = [];
        $designCustomArr = [];
        $programmingCustomArr = [];
        $programmingArr = [];
        $mark_upCustomArr = [];
        $mark_upArr = [];

        $em = $this->getDoctrine()->getManager();

        foreach ( $request->request as $key => $value ) {

            if ( strripos( $key, 'design' )  ) {
                if ( strripos( $key, 'new' ) ) {
                    $designCustomArr[$key] = $value;
                } else {
                    $designArr[preg_replace( '/\D/', '' , $key )][] = $value;
                }

            } elseif ( strripos( $key, 'programming' ) ) {

                if ( strripos( $key, 'new' ) ) {
                    $programmingCustomArr[$key] = $value;
                } else {
                    $programmingArr[preg_replace( '/\D/', '' , $key )][] = $value;
                }

            } elseif ( strripos( $key, 'mark_up' ) ) {
                if ( strripos( $key, 'new' ) ) {
                    $mark_upCustomArr[$key] = $value;
                } else {
                    $mark_upArr[preg_replace( '/\D/', '' , $key )][] = $value;
                }
            }

        }

        $pdf = $this->get("white_october.tcpdf")->create('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        $fontname1 = TCPDF_FONTS::addTTFfont( $_SERVER['DOCUMENT_ROOT'].'web/fonts/PTC75F.ttf', 'TrueTypeUnicode', '', 96);
        $fontname2 = TCPDF_FONTS::addTTFfont( $_SERVER['DOCUMENT_ROOT'].'web/fonts/PTC55F.ttf', 'TrueTypeUnicode', '', 96);

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

//Страница основных этапов работ (4 стр.)
        $pdf->AddPage();
        $bMargin = $pdf->getBreakMargin();
        $auto_page_break = $pdf->getAutoPageBreak();
        $pdf->SetAutoPageBreak(false, 0);
        $img_file = $_SERVER['DOCUMENT_ROOT'].'web/images/main_stages_of_work.jpg';
        $pdf->Image($img_file, 0, 0, 297, 210, '', '', '', false, 1300, '', false, false, 0);

        $choosedWorkStagesArr = array_diff( explode( ',' , $choosedWorkStages ) , array('') );

        $workStageHeight=65;
        $workStages = $this->workStages;
        foreach ( $choosedWorkStagesArr as $workStage) {
            $workStageHeight += 10;
            $pdf->writeHTMLCell( '' , '', 50, $workStageHeight, '<p style="font-size: 10px;color: #222222;">&bull; '.$workStages[$workStage].'</p>', $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = '', $autopadding = true);
        }

//Смета по разработке (Этап 1) (5 стр.)
        $pdf->AddPage();
        $bMargin = $pdf->getBreakMargin();
        $auto_page_break = $pdf->getAutoPageBreak();
        $pdf->SetAutoPageBreak(false, 0);
        $img_file = $_SERVER['DOCUMENT_ROOT'].'web/images/first-step.jpg';
        $pdf->Image($img_file, 0, 0, 297, 210, '', '', '', false, 1300, '', false, false, 0);

        $firstStepExecutorsArr = array_diff( explode( ',' , $request->request->get('first_step_executors') ) , array('') );

        $firstStepExecutorHeight=44;
        $executors = $this->executors;
        foreach ( $firstStepExecutorsArr as $firstStepExecutor ) {
            $firstStepExecutorHeight += 10;

            $pdf->writeHTMLCell( '' , '', 147, $firstStepExecutorHeight, '<p style="font-size: 10px;color: #222222;font-weight: lighter;">'.$executors[$firstStepExecutor][1].'</p>', $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = '', $autopadding = true);
            $pdf->writeHTMLCell( '' , '', 147, $firstStepExecutorHeight+5, '<p style="font-size: 7px;color: #222222;font-weight:100;">'.$executors[$firstStepExecutor][0].'</p>', $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = '', $autopadding = true);
        }

        $pdf->writeHTMLCell( '' , '', 207, 55, '<p style="font-size: 10px;color: #222222;">'.( $request->request->get('first_step-1') ).'</p>', $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = '', $autopadding = true);
        $pdf->writeHTMLCell( '' , '', 240, 55, '<p style="font-size: 10px;color: #222222;">'.( ( $request->request->get('first_step-1') )*( $request->request->get('first_step_hour_price') ) ).'</p>', $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = '', $autopadding = true);

        $pdf->writeHTMLCell( '' , '', 207, 68, '<p style="font-size: 10px;color: #222222;">'.( $request->request->get('first_step-2') ).'</p>', $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = '', $autopadding = true);
        $pdf->writeHTMLCell( '' , '', 240, 68, '<p style="font-size: 10px;color: #222222;">'.( ( $request->request->get('first_step-2') )*( $request->request->get('first_step_hour_price') ) ).'</p>', $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = '', $autopadding = true);

        $pdf->writeHTMLCell( '' , '', 207, 78, '<p style="font-weight: bold;font-size: 10px;color: #222222;">'.(( $request->request->get('first_step-1') )+( $request->request->get('first_step-2') )).'</p>', $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = '', $autopadding = true);
        $pdf->writeHTMLCell( '' , '', 240, 78, '<p style="font-weight: bold;font-size: 10px;color: #222222;">'.( ( $request->request->get('first_step-2')+$request->request->get('first_step-1') )*( $request->request->get('first_step_hour_price') ) ).'</p>', $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = '', $autopadding = true);


//Смета разработки дизайна (Этап 2) (6 стр.)
        $design_hour_price = $request->request->get("design_hour_price");
        $designFinishArr = $this -> getResultArr( $designArr , $designCustomArr , $design_hour_price );

//        foreach ($designArr as $paramId => $paramValue) {
//
//            if ( $paramValue[0] != 'on' || !$paramValue[1] ) {
//                unset($designArr[$paramId]);
//                continue;
//            }
//
//            $designArr[$paramId] = $paramValue[1];
//            $designIdsArr[] = $paramId;
//        }
//
//        $parametersNames = $em->getRepository('FreshCalcBundle:Parameters')->getParametersByIds($designIdsArr);
//
//        foreach ( $parametersNames as $parametersNamesVal) {
//
//            $designFinishArr[$parametersNamesVal['id']]['name'] = $parametersNamesVal['parameterName'];
//            $designFinishArr[$parametersNamesVal['id']]['value'] = $designArr[$parametersNamesVal['id']];
//        }
//
//        if ( $designCustomArr ) {
//
//            foreach ( $designCustomArr as $customParamKey => $customParamValue) {
//
//                $customParamId = explode( '-' , $customParamKey );
//
//                $designCustomArrNew[$customParamId[0]][] = $customParamValue;
//            }
//
//            foreach ( $designCustomArrNew as $customParamKeyNew => $customParamValueNew ) {
//
//                if ( $customParamValueNew[0] != 'on' || !$customParamValueNew[2] || !$customParamValueNew[1] ) continue;
//
//                $designFinishArr[$customParamKeyNew.'-new']['name'] = $customParamValueNew[1];
//                $designFinishArr[$customParamKeyNew.'-new']['value'] = $customParamValueNew[2];
//            }
//        }
//
        $pdf->SetFont($fontname2, '', 14, '', false);

        if ( count($designFinishArr) <= 18 ) {

            $pdf->AddPage();
            $bMargin = $pdf->getBreakMargin();
            $auto_page_break = $pdf->getAutoPageBreak();
            $pdf->SetAutoPageBreak(false, 0);
            $img_file = $_SERVER['DOCUMENT_ROOT'].'web/images/design-single.jpg';
            $pdf->Image($img_file, 0, 0, 297, 210, '', '', '', false, 1300, '', false, false, 0);

            $totalDesign = 0;

            foreach ( $designFinishArr as $designFinishVal ) {

                $totalDesign += $designFinishVal["value"];

            }

            $adaptiveDesign = round($totalDesign*($this->adaptive));
            array_push($designFinishArr, [ 'name'=>'Адаптивная версия дизайна', 'value'=>$adaptiveDesign, 'price'=>$adaptiveDesign*$design_hour_price] );

            $html123 = $this->renderView('FreshCalcBundle:PDF:tableTemplate.html.twig', array(
                'designFinishArr'  => $designFinishArr,
                'total' => [ 'name'=>'ИТОГО 2Й ЭТАП', 'value'=>$totalDesign+$adaptiveDesign, 'price'=>($totalDesign+$adaptiveDesign)*$design_hour_price ]
            ));

            $pdf->writeHTMLCell( '' , '', 49, 55, $html123, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = '', $autopadding = true);



        } elseif ( count($designFinishArr) > 18 && count($designFinishArr) <= 36 ) {

        } else {

        }

        $pdf->SetFont($fontname1, '', 14, '', false);
        $designExecutorsArr = array_diff( explode( ',' , $request->request->get('design_executors') ) , array('') );

        $designExecutorsHeight=44;
        $executors = $this->executors;
        foreach ( $designExecutorsArr as $designExecutor ) {
            $designExecutorsHeight += 10;

            $pdf->writeHTMLCell( '' , '', 147, $designExecutorsHeight, '<p style="font-size: 10px;color: #222222;font-weight: lighter;">'.$executors[$designExecutor][1].'</p>', $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = '', $autopadding = true);
            $pdf->writeHTMLCell( '' , '', 147, $designExecutorsHeight+5, '<p style="font-size: 7px;color: #222222;font-weight:100;">'.$executors[$designExecutor][0].'</p>', $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = '', $autopadding = true);
        }


//        echo '<pre>';var_dump($designFinishArr);die;


//        $siteTypeParameters1 = $em->getRepository('FreshCalcBundle:Parameters')->getParametersByIds([]);











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




    protected function getResultArr( $fixadParametersArr=[] , $customParametersArr=[] , $hourPrice )
    {
        $forComplicate = $this->for_complicate;

        $em = $this->getDoctrine()->getManager();
        if ( $fixadParametersArr ) {

            foreach ($fixadParametersArr as $paramId => $paramValue) {

                if ( $paramValue[0] != 'on' || !$paramValue[1] ) {
                    unset($fixadParametersArr[$paramId]);
                    continue;
                }

                $parametersArr[$paramId] = $paramValue[1];
                $designIdsArr[] = $paramId;
            }

            $parametersNames = $em->getRepository('FreshCalcBundle:Parameters')->getParametersByIds($designIdsArr);

            foreach ( $parametersNames as $parametersNamesVal) {

                $finishArr[$parametersNamesVal['id']]['name'] = $parametersNamesVal['parameterName'];
                $finishArr[$parametersNamesVal['id']]['value'] = round($parametersArr[$parametersNamesVal['id']]*$forComplicate);
                $finishArr[$parametersNamesVal['id']]['price'] = round($parametersArr[$parametersNamesVal['id']]*$forComplicate)*$hourPrice;
            }
        }


        if ( $customParametersArr ) {

            foreach ( $customParametersArr as $customParamKey => $customParamValue) {

                $customParamId = explode( '-' , $customParamKey );

                $customParamArrNew[$customParamId[0]][] = $customParamValue;
            }

            foreach ( $customParamArrNew as $customParamKeyNew => $customParamValueNew ) {

                if ( $customParamValueNew[0] != 'on' || !$customParamValueNew[2] || !$customParamValueNew[1] ) continue;

                $finishArr[$customParamKeyNew.'-new']['name'] = $customParamValueNew[1];
                $finishArr[$customParamKeyNew.'-new']['value'] = round($customParamValueNew[2]*$forComplicate);
                $finishArr[$customParamKeyNew.'-new']['price'] = round($customParamValueNew[2]*$forComplicate)*$hourPrice;
            }
        }

        return $finishArr;

    }








}
