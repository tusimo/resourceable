<?php

declare(strict_types=1);
/**
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\Resource\Generator;

use Hyperf\Command\Annotation\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @Command
 */
#[Command]
class ResourceDeleteCommand extends ResourceGenerateCommand
{
    public function __construct()
    {
        parent::__construct('resources:del');
        $this->setDescription('delete a resource');
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;

        $this->deleteResource($input, $output);

        // create migration
        $output->writeln(sprintf('<info>%s</info>', $this->getSingularResourceName() . ' resource deleted successfully.'));

        return 0;
    }

    protected function deleteFile($file)
    {
        $file = $this->getPath($file);
        @unlink($file);
        if (is_empty_dir(dirname($file))) {
            rmdir(dirname($file));
        }
    }

    protected function deleteResource(InputInterface $input, OutputInterface $output)
    {
        $this->deleteFile($this->getCurrentNamespace('App\\Model') . '\\' . $this->getStudlyResourceName());

        $this->deleteFile($this->getCurrentNamespace('App\\Contract\\Repository') . '\\' . $this->getStudlyResourceName() . 'RepositoryContract');

        $this->deleteFile($this->getCurrentNamespace('App\\Repository') . '\\' . $this->getStudlyResourceName() . '\\' . $this->getStudlyResourceName() . 'DbRepository');

        $this->deleteFile($this->getCurrentNamespace('App\\Resource') . '\\' . $this->getStudlyResourceName());

        $this->deleteFile($this->getCurrentNamespace('App\\Service') . '\\' . $this->getStudlyResourceName() . 'Service');

        $this->deleteFile($this->getCurrentNamespace('App\\Controller') . '\\' . $this->getStudlyResourceName() . 'Controller');
    }
}
