<?php

namespace Fresh\PatentingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\TemplateProcessor;

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
        //echo '<pre>';var_dump($request->request);die;

        $em = $this->getDoctrine()->getManager();

        $ourEntityId = $request->request->get('our_entity');

        $ourEntity = $em->getRepository('FreshPatentingBundle:LegalEntities')->findBy(array('id' => $ourEntityId));

        $templatesPath = $_SERVER['DOCUMENT_ROOT'].'/patenting_templates/';

        $templateProcessor = new TemplateProcessor($templatesPath.'contract.docx');

//        if ( $request->request->get('legal_entity_type') ) {
//            $templateProcessor->setValue('LEGAL_ENTITY', '');
//        } else {
//            $templateProcessor->setValue('FISICAL', '');
//        }
//
        $templateProcessor->deleteBlock('DELETEME');
//        $ourEntityId = $request->request->get('our_entity');
//        $ourEntityId = $request->request->get('our_entity');
//        $ourEntityId = $request->request->get('our_entity');
//        $ourEntityId = $request->request->get('our_entity');
//        $ourEntityId = $request->request->get('our_entity');
//        $ourEntityId = $request->request->get('our_entity');

        //echo '<pre>';var_dump($asd);die;





//        $templateProcessor = new TemplateProcessor($templatesPath.'contract.docx');

//        $templateProcessor->setValue('NAME', $customerName);
//        $templateProcessor->setValue('DEL1', 'asd11111111 1111111111111111 qwe123asdasd asdasdsd123');

        $savePath = $_SERVER['DOCUMENT_ROOT'].'/patenting_documents/'.'contract_1.docx';


        $templateProcessor->saveAs($savePath);
        echo '<pre>';var_dump($templateProcessor);die;

    }





}
