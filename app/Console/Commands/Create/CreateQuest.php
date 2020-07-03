<?php

namespace App\Console\Commands\Create;

use Illuminate\Console\Command;

class CreateQuest extends Command
{
    use StubFileCreatorTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:request {name} 
                                           {--admin : created at admin module}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'create request';

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
        $name = $this->argument('name');

        list($dir, $name, $namespace) = $this->getDirAndFileName($name);

        $this->path = ($this->option('admin') ? app_path('Admin' . DIRECTORY_SEPARATOR . 'Requests' . DIRECTORY_SEPARATOR) :

                        app_path('Http' .DIRECTORY_SEPARATOR. 'Requests'. DIRECTORY_SEPARATOR)) . ($dir ? : '');

        $this->file = $this->path . DIRECTORY_SEPARATOR . $name.'Request.php';

        $this->content = $this->requestContent($namespace, $name);

        $this->create();
    }


    private function requestContent($namespace, $name)
    {
        $content = "<?php \r\n\r\n";

        $content .= "namespace " . ($this->option('admin') ? 'App\\Admin\\RequestsNAMESPACE;' : 'App\\Http\\RequestsNAMESPACE;');

        $content .= "\r\n \r\n";

        $content .= "use App\Admin\Requests\AbstractRequest; \r\n";

        $content .= "\r\nclass DummyRequest extends AbstractRequest \r\n";
        $content .= "{ \r\n";

        // 拼装 rules 方法
        $content .= "\t /** \r\n";
        $content .= "\t   * Get the validation rules that apply to the request.\r\n";
        $content .= "\t   *\r\n";
        $content .= "\t   * @return array\r\n";
        $content .= "\t   */\r\n";

        $content .= "\t public function rules() \r\n";

        $content .= "\t { \r\n";
        $content .= "\t\t return [\r\n";
        $content .= "\t\t   //\r\n";
        $content .= "\t\t ];\r\n";

        $content .= "\t } \r\n";

        // 拼装 attributes
        $content .= "\t /** \r\n";
        $content .= "\t   * reset attributes.\r\n";
        $content .= "\t   *\r\n";
        $content .= "\t   * @return array\r\n";
        $content .= "\t   */\r\n";

        $content .= "\t public function attributes() \r\n";

        $content .= "\t { \r\n";
        $content .= "\t\t return [\r\n";
        $content .= "\t\t   //\r\n";
        $content .= "\t\t ];\r\n";

        $content .= "\t } \r\n";


        $content .= "}";

        return $this->replaceNameSpace($namespace, $this->replace('Dummy', $name,$content));

    }
}
