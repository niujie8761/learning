<?php

namespace App\Console\Commands\Create;


use Illuminate\Console\Command;

class CreateController extends Command
{
    use StubFileCreatorTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:controller {controller : controller name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Controller File';

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
        $service = ucfirst($this->argument('controller'));

        list($dir ,$name, $namespace) = $this->getDirAndFileName($service);

        $this->content = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR .'stubs'. DIRECTORY_SEPARATOR. 'controller.stub');

        $this->content = $this->replaceNameSpace( $namespace, $this->replace('Dummy', $name, $this->content));

        $this->content = $this->replaceNameSpace( $namespace, $this->replace('#date', date('Y年m月d日', time()), $this->content));

        $this->path = app_path('Admin/Controllers') .DIRECTORY_SEPARATOR . ($dir ? : '') . DIRECTORY_SEPARATOR;

        $this->file = sprintf('%sController.php', $this->path . $name);

        $this->create();
    }

}
