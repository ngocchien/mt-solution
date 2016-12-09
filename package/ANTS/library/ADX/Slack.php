<?php
namespace ADX;

use Maknz;

class Slack
{

    protected static $_instances = array();


    protected function __construct()
    {
    }

    protected function __clone()
    {
    }


    public static function getInstances($instance)
    {
        if (!isset(self::$_instances[$instance])) {
            //
            $config = Config::get('slack');
            //
            $channel_info = $config['slack']['channels'][$instance];
            //
            self::$_instances[$instance] = new Maknz\Slack\Client($channel_info['webhook'], $channel_info['settings']);
        }

        return self::$_instances[$instance];
    }


    public static function sendMessageChannel($instance, $message)
    {
        //
        $client = self::getInstances($instance);
        //
        $client->send($message);
    }

    public static function sendAttach($instance)
    {
        $client = self::getInstances($instance);
        $client->attach([
            'fallback' => 'Network traffic (kb/s): How does this look? @slack-ops - Sent by Julie Dodd - https://datadog.com/path/to/event',
            'title' => 'Network traffic (kb/s)',
            'text' => 'How does this look? @slack-ops - Sent by Julie Dodd',
            'color' => 'danger',
            'image_url' => 'https://a.slack-edge.com/66f9/img/api/attachment_example_datadog.png'
        ])->send('New alert from the monitoring system');
    }

    public static function sendMessageChannelWithEmoticon($instance, $message, $emoticon = ':warning:')
    {
        //
        $client = self::getInstances($instance);
        //
        $client->withIcon($emoticon)->send($message);
    }

    public static function sendMessageUser($f_user, $t_users_message)
    {
        //
        $client = self::getInstances('private');
        //
        foreach ($t_users_message as $t_user => $message) {
            $client->from('@' . $f_user)->to('@' . $t_user)->send($message);
        }
    }

    public static function closeAllConnections()
    {
        if (empty(self::$_instances)) {
            return;
        }

        foreach (self::$_instances as &$instances) {
            unset($instances);
        }
    }
}