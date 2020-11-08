[English](README.md) | 中文

# Shadowfax

Shadowfax是一个Laravel拓展包，它可以让你的Laravel应用运行在[Swoole](https://www.swoole.com/)之上，以获得巨大的性能提升。

## 特性

- 不破坏Laravel的开发体验，让Laravel程序在Swoole与PHP-FPM上都能运行
- 可放心地启用协程特性
- 无感知地使用数据库/Redis连接池

更多特性请阅读[《完整文档》](https://shadowfax.huangyi.tech/docs)。

## 快速使用

使用Composer将Shadowfax安装到Laravel项目中：

```shell
composer require huang-yi/shadowfax
```

再用Artisan命令`shadowfax:publish`发布配置文件:

```shell
php artisan shadowfax:publish
```

最后执行Shadowfax命令`start`启动服务器：

```shell
php shadowfax start
```

现在，你就可以通过`http://127.0.0.1:1215`来访问你的项目了。

## Benchmarks

我们使用开源软件[wrk](https://github.com/wg/wrk)进行压力测试。

### 环境1

- 硬件: 1 CPU, 4 Cores, 16GB Memory
- MacOS 10.15.3
- PHP 7.3.12（启用opcache）
- Swoole 4.4.13
- Laravel 7（无session中间件）
- Shadowfax 2.0.0（20个worker进程）

wrk启动4个线程，并发200进行压测：

```shell
wrk -t4 -c200 http://127.0.0.1:1215/
```

结果为**12430.20rps**：

```shell
Running 10s test @ http://127.0.0.1:1215/
  4 threads and 200 connections
  Thread Stats   Avg      Stdev     Max   +/- Stdev
    Latency    26.44ms   31.44ms 212.73ms   84.28%
    Req/Sec     3.13k   839.99     6.07k    65.75%
  124418 requests in 10.01s, 312.06MB read
  Socket errors: connect 0, read 54, write 0, timeout 0
Requests/sec:  12430.20
Transfer/sec:     31.18MB
```

### 环境2

- 硬件: 2 CPUs, 2 Cores, 4GB Memory
- CentOS 7.5.1804
- PHP 7.3.16（启用opcache）
- Swoole 4.4.17
- Laravel 7（无session中间件）
- Shadowfax 2.0.0（10个worker进程）

wrk启动2个线程，并发100进行压测：

```shell
wrk -c100 http://127.0.0.1:1215/
```

结果为**4001.76rps**：

```shell
Running 10s test @ http://127.0.0.1:1215/
  2 threads and 100 connections
  Thread Stats   Avg      Stdev     Max   +/- Stdev
    Latency    25.06ms   12.11ms  85.92ms   60.94%
    Req/Sec     4.02k    41.46     4.08k    79.79%
  40321 requests in 10.08s, 101.13MB read
Requests/sec:   4001.76
Transfer/sec:     10.04MB
```

## 单元测试

```shell
composer test
```

## 协议

Shadowfax是一个开源软件，遵循[MIT协议](LICENSE)。
