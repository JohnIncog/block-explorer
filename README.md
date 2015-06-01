
# Paycoin Block Explorer

## Features/Ideas

### Address Tagging
 * Allow user to tag an address with a name.
 * If an address is tagged by multiple users, tag will be removed

#### To Do

##### Address Monitor
 * Flag an address to be monitored and receive alerts when it makes transactions
 * Alert types: in browser, email, sms, web hook

##### User accounts
 * signup / login / password reset.
 
##### Admin
 * list users, edit, remove

##### Claim Address Page
 * Verify a singed message.
 * then can tag it as verified and url will show

##### Graphs
 * Inflation
 * Rich list
 * Difficulty
 * Value out
 * Number of transactions
 * Time between blocks?

## Install

Requirements: PHP, Mysql, paycoind

#### Mysql

Create database block-explorer

Import database structure ```/conf/block-explorer.sql```

#### Config
copy ```/conf/config.sample.php``` to ```/conf/config.php```

edit ```/conf/config.php```
