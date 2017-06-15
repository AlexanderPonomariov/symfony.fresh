<?php

namespace Fresh\PatentingBundle\Controller;

use Fresh\PatentingBundle\Entity\Contracts;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\TemplateProcessor;
use Symfony\Component\Validator\Constraints\DateTime;

class PatentingController extends Controller
{
    protected $monthUkr = ['','cічня', 'лютого','березеня','квітня','травня','червня','липеня','серпня','вересня','жовтня','листопада','грудня',];

    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $selfOrganizations = $em->getRepository('FreshPatentingBundle:LegalEntities')->findBy(array('isSelfOrganization' => 1));

        $organizations = $em->getRepository('FreshPatentingBundle:LegalEntities')->getOrganizations(0);

        return $this->render('FreshPatentingBundle:Patenting:index.html.twig',
            array(
                'selfOrganizations' => $selfOrganizations,
                'organizations' => $organizations,
            )
        );
    }


    public function getOrganizationsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $organizations = $em->getRepository('FreshPatentingBundle:LegalEntities')->getOrganizations($request->request->get('orgType'));
        //echo '<pre>';var_dump($organizations);die;

        $organizationTypeHtml = $this->renderView('FreshPatentingBundle:Patenting:organization_data_forms.html.twig', array(
            'organizationType'  => $request->request->get('orgType')
        ));



        return new JsonResponse(array('organizations' => $organizations, 'organizationTypeHtml' => $organizationTypeHtml));
    }


    public function getOrganizationDataAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $organizationData = $em->getRepository('FreshPatentingBundle:LegalEntities')->find($request->request->get('orgId'));

        $organizationTypeHtml = $this->renderView('FreshPatentingBundle:Patenting:organization_data_forms.html.twig', array(
            'organizationData'  => $organizationData
        ));

        //echo '<pre>';var_dump($organizationTypeHtml);die;
        return new JsonResponse(array('organizationTypeHtml' => $organizationTypeHtml));

    }


    public function createWordDocumentContractAction(Request $request)
    {
//        echo '<pre>';var_dump($request->request);die;

        $em = $this->getDoctrine()->getManager();

        $ourEntityId = $request->request->get('our_entity');

        $ourEntity = $em->getRepository('FreshPatentingBundle:LegalEntities')->findBy(array('id' => $ourEntityId));

        $templatesPath = $_SERVER['DOCUMENT_ROOT'].'/web/patenting_templates/';

        if( !$ourEntity[0]->getorganizationType()/*phisical*/ && !$request->request->get('legal_entity_type')/*phisical*/ ) {

            $contract = new TemplateProcessor($templatesPath.'contracts/contract_our_phisical_to_phisical.docx');
            $proxy = new TemplateProcessor($templatesPath.'proxies/proxy_our_legal_to_phisical.docx');

        } else if ( !$ourEntity[0]->getorganizationType()/*phisical*/ && $request->request->get('legal_entity_type')/*legal*/ ) {

            $contract = new TemplateProcessor($templatesPath.'contracts/contract_our_phisical_to_legal.docx');
            $proxy = new TemplateProcessor($templatesPath.'proxies/proxy_our_legal_to_legal.docx');

        } else if ( $ourEntity[0]->getorganizationType()/*legal*/ && $request->request->get('legal_entity_type')/*legal*/ ) {

            $contract = new TemplateProcessor($templatesPath.'contracts/contract_our_legal_to_legal.docx');
            $proxy = new TemplateProcessor($templatesPath.'proxies/proxy_our_legal_to_legal.docx');

        } else {/*legal - phisical*/

            $contract = new TemplateProcessor($templatesPath.'contracts/contract_our_legal_to_phisical.docx');
            $proxy = new TemplateProcessor($templatesPath.'proxies/proxy_our_legal_to_phisical.docx');

        }

        $monthUkr = $this->monthUkr;
        $date = new \DateTime();
        $contractDate = '«'.( $date->format('d') ).'» '.( $monthUkr[$date->format('n')] ).' '.( $date->format('Y') );

        /*==============================================================*/
        /* Add data to contract*/
        /*==============================================================*/
        $contract->setValue('surname', $request->request->get('surname') );
        $contract->setValue('name', $request->request->get('name') );
        $contract->setValue('second_name', $request->request->get('second_name') );
        $contract->setValue('name_short', mb_substr ( $request->request->get('name') , 0 , 1 ) );
        $contract->setValue('second_name_short', mb_substr ( $request->request->get('second_name') , 0 , 1 ) );
        $contract->setValue('organization_name', $request->request->get('organization_name') );
        $contract->setValue('identification_code', $request->request->get('identification_code') );
        $contract->setValue('passport_series', $request->request->get('passport_series') );
        $contract->setValue('passport_number', $request->request->get('passport_number') );
        $contract->setValue('passport_other', $request->request->get('passport_other') );
        $contract->setValue('customer_adress', $request->request->get('address') );

        $ourNamePhisical = ( $ourEntity[0]->getSurname() ).' '.( $ourEntity[0]->getName() ).' '.( $ourEntity[0]->getSecondName() ) ;
        $contract->setValue('our_name_phisical', $ourNamePhisical);
        $contract->setValue('our_name', $ourEntity[0]->getName() );
        $contract->setValue('our_second_name', $ourEntity[0]->getSecondName() );
        $contract->setValue('our_surname', $ourEntity[0]->getSurname() );
        $contract->setValue('our_organization_name', $ourEntity[0]->getOrganizationName());
        $contract->setValue('our_adress', $ourEntity[0]->getAddress() );
        $contract->setValue('our_name_short', mb_substr ( $ourEntity[0]->getName() , 0 , 1 ) );
        $contract->setValue('our_second_name_short', mb_substr ( $ourEntity[0]->getSecondName() , 0 , 1 ) );
        $contract->setValue('our_surname', $ourEntity[0]->getSurname() );
        $contract->setValue('our_identification_code', $ourEntity[0]->getIdentificationCode() );

        $contract->setValue('contract_date', $contractDate );
        $contract->setValue('contract_end_date', $date->format('Y')+2 );
        /*==============================================================*/
        /* END Add data to contract*/
        /*==============================================================*/

        /*==============================================================*/
        /* Add data to proxy*/
        /*==============================================================*/
        $proxy->setValue('surname', $request->request->get('surname') );
        $proxy->setValue('name', $request->request->get('name') );
        $proxy->setValue('second_name', $request->request->get('second_name') );
        $proxy->setValue('name_short', mb_substr ( $request->request->get('name') , 0 , 1 ) );
        $proxy->setValue('second_name_short', mb_substr ( $request->request->get('second_name') , 0 , 1 ) );
        $proxy->setValue('organization_name', $request->request->get('organization_name') );
        $proxy->setValue('identification_code', $request->request->get('identification_code') );
        $proxy->setValue('passport_series', $request->request->get('passport_series') );
        $proxy->setValue('passport_number', $request->request->get('passport_number') );
        $proxy->setValue('passport_other', $request->request->get('passport_other') );
        $proxy->setValue('customer_adress', $request->request->get('address') );
        $proxy->setValue('proxy_date', $contractDate );
        /*==============================================================*/
        /* END Add data to proxy*/
        /*==============================================================*/


        $lastContractNumber = $em->getRepository('FreshPatentingBundle:Contracts')->getMaxId();
        $contractNumber = ($ourEntity[0]->getPassportSeries()).'-'. (++$lastContractNumber[1]);
        $contract->setValue('contract_number', $contractNumber );
        // $contractAdd  = new Contracts();
        // $contractAdd->setContractNumber($contractNumber);
        // if ( $request->request->get('organizations') ) {
        //     $legalEntity = $em->getRepository('FreshPatentingBundle:LegalEntities')->find($request->request->get('organizations'));
        //     $contractAdd->setEntity($legalEntity);
        // }
        // $em->persist($contractAdd);
        // $em->flush();


        $savePath = $_SERVER['DOCUMENT_ROOT'].'/web/patenting_documents/';

        if ( !file_exists ( $savePath.'temporary' ) ) {
            mkdir($savePath.'temporary');
        }
        if ( file_exists ( $savePath.'archives/contract-sample.zip' ) ) {
            unlink ($savePath.'archives/contract-sample.zip' );
        }

        $contract->saveAs($savePath.'temporary/contract.docx');
        $proxy->saveAs($savePath.'temporary/proxy.docx');

        $zip = new \ZipArchive;
        //$zip->open($savePath.'/archives/contract-'.$contractNumber.'.zip', \ZipArchive::CREATE);
        $zip->open($savePath.'archives/contract-sample.zip', \ZipArchive::CREATE);
        $zip->addFile($savePath.'temporary/proxy.docx', 'proxy-for-contract-'.$contractNumber.'.docx');
        $zip->addFile($savePath.'temporary/contract.docx', 'contract-'.$contractNumber.'.docx' );

        $specification = $this->createSpecification(
            $request->request->get('prices_type'),
            $request->request->get('registrations_urgency'),
            $request->request->get('trademarks_type'),
            $request->request->get('trademarks_classes'),
            $request->request->get('declarants_quantity'),
            $request->request->get('search_neaded'),
            $request->request->get('colority'),
            $contractNumber
        );

        $zip->addFile($savePath.'temporary/'.$specification , $specification );

        $zip->close();

        $this->dirDel($savePath.'temporary');

        return new JsonResponse(array(
            'archive' => 'web/patenting_documents/archives/contract-sample.zip',
            'contract_number' => $contractNumber,
        ));

    }


    protected function createSpecification( $prices_type, $registrations_urgency, $trademarks_type, $trademarks_classes, $declarants_quantity = false, $search_neaded = false, $colority = false, $contractNumber ) {

        //echo '<pre>';var_dump($prices_type);var_dump($registrations_urgency);var_dump($trademarks_type);var_dump($trademarks_classes);var_dump($declarants_quantity);var_dump($search_neaded);var_dump($colority);die;

        $templatesPath = $_SERVER['DOCUMENT_ROOT'].'/web/patenting_templates/';
        $savePath = $_SERVER['DOCUMENT_ROOT'].'/web/patenting_documents/';

        $trademarksClasses = array_diff( explode ( ',' , $trademarks_classes ) , array('') );

        $countTrademarksClasses = count( $trademarksClasses );

        $em = $this->getDoctrine()->getManager();

        $additionalPrices = $em->getRepository('FreshPatentingBundle:PatentingPrices')->findBy(array('registartionUrgency' => 0));

        $pricesByData = $em->getRepository('FreshPatentingBundle:PatentingPrices')
            ->findBy(array(
                'registartionUrgency' => $registrations_urgency,
                'isPartner' => $prices_type,
                'registartionType' => $trademarks_type,
            ));

        $payCollectionForSearchPrice = $pricesByData[0]->getPrice() + ( $countTrademarksClasses-1 ) * ( $pricesByData[1]->getPrice() );
        $formalizationOfApplicationPrice = $pricesByData[3]->getPrice() + ( $countTrademarksClasses-1 ) * ( $pricesByData[4]->getPrice() );
        $payFeeForFilingApplicationPrice = $pricesByData[5]->getPrice() + ( $countTrademarksClasses-1 ) * ( $pricesByData[6]->getPrice() ) + ( $colority ? $additionalPrices[0]->getPrice() : 0 ) + ( $declarants_quantity ? $additionalPrices[2]->getPrice() : 0 ) * $countTrademarksClasses;
        $taxForCertification = $registrations_urgency == 1 ? $pricesByData[7] : $pricesByData[10] ;
        $payFeeForPublication = $registrations_urgency == 1 ? $pricesByData[8] : $pricesByData[11] ;
        $payFeeForPublicationOther =  $registrations_urgency == 1 ? $pricesByData[9] : $pricesByData[12] ;
        $payFeeForPublicationPrice = $payFeeForPublication->getPrice() + ( $countTrademarksClasses-1 ) * ( $payFeeForPublicationOther->getPrice() ) + ( $colority ? $additionalPrices[1]->getPrice() : 0 );
        $forGetingCertificate = $registrations_urgency == 1 ? $pricesByData[10] : $pricesByData[13] ;
        $payFeeForAcceleratedRegistartionPrice = $pricesByData[8]->getPrice() + ( $countTrademarksClasses-1 ) * ( $pricesByData[9]->getPrice() );

        if( $registrations_urgency == 1/*Standart*/ && $search_neaded/*With search*/ ) {

            $specification = new TemplateProcessor($templatesPath.'specification_standart_with_search.docx');

            $totalPrice = $payCollectionForSearchPrice + $pricesByData[2]->getPrice() + $formalizationOfApplicationPrice + $payFeeForFilingApplicationPrice + $payFeeForPublicationPrice + $taxForCertification->getPrice() + $forGetingCertificate->getPrice();

        } else if ( $registrations_urgency == 1/*Urgent*/ && !$search_neaded/*Without search*/ ) {

            $specification = new TemplateProcessor($templatesPath.'specification_standart_without_search.docx');

            $totalPrice = $formalizationOfApplicationPrice + $payFeeForFilingApplicationPrice + $payFeeForPublicationPrice + $taxForCertification->getPrice() + $forGetingCertificate->getPrice();

        } else if ( $registrations_urgency == 2/*Accelerated*/ ) {

            $specification = new TemplateProcessor($templatesPath.'specification_urgent.docx');

            $totalPrice = $pricesByData[2]->getPrice() + $payCollectionForSearchPrice + $formalizationOfApplicationPrice + $payFeeForFilingApplicationPrice + $pricesByData[7]->getPrice() + $payFeeForAcceleratedRegistartionPrice + $payFeeForPublicationPrice + $taxForCertification->getPrice() + $forGetingCertificate->getPrice();

        } else if ( $registrations_urgency == 3/*Accelerated*/ ) {

            $specification = new TemplateProcessor($templatesPath.'specification_accelerated.docx');

            $totalPrice = $formalizationOfApplicationPrice + $pricesByData[2]->getPrice() + $formalizationOfApplicationPrice + $payFeeForFilingApplicationPrice + $pricesByData[7]->getPrice() + $payFeeForAcceleratedRegistartionPrice + $payFeeForPublicationPrice + $taxForCertification->getPrice() + $forGetingCertificate->getPrice();
        }

        $monthUkr = $this->monthUkr;
        $date = new \DateTime();
        $specificationDate = '«'.( $date->format('d') ).'» '.( $monthUkr[$date->format('n')] ).' '.( $date->format('Y') );

        $specification->setValue('specification_date', $specificationDate );
        $specification->setValue('contract_number', $contractNumber );
        $specification->setValue('classes', implode ( ';' , $trademarksClasses ) );

        $specification->setValue('pay_collection_for_search', $pricesByData[0]->getServiceName() );
        $specification->setValue('pay_collection_for_search_price', $payCollectionForSearchPrice );

        $specification->setValue('preliminary_search', $pricesByData[2]->getServiceName() );
        $specification->setValue('preliminary_search_price', $pricesByData[2]->getPrice() );

        $specification->setValue('formalization_of_application', $pricesByData[3]->getServiceName() );
        $specification->setValue('formalization_of_application_price', $formalizationOfApplicationPrice );

        $specification->setValue('pay_fee_for_filing_application', $pricesByData[5]->getServiceName() );
        $specification->setValue('pay_fee_for_filing_application_price', $payFeeForFilingApplicationPrice );

        $specification->setValue('tax_for_certification', $taxForCertification->getServiceName() );
        $specification->setValue('tax_for_certification_price', $taxForCertification->getPrice() );

        $specification->setValue('pay_fee_for_publication', $payFeeForPublication->getServiceName()  );
        $specification->setValue('pay_fee_for_publication_price', $payFeeForPublicationPrice );

        $specification->setValue('for_geting_certificate', $forGetingCertificate->getServiceName() );
        $specification->setValue('for_geting_certificate_price', $forGetingCertificate->getPrice() );

        $specification->setValue('formalization_of_documents_on_accelerating', $pricesByData[7]->getServiceName() );
        $specification->setValue('formalization_of_documents_on_accelerating_price', $pricesByData[7]->getPrice() );

        $specification->setValue('pay_fee_for_accelerated_registartion', $pricesByData[8]->getServiceName()  );
        $specification->setValue('pay_fee_for_accelerated_registartion_price', $payFeeForAcceleratedRegistartionPrice );

        $specification->setValue('total_price', $totalPrice );


        //${${pay_fee_for_accelerated_registartion_price}},

        $a = ( $declarants_quantity ? $additionalPrices[2]->getPrice() : 0 );

        $specification->saveAs($savePath.'temporary/specification-'.$contractNumber.'.docx');

        return 'specification-'.$contractNumber.'.docx';

    }


    protected function dirDel ($dir)
    {
        $d = opendir( $dir );
        while( ($entry = readdir($d))!== false )
        {
            if ( $entry != "." && $entry != "..")
            {
                if (is_dir($dir."/".$entry))
                {
                    dirDel($dir."/".$entry);
                } else {
                    unlink ($dir."/".$entry );
                }
            }
        }
        closedir( $d );
        rmdir ( $dir );
    }

    public function saveAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $ourEntityId = $request->request->get('our_entity');

        $ourEntity = $em->getRepository('FreshPatentingBundle:LegalEntities')->findBy(array('id' => $ourEntityId));

        $lastContractNumber = $em->getRepository('FreshPatentingBundle:Contracts')->getMaxId();
        $contractNumber = ($ourEntity[0]->getPassportSeries()).'-'. (++$lastContractNumber[1]);









        $contractAdd  = new Contracts();
        $contractAdd->setContractNumber($contractNumber);
        if ( $request->request->get('organizations') ) {
            $legalEntity = $em->getRepository('FreshPatentingBundle:LegalEntities')->find($request->request->get('organizations'));
            $contractAdd->setEntity($legalEntity);
        }
        $em->persist($contractAdd);
        $em->flush();


    }





}
