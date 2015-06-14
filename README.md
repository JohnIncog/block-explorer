
# Paycoin Block Explorer

## Features/Ideas

### Address Tagging
 * Allow user to tag an address with a name.
 * If an address is tagged by multiple users, tag will be removed
 * Verify with a singed message and add a url to the address.

##### Graphs
 * Inflation
 * Rich list
 * Outstanding


#### To Do

##### Address Monitor
 * Flag an address to be monitored and receive alerts when it makes transactions
 * Alert types: in browser, email, sms, web hook

##### User accounts
 * signup / login / password reset.
 
##### Admin
 * list users, edit, remove


## Install

Requirements: PHP, Mysql, paycoind, memcached

#### Mysql

Create database block-explorer

Import database structure ```/conf/block-explorer.sql```

#### Config
copy ```/conf/config.sample.php``` to ```/conf/config.php```

edit ```/conf/config.php```

#### Crons

```
30 * * * * php /vagrant/app/public/index.php /cli/buildRichList >> /tmp/buildRichList.log
*/1 * * * * php /vagrant/app/public/index.php /cli/buildDatabase >> /tmp/buildDatabase.log
```

