<?php

namespace App\Console\Commands\Create;


use Illuminate\Console\Command;

class CreateService extends Command
{
    use StubFileCreatorTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:service {service : service name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Service File';

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
        $service = ucfirst($this->argument('service'));

        list($dir ,$name, $namespace) = $this->getDirAndFileName($service);

        $this->content = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR .'stubs'. DIRECTORY_SEPARATOR. 'service.stub');

        $this->content = $this->replaceNameSpace( $namespace, $this->replace('Dump', $name, $this->content));

        $this->path = app_path('Service') .DIRECTORY_SEPARATOR . ($dir ? : '') . DIRECTORY_SEPARATOR;

        $this->file = sprintf('%sService.php', $this->path . $name);

        $this->create();
    }

}
