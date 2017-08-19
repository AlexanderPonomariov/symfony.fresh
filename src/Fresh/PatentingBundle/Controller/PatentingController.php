<?php

namespace Fresh\PatentingBundle\Controller;

use Fresh\PatentingBundle\Entity\Contracts;
use Fresh\PatentingBundle\Entity\LegalEntities;
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
    protected $monthUkr = ['','cічня', 'лютого','березеня','квітня','травня','червня','липня','серпня','вересня','жовтня','листопада','грудня',];

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
        $contracts = $em->getRepository('FreshPatentingBundle:Contracts')->findBy(array( 'entity' => $request->request->get('orgId') ) );

        $organizationTypeHtml = $this->renderView('FreshPatentingBundle:Patenting:organization_data_forms.html.twig', array(
            'organizationData'  => $organizationData,
            'contracts'  => $contracts,
        ));

        //echo '<pre>';var_dump($organizationTypeHtml);die;
        return new JsonResponse(array('organizationTypeHtml' => $organizationTypeHtml));

    }


    public function createWordDocumentContractAction(Request $request)
    {
        //echo '<pre>';var_dump($request->request);die;
        mb_internal_encoding("UTF-8");

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
        if ( !$request->request->get('contracts') ) {
            $contract->setValue('surname', $request->request->get('surname'));
            $contract->setValue('name', $request->request->get('name'));
            $contract->setValue('second_name', $request->request->get('second_name'));
            $contract->setValue('name_short', mb_substr($request->request->get('name'), 0, 1));
            $contract->setValue('second_name_short', mb_substr($request->request->get('second_name'), 0, 1));
            $contract->setValue('organization_name', $request->request->get('organization_name'));
            $contract->setValue('identification_code', $request->request->get('identification_code'));
            $contract->setValue('passport_series', $request->request->get('passport_series'));
            $contract->setValue('passport_number', $request->request->get('passport_number'));
            $contract->setValue('passport_other', $request->request->get('passport_other'));
            $contract->setValue('customer_adress', $request->request->get('address'));

            $ourNamePhisical = ($ourEntity[0]->getSurname()) . ' ' . ($ourEntity[0]->getName()) . ' ' . ($ourEntity[0]->getSecondName());
            $contract->setValue('our_name_phisical', $ourNamePhisical);
            $contract->setValue('our_name', $ourEntity[0]->getName());
            $contract->setValue('our_second_name', $ourEntity[0]->getSecondName());
            $contract->setValue('our_surname', $ourEntity[0]->getSurname());
            $contract->setValue('our_organization_name', $ourEntity[0]->getOrganizationName());
            $contract->setValue('our_adress', $ourEntity[0]->getAddress());
            $contract->setValue('our_name_short', mb_substr($ourEntity[0]->getName(), 0, 1));
            $contract->setValue('our_second_name_short', mb_substr($ourEntity[0]->getSecondName(), 0, 1));
            $contract->setValue('our_surname', $ourEntity[0]->getSurname());
            $contract->setValue('our_identification_code', $ourEntity[0]->getIdentificationCode());
            $contract->setValue('our_basis', $ourEntity[0]->getPassportOther());

            $contract->setValue('contract_date', $contractDate);
            $contract->setValue('contract_end_date', $date->format('Y') + 2);

            /*==============================================================*/
            /* END Add data to contract*/
            /*==============================================================*/

            /*==============================================================*/
            /* Add data to proxy*/
            /*==============================================================*/
            $proxy->setValue('surname', $request->request->get('surname'));
            $proxy->setValue('name', $request->request->get('name'));
            $proxy->setValue('second_name', $request->request->get('second_name'));
            $proxy->setValue('name_short', mb_substr($request->request->get('name'), 0, 1));
            $proxy->setValue('second_name_short', mb_substr($request->request->get('second_name'), 0, 1));
            $proxy->setValue('organization_name', $request->request->get('organization_name'));
            $proxy->setValue('identification_code', $request->request->get('identification_code'));
            $proxy->setValue('passport_series', $request->request->get('passport_series'));
            $proxy->setValue('passport_number', $request->request->get('passport_number'));
            $proxy->setValue('passport_other', $request->request->get('passport_other'));
            $proxy->setValue('customer_adress', $request->request->get('address'));
            $proxy->setValue('proxy_date', $contractDate);
            /*==============================================================*/
            /* END Add data to proxy*/
            /*==============================================================*/
        }

        $lastContractNumber = $em->getRepository('FreshPatentingBundle:Contracts')->getMaxId();
        $contractNumber = ($ourEntity[0]->getPassportSeries()).'-'. (++$lastContractNumber[1]);
        $contract->setValue('contract_number', $contractNumber );

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
        $zip->open($savePath.'archives/contract-sample.zip', \ZipArchive::CREATE);

        if ( !$request->request->get('contracts') ) {
            $zip->addFile($savePath . 'temporary/proxy.docx', 'proxy-for-contract-' . $contractNumber . '.docx');
            $zip->addFile($savePath . 'temporary/contract.docx', 'contract-' . $contractNumber . '.docx');
        }

        $specification = $this->createSpecification(
            $request->request->get('prices_type'),
            $request->request->get('registrations_urgency'),
            $request->request->get('trademarks_type'),
            $request->request->get('trademarks_classes'),
            $request->request->get('declarants_quantity'),
            $request->request->get('search_neaded'),
            $request->request->get('colority'),
            $contractNumber,
            $request->request->get('contracts'),
            mb_substr($request->request->get('name'), 0, 1).'. '.mb_substr($request->request->get('second_name'), 0, 1).'. '.$request->request->get('surname'),
            mb_substr($ourEntity[0]->getName(), 0, 1).'. '.mb_substr($ourEntity[0]->getSecondName(), 0, 1).'. '.$ourEntity[0]->getSurname()
        );

        $attachment = $this->createAttachment(
            $request->request->get('name'),
            $request->request->get('second_name'),
            $request->request->get('surname'),
            $request->request->get('address'),
            $request->request->get('trademarks_classes'),
            $contractNumber,
            $request->request->get('contracts'),
            $contractDate,
            mb_substr($ourEntity[0]->getName(), 0, 1).'. '.mb_substr($ourEntity[0]->getSecondName(), 0, 1).'. '.$ourEntity[0]->getSurname()
        );

        //echo '<pre>';var_dump($attachment);die;

        $zip->addFile($savePath.'temporary/'.$specification , $specification );
        $zip->addFile($savePath.'temporary/'.$attachment , $attachment );

        $zip->close();

        $this->dirDel($savePath.'temporary');

        return new JsonResponse(array(
            'archive' => 'web/patenting_documents/archives/contract-sample.zip',
            'contract_number' => $contractNumber,
        ));

    }


    protected function createSpecification( $prices_type, $registrations_urgency, $trademarks_type, $trademarks_classes, $declarants_quantity = false, $search_neaded = false, $colority = false, $contractNumber, $contractNumberExisting, $customerShortName, $ourShortName ) {

        //echo '<pre>';var_dump($prices_type);var_dump($registrations_urgency);var_dump($trademarks_type);var_dump($trademarks_classes);var_dump($declarants_quantity);var_dump($search_neaded);var_dump($colority);die;
        mb_internal_encoding("UTF-8");

        $contractNumber = $contractNumberExisting ? $contractNumberExisting : $contractNumber ;



        $templatesPath = $_SERVER['DOCUMENT_ROOT'].'/web/patenting_templates/specification/';
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

        if ( $contractNumberExisting ) {
            $attachmentNumberByContract = $em->getRepository('FreshPatentingBundle:Contracts')->findBy(array('contractNumber' => $contractNumberExisting ));
            $number = $attachmentNumberByContract[0]->getAttachmentNumber() + 1;
        } else {
            $number = 1;
        }

        //echo '<pre>';var_dump($number);die;

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

            $paragraph_6_1 = $payCollectionForSearchPrice + $pricesByData[2]->getPrice();
            $paragraph_6_2 = $formalizationOfApplicationPrice + $payFeeForFilingApplicationPrice;
            $paragraph_6_3 = $payFeeForPublicationPrice + $taxForCertification->getPrice() + $forGetingCertificate->getPrice();

//            $totalPrice = $payCollectionForSearchPrice + $pricesByData[2]->getPrice() + $formalizationOfApplicationPrice + $payFeeForFilingApplicationPrice + $payFeeForPublicationPrice + $taxForCertification->getPrice() + $forGetingCertificate->getPrice();
//
//            var_dump('1');
//            var_dump($paragraph_6_1);
//            var_dump($paragraph_6_2);
//            var_dump($paragraph_6_3);
//            var_dump($totalPrice);
//            die;

        } else if ( $registrations_urgency == 1/*Urgent*/ && !$search_neaded/*Without search*/ ) {

            $specification = new TemplateProcessor($templatesPath.'specification_standart_without_search.docx');

            $paragraph_6_1 = $formalizationOfApplicationPrice + $payFeeForFilingApplicationPrice;
            $paragraph_6_2 = $payFeeForPublicationPrice + $taxForCertification->getPrice() + $forGetingCertificate->getPrice();
            $paragraph_6_3 = '';

//            $totalPrice = $formalizationOfApplicationPrice + $payFeeForFilingApplicationPrice + $payFeeForPublicationPrice + $taxForCertification->getPrice() + $forGetingCertificate->getPrice();
//
//            var_dump('2');
//            var_dump($paragraph_6_1);
//            var_dump($paragraph_6_2);
//            var_dump($paragraph_6_3);
//            var_dump($totalPrice);
//            die;

        } else if ( $registrations_urgency == 3/*Urgent*/ ) {

            $specification = new TemplateProcessor($templatesPath.'specification_urgent.docx');

            $paragraph_6_1 = $payCollectionForSearchPrice + $pricesByData[2]->getPrice();
            $paragraph_6_2 = $formalizationOfApplicationPrice + $payFeeForFilingApplicationPrice + $pricesByData[7]->getPrice() + $payFeeForAcceleratedRegistartionPrice;
            $paragraph_6_3 = $payFeeForPublicationPrice + $taxForCertification->getPrice() + $forGetingCertificate->getPrice();

//            $totalPrice = $pricesByData[2]->getPrice() + $payCollectionForSearchPrice + $formalizationOfApplicationPrice + $payFeeForFilingApplicationPrice + $pricesByData[7]->getPrice() + $payFeeForAcceleratedRegistartionPrice + $payFeeForPublicationPrice + $taxForCertification->getPrice() + $forGetingCertificate->getPrice();
//
//            var_dump('3');
//            var_dump($paragraph_6_1);
//            var_dump($paragraph_6_2);
//            var_dump($paragraph_6_3);
//            var_dump($totalPrice);
            die;
        } else if ( $registrations_urgency == 2/*Accelerated*/ ) {

            $specification = new TemplateProcessor($templatesPath.'specification_accelerated.docx');

            $paragraph_6_1 = $payCollectionForSearchPrice + $pricesByData[2]->getPrice();
            $paragraph_6_2 = $formalizationOfApplicationPrice + $payFeeForFilingApplicationPrice + $pricesByData[7]->getPrice() + $payFeeForAcceleratedRegistartionPrice;
            $paragraph_6_3 = $payFeeForPublicationPrice + $taxForCertification->getPrice() + $forGetingCertificate->getPrice();
//
//            $totalPrice = $formalizationOfApplicationPrice + $pricesByData[2]->getPrice() + $formalizationOfApplicationPrice + $payFeeForFilingApplicationPrice + $pricesByData[7]->getPrice() + $payFeeForAcceleratedRegistartionPrice + $payFeeForPublicationPrice + $taxForCertification->getPrice() + $forGetingCertificate->getPrice();
//
//            var_dump('4');
//            var_dump($paragraph_6_1);
//            var_dump($paragraph_6_2);
//            var_dump($paragraph_6_3);
//            var_dump($totalPrice);
//            die;
        }

        $totalPrice = $paragraph_6_1 + $paragraph_6_2 + $paragraph_6_3;

        $monthUkr = $this->monthUkr;
        $date = new \DateTime();
        $specificationDate = '«'.( $date->format('d') ).'» '.( $monthUkr[$date->format('n')] ).' '.( $date->format('Y') );

        $specification->setValue('specification_date', $specificationDate );
        $specification->setValue('contract_number', $contractNumber );
        $specification->setValue('classes', implode ( ';' , $trademarksClasses ) );

        $specification->setValue('pay_collection_for_search', $pricesByData[0]->getServiceName() );
        $specification->setValue('pay_collection_for_search_price', sprintf("%.2f", $payCollectionForSearchPrice) );

        $specification->setValue('preliminary_search', $pricesByData[2]->getServiceName() );
        $specification->setValue('preliminary_search_price', sprintf("%.2f", $pricesByData[2]->getPrice()) );

        $specification->setValue('formalization_of_application', $pricesByData[3]->getServiceName() );
        $specification->setValue('formalization_of_application_price', sprintf("%.2f", $formalizationOfApplicationPrice) );

        $specification->setValue('pay_fee_for_filing_application', $pricesByData[5]->getServiceName() );
        $specification->setValue('pay_fee_for_filing_application_price', sprintf("%.2f", $payFeeForFilingApplicationPrice) );

        $specification->setValue('tax_for_certification', $taxForCertification->getServiceName() );
        $specification->setValue('tax_for_certification_price', sprintf("%.2f", $taxForCertification->getPrice()) );

        $specification->setValue('pay_fee_for_publication', $payFeeForPublication->getServiceName()  );
        $specification->setValue('pay_fee_for_publication_price', sprintf("%.2f", $payFeeForPublicationPrice) );

        $specification->setValue('for_geting_certificate', $forGetingCertificate->getServiceName() );
        $specification->setValue('for_geting_certificate_price', sprintf("%.2f", $forGetingCertificate->getPrice()) );

        $specification->setValue('formalization_of_documents_on_accelerating', $pricesByData[7]->getServiceName() );
        $specification->setValue('formalization_of_documents_on_accelerating_price', sprintf("%.2f", $pricesByData[7]->getPrice()) );

        $specification->setValue('pay_fee_for_accelerated_registartion', $pricesByData[8]->getServiceName()  );
        $specification->setValue('pay_fee_for_accelerated_registartion_price', sprintf("%.2f", $payFeeForAcceleratedRegistartionPrice) );

        $specification->setValue('total_price', sprintf("%.2f", $totalPrice) );
        $specification->setValue('total_price_text', $this->num2text_ua($totalPrice) );

        $specification->setValue('attachment_number', $number*2-1 );
        $specification->setValue('number', $number );

        $specification->setValue('paragraph_6_1', sprintf("%.2f", $paragraph_6_1) );
        $specification->setValue('paragraph_6_1_text', $this->num2text_ua($paragraph_6_1) );
        $specification->setValue('paragraph_6_2', sprintf("%.2f", $paragraph_6_2) );
        $specification->setValue('paragraph_6_2_text', $this->num2text_ua($paragraph_6_2) );
        $specification->setValue('paragraph_6_3', sprintf("%.2f", $paragraph_6_3) );
        $specification->setValue('paragraph_6_3_text', $this->num2text_ua($paragraph_6_3) );

        $specification->setValue('customer_short_name', $customerShortName );
        $specification->setValue('our_short_name', $ourShortName );

        $specification->saveAs($savePath.'temporary/specification-'.$contractNumber.'.docx');

        return 'specification-'.$contractNumber.'.docx';

    }

    protected function createAttachment( $name , $secondName , $surname , $address , $trademarks_classes , $contractNumber , $contractNumberExisting , $date , $ourShortName )
    {
		mb_internal_encoding("UTF-8");
        $em = $this->getDoctrine()->getManager();

        $templatesPath = $_SERVER['DOCUMENT_ROOT'].'/web/patenting_templates/attachment/';
        $savePath = $_SERVER['DOCUMENT_ROOT'].'/web/patenting_documents/';

        $contractNumber = $contractNumberExisting ? $contractNumberExisting : $contractNumber ;

        $trademarksClasses = array_diff( explode ( ',' , $trademarks_classes ) , array('') );

        $trademarksClassesString = implode( ',' , $trademarksClasses );

        if ( $contractNumberExisting ) {
            $attachmentNumberByContract = $em->getRepository('FreshPatentingBundle:Contracts')->findBy(array('contractNumber' => $contractNumberExisting ));
            $number = $attachmentNumberByContract[0]->getAttachmentNumber() + 1;
        } else {
            $number = 1;
        }

        $attachment = new TemplateProcessor($templatesPath.'attachment.docx');

        $attachment->setValue('contract_number', $contractNumber );
        $attachment->setValue('attachment_date', $date );
        $attachment->setValue('attachment_number', 2*$number );
        $attachment->setValue('trademark_classes', $trademarksClassesString );


        $attachment->setValue('surname', $surname );
        $attachment->setValue('name', $name );
        $attachment->setValue('second_name', $secondName );

        $attachment->setValue('customer_address', $address );
        $attachment->setValue('customer_short_name', mb_substr($name, 0, 1).'. '.mb_substr($secondName, 0, 1).'. '.$surname);
        $attachment->setValue('our_short_name', $ourShortName );



        $attachment->saveAs($savePath.'temporary/attachment-'.$contractNumber.'.docx');

        return 'attachment-'.$contractNumber.'.docx';

    }




    public function saveAction(Request $request)
    {
        //echo '<pre>';var_dump($request->request);die;
		mb_internal_encoding("UTF-8");
        $em = $this->getDoctrine()->getManager();

        $ourEntityId = $request->request->get('our_entity');

        $ourEntity = $em->getRepository('FreshPatentingBundle:LegalEntities')->findBy(array('id' => $ourEntityId));

        $lastContractNumber = $em->getRepository('FreshPatentingBundle:Contracts')->getMaxId();

        $contractNumber = ($ourEntity[0]->getPassportSeries()).'-'. (++$lastContractNumber[1]);



        if ( !$request->request->get('organizations') || $request->request->get('update_customer') || $request->request->get('add_customer') ) {
//            echo '<pre>';var_dump($request->request->get('organizations'));die;
            $newLegalEntity = ( !$request->request->get('organizations') || $request->request->get('add_customer') ) ? new LegalEntities() : $em->getRepository('FreshPatentingBundle:LegalEntities')->find( $request->request->get('organizations') );

            if ( $request->request->get('legal_entity_type') ) {
                $asd = $em->getRepository('FreshPatentingBundle:LegalEntities')->find( $request->request->get('organizations') );
            } else {

            }


            $newLegalEntity->setorganizationType($request->request->get('legal_entity_type'));
            $newLegalEntity->setIsSelfOrganization(false);
            $newLegalEntity->setName($request->request->get('name'));
            $newLegalEntity->setSecondName($request->request->get('second_name'));
            $newLegalEntity->setSurname($request->request->get('surname'));
            $newLegalEntity->setAddress($request->request->get('address'));
            $newLegalEntity->setIdentificationCode($request->request->get('identification_code'));

            $identificationCodeExist = false;
            $passportSeriesExist = false;
            $passportNumberExist = false;

            if ( $request->request->get('legal_entity_type') /*Legal*/ ) {

                $newLegalEntity->setOrganizationName($request->request->get('organization_name'));

                $identificationCodeExist = $em->getRepository('FreshPatentingBundle:LegalEntities')->findBy(array('identificationCode' => $request->request->get('identification_code') ));

            } else { /*Phisical*/

                $newLegalEntity->setOrganizationName(false);
                $newLegalEntity->setPassportSeries($request->request->get('passport_series'));
                $newLegalEntity->setPassportNumber($request->request->get('passport_number'));
                $newLegalEntity->setPassportOther($request->request->get('passport_other'));

                $passportSeriesExist = $em->getRepository('FreshPatentingBundle:LegalEntities')->findBy(array('passportSeries' => $request->request->get('passport_series') ));
                $passportNumberExist = $em->getRepository('FreshPatentingBundle:LegalEntities')->findBy(array('passportNumber' => $request->request->get('passport_number') ));

            }

            if ( ($identificationCodeExist || ( $passportSeriesExist && $passportNumberExist ) ) && !$request->request->get('update_customer') ) {
                return new JsonResponse(array(
                    'message' => 'Такой клиент уже существует',
                    'error' => true,
                ));
            }


            $em->persist($newLegalEntity);
            $em->flush();
        }

        if ( $request->request->get('contracts') ) {
            $attachmentNumberByContract = $em->getRepository('FreshPatentingBundle:Contracts')->findBy(array('contractNumber' => $request->request->get('contracts')));
            $attachmentNumber = $attachmentNumberByContract[0]->getAttachmentNumber() + 1;
            $attachmentNumberByContract[0]->setAttachmentNumber($attachmentNumber);
        } else {
            $contractAdd  = new Contracts();
            $contractAdd->setContractNumber($contractNumber);
            if ( $request->request->get('organizations') && !$request->request->get('add_customer') ) {
                $legalEntity = $em->getRepository('FreshPatentingBundle:LegalEntities')->find($request->request->get('organizations'));
                $contractAdd->setEntity($legalEntity);
            } else {
                $lastLegalEntityId = $em->getRepository('FreshPatentingBundle:LegalEntities')->getMaxId();
                $legalEntity = $em->getRepository('FreshPatentingBundle:LegalEntities')->find($lastLegalEntityId[1]);
                $contractAdd->setEntity($legalEntity);
            }
            $contractAdd->setAttachmentNumber(1);
            $em->persist($contractAdd);
        }

        $contractNumber = $request->request->get('contracts') ? $request->request->get('contracts').'new' : $contractNumber ;

        $em->flush();

        $newArchivaName = $_SERVER['DOCUMENT_ROOT'].'/web/patenting_documents/archives/contract-'.$contractNumber.'.zip';
        rename ( $_SERVER['DOCUMENT_ROOT'].'/web/patenting_documents/archives/contract-sample.zip' , $newArchivaName );

        return new JsonResponse(array(
            'message' => 'Данные сохранены',
            'archive' => 'web/patenting_documents/archives/contract-'.$contractNumber.'.zip',
        ));

    }



