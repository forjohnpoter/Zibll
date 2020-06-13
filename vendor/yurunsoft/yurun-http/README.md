# YurunHttp

[![Latest Version](https://poser.pugx.org/yurunsoft/yurun-http/v/stable)](https://packagist.org/packages/yurunsoft/yurun-http)
[![IMI Doc](https://img.shields.io/badge/docs-passing-green.svg)](http://doc.yurunsoft.com/YurunHttp)
[![IMI License](https://img.shields.io/github/license/Yurunsoft/YurunHttp.svg)](https://github.com/Yurunsoft/YurunHttp/blob/master/LICENSE)

## 简介

YurunHttp 是开源的 PHP HTTP 类库，支持链式操作，简单易用。

支持所有常见的 GET、POST、PUT、DELETE、UPDATE 等请求方式，支持 Http2、WebSocket、浏览器级别 Cookies 管理、上传下载、设置和读取 header、Cookie、请求参数、失败重试、限速、代理、证书等。

API 文档：[https://apidoc.gitee.com/yurunsoft/YurunHttp](https://apidoc.gitee.com/yurunsoft/YurunHttp)

欢迎各位加入技术支持群17916227[![点击加群](https://pub.idqqimg.com/wpa/images/group.png "点击加群")](https://jq.qq.com/?_wv=1027&k=5wXf4Zq)，如有问题可以及时解答和修复。

更加欢迎各位来提交PR（[码云](https://gitee.com/yurunsoft/YurunHttp)/[Github](https://github.com/Yurunsoft/YurunHttp)），一起完善YurunHttp，让它能够更加好用。

## 重大版本更新日志

> 每个小版本的更新日志请移步到 Release 查看

v3.4.0 新增支持 `Http2` 全双工用法

v3.3.0 新增支持 `Http2` 兼容用法

v3.2.0 新增支持 `Swoole WebSocket` 客户端

v3.1.0 引入浏览器级别 `Cookies` 管理

v3.0.0 新增支持 `Swoole` 协程

v2.0.0 黑历史，不告诉你

v1.3.1 支持 `Composer`

v1.0-1.3 初期版本迭代

## Composer

本项目可以使用composer安装，遵循psr-4自动加载规则，在你的 `composer.json` 中加入下面的内容

```json
{
    "require": {
        "yurunsoft/yurun-http": "^3.4.0"
    }
}
```

然后执行 `composer update` 安装。

之后你便可以使用 `include "vendor/autoload.php";` 来自动加载类。（ps：不要忘了namespace）

## 用法

更加详细的用法请看 `examples` 目录中的示例代码

### 简单调用

```php
<?php
use Yurun\Util\HttpRequest;

$http = new HttpRequest;
$response = $http->ua('YurunHttp')
                 ->get('http://www.baidu.com');

echo 'html:', PHP_EOL, $response->body();
```

### PSR-7 请求构建

```php
<?php
use Yurun\Util\YurunHttp\Http\Request;
use Yurun\Util\YurunHttp;

$url = 'http://www.baidu.com';

// 构造方法定义：__construct($uri = null, array $headers = [], $body = '', $method = RequestMethod::GET, $version = '1.1', array $server = [], array $cookies = [], array $files = [])
$request = new Request($url);

// 发送请求并获取结果
$response = YurunHttp::send($request);

var_dump($response);
```

### Swoole 协程模式

```php
<?php
use Yurun\Util\YurunHttp;
use Yurun\Util\HttpRequest;

// 设置默认请求处理器为 Swoole
YurunHttp::setDefaultHandler(\Yurun\Util\YurunHttp\Handler\Swoole::class);

// Swoole 处理器必须在协程中调用
go('test');

function test()
{
    $http = new HttpRequest;
    $response = $http->get('http://www.baidu.com');
    echo 'html:', PHP_EOL, $response->body();
}
```

### WebSocket Client

```php
YurunHttp::setDefaultHandler(\Yurun\Util\YurunHttp\Handler\Swoole::class);
go(function(){
    $url = 'ws://127.0.0.1:1234/';
    $http = new HttpRequest;
    $client = $http->websocket($url);
    if(!$client->isConnected())
    {
        throw new \RuntimeException('Connect failed');
    }
    $client->send('data');
    $recv = $client->recv();
    var_dump('recv:', $recv);
    $client->close();
});
```

### Http2 兼容用法

```php
$http = new HttpRequest;
$http->protocolVersion = '2.0'; // 这句是关键
$response = $http->get('https://wiki.swoole.com/');
```

Curl、Swoole Handler 都支持 Http2，但需要注意的是编译时都需要带上启用 Http2 的参数。

查看是否支持：

Curl: `php --ri curl`

Swoole: `php --ri swoole`

### Http2 全双工用法

> 该用法仅支持 Swoole

```php
$uri = new Uri('https://wiki.swoole.com/');

// 客户端初始化和连接
$client = new \Yurun\Util\YurunHttp\Http2\SwooleClient($uri->getHost(), Uri::getServerPort($uri), 'https' === $uri->getScheme());
$client->connect();

// 请求构建
$httpRequest = new HttpRequest;
$request = $httpRequest->header('aaa', 'bbb')->buildRequest($uri, [
    'date'  =>  $i,
], 'POST', 'json');

for($i = 0; $i < 10; ++$i)
{
    go(function() use($client, $request){
        // 发送（支持在多个协程执行）
        $streamId = $client->send($request);
        var_dump('send:' . $streamId);

        // 接收（支持在多个协程执行）
        $response = $client->recv($streamId, 3);
        $content = $response->body();
        var_dump($response);
    });
}
```

> 具体用法请看 `examples/http2Client.php`

## 捐赠

<img src="https://raw.githubusercontent.com/Yurunsoft/YurunHttp/master/res/pay.png"/>

开源不求盈利，多少都是心意，生活不易，随缘随缘……