<?php

namespace zxf\WeChat\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\Console\AboutCommand;
use Composer\InstalledVersions;

class WeChatServiceProvider extends ServiceProvider
{
    /**
     * 服务提供者是否延迟加载
     */
    protected bool $defer = false;

    /**
     * 启动服务
     */
    public function boot(): void
    {
        // 发布 配置文件
        $this->registerNamespaces();

        // 把 zxf/wechat 添加到 about 命令中
        AboutCommand::add('Extend', [
            'zxf/wechat' => fn () => InstalledVersions::getPrettyVersion('zxf/wechat'),
        ]);
    }

    /**
     * 注册服务
     */
    public function register(): void
    {
        // 注册路由服务提供者
    }

    /**
     * Register package's namespaces.
     */
    protected function registerNamespaces()
    {
        // 把config 文件夹类的配置文件 发布到 config 文件夹下
        $this->publishes([
            __DIR__.'/../../../config/' => config_path(''),
        ], 'config');

    }

    /**
     * 获取服务提供者提供的服务
     */
    public function provides(): array
    {
        return [
        ];
    }
}
