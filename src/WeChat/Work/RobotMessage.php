<?php

namespace Appx\Support\WorkWechat;

use Exception;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;

/**
 * 通过企业微信机器人发送 群聊天消息（小秘书）
 *
 * 示例：
 *     1、发送文本消息，支持@功能
 *         RobotMessage::init()->sendText('聊天机器人key', "第一行 \n 第二行...");
 *         RobotMessage::init()->sendText('聊天机器人key', "通知 \n 内容...", ['lisi','wangwu']);
 *     2、发送Markdown消息，不支持@功能
 *         RobotMessage::init()->sendMarkdown('聊天机器人key', "### 标题 \n 内容...");
 */
class RobotMessage extends MessageBase
{
    /**
     * @var string 消息推送的webhook地址
     */
    protected string $webhookUrl = 'https://qyapi.weixin.qq.com/cgi-bin/webhook/send';

    /**
     * 组装群机器人发送消息的URL
     *
     * @param  string  $robotKey  从URL中提取的 key, 群聊中添加机器人得到的 key
     */
    protected function getRobotRequestUrl(string $robotKey): string
    {
        return "{$this->webhookUrl}?key={$robotKey}";
    }

    /**
     * 发送文本消息,  支持@功能
     *
     * @param  string  $robotKey  群聊机器人的key
     * @param  string  $content  需要发送的文本消息内容，换行请用 \n
     * @param  array  $atMembers  需要@的成员列表，默认为空，配置的值为 企业微信帐号，
     *                            例如: ['zhangsan', 'lisi'], // 企业微信用户ID，@所有人使用 ['@all']
     * @return array{
     *     errcode: int,
     *     errmsg : string,
     * }
     *
     * @throws Exception
     */
    public function sendText(string $robotKey, string $content, array $atMembers = []): array
    {
        try {
            $response = $this->httpClient->post($this->getRobotRequestUrl($robotKey), [
                'json' => [
                    'msgtype' => 'text',
                    'text' => [
                        'content' => $content,
                        // 可选：@特定成员
                        'mentioned_list' => $atMembers, // 企业微信用户ID
                        // 可选：@所有人
                        // 'mentioned_list' => ['@all'],
                        // 可选：通过手机号@
                        // 'mentioned_mobile_list' => ['13800001111']
                    ],
                ],
            ]);

            return json_decode((string) $response->getBody(), true);

        } catch (RequestException $e) {
            // 网络请求错误
            throw new Exception('网络请求失败: '.$e->getMessage());
        } catch (Exception $e) {
            throw new Exception('请求失败: '.$e->getMessage());
        }
    }

    /**
     * 发送Markdown消息:支持字体颜色的语法
     *
     * @param  string  $robotKey  群聊机器人的key
     * @param  string  $content  需要发送的Markdown消息内容，换行请用 \n
     * @return array{
     *     errcode: int,
     *     errmsg : string,
     * }
     *
     * @throws Exception
     */
    public function sendMarkdown(string $robotKey, string $content): array
    {

        try {
            $response = $this->httpClient->post($this->getRobotRequestUrl($robotKey),
                [
                    'json' => [
                        'msgtype' => 'markdown',
                        'markdown' => [
                            'content' => $content,
                        ],
                    ],
                ]
            );

            return json_decode((string) $response->getBody(), true);
        } catch (RequestException $e) {
            // 网络请求错误
            throw new Exception('网络请求失败: '.$e->getMessage());
        } catch (Exception $e) {
            throw new Exception('请求失败: '.$e->getMessage());
        }
    }

    /**
     * 发送Markdown V2消息 : 不支持字体颜色、@群成员的语法
     *
     * @param  string  $robotKey  群聊机器人的key
     * @param  string  $content  需要发送的Markdown消息内容，换行请用 \n
     * @return array{
     *     errcode: int,
     *     errmsg : string,
     * }
     *
     * @throws Exception
     */
    public function sendMarkdownV2(string $robotKey, string $content): array
    {

        try {
            $response = $this->httpClient->post($this->getRobotRequestUrl($robotKey),
                [
                    'json' => [
                        'msgtype' => 'markdown_v2',
                        'markdown_v2' => [
                            'content' => $content,
                        ],
                    ],
                ]
            );

            return json_decode((string) $response->getBody(), true);
        } catch (RequestException $e) {
            // 网络请求错误
            throw new Exception('网络请求失败: '.$e->getMessage());
        } catch (Exception $e) {
            throw new Exception('请求失败: '.$e->getMessage());
        }
    }

    /**
     * 发送图片消息
     *
     * @param  string  $robotKey  群聊机器人的key
     * @param  string  $imgBase64  图片内容的base64编码 ,图片（base64编码前）最大不能超过2M，支持JPG,PNG格式
     * @param  string  $imgMd5  图片内容（base64编码前）的md5值
     * @return array{
     *     errcode: int,
     *     errmsg : string,
     * }
     *
     * @throws Exception|GuzzleException
     */
    public function sendImage(string $robotKey, string $imgBase64, string $imgMd5): array
    {
        try {
            $response = $this->httpClient->post($this->getRobotRequestUrl($robotKey),
                [
                    'json' => [
                        'msgtype' => 'image',
                        'image' => [
                            'base64' => $imgBase64, // 图片内容的base64编码
                            'md5' => $imgMd5, // 图片内容（base64编码前）的md5值
                        ],
                    ],
                ]
            );

            return json_decode((string) $response->getBody(), true);
        } catch (RequestException $e) {
            // 网络请求错误
            throw new Exception('网络请求失败: '.$e->getMessage());
        } catch (Exception $e) {
            throw new Exception('请求失败: '.$e->getMessage());
        }
    }

