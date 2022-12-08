# pepperfox

## Background

Once upon a time there were Personal Finance software programs you ran on your own computer to "manage your finances".  One of these was Microsoft Money and banks and brokerages adhered to the [OFX Standard](https://www.ofx.net/) for their data exporting and usually had OFX Servers in the cloud that would return your transaction data to you.

Today many of those servers are gone and you have to manually download the `.ofx` files manually if possible.  Some Financial Institutions may only give you mostly useless `.csv` files.

After Microsoft discontinued support of Money they did give the world a free, [Deluxe Sunset Version](https://download.cnet.com/Microsoft-Money-Plus-Sunset-Deluxe/3000-2057_4-77545178.html). And you could get a package called [PocketSense](https://sites.google.com/site/pocketsense/home/msmoneyfixp1/ofx-automation) that helped with the data importing.

## Solution

Well to get back to the heyday of adherence to Standards you can combine a few technologies:

* An account on [Mint]{https://mint.intuit.com) which can still talk to Financial Institutions,
* The open source [mintapi](https://github.com/mintapi/mintapi/blob/main/README.md) which can extract data from `mint.com` as `.json` files,
* This technology, *pepperfox* which can convert those `.json` files into `.ofx` files,
* And *PocketSense* which will feed them into Microsoft Money.

These are easy to install on a Linux without being `root`.  The *mintapi* and *pepperfox* components are implemented as [Docker](https://www.docker.com/) images so you don't need to worry about conflicts between versions of, say, python needed by *mintapi* and whatever you otherwise need on your Linux.  You do need to have *docker* installed and fixed so can run it without being root.
