<?php
/**
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * @NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 */

return array(
    'queue' => array(
        'adapters' => array(
            'info_buyer' => array(
                'adapter' => 'gearman',
                'servers' => array(
                    array(
                        'host' => '10.199.0.1',
                        'port' => '4731',
                    ),
                ),
                'function' => array(
                    'admin_process' => JOB_PREFIX_BUYER . "adx_admin_process_job",
                    'admin_elastic' => JOB_PREFIX_BUYER . "adx_admin_elastic_job",
                    'admin_redis' => JOB_PREFIX_BUYER . "adx_admin_redis_job",
                    'admin_campaign' => JOB_PREFIX_BUYER . "adx_admin_campaign_job",
                    'admin_creative' => JOB_PREFIX_BUYER . "adx_admin_creative_job",
                    'admin_lineitem' => JOB_PREFIX_BUYER."adx_admin_lineitem_job",
                    'elastic_helper' => JOB_PREFIX_BUYER."adx_admin_elastic_helper_job",
                    'admin_monitor' => JOB_PREFIX_BUYER."adx_admin_monitor_job"
                )
            ),
            'info_adx' => array(
                'adapter' => 'gearman',
                'servers' => array(
                    array(
                        'host' => '10.199.0.1',
                        'port' => '4730',
                    ),
                ),
                'function' => array(
                    'admin_process' => JOB_PREFIX_ADX . "adx_admin_process_job",
                    'admin_elastic' => JOB_PREFIX_ADX . "adx_admin_process_elastic_job",
                    'admin_redis_buyer' => JOB_PREFIX_ADX . "adx_admin_redis_buyer_job",
                    'admin_redis_seller' => JOB_PREFIX_ADX . "adx_admin_redis_seller_job",
                )
            )
        )
    )
);
