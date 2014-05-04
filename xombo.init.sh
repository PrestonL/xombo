#! /bin/sh

# $Id: xombo,v 1.1 2013/04/09 03:12:59 nolte Exp $

# xombo

# for server initialization

### BEGIN INIT INFO
# Provides:          xombo
# Required-Start:    $local_fs $remote_fs
# Required-Stop:     $local_fs
# Default-Start:     1 2 3 4 5
# Default-Stop:
# Short-Description: sets up deployments for xombo
# Description: deployments
### END INIT INFO

PATH=/sbin:/bin:/usr/sbin:/usr/bin

case "$1" in
  start)
	touch /home/ubuntu/deployment.txt
	;;
  stop)
	rm -rf /home/ubuntu/deployment.txt
	;;
  *)
        ;;
esac

exit 0
