#!/bin/sh
export PATH=/usr/kerberos/sbin:/usr/kerberos/bin:/usr/local/sbin:/usr/local/bin:/sbin:/bin:/usr/sbin:/usr/bin:/root/bin
. /data/www/public_html/v3.adx.vn/server/scripts/production/init_v3.adx.vn.sh
prefix_log="/data/www/public_html/v3.adx.vn/server/server/job/log"
env="production"
case "$1" in
1min)
    ##monitor elastic-buyer
    $php /data/www/public_html/v3.adx.vn/server/job/crontab/monitor.php --env $env --type "elastic-buyer"

    ##monitor worker-subscribe
    $php /data/www/public_html/v3.adx.vn/server/job/crontab/monitor.php --env $env --type "worker-subscribe-buyer"

    ##monitor worker-buyer
    $php /data/www/public_html/v3.adx.vn/server/job/crontab/monitor.php --env $env --type "worker-buyer"

    ##monitor worker-adx
    $php /data/www/public_html/v3.adx.vn/server/job/crontab/monitor.php --env $env --type "worker-adx"

    ##monitor logs-php-v3
    $php /data/www/public_html/v3.adx.vn/server/job/crontab/monitor.php --env $env --type "logs-php-v3"

    ##monitor logs-php-v1
    $php /data/www/public_html/v3.adx.vn/server/job/crontab/monitor.php --env $env --type "logs-php-v1"

    ##monitor campaign-no-lineitem
    $php /data/www/public_html/v3.adx.vn/server/job/crontab/monitor.php --env $env --type "campaign-no-lineitem"

    ##monitor creative-no-lineitem
    $php /data/www/public_html/v3.adx.vn/server/job/crontab/monitor.php --env $env --type "creative-no-lineitem"
    ;;
5min)
    ## Repair data elastic
    $php /data/www/public_html/v3.adx.vn/server/job/crontab/elastic.php --env $env --type "backup-elastic"

    ##monitor status index
    $php /data/www/public_html/v3.adx.vn/server/job/crontab/monitor.php --env $env --type "check-status-index-es"
	;;
30min)

	;;
hour)
    ## LINEITEM
    $php /data/www/public_html/v3.adx.vn/server/job/crontab/monitor.php --env $env --type "check-key-lineitem"

    ## Monitor miss data ES
    $php /data/www/public_html/v3.adx.vn/server/job/crontab/monitor.php --env $env --type "monitor-data-es"

    ##Audience
    ##Update total visitor audience
    $php /data/www/public_html/v3.adx.vn/server/job/crontab/process.php --env $env --type "update-visitor-audience"

    ##monitor campaign target
    $php /data/www/public_html/v3.adx.vn/server/job/crontab/monitor.php --env $env --type "monitor-target-campaign"
	;;
2hour)


	;;
day_0h)

	## Repair data elastic
	$php /data/www/public_html/v3.adx.vn/server/job/crontab/elastic.php --env $env --type "repair-data-elastic"
	## Remove api logs elastic
	$php /data/www/public_html/v3.adx.vn/server/job/crontab/elastic.php --env $env --type "remove-api-logs"
	##Remove monitor query
	$php /data/www/public_html/v3.adx.vn/server/job/crontab/elastic.php --env $env --type "remove-monitor-query"

	## REDIS - KhamDB

	## CREATIVE - KhamDB


	## AUDIENCE - KhamDB

	## REMARKETING - KhamDB

	## BOOKING - KhamDB

	## LINEITEM - KhanhHV
	$php /data/www/public_html/v3.adx.vn/server/job/crontab/lineitem.php --env $env --type "lineitem-pending-to-running"

    ## LINEITEM FINISH - KhanhHV
    $php /data/www/public_html/v3.adx.vn/server/job/crontab/lineitem.php --env $env --type "lineitem-finish"

	;;
day_0h30)
	##### RUN at 00h30 everyday #####

	## AUDIENCE - KhamDB

	## REMARKETING - KhamDB

	## Send Mail Warning Campaign don't finish target daily

	;;
day_1h)
    ##Audience - GIANGNT
	##Estimated total visitor audience
    $php /data/www/public_html/v3.adx.vn/server/job/crontab/process.php --env $env --type "estimated-audience"
	## Recalculate data booking - TienTM

	## Synchronous Oracle = ES

	;;
day_2h)
	## REDIS - KhamDB

    ## PROCESS
    $php /data/www/public_html/v3.adx.vn/server/job/crontab/process.php --env $env --type "build-key-detail-demographic"
	;;
day_3h)
	## REDIS - KhamDB

	;;
day_6h)
	## Update Similar Product - KhamDB

	;;
day_9h)
	## REDIS - KhamDB

	;;
day_23h50)
	##### RUN at 23h50 everyday #####
	## Build data booking - TienTM

	;;
update_dynamic_product)
	##### RUN every minute #####

	;;
stop_creative_pause)
	##### RUN at 05h everyday #####

	;;
cpd_job)
	##### RUN at 06:00  every day  #####

	;;
debug)

    ## Repair data elastic
	$php /data/www/public_html/v3.adx.vn/server/job/crontab/elastic.php --env $env --type "repair-data-elastic"
	;;
*)
	echo $"Usage: $0 {30min|hour|day_0h|day_0h30|day_1h|day_23h50|1min}"
	exit 2
esac
