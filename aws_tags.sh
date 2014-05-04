#!/bin/bash
if [ ! -f /tmp/tags.json ]; then
	export AVAILABILITY_ZONE=`wget -qO- http://instance-data/latest/meta-data/placement/availability-zone`
	export REGION_ID=${AVAILABILITY_ZONE:0:${#AVAILABILITY_ZONE} - 1}
	ec2-describe-tags -F resource-id=`wget -q -O - http://instance-data/latest/meta-data/instance-id` --show-empty-fields `~/aws_auth.php` --region $REGION_ID > /tmp/tags
	`pwd -L`/aws_tags.php /tmp/tags
fi
cat /tmp/tags.json
