<?php

namespace StudioArtlan\SngBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

// these import the "@Route" and "@Template" annotations
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class MainController extends Controller
{
    /**
     * @Route("/", name="Index")
     * @Template()
     */
    public function indexAction()
    {
    	return array( 'ngAppConfig' => $this->get('studioartlan.sng_config')->getNgAppConfig() );
    }

}
