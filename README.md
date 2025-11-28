# 微信开发

![](https://img.shields.io/packagist/dt/zxf/wechat) ![](https://img.shields.io/github/stars/zhaoxianfang/wechat.svg) ![](https://img.shields.io/github/forks/zhaoxianfang/wechat.svg) ![](https://img.shields.io/github/tag/zhaoxianfang/wechat.svg) ![](https://img.shields.io/github/release/zhaoxianfang/wechat.svg) ![](https://img.shields.io/github/issues/zhaoxianfang/wechat.svg)

提供的通用微信开发类库，还标记了微信开发类库的详细来源文档。

## 📦 安装

```bash
composer require zxf/wechat
```
## 配置

### Laravel
发布配置
````php
php artisan vendor:publish --provider="zxf\WeChat\Providers\WeChatServiceProvider"
````

### 其他框架
把配置文件 `config/wechat.php` 复制到框架对应的配置文件目录
