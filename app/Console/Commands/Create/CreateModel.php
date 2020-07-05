<?php

namespace App\Console\Commands\Create;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CreateModel extends Command
{
    use StubFileCreatorTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:model
                            {model : model name}
                            {--table= : If Set Table Name Will Not Use Model And Use This Option}
                            {--hasField=no : is need generate field default no }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Model';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }


    public function handle()
    {
        $model = ucfirst($this->argument('model'));

        $hasField = $this->option('hasField') == 'no' ? false : true;

        list($dir, $model, $namespace) = $this->getDirAndFileName($model);

        $this->path = app_path('Model') . DIRECTORY_SEPARATOR . ($dir ? : '');

        $this->file = $this->path . ($dir ? DIRECTORY_SEPARATOR : '') . ucfirst($model) . '.php';

        $this->content = $this->makeModel($model, $hasField, $namespace);

        $this->create();
    }

    /**
     * 创建模型内容
     *
     * @param $modelName
     * @param $hasField
     * @param $namespace
     * @return mixed|string
     */
    private function makeModel($modelName, $hasField, $namespace)
    {
        $modelContents = "<?php \r\n \r\n";
        $modelContents .= "namespace App\ModelNAMESPACE;\r\n \r\n";
        $modelContents .= "use App\Model\BaseModel;\r\n \r\n";
        $modelContents .= 'class $model extends BaseModel';
        $modelContents .= "\r\n{ \r\n \t";
        $modelContents .= 'protected $table = \'' . 'TABLE\';';
        $modelContents .= "\r\n \r\n \t";
        $modelContents .= 'protected $fillable = FILL;';

        if ($hasField) {
            $modelContents  = $this->writeField($modelContents, $modelName);
        }
        $modelContents = $this->replace('$model', ucfirst($modelName), $modelContents);
        $modelContents = $this->replace('TABLE', $this->getTable($modelName), $modelContents);
        $modelContents = $this->replace('FILL', $this->fillField($modelName), $modelContents);
        $modelContents .= "\r\n }";

        return $this->replaceNameSpace($namespace, $modelContents);
    }

    /**
     * 创建模型字段
     *
     * @param $modelContents
     * @param $modelName
     * @return string
     */
    private function writeField($modelContents, $modelName)
    {
        $info = Db::select('show full columns from ' . config('database.connections.mysql.prefix') . $this->getTable($modelName));

        foreach ($info as $value) {
            $modelContents .= sprintf("\r\n %s \t protected $%s = '%s'; \r\n", $this->fieldComment($value->Comment, $value->Type), $this->combine($value->Field), $value->Field);
        }

        return $modelContents;
    }

    /**
     * 填充数据
     *
     * @param $modelName
     * @return string
     */
    private function fillField($modelName)
    {
        $info = Db::select('show full columns from ' . config('database.connections.mysql.prefix') . $this->getTable($modelName));

        $arr = "[ \r\n";
        foreach ($info as $value) {
            $arr .= sprintf("\t\t '%s', %s \r\n" , $value->Field, $value->Comment ? '// ' . $value->Comment : '');
        }
        $arr = rtrim(trim($arr), ',');

        return $arr . "\r\n \t ]";
    }
    /**
     * 字符注释
     *
     * @param $comment
     * @param $type
     * @return string
     */
    private function fieldComment($comment, $type)
    {
        return sprintf("\t /** \r\n \t  * %s \r\n \t  * @desc %s \r\n \t  */ \r\n", $type, $comment);
    }

    /**
     * 驼峰分割
     *
     * @param string $string
     * @param string $separator
     * @return string
     */
    private function unCamelize(string $string, string $separator = '_')
    {
        return strtolower(preg_replace('/(?<=[a-z])([A-Z])/', $separator . '$1', $string));
    }

    /**
     * 组合字符
     *
     * @param string $string
     * @return string
     */
    private function combine(string $string)
    {
        $s = explode('_', $string);
        array_walk($s, function (&$value, $key) {
            if ($key) {
                $value = ucfirst($value);
            }
        });
        return implode($s, '');
    }


    protected function getTable($modelname)
    {
        return $this->option('table') ? : $this->unCamelize($modelname);
    }
}
