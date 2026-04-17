<?php

namespace Appx\Support\WorkWechat;

use EasyWeChat\Kernel\Messages\TextCard;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;

/**
 * 通过 AgentId 发送消息到企业微信「自建」应用，例如： 测试助手
 *
 * 示例：
 *     1、发送消息到企业微信 自建 应用，不支持@功能
 *          CustomAppMessage::init()->sendTextToWorkApp('自建应用的 AgentId','UserID1|UserID2', "第一行 \n 第二行...");
 * 通过企业微信机器人发送 群聊天消息（小秘书）
 */
class CustomAppMessage extends MessageBase
{

    /**
     * 发送消息到企业微信「自建」应用，例如： 测试助手
     *
     * @param  string  $agentId  自建应用的 AgentId
     * @param  string  $toUsers  接收成员ID列表，'UserID1|UserID2'，用“|”分隔。
     * @param  string  $content  发送的文本消息内容
     * @return array{
     *     errcode: int,
     *     errmsg : string,
     *     msgid : string,
     * }
     *
     * @throws Exception|GuzzleException
     */
    public function sendTextToWorkApp(string $agentId, string $toUsers, string $content): array
    {
        // 2. 发送文本消息
        try {
            return $this->workWeChat->message->send([
                'touser' => $toUsers, // 接收成员ID列表，'UserID1|UserID2'，用“|”分隔。也可用"@all"发给全部成员[citation:3]。
                'msgtype' => 'text',
                'agentid' => $agentId, // 这里需要再次指定应用ID ,eg:1000029
                'text' => [
                    'content' => $content,
                ],
            ]);
        } catch (Exception $e) {
            // 处理异常，如网络错误等
            throw new Exception('发送异常: '.$e->getMessage());
        }
    }

    /**
     * 发送文本卡片消息到企业微信「自建」应用，例如： 测试助手
     *
     * @param  string  $agentId  自建应用的 AgentId
     * @return array{
     *     errcode: int,
     *     errmsg : string,
     * }
     *
     * @throws Exception
     */
    public function sendTextCardToWorkApp(string $agentId = '', string $title = '', string $description = '', string $url = '', string|array $toUsers = ''): array
    {
        try {
            ! empty($agentId) && ($this->config['agent_id'] = $agentId);
            $this->setConfig($this->config);

            // 获取 Messenger 实例
            $messenger = $this->workWeChat->messenger;

            // 准备消息
            $message = new TextCard([
                'title' => $title,
                'description' => $description,
                'url' => $url,
            ]);

            // 发送
            return $messenger->message($message)->toUser($toUsers)->send();

        } catch (RequestException $e) {
            // 网络请求错误
            throw new Exception('网络请求失败: '.$e->getMessage());
        } catch (Exception $e) {
            throw new Exception('请求失败: '.$e->getMessage());
        }
    }
}
