<?php

namespace StudioArtlan\SngBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Config\FileLocator;

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Config\Definition\Processor;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

class NgRoutesCommand extends ContainerAwareCommand
{
	const DEFAULT_ROUTING_TYPE = 'ui-router';
	
	const FOLDER_BASE_ASSETS = 'src/StudioArtlan/SngBundle/Resources/public';
	const SUBFOLDER_APP = 'app';
	const SUBFOLDER_PARTIALS = 'partials';
	const SUBFOLDER_JS = 'js';
	const SUBFOLDER_IMG = 'img';
	const SUBFOLDER_CSS = 'css';
	
	const ROUTES_FILE = 'sngroutes.js';

	const TEMPLATES_FOLDER = 'views';
	const TEMPLATES_EXTENSION = '.html';
	
	var $routingConfig = null;
	var $routingType = null;
	
    protected function configure()
    {
        $this
            ->setName('sng:ngroutes')
            ->setDescription('Generates Angular routes from Symfony controllers')
            ->addArgument('routing-type', InputArgument::OPTIONAL, 'Routing type to use')
            ->addOption('list-types', null, InputOption::VALUE_NONE, 'Lists available routing types')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

		$this->output = $output;
		
    	$locator = new FileLocator('src/StudioArtlan/SngBundle/Resources/config/');
		$resource = $locator->locate('sngrouting.yml', null, true);
		
		$sngConfig = Yaml::parse($resource);
		//$appConfig = Yaml::parse(__DIR__ . 'app/config/');
		
		$this->routingConfig = $sngConfig;

        if ($input->getOption('list-types')) {
        		
			$this->listRouteTypes();
			return;
        }

        $this->routingType = $input->getArgument('routing-type');
		if (!$this->routingType) $this->routingType = self::DEFAULT_ROUTING_TYPE;
		
		$routingTypeConfig = @$this->routingConfig['routing-types'][$this->routingType];

		if (!$routingTypeConfig){
			$output->writeln("Routing type '" . $this->routingType . "' not available");
			return;
		}
		
		$renderTemplate = @$routingTypeConfig['template'];
		if (!$renderTemplate)
			$renderTemplate = $this->routingType;  

		$routes = $this->parseRoutes();
				
		$routesOutput = $this->getContainer()->get('templating')->render("StudioArtlanSngBundle:Sng:Routes/$renderTemplate.txt.php", array( 'routes' => $routes));

		self::createFile(self::getPath(self::SUBFOLDER_APP, self::SUBFOLDER_JS, self::ROUTES_FILE), $routesOutput);
        $output->writeln($routesOutput);
		
        $output->writeln("Angular routes generated for routing type '" . $this->routingType . "'");

    }

	public function listRouteTypes()
	{
        $availableRoutes = $this->routingConfig['routing-types'];
		
		$this->output->writeln('');
		$this->output->writeln('Available routing types:');
		$this->output->writeln('');
		
		foreach ($availableRoutes as $routeIdentifier => $route) {
			$this->output->writeln($routeIdentifier . ' - ' . $route['name'] . ' (' . $route['url'] .')');	
		}
	}
	
	private function parseRoutes()
	{

    	$router = $this->getContainer()->get('router');
    	$collection = $router->getRouteCollection();
		$allRoutes = $collection->all();

		$routes = array();
		
		foreach ($allRoutes as $routeIdentifier => $route) {
			$pattern = $route->getPattern();
			$templateName = preg_replace('/{[^}]*}/', '', $pattern);
			$templateName = rtrim($templateName, '/');
			
			if (!$templateName) {
				$templateName = 'index';
			}
			
			$templateName = self::TEMPLATES_FOLDER . $templateName . self::TEMPLATES_EXTENSION;
			$templateFile = self::getPath(self::SUBFOLDER_APP, self::SUBFOLDER_PARTIALS, $templateName);

			$this->output->writeln("Creating template: $templateName -> $templateFile");
			self::createFile($templateFile);
			
			$routes[$routeIdentifier] = array(
				'url' => $pattern,
				'templateName' => $templateName,
				'route' => $route
			);
		}
		
		return $routes;
	}
	
	private static function createFile($fileName, $contents = null, $overwrite = false) {
		
		$fs = new Filesystem();

		if (!$overwrite && $fs->exists($fileName) )
			return;

		$path = pathinfo($fileName, PATHINFO_DIRNAME);

		$fs->mkdir($path);
		$fs->touch($fileName);

		if ($contents !== null)
			file_put_contents($fileName, $contents);
	}

	private static function getPath()
	{
		$folders = func_get_args();
		$folderPath = implode('/', $folders);

		return self::FOLDER_BASE_ASSETS . '/' . self::concatPath($folders);
	}

	private static function concatPath($pathParts)
	{
		return implode('/', $pathParts);
	}
	
}
