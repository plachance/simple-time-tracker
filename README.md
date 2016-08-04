README
======

Minimalist time tracking software.

Requirements
------------

* PHP 7
* ext-intl
* ext-apcu
* PostgreSQL 9.5+
* Composer

Installation
------------

* Run `./bin/setup prod` and follow the instructions.
* Run `./bin/acl` to setup proper ACLs.

Update
------

* If you update from 1.0, run `./bin/console doctrine:migrations:version 00000000000000 --add`.
* Run `./bin/update prod` and follow the instructions.

Usage
-----

* Create users with ```./bin/console :user:create -e prod```
* Change existing user's password with ```./bin/console :user:change-password -e prod```

Contributing
------------

Run `./bin/setup` to set up the project in an initial state or to reset the the project back to its initial state.
Run `./bin/update` after you run `git pull` to ensure the project is up to update.
Make sure there's no known security vulnerabilities, the code respect standards and all tests pass by running `./bin/test`.

License
-------

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details