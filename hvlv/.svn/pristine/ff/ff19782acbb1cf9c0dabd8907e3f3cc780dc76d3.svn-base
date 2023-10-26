#!/bin/sh
##tcp|in|d=3306|s=220.190.230.25 # KY server
cip=`grep -o  's=[0-9.]\+ # KY server' /etc/csf/csf.allow`
nip=`cat /home/pcaexpre/os/protected/runtime/ky.ip`

if [ "$cip" = "$nip" ]; then
	echo 'IP not changed'
else
	/bin/sed -i "s/$cip/$nip/" /etc/csf/csf.allow
	/usr/sbin/csf -r
	echo 'IP changed'
fi

