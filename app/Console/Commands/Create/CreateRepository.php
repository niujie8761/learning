<?php

namespace App\Console\Commands\Create;

use Illuminate\Console\Command;

class CreateRepository extends Command
{
    use StubFileCreatorTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:repository 
                            {repository : The Repository File Name}
                            {--model= : Its A Extra Choose, If Null, Will Use Repository Name }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Repository File';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
        $repository = ucfirst($this->argument('repository'));

        $model = $this->option('model') ? : $repository;

        if (! file_exists(app_path('Models') .DIRECTORY_SEPARATOR. $this->replace('\\', DIRECTORY_SEPARATOR, $model). '.php')) {
            $this->error(sprintf('%sModel Does Not Exist', ucfirst($model)));
            return;
        }

        list($dir, $name, $namespace) = $this->getDirAndFileName($repository);

        $this->path = app_path('Repositories') .DIRECTORY_SEPARATOR. ($dir ? : '') . DIRECTORY_SEPARATOR;

        $this->file = $this->path . $name . 'Repository.php';

        $this->content = $this->getContent($name, $model, $namespace);

        $this->create();
    }

    protected function getContent($repository, $model, $namespace)
    {
        $repositoryStubPath = __DIR__ . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR . 'respository.stub';
        //dd($repository);
        $content = file_get_contents($repositoryStubPath);
        $replaceContent = $this->replace('Dummy', $repository, $content);
        $replaceContent = $this->replace('MODELNAME',  $this->getModelName($model), $replaceContent);
        $replaceContent = $this->replace('$name',  lcfirst($this->getModelName($model)), $replaceContent);
        $replaceContent = $this->replace('$Model', ucfirst($model), $replaceContent);
        $replaceContent = $this->replace('model', lcfirst($this->getModelName($model)), $replaceContent);
        $replaceContent = $this->replaceNameSpace($namespace, $replaceContent);
        return $replaceContent;
    }

    protected function getModelName($model)
    {
        if (strpos($model, '\\')) {
            return @array_pop(explode('\\', $model));
        }

        return $model;
    }
}
