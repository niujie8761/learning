<?php

namespace App\Console\Commands\Create;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class CreateResource extends Command
{
    use StubFileCreatorTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:transformer
                            {name} 
                            {--admin : create at Admin module}
                            {--collection : create Collect Resource}';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'create resource file';

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
       // dd(Str::snake('NameSpace', '_'));
        $name = $this->argument('name');

        list($dir, $name, $namespace) = $this->getDirAndFileName($name);

        $this->path = ($this->option('admin')? app_path('Admin' . DIRECTORY_SEPARATOR . 'Transformers' . DIRECTORY_SEPARATOR) :

                app_path('Http' .DIRECTORY_SEPARATOR. 'Transformers'. DIRECTORY_SEPARATOR)) . ($dir ? : '');

        $this->file = sprintf($this->path . DIRECTORY_SEPARATOR . $name.'%s.php', $this->option('collection') ? 'Collection' : 'Resource');

        $this->content = $this->content($name,$namespace);

        $this->create();
    }

    private function content($name, $namespace)
    {
        $content = "<?php \r\n\r\n";

        $content .= "namespace " . ($this->option('admin')? 'App\\Admin\\TransformersNAMESPACE;' : 'App\\Http\\TransformersNAMESPACE;');

        $content .= "\r\n \r\n";

        $content .= sprintf("use App\\AbstractClass\\Transformers\\Base%s; \r\n", $this->option('collection') ? 'Collection' : 'Resource');

        $content .= sprintf("\r\nclass Dummy%s extends Base%s \r\n", $this->option('collection') ? 'Collection' : 'Resource',$this->option('collection') ? 'Collection' : 'Resource');

        $content .= "{ \r\n";
        $content .= "\t /** \r\n";
        $content .= "\t   * set need field.\r\n";
        $content .= "\t   *\r\n";
        $content .= "\t   * @var array\r\n";
        $content .= "\t   */\r\n";

        $content .= "\t   protected \$field = []; \r\n\r\n\r\n";

        $content .= "}";

        return $this->replaceNameSpace($namespace, $this->replace('Dummy', $name, $content));

    }
}
