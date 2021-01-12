---
title: Installation
description: Installation instructions for Lighthouse GraphQL Passport Auth
extends: _layouts.documentation
section: content
---

# Pre requisites {#installation-pre-requisites}
This package requires you to install [Laravel Passport](https://laravel.com/docs/8.x/passport) prior to use it

# Installation {#installation-installation}
To install run 

``` bash
composer require joselfonseca/lighthouse-graphql-passport-auth
```

ServiceProvider will be attached automatically

Run this command to publish the migration, schema and configuration file
``` bash
php artisan vendor:publish --provider="Joselfonseca\LighthouseGraphQLPassport\Providers\LighthouseGraphQLPassportServiceProvider"
```

Add the following env vars to your .env from the `oauth_clients` and the password grant client

```
PASSPORT_CLIENT_ID=
PASSPORT_CLIENT_SECRET=
```

You are done with the installation!
 
