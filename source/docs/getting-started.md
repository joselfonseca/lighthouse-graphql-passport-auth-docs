---
title: Getting Started
description: Getting started with Lighthouse GraphQL Passport Auth
extends: _layouts.documentation
section: content
---

# Getting Started {#getting-started}

GraphQL mutations for Laravel Passport using Lighthouse PHP.

## Motivation {#getting-started-motivation}

Every time we start a new project and need to have an IOS or Android client we need to provide authentication methods for them. If the application is simple and does not require any third party service for authentication like Cognito or Firebase auth, we can provide such functionality withing our GraphQL API. Writting such boilerplate takes time and is a repetitive task that we wanted to extract so we could start with a nice scaffold for authentication in our GraphQL server.

## Common use cases {#getting-started-common-use-cases}

You should evaluate if this package will serve your use case, some times it does not fit. Here is a list of common use cases where this package has been used

- **IOS app backend:** An IOS app that uses authentication in the GraphQL server via laravel.
- **Android app backend:** An Android app that uses authentication in the GraphQL server via laravel.
- **React, vue or angular app:** A Web application that uses laravel passport authentication is also a good candidate although we prefer to use laravel's default authentication system with encrypted cookies. This off course requires you to render the front app in the same laravel project, for some application that is OK although it does not always apply.

 
## Where you should avoid this package {#getting-started-where-you-should-avoid-this-package}

You should not use this package if you are using a 3rd party authentication system like AWS Cognito, Firebase Auth or Auth0.

## Why the OAuth client is used in the backend and not from the client application?

When an application that needs to be re compiled and re-deploy to stores like an iOS app needs to change the client for whatever reason, it becomes a blocker for QA or even brakes the production app if the client is removed. The app will not work until the new version with the updated keys is deployed. There are alternatives to store this configuration in the client but for this use case we are relying on the backend to be the OAuth client

## Change log

Please see the releases page [https://github.com/joselfonseca/lighthouse-graphql-passport-auth/releases](https://github.com/joselfonseca/lighthouse-graphql-passport-auth/releases)

## Contributing

Please see [CONTRIBUTING](https://github.com/joselfonseca/lighthouse-graphql-passport-auth/blob/master/CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email jose at ditecnologia dot com instead of using the issue tracker.

## Credits

- [Jose Luis Fonseca](https://github.com/joselfonseca)
- [All Contributors](https://github.com/joselfonseca/lighthouse-graphql-passport-auth/graphs/contributors)

## License

The MIT License (MIT). Please see [License File](https://github.com/joselfonseca/lighthouse-graphql-passport-auth/blob/master/license.md) for more information. 
