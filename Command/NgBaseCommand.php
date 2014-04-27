<?php

namespace StudioArtlan\SyngBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Config\FileLocator;

use Symfony\Component\Yaml\Yaml;

use StudioArtlan\CommonLibsBundle\Services\FileUtils;
use StudioArtlan\CommonLibsBundle\Services\ConfigUtils;
use StudioArtlan\SyngBundle\Services\SyngConfig;

class NgBaseCommand extends ContainerAwareCommand
{
	const DEFAULT_ROUTING_TYPE = 'ui-router';
	
	const FOLDER_SOURCE_BASE_ASSETS = 'Resources/public-skel';
	const FOLDER_TARGET_BASE_ASSETS = 'Resources/public';
	
	const FILE_TYPE_JS = 'js';
	const FILE_TYPE_JSON = 'json';
	const FILE_TYPE_HTML = 'html';
	
	const TEMPLATING_ENGINE_PHP = 'php';
	const TEMPLATING_ENGINE_TWIG = 'twig';
	
	const SUBFOLDER_APP = 'app';
	const SUBFOLDER_PARTIALS = 'partials';
	const SUBFOLDER_JS = 'js';
	const SUBFOLDER_IMG = 'img';
	const SUBFOLDER_CSS = 'css';
	
	const ROUTES_FILE = 'routes/syngroutes.js';

	const TEMPLATES_FOLDER = 'views';
	const TEMPLATES_EXTENSION = '.html';
	
	var $routingType;
	var $targetBundle;
	var $syngConfig;

    protected function configure()
    {
        $this
            ->setName('syng:generate:ngroutes')
            ->setDescription('Generates Angular routes from Symfony controllers')
            ->addArgument('bundle', InputArgument::OPTIONAL, 'Bundle in which to generate the routing')
			->addArgument('routing-type', InputArgument::OPTIONAL, 'Routing type to use')
            ->addOption('list-types', null, InputOption::VALUE_NONE, 'Lists available routing types')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
		$this->output = $output;
		$this->input = $input;
		$this->getTargetBundle();
        $this->parseConfig();
    }

	protected function renderTargetBundleFile($renderTemplate, $variables, $outputFolder = '', $outputFileName = null, $overwrite = false, $type = null, $templatingEnine = self::TEMPLATING_ENGINE_PHP)
	{
		$ext = pathinfo($renderTemplate, PATHINFO_EXTENSION);
		
		if (!$type)
			$type = $ext;

		if (!$ext)
			$renderTemplate .= ".$type";

		if (!$outputFileName)
			$outputFileName = "$renderTemplate";

		$templatePath = "StudioArtlanSyngBundle:Skeleton:$renderTemplate.$templatingEnine";
		$outputContent = $this->getContainer()->get('templating')->render($templatePath, $variables);
		$outputFilePath = $this->getTargetBundleFilePath($outputFolder, $outputFileName);
		$this->output->writeln("Generating file: $templatePath -> $outputFilePath");
		FileUtils::createFile($outputFilePath, $outputContent, $overwrite);
	}
	
	protected function getTargetBundle()
	{
		$this->targetBundle = $this->input->getArgument('bundle');
		if (!$this->targetBundle) {
			$this->output->writeln("You must specify a target bundle.");
			return false;
		}
			
		$availableBundles = $this->getContainer()->getParameter('kernel.bundles');

		if (empty($availableBundles[$this->targetBundle])) {
			$this->output->writeln("Bundle '" . $this->targetBundle . "' doesn't exist");
			return false;
		}
		
		return true;
	}

	protected function parseConfig()
	{
		$this->syngLocalConfig = $this->getContainer()->get('studioartlan.config_utils')->yamlParse('@StudioArtlanSyngBundle/Resources/config/syng.yml');
	}
	
	public function getSyngConfig()
	{
		return $this->getContainer()->get('studioartlan.syng_config');
	}
	
	protected function getTargetBundleFilePath()
	{
		return $this->getFilePath(FileUtils::concatPath($this->targetBundle, self::FOLDER_TARGET_BASE_ASSETS ), func_get_args());
	}

	protected function getBundleFilePath()
	{
		return $this->getFilePath(FileUtils::concatPath('StudioArtlanSyngBundle', self::FOLDER_SOURCE_BASE_ASSETS ), func_get_args());
	}

	private function getFilePath($bundlePrefix, $pathParts)
	{
		$kernel = $this->getContainer()->get('kernel');
		$basePath = $kernel->locateResource('@' . $bundlePrefix);

		return FileUtils::concatPath($basePath, FileUtils::concatPathArray($pathParts));
	}
	
}
