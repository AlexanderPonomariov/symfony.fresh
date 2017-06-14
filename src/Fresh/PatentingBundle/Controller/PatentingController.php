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
        echo '<pre>';var_dump($request->request);die;

        $em = $this->getDoctrine()->getManager();

        $ourEntityId = $request->request->get('our_entity');

        $ourEntity = $em->getRepository('FreshPatentingBundle:LegalEntities')->findBy(array('id' => $ourEntityId));

        $templatesPath = $_SERVER['DOCUMENT_ROOT'].'/patenting_templates/';

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

        $monthUkr = ['','Січень', 'Лютий','Березень','Квітень','Травень','Червень','Липень','Серпень','Вересень','Жовтень','Листопад','Грудень',];
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
        $contractAdd  = new Contracts();
        $contractAdd->setContractNumber($contractNumber);
        if ( $request->request->get('organizations') ) {
            $legalEntity = $em->getRepository('FreshPatentingBundle:LegalEntities')->find($request->request->get('organizations'));
            $contractAdd->setEntity($legalEntity);
        }
        $em->persist($contractAdd);
        $em->flush();


        $savePath = $_SERVER['DOCUMENT_ROOT'].'/patenting_documents/';
        mkdir($savePath.'temporary');
        $contract->saveAs($savePath.'temporary/contract_1.docx');
        $proxy->saveAs($savePath.'temporary/proxy_1.docx');

        $zip = new \ZipArchive;
        $zip->open($savePath.'/archives/contract-'.$contractNumber.'.zip', \ZipArchive::CREATE);
        $zip->addFile($savePath.'temporary/proxy.docx', 'proxy-for-contract-'.$contractNumber.'.docx');
        $zip->addFile($savePath.'temporary/contract.docx', 'contract-'.$contractNumber.'.docx' );





        $zip->close();

        $this->dirDel($savePath.'temporary');

        return new JsonResponse(array(
            'archive' => $savePath.'/archives/contract-'.$contractNumber.'.zip',
            'contract_number' => $contractNumber,
        ));

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





}
