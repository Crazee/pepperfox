#!/bin/sh

PF=$HOME/pepperfox
VER=1.0

if [ -e $PF ]
then

    echo "To reinstall pepperfox, first remove the existing $PF directory."
    exit 1

fi

mkdir $PF
cd $PF

mkdir bin mint session ofx

chmod 777 mint session

cat >bin/version <<!!
#!/bin/sh

echo "pepperfox version $VER"

!!

cat >bin/run <<!!
#!/bin/sh

echo "Please run $PF/bin/setup first to setup your Mint credentials."

!!

cat >bin/setup <<'!!'
#!/bin/sh

PF=$HOME/pepperfox

if [ $# -ne 3 ]
then
    echo "Usage: $PF/bin/setup <mint-email> <mint-password> <mint-soft-token>"
    exit 1
fi

cd $PF

cat >config <<!
headless
use-chromedriver-on-path
accounts
transactions
transaction-date-filter=5
mfa-method=soft-token
mfa-token=$3
session-path=/fox/session
filename=/fox/mint/data
!

cat >bin/run <<!
#!/bin/sh

user=`id -u`:`id -g`

rm -f $PF/mint/* $PF/ofx/*

docker run --rm --mount type=bind,source=$PF,destination=/fox --shm-size=2g ghcr.io/mintapi/mintapi mintapi $1 $2 --config-file /fox/config 

docker run --user $user --rm --mount type=bind,source=$PF,destination=/fox --shm-size=2g ghcr.io/crazee/pepperfox

!

echo "Setup complete.  Use $PF/bin/run to fetch your data.  Output will be in $PF/ofx."

!!

chmod 755 bin/*

echo "pepperfox version $VER installed in $PF."
echo "Run $PF/bin/setup next."
