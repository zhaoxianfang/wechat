<?php

namespace Appx\Support\WorkWechat;

use EasyWeChat\Factory;
use EasyWeChat\Work\Application;
use Exception;
use Illuminate\Support\Facades\App;
use Modules\WorkWechat\Entities\WorkWechatApplication;

/**
 * 企业微信 发送消息基类
 */
abstract class MessageBase
{
    /**
     * @var Application 企业微信应用实例
     */
    protected Application $workWeChat;

    protected \GuzzleHttp\Client $httpClient;

    /**
     * @var array 配置
     */
    protected array $config;

    public function __construct(array $config = [], string $identify = '')
    {
        $this->setConfig($config, $identify);
    }

    /**
     * 静态初始化方法 - 使用后期静态绑定实例化当前调用的类
     *
     * @return static 返回当前类的实例
     */
    public static function init(array $config = [], string $identify = ''): static
    {
        // 使用后期静态绑定创建当前调用类的实例
        return new static($config, $identify);
    }

    /**
     * 设置企业微信配置
     *
     * @return $this
     *
     * @throws Exception
     */
    public function setConfig(array $config = [], string $identify = ''): static
    {
        if (! empty($config)) {
            // 直接给出配置
            $this->config = $config;
        } else {
            if (App::environment(['development', 'testing'])) {
                // 实例化应用 - 测试助手
                $this->config = [
                    'corp_id' => 'ww6e6417f0d31de595',
                    'secret' => 'TFmRx6UFESSLrKJIPRFl3g84j1nCa-dfbB4uAmTase0',
                    'agent_id' => '1000069',
                ];
            } else {
                if (empty($identify)) {
                    // 获取配置文件
                    $this->config = config('wechat.work.default');
                } else {
                    // 通过不同的应用场景获取配置
                    $application = WorkWechatApplication::query()
                        ->where('status', 1)
                        ->whereHasIn('messages', function (Builder $builder) use ($identify) {
                            $builder->where('identify', $identify)
                                ->where('status', 1);
                        })
                        ->first();
                    $this->config = [
                        'corp_id' => $application->corp_id,
                        'secret' => $application->secret,
                        'agent_id' => $application->agent_id,
                    ];
                }
                if (empty($this->config)) {
                    throw new Exception('企业微信应用未配置');
                }
            }
        }

        // 创建应用实例
        $this->workWeChat = Factory::work($this->config);

        // 获取 HTTP 客户端
        $this->httpClient = $this->workWeChat->getHttpClient();

        return $this;
    }

}
