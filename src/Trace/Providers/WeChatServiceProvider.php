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
     * 获取服务提供者提供的服务
     */
    public function provides(): array
    {
        return [
        ];
    }
}