//============================================================================================================================
// Delete directory and inlying files
//============================================================================================================================
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
//============================================================================================================================
// END Delete directory and inlying files
//============================================================================================================================


//============================================================================================================================
// Numbers to ukrainian text
//============================================================================================================================
    private function num2text_ua($num) {
        $num = trim(preg_replace('~s+~s', '', $num)); // отсекаем пробелы
        if (preg_match("/, /", $num)) {
            $num = preg_replace("/, /", ".", $num);
        } // преобразует запятую
        if (is_numeric($num)) {
            $num = round($num, 2); // Округляем до сотых (копеек)
            $num_arr = explode(".", $num);
            $amount = $num_arr[0]; // переназначаем для удобства, $amount - сумма без копеек
            if (strlen($amount) <= 3) {
                $res = implode(" ", $this->Triada($amount)) . $this->Currency($amount);
            } else {
                $amount1 = $amount;
                while (strlen($amount1) >= 3) {
                    $temp_arr[] = substr($amount1, -3); // засовываем в массив по 3
                    $amount1 = substr($amount1, 0, -3); // уменьшаем массив на 3 с конца
                }
                if ($amount1 != '') {
                    $temp_arr[] = $amount1;
                } // добавляем то, что не добавилось по 3
                $i = 0;
                foreach ($temp_arr as $temp_var) { // переводим числа в буквы по 3 в массиве
                    $i++;
                    if ($i == 3 || $i == 4) { // миллионы и миллиарды мужского рода, а больше миллирда вам все равно не заплатят
                        if ($temp_var == '000') {

                            $temp_res[] = '';
                        } else {
                            $temp_res[] = implode(" ", $this->Triada($temp_var, 1)) . $this->GetNum($i, $temp_var);
                        } # if
                    } else {
                        if ($temp_var == '000') {
                            $temp_res[] = '';
                        } else {
                            $temp_res[] = implode(" ", $this->Triada($temp_var)) . $this->GetNum($i, $temp_var);
                        } # if
                    } # else
                } # foreach
                $temp_res = array_reverse($temp_res); // разворачиваем массив
                $res = implode(" ", $temp_res) . $this->Currency($amount);
            }
            if (!isset($num_arr[1]) || $num_arr[1] == '') {
                $num_arr[1] = '00';
            }
            return $res . ', ' . $num_arr[1] . ' коп.';
        } # if
    }

    private function Triada($amount, $case = null) {
        $_1_2 = ["","один","два"];
        $_1_19 = ["","одна","дві","три","чотири","п'ять","шість","сім","вісім","дев'ять","десять","одинадцять","дванадцять","тринадцять","чотирнадцять","п'ятнадцять", "шістнадцять","сімнадцять","вісімнадцять","дев'ятнадцять"];
        $des = ["","","двадцять","тридцять","сорок","п'ятдесят","шістдесят","сімдесят","вісімдесят","дев'яносто"];
        $hang = ["","сто","двісті","триста","чотириста","п'ятсот","шістсот","сімсот","вісімсот","дев'ятьсот"];
        $namecurr = ["","гривня","гривні","гривень"];
        $nametho = ["","тисяча","тисячі","тисяч"];
        $namemil = ["","мільйон","мільйона","мільйонів"];
        $namemrd = ["","мільярд","мільярда","мільярдів"];
        $count = strlen($amount);
        for ($i = 0; $i < $count; $i++) {
            $triada[] = substr($amount, $i, 1);
        }
        $triada = array_reverse($triada); // разворачиваем массив для операций
        if (isset($triada[1]) && $triada[1] == 1) { // строго для 10-19
            $triada[0] = $triada[1] . $triada[0]; // Объединяем в единицы
            $triada[1] = ''; // убиваем десятки
            $triada[0] = $_1_19[$triada[0]]; // присваиваем
        } else { // а дальше по обычной схеме
            if (isset($case) && ($triada[0] == 1 || $triada[0] == 2)) { // если требуется м.р.
                $triada[0] = $_1_2[$triada[0]]; // единицы, массив мужского рода
            } else {
                if ($triada[0] != 0) {
                    $triada[0] = $_1_19[$triada[0]];
                } else {
                    $triada[0] = '';
                } // единицы
            } # if
            if (isset($triada[1]) && $triada[1] != 0) {
                $triada[1] = $des[$triada[1]];
            } else {
                $triada[1] = '';
            } // десятки
        }
        if (isset($triada[2]) && $triada[2] != 0) {
            $triada[2] = $hang[$triada[2]];
        } else {
            $triada[2] = '';
        } // сотни
        $triada = array_reverse($triada); // разворачиваем массив для вывода
        foreach ($triada as $triada_) { // вычищаем массив от пустых значений
            if ($triada_ != '') {
                $triada1[] = $triada_;
            }
        } # foreach
        return $triada1;
    }

    private function Currency($amount) {
        $_1_2 = ["","один","два"];
        $_1_19 = ["","одна","дві","три","чотири","п'ять","шість","сім","вісім","дев'ять","десять","одинадцять","дванадцять","тринадцять","чотирнадцять","п'ятнадцять", "шістнадцять","сімнадцять","вісімнадцять","дев'ятнадцять"];
        $des = ["","","двадцять","тридцять","сорок","п'ятдесят","шістдесят","сімдесят","вісімдесят","дев'яносто"];
        $hang = ["","сто","двісті","триста","чотириста","п'ятсот","шістсот","сімсот","вісімсот","дев'ятьсот"];
        $namecurr = ["","гривня","гривні","гривень"];
        $nametho = ["","тисяча","тисячі","тисяч"];
        $namemil = ["","мільйон","мільйона","мільйонів"];
        $namemrd = ["","мільярд","мільярда","мільярдів"];
        $last2 = substr($amount, -2); // последние 2 цифры
        $last1 = substr($amount, -1); // последняя 1 цифра
        $last3 = substr($amount, -3); //последние 3 цифры
        if ((strlen($amount) != 1 && substr($last2, 0, 1) == 1) || $last1 >= 5 || $last3 == '000') {
            $curr = $namecurr[3];
        } // от 10 до 19
        else if ($last1 == 1) {
            $curr = $namecurr[1];
        } // для 1-цы
        else {
            $curr = $namecurr[2];
        } // все остальные 2, 3, 4
        return ' ' . $curr;
    }

    private function GetNum($level, $amount) {
        $_1_2 = ["","один","два"];
        $_1_19 = ["","одна","дві","три","чотири","п'ять","шість","сім","вісім","дев'ять","десять","одинадцять","дванадцять","тринадцять","чотирнадцять","п'ятнадцять", "шістнадцять","сімнадцять","вісімнадцять","дев'ятнадцять"];
        $des = ["","","двадцять","тридцять","сорок","п'ятдесят","шістдесят","сімдесят","вісімдесят","дев'яносто"];
        $hang = ["","сто","двісті","триста","чотириста","п'ятсот","шістсот","сімсот","вісімсот","дев'ятьсот"];
        $namecurr = ["","гривня","гривні","гривень"];
        $nametho = ["","тисяча","тисячі","тисяч"];
        $namemil = ["","мільйон","мільйона","мільйонів"];
        $namemrd = ["","мільярд","мільярда","мільярдів"];
        if ($level == 1) {
            $num_arr = null;
        } else if ($level == 2) {
            $num_arr = $nametho;
        } else if ($level == 3) {
            $num_arr = $namemil;
        } else if ($level == 4) {
            $num_arr = $namemrd;
        } else {
            $num_arr = null;
        }
        if (isset($num_arr)) {
            $last2 = substr($amount, -2);
            $last1 = substr($amount, -1);
            if ((strlen($amount) != 1 && substr($last2, 0, 1) == 1) || $last1 >= 5) {
                $res_num = $num_arr[3];
            } // 10-19
            else if ($last1 == 1) {
                $res_num = $num_arr[1];
            } // для 1-цы
            else {
                $res_num = $num_arr[2];
            } // все остальные 2, 3, 4
            return ' ' . $res_num;
        } # if
    }
//============================================================================================================================
// END Numbers to ukrainian text
//============================================================================================================================

}
