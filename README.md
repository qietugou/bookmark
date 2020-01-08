<h1 align="center"> bookmark </h1>

<p align="center">git remote add origin git@github.com:gotophp/bookmark.git </p>


## Installing

```shell
$ composer require qietugou/bookmark -vvv
```

## 使用

```php
use Qietugou\Bookmark\Bookmark;

$book = new Bookmark('您的浏览器书签路径');


$book->getBookmarks()->toResult();

```

## 方法

```php
// 传 true 原样输出
$book = new Bookmark('path', true);

// 导航栏
$book->getBookmarks()->toBarResult();
// 其它
$book->getBookmarks()->toOtherResult();
// 移动端设备
$book->getBookmarks()->toSyncedResult();
```


## License

MIT