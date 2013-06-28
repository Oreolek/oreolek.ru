This is the source code for the new version of [my personal website.](http://oreolek.ru/) I do it in my spare time for my own liking. 

License is [AGPL 3.0.](http://www.tldrlegal.com/l/AGPL3)

Oreolek.

## Requirements
* Kohana 3.3
* MySQL or MariaDB
* Sphinx

## Installation

* `git clone` - I assume you are familiar with this command
* `git submodule init`
* `git submodule update`
* Also you may need to init & update submodules for some modules
* Copy `application/config/database.php.example` to `application/config/database.php` and edit it.
* Copy `application/config/auth.php.example` to `application/config/auth.php` and edit it.
* Import SQL schema from schema.sql (autoinstall currently not working)
* Open in browser `SERVER_ADDR/install`
* Set server variable `KOHANA_ENV` to `production` when you're done hacking
