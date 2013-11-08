nagios-oneclick
===============

A Nagios one-click acknowledge script for taking care of alerts on the go.

== Requirements ==
* This script assumes you have Nagios already installed
* This script requires that Nagios commands are enabled
* This script is written in PHP, so obviously you need PHP running on your Nagios server.
* Nagios authentication must be encrypted in SHA

== Implementation ==
In oneclick.php, manage the following:

```//
// Edit the next three values to fit your implementation
//
$statusfile = '/usr/local/nagios/var/status.dat';
$commandfile = '/usr/local/nagios/var/rw/nagios.cmd';
$htpassfile = '/var/httpd/conf/htpass';
// Stop editing```
