<?php
/**
 * Created by PhpStorm.
 * User: wj
 * Date: 2019-03-19
 * Time: 09:23
 */
namespace App\Console\Commands\Create;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

trait StubFileCreatorTrait
{
    protected $fileSystem;

    protected $path;

    protected $file;

    protected $content;

    /**
     * 获取路径和文件名
     *
     * @httpMethod
     * @time 2019年03月23日
     * @email wuyanwen@baijiayun.com
     * @param $arg
     * @return array
     */
    protected function getDirAndFileName($arg)
    {
        if (! Str::contains($arg, '\\')) {
            return ['', $arg, ''];
        }

        $dir = explode('\\', $arg);

        $fileName = array_pop($dir);

        return [implode(DIRECTORY_SEPARATOR, $dir), $fileName, implode('\\', $dir)];
    }

    /**
     * 创建文件路径
     *
     * @httpMethod
     * @time 2019年03月23日
     * @email wuyanwen@baijiayun.com
     */
    protected function createDir()
    {
        if (! $this->fileSystem->isDirectory($this->path)) {
            $this->fileSystem->makeDirectory($this->path, 0777, true, true);
        }
    }

    /**
     * 文件是否存在
     *
     * @httpMethod
     * @time 2019年03月23日
     * @email wuyanwen@baijiayun.com
     * @return bool
     */
    public function isFileExisted()
    {
        if ($this->fileSystem->exists($this->file)) {
            exit($this->error(sprintf('%s Was Created.', $this->file)));
        }

        return true;
    }

    /**
     *
     *
     * @httpMethod
     * @time 2019年03月23日
     * @email wuyanwen@baijiayun.com
     */
    public function createFile()
    {
        $this->fileSystem->put($this->file, $this->content);
    }


    /**
     * 检测名文件是否符合
     *
     * @time 2019年03月19日
     * @email wuyanwen@baijiayun.com
     * @param $filename
     * @return false|int
     */
    public function checkFileName($filename)
    {
        return preg_match('/^[A-Z][a-z]{1,}$/', $filename);
    }


    /**
     * 创建
     *
     * @httpMethod
     * @time 2019年03月23日
     * @email wuyanwen@baijiayun.com
     */
    public function create()
    {
        $this->fileSystem = new Filesystem();

        $this->createDir();

        $this->isFileExisted();

        $this->createFile();

        $this->info(sprintf('%s Has Been Created Successfully ',$this->fileSystem->basename($this->file)));
    }


    /**
     * 替换
     *
     * @httpMethod
     * @time 2019年03月23日
     * @email wuyanwen@baijiayun.com
     * @param $search
     * @param $replace
     * @param $content
     * @return string
     */
    public function replace($search, $replace, $content)
    {
        return str_replace($search, $replace, $content);
    }

    /**
     * 替换 NAMESPACE
     *
     * @httpMethod
     * @time 2019年03月23日
     * @email wuyanwen@baijiayun.com
     * @param $namespace
     * @param $content
     * @return string
     */
    public function replaceNameSpace($namespace, $content)
    {
        return $this->replace('NAMESPACE', $namespace ? '\\'.$namespace : '', $content);
    }


    public function getFileContent($path)
    {
        return file_get_contents($path);
    }
}