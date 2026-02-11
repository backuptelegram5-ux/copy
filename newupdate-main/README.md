# VodaNew

This repository hosts a collection of PHP and Node.js scripts designed for automating
web requests to several online gaming platforms (e.g., gameplay.mzansigames.club,
yellorush.co.za, wozagames.com). The scripts work together to generate request
headers, manage cookies, and submit scores.

## Key Components

- **Tools.php / Tools-mtn.php / Tools-mtn-v2.php / Tools-telkom-v2.php**
  Helper libraries to scrape leaderboard positions, choose target scores, and craft
  HTTP requests with custom headers or random user agents.

- **xavi*.php**  
  Scripts that assemble scores and call the helper functions to send game requests.
  Different variants (xavi.php, xavi-mtn.php, xavi-telkom.php, etc.) support various
  network providers.

- **requests-*.php / tests.php**
  Wrapper scripts that use the Zebra_CURL library for concurrency. `requests.php`
  and `tests.php` load cookies from `cookies-mtn.json`, lock them during use,
  and fire multiple `xavi-test.php` requests in parallel. Other variants such as
  `requests-voda2.php` work the same way but read from `cookies.json`.

- **cookies.json** and **cookies-mtn.json**
  Store cookie strings with an `isFree` flag so multiple runs can share cookies
  without conflicts. `cookies-mtn.json` is used by the MTN scripts.


- **update-mtn.php**
  Utility script that resets the `isFree` flags in `cookies-mtn.json` so the
  MTN cookie pool can be reused.

- **website.php**
  Simple page for submitting cookies. Users enter the password, paste the cookie
  string, and select whether it belongs to Vodacom or MTN. The cookie is then
  validated and saved to the matching file (`cookies.json` or
  `cookies-mtn.json`).

- **app.js**
  A small Express server that returns encrypted “X-CHAVI” tokens needed by the game
  endpoints.

## Usage

The `xavi` scripts combine with the `requests-*` wrappers to automate score
submissions. Cookies are pulled from either `cookies.json` or `cookies-mtn.json`,
locked during execution, and released afterward. Use `update-mtn.php` to reset
the MTN cookie pool when needed. Zebra_CURL handles concurrency for faster batch
requests.

These tools demonstrate automated HTTP requests, cookie management, and concurrent
processing. Use them responsibly and only for legitimate testing or learning
purposes.
