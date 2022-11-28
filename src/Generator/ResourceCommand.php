<?php

declare(strict_types=1);
/**
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\Resource\Generator;

use Hyperf\Utils\Str;
use Hyperf\Command\Annotation\Command;
use Hyperf\Devtool\Generator\GeneratorCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @Command
 */
#[Command]
class ResourceCommand extends GeneratorCommand
{
    public function __construct()
    {
        parent::__construct('gen:resources');
        $this->setDescription('Create a new resource');
        $this->addOption('no-migration', 'm', InputOption::VALUE_NONE, 'Do not create migration file.');
        $this->addOption('no-routes', 'r', InputOption::VALUE_NONE, 'Do not create routes.');
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

        $this->generateResource($input, $output);

        $this->generateMigration();

        $this->generateRoutes();
        // create migration
        $output->writeln(sprintf('<info>%s</info>', $this->getSingularResourceName() . ' resource created successfully.'));

        return 0;
    }

    /**
     * Call another console command.
     */
    public function call(string $command, array $arguments = []): int
    {
        $arguments['command'] = $command;

        return $this->getApplication()->find($command)->run($this->createInputFromArguments($arguments), $this->output);
    }

    protected function generateResource(InputInterface $input, OutputInterface $output)
    {
        $this->buildResource(
            $input,
            $output,
            $this->getStudlyResourceName(),
            'App\\Model\\',
            __DIR__ . '/stubs/model.stub'
        );

        $this->buildResource(
            $input,
            $output,
            $this->getStudlyResourceName() . 'RepositoryContract',
            'App\\Contract\\Repository',
            __DIR__ . '/stubs/contract.stub'
        );

        $this->buildResource(
            $input,
            $output,
            $this->getStudlyResourceName() . 'DbRepository',
            'App\\Repository\\' . $this->getStudlyResourceName(),
            __DIR__ . '/stubs/repository.stub'
        );

        $this->buildResource(
            $input,
            $output,
            $this->getStudlyResourceName(),
            'App\\Resource',
            __DIR__ . '/stubs/resource.stub'
        );

        $this->buildResource(
            $input,
            $output,
            $this->getStudlyResourceName() . 'Service',
            'App\\Service',
            __DIR__ . '/stubs/service.stub'
        );

        $this->buildResource(
            $input,
            $output,
            $this->getStudlyResourceName() . 'Controller',
            'App\\Controller',
            __DIR__ . '/stubs/controller.stub'
        );
    }

    protected function generateRoutes()
    {
        if ($this->input->getOption('no-routes')) {
            return;
        }

        $routeSTR = <<<'EOF'
        
//@AutoCreateStart:%s
//@todo merge the content below into one group
Router::addGroup('/api', function () {
    Router::addGroup('/v1', function () {
        Router::resource('%s', \App\Controller\%sController::class);
    });
});
//@AutoCreateEnd:%s

EOF;
        $routeString = sprintf(
            $routeSTR,
            $this->getPluralResourceName(),
            $this->getPluralResourceName(),
            $this->getStudlyResourceName(),
            $this->getPluralResourceName()
        );

        $originalRouterString = file_get_contents($this->getRouteFile());
        if (Str::contains($originalRouterString, $routeString)) {
            return;
        }

        file_put_contents($this->getRouteFile(), $routeString, FILE_APPEND);
    }

    protected function getRouteFile()
    {
        return BASE_PATH . '/config/routes.php';
    }

    protected function generateMigration()
    {
        if ($this->input->getOption('no-migration')) {
            return;
        }
        $this->call('gen:migration', [
            'name' => 'create_' . $this->getPluralResourceName() . '_table',
        ]);
    }

    protected function buildResource(InputInterface $input, OutputInterface $output, string $resourceFileName, string $namespace, string $stub)
    {
        $this->input->setOption('namespace', $namespace);

        $name = $this->qualifyClass($resourceFileName);

        $path = $this->getPath($name);

        // First we will check to see if the class already exists. If it does, we don't want
        // to create the class and overwrite the user's code. So, we will bail out so the
        // code is untouched. Otherwise, we will continue generating this class' files.
        if (($input->getOption('force') === false) && $this->alreadyExists($resourceFileName)) {
            $output->writeln(sprintf('<fg=red>%s</>', $name . ' already exists!'));
            return 0;
        }

        // Next, we will generate the path to the location where this class' file should get
        // written. Then, we will build the class and make the proper replacements on the
        // stub files so that it gets the correctly formatted namespace and class name.
        $this->makeDirectory($path);

        file_put_contents($path, $this->buildClassWithStub($name, $stub));

        $output->writeln(sprintf('<info>%s</info>', $name . ' created successfully.'));

        $this->openWithIde($path);
    }

    protected function getStub(): string
    {
        return $this->getConfig()['stub'] ?? __DIR__ . '/stubs/controller.stub';
    }

    protected function getDefaultNamespace(): string
    {
        return $this->getConfig()['namespace'] ?? 'App\\Controller';
    }

    /**
     * Build the class with the given name.
     *
     * @param string $name
     * @param mixed $stub
     * @return string
     */
    protected function buildClassWithStub($name, $stub)
    {
        $stub = file_get_contents($stub);

        return $this->replaceNamespace($stub, $name)->replaceResource($stub)
            ->replaceClass($stub, $name);
    }

    protected function getResourceName(): string
    {
        return Str::singular($this->getNameInput());
    }

    protected function replaceResource(string &$stub)
    {
        $stub = str_replace(
            ['%SINGULAR_RESOURCE%'],
            [$this->getSingularResourceName()],
            $stub
        );

        $stub = str_replace(
            ['%PLURAL_RESOURCE%'],
            [$this->getPluralResourceName()],
            $stub
        );

        $stub = str_replace(
            ['%SNAKE_RESOURCE%'],
            [$this->getSnakeResourceName()],
            $stub
        );

        $stub = str_replace(
            ['%CAMEL_RESOURCE%'],
            [$this->getCamelResourceName()],
            $stub
        );

        $stub = str_replace(
            ['%STUDLY_RESOURCE%'],
            [$this->getStudlyResourceName()],
            $stub
        );

        return $this;
    }

    protected function replaceResourceController(string &$stub, string $name)
    {
    }

    /**
     * 返回资源的单数.
     */
    protected function getSingularResourceName(): string
    {
        return Str::singular($this->getResourceName());
    }

    /**
     * 返回资源的复数.
     */
    protected function getPluralResourceName(): string
    {
        return Str::plural($this->getResourceName());
    }

    /**
     * 返回蛇形资源名称.
     */
    protected function getSnakeResourceName(): string
    {
        return Str::snake($this->getResourceName());
    }

    /**
     * 返回驼峰资源名称.
     */
    protected function getCamelResourceName(): string
    {
        return Str::camel($this->getResourceName());
    }

    protected function getStudlyResourceName(): string
    {
        return Str::studly($this->getResourceName());
    }

    /**
     * Create an input instance from the given arguments.
     */
    protected function createInputFromArguments(array $arguments): ArrayInput
    {
        return tap(new ArrayInput(array_merge($this->context(), $arguments)), function (InputInterface $input) {
            if ($input->hasParameterOption(['--no-interaction'], true)) {
                $input->setInteractive(false);
            }
        });
    }

    /**
     * Get all of the context passed to the command.
     */
    protected function context(): array
    {
        return collect($this->input->getOptions())->only([
            'ansi',
            'no-ansi',
            'no-interaction',
            'quiet',
            'verbose',
        ])->filter()->mapWithKeys(function ($value, $key) {
            return ["--{$key}" => $value];
        })->all();
    }
}
