<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class LoadFixturesCommand extends ContainerAwareCommand
{
    const PATH = 'src/AppBundle/DataFixtures/ORM/Realistic';

    protected function configure()
    {
        $this->setName('mkr:fixtures:load');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @throws \Symfony\Component\Console\Exception\ExceptionInterface
     *
     * @return array|\object[]
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->getApplication()->find('doctrine:schema:drop')->run(new ArrayInput(['--force' => true]), $output);
        $this->getApplication()->find('doctrine:schema:update')->run(new ArrayInput(['--force' => true]), $output);

        $container = $this->getContainer();
        $doctrine = $container->get('doctrine');
        $manager = $doctrine->getManager();

        $loaderService = $container->get('hautelook_alice.fixtures.loader');
        $persisterClass = 'Nelmio\Alice\Persister\Doctrine';

        $files = [];

        $finder = new Finder();
        $dir = $this->getContainer()->get('kernel')->getRootDir() . '/../' . static::PATH;
        $finder->files()->in($dir);
        /**
         * @var SplFileInfo $file
         */
        foreach ($finder as $file) {
            $files[] = $file->getRealPath();
        }

        return $loaderService->load(new $persisterClass($manager), $files);
    }
}
