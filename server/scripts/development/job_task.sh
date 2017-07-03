#!/bin/sh
export PATH=/usr/kerberos/sbin:/usr/kerberos/bin:/usr/local/sbin:/usr/local/bin:/sbin:/bin:/usr/sbin:/usr/bin:/root/bin
. /var/www/html/mt-solution/server/scripts/development/init.sh
prefix_log="/var/www/html/mt-solution/server/job/log"
env="development"
case "$1" in
1min)

	;;
5min)

	;;
30min)

	;;
hour)

	;;
2hour)

	;;
day_0h)

    ##$php /home/khamdb/v3.adx.vn/server/job/crontab/elastic.php --env $env --type "repair-data-elastic"
	;;
day_0h30)
	;;
day_1h)
	;;
day_2h)
	;;
day_3h)
	;;
day_6h)
    $php /var/www/html/mt-solution/server/job/crontab/process.php --env $env --type "crontab-upload"
	;;
day_9h)
	;;
day_23h50)
	;;
*)
	echo $"Usage: $0 {30min|hour|day_0h|day_0h30|day_1h|day_23h50|1min}"
	exit 2
esac
