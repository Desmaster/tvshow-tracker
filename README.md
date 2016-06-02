Timpack_TvshowTracker
=====================
Track tv shows using the streets, the docks and a vault

Facts
-----
- Version: 1.0.0-alpha
- [Repository on Github](https://github.com/Desmaster/tvshow-tracker)
- [Direct download link](https://github.com/Desmaster/tvshow-tracker/archive/master.zip)

Description
-----------
Track tv shows using the streets, the docks and a vault

Installation
------------
*Composer*

`Package not registered at packagist, yet`

*Clone*

`git clone https://github.com/Desmaster/tvshow-tracker.git .`

*Direct download*

[Download latest version here](https://github.com/Desmaster/tvshow-tracker/archive/master.zip)

**Install dependencies**

Run `composer install` or `composer update` if you're feeling lucky.

**The Movie Database Account**

- Create an account on [themoviedb.org](https://www.themoviedb.org/account/signup)
- Log in and go to your account -> API -> Create the api key

**Configuration**

- Copy/paste `config/tv.sample.json` to `config/tv.json`
- In the `config/tv.json` fill in your [themoviedb.org](https://www.themoviedb.org) credentials and your api key under `tmdb`
- Then you fill in your tranmission-daemon details under `transmission`.

How to use
------------
For now, run `php bin/console tv:sync`

Requirements
------------
- `transmission-daemon` running somewhere, as long as you can reach it. If you need to install it, [lmgtfy](http://letmegooglethat.com/?q=how+to+install+transmission-daemon)!
- Make sure transmission-daemon has `rpc-enabled` set to `true`
- PHP >= 5.5.0
- Would be nice to have composer on your environment to install the dependencies
- See more at composer.json

Compatibility
-------------
- PHP 5.5.0

Roadmap
-------
- Add console logging (In progress)
- Add file logging
- Add vault checking integration
- Package on Packagist
- Write tests
