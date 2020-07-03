<?php

namespace App\Console\Commands\Create;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;


class CreateTable extends Command
{
    use StubFileCreatorTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:table
                            {table : export table}
                            {--except= : except table, can you use prefix}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'create table';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->fileSystem = new Filesystem();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
        $table = $this->argument('table');
        $except = $this->option('except');
        // exit($this->info('功能停止使用'));

        $tables = $this->getTables($table);

        if ($except) {
            $tables = $this->except($tables, $except);
        }

        $total = count($tables) - 1;
        foreach ( $tables as $key => $item) {
            $table = $item->Name;

            list($engine, $comment) = $this->getTableComment($table);
            $content = $this->getFileContent(__DIR__ . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR . 'migration.stub');
            //dd($content);
            $replaceContent = $this->replace('ENGINE', $engine, $content);
            $replaceContent = $this->replace('COMMENT', $comment, $replaceContent);
            $replaceContent = $this->replace('CONTENT', $this->content($table), $replaceContent);
            $replaceContent = $this->replace('Dump', $this->combineTableName($table), $replaceContent);
            $replaceContent = $this->replace('TABLE', $table, $replaceContent);
            $this->file = database_path('migrations') . DIRECTORY_SEPARATOR . date('Y_m_d_his') . '_create_' . $table . '_table.php';
            $this->content = $replaceContent;
            $this->createFile();
            $this->info($table . sprintf(' migration file 创建成功 %s', '�'));
            if ($key == $total) {
                $this->info(sprintf('migration 全部创建成功 %s', '�'));}
        }
    }


    protected function combineTableName($table)
    {
        if (strpos($table, '_') === false) {
            return ucfirst($table);
        }

        $t = '';
        foreach (explode('_', $table) as $v) {
            $t .= ucfirst($v);
        }

        return $t;
    }

    public function content($table)
    {
        $info = Db::select('show full columns from  `' . $table . '`');
        $content = '';
        foreach ($info as $key => $item) {
            if (stripos($item->Type, 'int') === 0) {
                if ($key == 0) {
                    $content .= sprintf('$table->increments(\'%s\')', $item->Field);
                } else {
                    if ($this->isUnsignedInt($item->Type)) {
                        $content .= sprintf('$table->unsignedinteger(\'%s\')', $item->Field);
                    } else {
                        $content .= sprintf('$table->integer(\'%s\')', $item->Field);
                    }
                }
            } else if (stripos(strtolower($item->Type), 'tinyint') === 0) {
                if ($this->isUnsignedInt($item->Type)) {
                    $content .= sprintf('$table->tinyInteger(\'%s\')', $item->Field);
                } else {
                    $content .= sprintf('$table->unsignedTinyInteger(\'%s\')', $item->Field);
                }
            } else if (stripos($item->Type, 'varchar') === 0) {
                $content .= sprintf('$table->string(\'%s\', %d)', $item->Field, (int)$this->getLength($item->Type));
            } else if (stripos($item->Type, 'char') === 0) {
                $content .= sprintf('$table->char(\'%s\', %d)', $item->Field, (int)$this->getLength($item->Type));
            } else if (stripos($item->Type, 'text') === 0) {
                $content .= sprintf('$table->text(\'%s\')', $item->Field);
            } else if (stripos($item->Type, 'datetime') === 0) {
                $content .= sprintf('$table->dateTime(\'%s\')', $item->Field);
            } else if (stripos($item->Type, 'longtext') === 0) {
                $content .= sprintf('$table->longText(\'%s\')', $item->Field);
            } else if (stripos($item->Type, 'float') === 0) {
                if ($this->getLength($item->Type)) {
                    list($first, $second) = explode(',', $this->getLength($item->Type));
                    $content .= sprintf('$table->float(\'%s\', %d, %d)', $item->Field, intval($first), intval($second));
                } else {
                    $content .= sprintf('$table->float(\'%s\')', $item->Field);
                }
            } else if (stripos($item->Type, 'smallint') === 0) {
                $content .= sprintf('$table->unsignedSmallInteger(\'%s\')', $item->Field);
            } else if (stripos($item->Type, 'mediumtext') === 0) {
                $content .= sprintf('$table->mediumText(\'%s\')', $item->Field);
            } else if (stripos($item->Type, 'decimal') === 0) {
                list($first, $second) = explode(',', $this->getLength($item->Type));
                $content .= sprintf('$table->decimal(\'%s\', %d, %d)', $item->Field, intval($first), intval($second));
            }else if (stripos($item->Type, 'enum') === 0) {
                $numbers = explode(',', $this->getLength($item->Type));
                $arr = '[';
                foreach ($numbers as $value) {
                    $arr .= sprintf('%s,', $value);
                }
                $arr = rtrim($arr, ',');
                $arr .= ']';

                $content .= sprintf('$table->enum(\'%s\', %s)', $item->Field, $arr);
            }else if (stripos($item->Type, 'mediumint') === 0) {
                $content .= sprintf('$table->mediumInteger(\'%s\')', $item->Field);
            }else if (stripos($item->Type, 'bigint') === 0) {
                if ($key == 0) {
                    $content .= sprintf('$table->bigIncrements(\'%s\')', $item->Field);
                } else {
                    $content .= sprintf('$table->unsignedBigInteger(\'%s\')', $item->Field);
                }
            }else if (stripos($item->Type, 'timestamp') === 0) {
                $content .= sprintf('$table->timestampTz(\'%s\')', $item->Field);
            }else if (stripos($item->Type, 'date') === 0) {
                $content .= sprintf('$table->date(\'%s\')', $item->Field);
            }

            if ($item->Null == 'NO') {
                $content .= '->nullable(false)';
            }else{
                $content .= '->nullable(true)';
            }

            if ($key != 0 && $item->Default !== Null) {
                if (is_numeric($item->Default)) {
                    $content .= sprintf('->default(%s)', $item->Default);
                } else {
                    $content .= sprintf('->default(\'%s\')', $item->Default);
                }
            }
            $content.=sprintf('->comment(\'%s\');', $item->Comment ? : '');
            $content .= "\r\n";
            $content .= "\t\t\t";
        }

        // 获取表索引
        $indexs = $this->getIndexs(DB::select('show keys from `' . $table .'`'));

        foreach ($indexs as $indexName => $index) {
            // 判断是否是 PRIMARY 或者是 UNIQUE 主键
            if ($indexName == 'PRIMARY') {
                $content .= sprintf('$table->primary(%s);', $this->combineArrString($index['index']));
            } else if ($index['is_unique']) {
                $content .= sprintf('$table->unique(%s, \'%s\');', $this->combineArrString($index['index']), $indexName);
            } else {
                $content .= sprintf('$table->index(%s, \'%s\');', $this->combineArrString($index['index']), $indexName);
            }
            $content .= "\r\n \t\t\t";
        }

        return $content;
    }

    protected function combineArrString($array)
    {
        if (count($array) < 2) {
            return sprintf('\'%s\'', $array[0]);
        }
        $arr = '[';
        foreach ($array as $value) {
            $arr .= sprintf('\'%s\',', $value);
        }
        $arr = rtrim($arr, ',');
        $arr .= ']';
        return $arr;
    }

    protected function isUnsignedInt($type)
    {
        return stripos($type, 'unsigned') === false ? false : true;
    }
    protected function getIndexs($indexs)
    {
        $_indexs = [];
        foreach ($indexs as $key => $index) {
            if ($key > 0) {
                $_indexs[$index->Key_name]['index'][] = $index->Column_name;
                $_indexs[$index->Key_name]['is_unique'] = $index->Non_unique ? false : true;
            }
        }
        return $_indexs;
    }
    protected function getTableComment($table)
    {
        $tables = DB::select('show table status');
        foreach ($tables as $v) {
            if ($v->Name == $table) {
                $comment = $v->Comment;
                return [$v->Engine, $comment];
            }
        }
    }

    protected function getTables($table): array
    {
        $tables = DB::select('show table status');

        if ($table == 'all') {
            return $tables;
        }

        $choose = [];
        foreach ($tables as $t)  {
            if (strpos($t->Name, $table) !== false) {
                $choose[] = $t;
            }
        }

        return $choose;
    }

    protected function except($tables, $except)
    {
        $excepts = stringToArrayByComma($except);

        $excepts[] = 'migrations';

        while (count($excepts)) {
            $except = array_pop($excepts);

            foreach ($tables as $key => $table) {
                if (starts_with($table->Name, $except)) {
                    unset($tables[$key]);
                }
            }
        }

        return $tables;
    }

    protected function getLength($s)
    {
        preg_match_all('/.*?\((.*?)\).*?/i', $s, $c);

        return $c[1][0] ?? '';
    }
}
