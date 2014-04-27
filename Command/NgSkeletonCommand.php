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

class NgSkeletonCommand extends NgBaseCommand
{
	
    protected function configure()
    {
        $this
            ->setName('syng:generate:skeleton')
            ->setDescription('Generates AngularJS App skeleton')
            ->addArgument('bundle', InputArgument::OPTIONAL, 'Bundle in which to generate the routing')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

		$this->output = $output;
		$this->input = $input;
		if (!$this->getTargetBundle()) return;
		$this->parseConfig();


		$srcFolder = $this->getBundleFilePath();
		$dstFolder = $this->getTargetBundleFilePath();
		
		$fs = new Filesystem();
		
		$this->output->writeln("Copying skeleton folder: $srcFolder -> $dstFolder...");
		
		$fs->mirror($srcFolder, $dstFolder, null, array( 'override' => false ));
		
		$this->renderTargetBundleFile(
			'app/js/app.js',
			array('appConfig' => $this->getSyngConfig()->getNgAppConfig())
		);
		$this->renderTargetBundleFile(
			'app/index.html',
			array('appConfig' => $this->getSyngConfig()->getNgAppConfig())
		);

		
		$this->output->writeln("DONE.");

    }

	
}
