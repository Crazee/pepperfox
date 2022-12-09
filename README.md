# pepperfox

## Background

Once upon a time there were several Personal Finance software programs you ran on your own computer to "manage your finances".  One of these was Microsoft Money and banks and brokerages adhered to the [OFX Standard](https://www.ofx.net/) for their data exporting and usually had OFX Servers in the cloud that would return your transaction data to you.

Today many of those servers are gone and you have to download the `.ofx` files manually if possible.  Some Financial Institutions may only give you mostly useless `.csv` files.

After Microsoft discontinued support of Money they did give the world a free, [Deluxe Sunset Version](https://download.cnet.com/Microsoft-Money-Plus-Sunset-Deluxe/3000-2057_4-77545178.html). And you could get a package called [PocketSense](https://sites.google.com/site/pocketsense/home/msmoneyfixp1/ofx-automation) that helped with the data importing.

## Solution

Well to get back to the heyday of adherence to Standards you can combine a few technologies:

* An account on [Mint](https://mint.com) which can still talk to Financial Institutions,
* The open source [mintapi](https://github.com/mintapi/mintapi/blob/main/README.md) which can extract data from `mint.com` as `.json` files,
* This technology, *pepperfox* which can convert those `.json` files into `.ofx` files,
* And *PocketSense* which will feed them into Microsoft Money.

These are easy to install on a Linux without being `root`.  The *mintapi* and *pepperfox* components are implemented as [Docker](https://www.docker.com/) images so you don't need to worry about conflicts between versions of, say, *python* needed by *mintapi* and whatever you otherwise need on your Linux.  You do need to have *docker* installed and fixed so you can run it without being root.

## Installation

You can create a new account on Mint just for these purposes and add your "troublesome" accounts.  It is fussy about authentication and I've found the only method that works well for *mintapi* is the `soft-token` method.  You'll need to have [Google Authenticator](https://en.wikipedia.org/wiki/Google_Authenticator) or an equivalent [TOTP](https://en.wikipedia.org/wiki/Time-based_One-time_Password_Algorithm) program.  Please follow the instructions in [MFA Authentication Methods](https://github.com/mintapi/mintapi/blob/main/README.md#mfa-authentication-methods) to set things up with Mint and get the 32-character `soft-token` you will need to set up your cloud connection.

To get the software 

```
wget https://pepperfox.biggianthead.org/install-pepperfox.sh
sh install-pepperfox.sh
```

which will put stuff in your `$HOME/pepperfox` directory.  Then to connect things up, run

```
$HOME/pepperfox/bin/setup <mint-email> <mint-password> <soft-token>
```
and you should be good to go.

## Usage

Whenever you want to get recent transaction data (last 3 months) just do:

```
$HOME/pepperfox/bin/run
```

and then move the files in the `$HOME/pepperfox/ofx` directory to your `.../pocketsense/import/` directory for *PocketSense* to process.  Voila!

For now, *pepperfox* Version 1.0 only handles credit card transactions.
