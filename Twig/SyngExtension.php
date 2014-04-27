<?php

namespace StudioArtlan\SyngBundle\Twig;

use StudioArtlan\SyngBundle\Services\SyngConfig;

class SyngExtension extends \Twig_Extension
{
	var $syngConfig;
	
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
        return 'syng_extension';
    }
}