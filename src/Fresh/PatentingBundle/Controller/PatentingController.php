<?php

namespace Fresh\PatentingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

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

}
