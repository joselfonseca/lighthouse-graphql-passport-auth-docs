---
title: Usage
description: Usage instructions for Lighthouse GraphQL Passport Auth
extends: _layouts.documentation
section: content
---

# Usage {#usage}

the package will add 9 mutations to your GraphQL API

```
extend type Mutation {
    login(input: LoginInput): AuthPayload!
    refreshToken(input: RefreshTokenInput): RefreshTokenPayload!
    logout: LogoutResponse!
    forgotPassword(input: ForgotPasswordInput!): ForgotPasswordResponse!
    updateForgottenPassword(input: NewPasswordWithCodeInput): ForgotPasswordResponse!
    register(input: RegisterInput @spread): AuthPayload!
    socialLogin(input: SocialLoginInput! @spread): AuthPayload!
    verifyEmail(input: VerifyEmailInput! @spread): AuthPayload!
    updatePassword(input: UpdatePassword! @spread): UpdatePasswordResponse!
}
```

- **login:** Will allow your clients to log in by using the password grant client.
- **refreshToken:** Will allow your clients to refresh a passport token by using the password grant client.
- **logout:** Will allow your clients to invalidate a passport token.
- **forgotPassword:** Will allow your clients to request the forgot password email.
- **updateForgottenPassword:** Will allow your clients to update the forgotten password from the email received.
- **register:** Will allow your clients to register a new user using the default Laravel registration fields
- **socialLogin:** Will allow your clients to log in using access token from social providers using socialite
- **verifyEmail:** Will allow your clients to verify the email after they receive a token in the email
- **updatePassword:** Will allow your clients to update the logged in user password - This requires the global **AuthenticateWithApiGuard** registered in the lighthouse config

## Using the email verification {#email-verification}

