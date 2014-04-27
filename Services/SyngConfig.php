<?php

namespace StudioArtlan\SyngBundle\Services;

use StudioArtlan\CommonLibsBundle\Services\ConfigUtils;

class SyngConfig {

	var $globalConfig;
	
	public function __construct(\AppKernel $kernel)
	{
		$this->kernel = $kernel;
		$this->globalConfig = $this->kernel->getContainer()->get('studioartlan.config_utils')->yamlParse('syng.yml');
	}

	public function getNgAppConfig()
	{
		return $this->getGlobalConfigParameter('ng-app');
	}

	public function getGlobalConfigParameter($parameter)
	{
		return $this->globalConfig[$parameter];
	}
	
	public function getGlobalConfig()
	{
		return $this->globalConfig;
	}

}

?>