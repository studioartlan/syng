<?php

namespace StudioArtlan\SyngBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Config\FileLocator;

use Symfony\Component\Yaml\Yaml;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

use StudioArtlan\CommonLibsBundle\Services\FileUtils;

class NgRoutesCommand extends NgBaseCommand
{

	var $routingType = null;
	
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
		$this->parseConfig();
				
        if ($input->getOption('list-types')) {
        	$this->parseConfig();
			$this->listRouteTypes();
			return;
        }

		if (!$this->getTargetBundle()) return;
		
		$this->routingType = $input->getArgument('routing-type');
		if (!$this->routingType) {
			$this->routingType = self::DEFAULT_ROUTING_TYPE;
		}
		
		

		$availableRoutes = $this->getRoutingTypes();
		$routingTypeConfig = @$availableRoutes[$this->routingType];
		if (!$routingTypeConfig) {
			$output->writeln("Routing type '" . $this->routingType . "' not available");
			return;
		}

		$renderTemplate = @$routingTypeConfig['template'];
		if (!$renderTemplate)
			$renderTemplate = $this->routingType;  

		$routes = $this->parseRoutes();

		$this->renderTargetBundleFile(
			"Routing/$renderTemplate.js",
			array('routes' => $routes, 'appConfig' => $this->getSyngConfig()->getNgAppConfig()),
			FileUtils::concatPath(self::SUBFOLDER_APP, self::SUBFOLDER_JS),
			self::ROUTES_FILE,
			true
		);

		$this->renderTargetBundleFile(
			"app/js/controllers/syngcontrollers.js",
			array('routes' => $routes, 'appConfig' => $this->getSyngConfig()->getNgAppConfig()),
			null,
			null,
			true
		);

        $output->writeln("Angular routes generated for routing type '" . $this->routingType . "'.");

    }

	public function listRouteTypes()
	{
        $availableRoutingTypes = $this->getRoutingTypes();
		
		$this->output->writeln('');
		$this->output->writeln('Available routing types:');
		$this->output->writeln('');
		
		foreach ($availableRoutingTypes as $routingTypeIdentifier => $routingType) {
			$this->output->writeln($routingTypeIdentifier . ' - ' . $routingType['name'] . ' (' . $routingType['url'] .')');	
		}
	}
	
	private function parseRoutes()
	{

    	$router = $this->getContainer()->get('router');
    	$collection = $router->getRouteCollection();
		$allRoutes = $collection->all();

		$routes = array();
		
		foreach ($allRoutes as $routeIdentifier => $route) {
			
			if (strpos($routeIdentifier, '_') === 0)
				continue;
			
			$pattern = $route->getPattern();
			$templateName = preg_replace('/{[^}]*}/', '', $pattern);
			$templateName = rtrim($templateName, '/');
			
			if (!$templateName) {
				$templateName = 'index';
			}
			
			$templateName = FileUtils::concatPath( self::SUBFOLDER_PARTIALS, self::TEMPLATES_FOLDER, $templateName . self::TEMPLATES_EXTENSION);
			$templateFile = self::getTargetBundleFilePath(self::SUBFOLDER_APP, $templateName);
			
			$this->output->writeln("Creating template: $templateName -> $templateFile");
			FileUtils::createFile($templateFile);

			$routes[$routeIdentifier] = array(
				'url' => $pattern,
				'apiUrl' => $pattern,
				'templateName' => $templateName,
				'route' => $route
			);
		}
		
		return $routes;
	}

	public function getRoutingTypes()
	{
		return $this->syngLocalConfig['routing-types'];
	}
	

	
}