If you want to use the email verification feature that comes with laravel, please follow the instruction in the laravel documentation to configure the model in [https://laravel.com/docs/6.x/verification](https://laravel.com/docs/6.x/verification), once that is done add the following traits

```php
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Joselfonseca\LighthouseGraphQLPassport\HasLoggedInTokens;
use Joselfonseca\LighthouseGraphQLPassport\MustVerifyEmailGraphQL;

class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable;
    use HasApiTokens;
    use HasSocialLogin;
    use MustVerifyEmailGraphQL;
    use HasLoggedInTokens;
}
```
This will add some methods for the email notification to be sent with a token. Use the token in the following mutation.

```js
{
  mutation {
      verifyEmail(input: {
          "token": "HERE_THE_TOKEN"
      }) {
          access_token
          refresh_token
          user {
              id
              name
              email
          }
      }
  }
}
```   
If the token is valid the tokens will be issued.
> Is very important that you remove the `SendEmailVerificationNotification` listener from your `EventServiceProvider` or 2 emails will be sent.

> The token generated for this package to verify the email is different from the one created by default in Laravel due to implementation details. This means that this same token won't work for verifying the user's email with the laravel default views.

## Using socialite for social login {#socialite-integration}

If you want to use the mutation for social login, please add the `Joselfonseca\LighthouseGraphQLPassport\HasSocialLogin` trait to your user model like this

```php
use Joselfonseca\LighthouseGraphQLPassport\HasSocialLogin;

class User extends Authenticatable
{
    use Notifiable;
    use HasApiTokens;
    use HasSocialLogin;
}
```
This will add a method that is used by the mutation to get the user from the social network and create or get it from the DB based on the `provider` and `provider_id`

```php
    /**
     * @param Request $request
     * @return mixed
     */
    public static function byOAuthToken(Request $request)
    {
        $userData = Socialite::driver($request->get('provider'))->userFromToken($request->get('token'));
        try {
            $user = static::where('provider', Str::lower($request->get('provider')))->where('provider_id', $userData->getId())->firstOrFail();
        } catch (ModelNotFoundException $e) {
            $user = static::create([
                'name' => $userData->getName(),
                'email' => $userData->getEmail(),
                'provider' => $request->get('provider'),
                'provider_id' => $userData->getId(),
                'password' => Hash::make(Str::random(16)),
                'avatar' => $userData->getAvatar()
            ]);
        }
        Auth::onceUsingId($user->id);
        return $user;
    }
``` 

You can override the method and add more fields if you need to.

*Make sure Socialite is configured properly to use the social network, please see [Laravel Socialite](https://laravel.com/docs/6.x/socialite)* 

## Global Authenticate middleware {#global-middleware}

> This may not longer be required since Lighthouse introduced the same method in the code in the latest versions.

You can use the [guard](https://lighthouse-php.com/4.10/api-reference/directives.html#guard) to validate that the user is logged in, however this will not set the User property on the context, for this you will have to register the global middleware provided so you can have access to the user in the context object

Set the global middleware `\Joselfonseca\LighthouseGraphQLPassport\Http\Middleware\AuthenticateWithApiGuard::class` in the lighthouse php config

```php

return [

    /*
    |--------------------------------------------------------------------------
    | Route Configuration
    |--------------------------------------------------------------------------
    |
    | Controls the HTTP route that your GraphQL server responds to.
    | You may set `route` => false, to disable the default route
    | registration and take full control.
    |
    */

    'route' => [
        /*
         * The URI the endpoint responds to, e.g. mydomain.com/graphql.
         */
        'uri' => 'graphql',

        /*
         * Lighthouse creates a named route for convenient URL generation and redirects.
         */
        'name' => 'graphql',

        /*
         *
         * Beware that middleware defined here runs before the GraphQL execution phase,
         * so you have to take extra care to return spec-compliant error responses.
         * To apply middleware on a field level, use the @middleware directive.
         */
        'middleware' => [
            \Nuwave\Lighthouse\Support\Http\Middleware\AcceptJson::class,
            \Joselfonseca\LighthouseGraphQLPassport\Http\Middleware\AuthenticateWithApiGuard::class
        ],
    ],
...
```

This will set the logged in user in the guard for the context object

```php
return $context->user(); // will return the logged in user.
``` 
## Events emitted by this package {#events}

**UserLoggedIn:** This event will be emitted when a new access token is created via the login mutation, this event receives the user model

```php
<?php

namespace Joselfonseca\LighthouseGraphQLPassport\Events;

use Illuminate\Contracts\Auth\Authenticatable;

/**
 * Class UserLoggedIn.
 */
class UserLoggedIn
{
    /**
     * @var Authenticatable
     */
    public $user;

    /**
     * UserLoggedIn constructor.
     *
     * @param Authenticatable $user
     */
    public function __construct(Authenticatable $user)
    {
        $this->user = $user;
    }
}
``` 

**UserLoggedOut** This event will be emitted when the logout mutation was called and the token has been revoked, this event receives the user model

````php
<?php

namespace Joselfonseca\LighthouseGraphQLPassport\Events;

use Illuminate\Contracts\Auth\Authenticatable;

/**
 * Class UserLoggedOut.
 */
class UserLoggedOut
{
    /**
     * @var Authenticatable
     */
    public $user;

    /**
     * UserLoggedOut constructor.
     *
     * @param Authenticatable $user
     */
    public function __construct(Authenticatable $user)
    {
        $this->user = $user;
    }
}
````

**Illuminate\Auth\Events\Registered** This event will be emitted when the user is registered via the register mutation or using the socialite integration, this event receives the user model and is part of the Laravel Default Authentication system
**UserRefreshedToken** This event will be emitted when the user refresh a token via de refresh token mutation, it received the user model.

````php
<?php

namespace Joselfonseca\LighthouseGraphQLPassport\Events;

use Illuminate\Contracts\Auth\Authenticatable;

/**
 * Class UserRefreshedToken.
 */
class UserRefreshedToken
{
    /**
     * @var Authenticatable
     */
    public $user;

    /**
     * UserRefreshedToken constructor.
     *
     * @param Authenticatable $user
     */
    public function __construct(Authenticatable $user)
    {
        $this->user = $user;
    }
}
````

**PasswordUpdated** This event will be emmited from the `updatePassword` and `updateForgottenPassword` mutations after the user has set the new password. This event receives the user model as well.

````php

<?php

namespace Joselfonseca\LighthouseGraphQLPassport\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class PasswordUpdated.
 */
class PasswordUpdated
{
    use Dispatchable;
    use SerializesModels;

    /**
     * @var
     */
    public $user;

    /**
     * PasswordUpdated constructor.
     *
     * @param $user
     */
    public function __construct($user)
    {
        $this->user = $user;
    }
}
````
**ForgotPasswordRequested** This event will be emitted when the user requests an email for forgotten password. In this case only the email is passed to the event. 

```php
<?php

namespace Joselfonseca\LighthouseGraphQLPassport\Events;

/**
 * Class ForgotPasswordRequested.
 */
class ForgotPasswordRequested
{
    /**
     * @var string
     */
    public $email;

    /**
     * ForgotPasswordRequested constructor.
     *
     * @param string $email
     */
    public function __construct(string $email)
    {
        $this->email = $email;
    }
}
```
  