    /**
     * 发送图文消息
     *
     * @param  string  $robotKey  群聊机器人的key
     * @param  string  $title  标题，不超过128个字节，超过会自动截断
     * @param  string  $url  点击后跳转的URL
     * @param  string  $desc  [可选]描述，不超过512个字节，超过会自动截断
     * @param  string  $picUrl  [可选]图文消息的图片链接，支持JPG、PNG格式，较好的效果为大图 1068*455，小图150*150。
     * @return array{
     *     errcode: int,
     *     errmsg : string,
     * }
     *
     * @throws Exception|GuzzleException
     */
    public function sendNews(string $robotKey, string $title, string $url, string $desc = '', string $picUrl = ''): array
    {
        try {
            $response = $this->httpClient->post($this->getRobotRequestUrl($robotKey),
                [
                    'json' => [
                        'msgtype' => 'news',
                        'news' => [
                            'articles' => [
                                'title' => $title,
                                'description' => $desc,
                                'url' => $url,
                                'picurl' => $picUrl,
                            ],
                        ],
                    ],
                ]
            );

            return json_decode((string) $response->getBody(), true);
        } catch (RequestException $e) {
            // 网络请求错误
            throw new Exception('网络请求失败: '.$e->getMessage());
        } catch (Exception $e) {
            throw new Exception('请求失败: '.$e->getMessage());
        }
    }

    /**
     * 发送文件消息
     *
     * @param  string  $robotKey  群聊机器人的key
     * @param  string  $mediaId  文件id，通过下文的文件上传接口获取
     * @return array{
     *     errcode: int,
     *     errmsg : string,
     * }
     *
     * @throws Exception|GuzzleException
     */
    public function sendFile(string $robotKey, string $mediaId): array
    {
        try {
            $response = $this->httpClient->post($this->getRobotRequestUrl($robotKey),
                [
                    'json' => [
                        'msgtype' => 'file',
                        'file' => [
                            'media_id' => $mediaId,
                        ],
                    ],
                ]
            );

            return json_decode((string) $response->getBody(), true);
        } catch (RequestException $e) {
            // 网络请求错误
            throw new Exception('网络请求失败: '.$e->getMessage());
        } catch (Exception $e) {
            throw new Exception('请求失败: '.$e->getMessage());
        }
    }

    /**
     * 发送语音消息
     *
     * @param  string  $robotKey  群聊机器人的key
     * @param  string  $mediaId  语音文件id，通过下文的文件上传接口获取
     * @return array{
     *     errcode: int,
     *     errmsg : string,
     * }
     *
     * @throws Exception|GuzzleException
     */
    public function sendMedia(string $robotKey, string $mediaId): array
    {
        try {
            $response = $this->httpClient->post($this->getRobotRequestUrl($robotKey),
                [
                    'json' => [
                        'msgtype' => 'voice',
                        'voice' => [
                            'media_id' => $mediaId,
                        ],
                    ],
                ]
            );

            return json_decode((string) $response->getBody(), true);
        } catch (RequestException $e) {
            // 网络请求错误
            throw new Exception('网络请求失败: '.$e->getMessage());
        } catch (Exception $e) {
            throw new Exception('请求失败: '.$e->getMessage());
        }
    }

    /**
     * 发送文本通知模版卡片消息
     *
     * @param  string  $robotKey  群聊机器人的key
     * @param  string  $title  [可选]一级标题，建议不超过26个字。模版卡片主要内容的一级标题main_title.title和二级普通文本sub_title_text必须有一项填写
     * @param  string  $desc  [可选]标题辅助信息，建议不超过30个字
     * @param  int  $type  卡片跳转类型，1 代表跳转url，2 代表打开小程序。text_notice模版卡片中该字段取值范围为[1,2]
     * @param  string  $url  [可选]跳转事件的url，card_action.type是1时必填
     * @param  string  $appid  [可选]跳转事件的小程序的appid，card_action.type是2时必填
     * @param  string  $pagepath  [可选]跳转事件的小程序的pagepath，card_action.type是2时选填
     * @return array{
     *     errcode: int,
     *     errmsg : string,
     * }
     *
     * @throws Exception
     */
    public function sendTextTemplateCard(string $robotKey, string $title = '', string $desc = '', int $type = 1, string $url = '', string $appid = '', string $pagepath = ''): array
    {
        try {
            $response = $this->httpClient->post($this->getRobotRequestUrl($robotKey), [
                'json' => [
                    'msgtype' => 'template_card',
                    'template_card' => [
                        'card_type' => 'text_notice',
                        // 模版卡片的主要内容，包括一级标题和标题辅助信息
                        'main_title' => [
                            'title' => $title, // [可选]一级标题，建议不超过26个字。模版卡片主要内容的一级标题main_title.title和二级普通文本sub_title_text必须有一项填写
                            'desc' => $desc, // [可选]标题辅助信息，建议不超过30个字
                        ],
                        // 整体卡片的点击跳转事件，text_notice模版卡片中该字段为必填项
                        'card_action' => [
                            'type' => $type, // 卡片跳转类型，1 代表跳转url，2 代表打开小程序。text_notice模版卡片中该字段取值范围为[1,2]
                            'url' => $url, //  [可选]跳转事件的url，card_action.type是1时必填
                            'appid' => $appid, //  [可选]跳转事件的小程序的appid，card_action.type是2时必填
                            'pagepath' => $pagepath, // [可选]跳转事件的小程序的pagepath，card_action.type是2时选填
                        ],
                    ],
                ],
            ]);

            return json_decode((string) $response->getBody(), true);

        } catch (RequestException $e) {
            // 网络请求错误
            throw new Exception('网络请求失败: '.$e->getMessage());
        } catch (Exception $e) {
            throw new Exception('请求失败: '.$e->getMessage());
        }
    }
}
