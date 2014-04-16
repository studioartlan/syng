<?php

namespace StudioArtlan\SngBundle\Twig;

use StudioArtlan\SngBundle\Services\SngConfig;

class SngExtension extends \Twig_Extension
{
	var $sngConfig;
	
    public function getFilters()
    {
        return array(
        );
    }

    public function getFunctions()
    {
        return array(
        );
    }

    public function getName()
    {
        return 'sng_extension';
    }
}