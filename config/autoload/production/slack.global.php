<?php

return array(
    'slack' => array(
        'channels' => array(
            'application' => array(
                'webhook' => 'https://hooks.slack.com/services/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',
                'settings' => array(
                    'username' => 'ANTS Bot',
                    'channel' => 'production',
                    'link_names' => true
                )
            ),
            'monitor_elastic' => array(
                'webhook' => 'https://hooks.slack.com/services/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',
                'settings' => array(
                    'username' => 'ANTS Bot',
                    'channel' => 'elastic',
                    'link_names' => true
                )
            ),
            'monitor_worker' => array(
                'webhook' => 'https://hooks.slack.com/services/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',
                'settings' => array(
                    'username' => 'ANTS Bot',
                    'channel' => 'worker',
                    'link_names' => true
                )
            ),
            'private' => array(
                'webhook' => 'https://hooks.slack.com/services/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',
                'settings' => array(
                    'link_names' => true
                )
            )
        )
    )
);
