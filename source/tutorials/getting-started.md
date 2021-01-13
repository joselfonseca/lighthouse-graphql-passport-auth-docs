---
title: Getting Started tutorial
description: Getting started with Lighthouse GraphQL Passport Auth in Laravel
extends: _layouts.documentation
section: content
date: 2021-01-13
---

#Getting started with GraphQL Authentication

Writing a GraphQL API with Lighthouse PHP and Laravel is really fun, but something that you always have to do is add authentication. This is a common task that Laravel already covers with Passport, but what if you want to have your login and refresh token endpoints as mutations? That is actually a good idea since you don’t really have to document your authentication mechanisms apart from your GraphQL API. You can have it with mutations as well. This is what inspired me to write a little package called Lighthouse GraphQL Passport Auth which at first seems like a long name and to tell you the truth, it is. But let’s dive into how to use it and we worry about the name later XD.

##Installation and configuration

First thing we need to do is install the package, now please keep in mind that Lighthouse PHP is required as well as passport, so why don’t we install it in one shot? In a brand new laravel app please enter:

```bash
composer require nuwave/lighthouse laravel/passport joselfonseca/lighthouse-graphql-passport-auth
```

Now let’s configure each of the packages starting with Laravel Passport, for that we are going to first run the migrations.

```bash
php artisan migrate
```
![](/assets/images/migrations.png)

Then we should run the passport Install command.

```bash
php artisan passport:install
```

![](/assets/images/passport.png)

Then add the HasApiTokens trait to your user model

```php

namespace App;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;
}
```

Now we should register the passport routes as we still need them to be able to get tokens internally.

```php
namespace App\Providers;

use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];
    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
        Passport::routes();
    }
}
```
Once we have this let’s add the passport driver to the API guard in the config/auth.php file.

```php
'guards' => [
    'web' => [
        'driver' => 'session',
        'provider' => 'users',
    ],
    'api' => [
        'driver' => 'passport',
        'provider' => 'users',
    ],
],
```
This should conclude the passport configuration.

Now let’s configure and install the Lighthouse PHP package. since it was already pulled from composer we just need to run the following commands to publish the default schema.

```bash
php artisan vendor:publish --provider="Nuwave\Lighthouse\LighthouseServiceProvider" --tag=schema
```

This command will create a file in graphql/schema.graphql with the following schema


```graphql
"A datetime string with format `Y-m-d H:i:s`, e.g. `2018-01-01 13:00:00`."
scalar DateTime @scalar(class: "Nuwave\\Lighthouse\\Schema\\Types\\Scalars\\DateTime")

"A date string with format `Y-m-d`, e.g. `2011-05-23`."
scalar Date @scalar(class: "Nuwave\\Lighthouse\\Schema\\Types\\Scalars\\Date")

type Query {
    users: [User!]! @paginate(type: "paginator" model: "App\\User")
    user(id: ID @eq): User @find(model: "App\\User")
}
type User {
    id: ID!
    name: String!
    email: String!
    created_at: DateTime!
    updated_at: DateTime!
}
```
If you have this file you should be ready to continue with the Lighthouse GraphQL Passport Auth package.

To get the values you need to open your database client and grab the password oauth client from the oauth_clients table, get the client id and secret and put them in the .env file

```env
PASSPORT_CLIENT_ID=2
PASSPORT_CLIENT_SECRET=69YJomGV9plchWIkGD2PyKBBTHfkNA7H83iYGc6j
```

Once you do that, publish the package configuration and default schema.

```shell
php artisan vendor:publish --provider="Joselfonseca\LighthouseGraphQLPassport\Providers\LighthouseGraphQLPassportServiceProvider"
```

This command should publish 2 files, the config/lighthouse-graphql-passport.php and graphql/auth.graphql file. For convenience and to be able to have better control over the auth schema lets update the config file to use the published schema by changing the value for the schema property to the path to the published file like this:

```php
'schema' => base_path('graphql/auth.graphql')
```

This will allow us to manipulate the schema if we need to extend it or make changes to the resolvers.

Now let’s remove the user type from the auth schema since we already have it in the default one exported before. Go to the graphql/auth/graphql file and remove the following type

```graphql
type User {
    id: ID!
    name: String!
    email: String!
}
```

Once we do that, we need to add a default mutation to the schema so the auth can extend it or we can simply move the auth mutations to the Mutation type in the main schema file. So look for this code in the graphql/auth.graphql file and move it to graphql/schema.graphql file

```graphql
extend type Mutation {
    login(input: LoginInput @spread): AuthPayload! @field(resolver: "Joselfonseca\\LighthouseGraphQLPassport\\GraphQL\\Mutations\\Login@resolve")
    refreshToken(input: RefreshTokenInput @spread): RefreshTokenPayload! @field(resolver: "Joselfonseca\\LighthouseGraphQLPassport\\GraphQL\\Mutations\\RefreshToken@resolve")
    logout: LogoutResponse! @field(resolver: "Joselfonseca\\LighthouseGraphQLPassport\\GraphQL\\Mutations\\Logout@resolve")
    forgotPassword(input: ForgotPasswordInput! @spread): ForgotPasswordResponse! @field(resolver: "Joselfonseca\\LighthouseGraphQLPassport\\GraphQL\\Mutations\\ForgotPassword@resolve")
    updateForgottenPassword(input: NewPasswordWithCodeInput @spread): ForgotPasswordResponse! @field(resolver: "Joselfonseca\\LighthouseGraphQLPassport\\GraphQL\\Mutations\\ResetPassword@resolve")
    register(input: RegisterInput @spread): RegisterResponse! @field(resolver: "Joselfonseca\\LighthouseGraphQLPassport\\GraphQL\\Mutations\\Register@resolve")
    socialLogin(input: SocialLoginInput! @spread): AuthPayload! @field(resolver: "Joselfonseca\\LighthouseGraphQLPassport\\GraphQL\\Mutations\\SocialLogin@resolve")
    verifyEmail(input: VerifyEmailInput! @spread): AuthPayload! @field(resolver: "Joselfonseca\\LighthouseGraphQLPassport\\GraphQL\\Mutations\\VerifyEmail@resolve")
    updatePassword(input: UpdatePassword! @spread): UpdatePasswordResponse! @field(resolver: "Joselfonseca\\LighthouseGraphQLPassport\\GraphQL\\Mutations\\UpdatePassword@resolve") @guard(with: ["api"])
}
```

Then remove the extend word

```graphql
type Mutation {
    login(input: LoginInput @spread): AuthPayload! @field(resolver: "Joselfonseca\\LighthouseGraphQLPassport\\GraphQL\\Mutations\\Login@resolve")
    refreshToken(input: RefreshTokenInput @spread): RefreshTokenPayload! @field(resolver: "Joselfonseca\\LighthouseGraphQLPassport\\GraphQL\\Mutations\\RefreshToken@resolve")
    logout: LogoutResponse! @field(resolver: "Joselfonseca\\LighthouseGraphQLPassport\\GraphQL\\Mutations\\Logout@resolve")
    forgotPassword(input: ForgotPasswordInput! @spread): ForgotPasswordResponse! @field(resolver: "Joselfonseca\\LighthouseGraphQLPassport\\GraphQL\\Mutations\\ForgotPassword@resolve")
    updateForgottenPassword(input: NewPasswordWithCodeInput @spread): ForgotPasswordResponse! @field(resolver: "Joselfonseca\\LighthouseGraphQLPassport\\GraphQL\\Mutations\\ResetPassword@resolve")
    register(input: RegisterInput @spread): RegisterResponse! @field(resolver: "Joselfonseca\\LighthouseGraphQLPassport\\GraphQL\\Mutations\\Register@resolve")
    socialLogin(input: SocialLoginInput! @spread): AuthPayload! @field(resolver: "Joselfonseca\\LighthouseGraphQLPassport\\GraphQL\\Mutations\\SocialLogin@resolve")
    verifyEmail(input: VerifyEmailInput! @spread): AuthPayload! @field(resolver: "Joselfonseca\\LighthouseGraphQLPassport\\GraphQL\\Mutations\\VerifyEmail@resolve")
    updatePassword(input: UpdatePassword! @spread): UpdatePasswordResponse! @field(resolver: "Joselfonseca\\LighthouseGraphQLPassport\\GraphQL\\Mutations\\UpdatePassword@resolve") @guard(with: ["api"])
}
```

You should now have your schema files like this

*graphql/auth.graphql*

```graphql
input LoginInput {
    username: String!
    password: String!
}

input RefreshTokenInput {
    refresh_token: String
}

type User {
    id: ID!
    name: String!
    email: String!
}

type AuthPayload {
    access_token: String
    refresh_token: String
    expires_in: Int
    token_type: String
    user: User
}

type RefreshTokenPayload {
    access_token: String!
    refresh_token: String!
    expires_in: Int!
    token_type: String!
}

type LogoutResponse {
    status: String!
    message: String
}

type ForgotPasswordResponse {
    status: String!
    message: String
}

type RegisterResponse {
    tokens: AuthPayload
    status: RegisterStatuses!
}

type UpdatePasswordResponse {
    status: String!
    message: String
}

enum RegisterStatuses {
    MUST_VERIFY_EMAIL
    SUCCESS
}

input ForgotPasswordInput {
    email: String! @rules(apply: ["required", "email"])
}

input NewPasswordWithCodeInput {
    email: String! @rules(apply: ["required", "email"])
    token: String! @rules(apply: ["required", "string"])
    password: String! @rules(apply: ["required", "confirmed", "min:8"])
    password_confirmation: String!
}

input RegisterInput {
    name: String! @rules(apply: ["required", "string"])
    email: String! @rules(apply: ["required", "email", "unique:users,email"])
    password: String! @rules(apply: ["required", "confirmed", "min:8"])
    password_confirmation: String!
}

input SocialLoginInput {
    provider: String! @rules(apply: ["required"])
    token: String! @rules(apply: ["required"])
}

input VerifyEmailInput {
    token: String!
}

input UpdatePassword {
    old_password: String!
    password: String! @rules(apply: ["required", "confirmed", "min:8"])
    password_confirmation: String!
}
```

*graphql/schema.graphql*

```graphql
"A datetime string with format `Y-m-d H:i:s`, e.g. `2018-01-01 13:00:00`."
scalar DateTime @scalar(class: "Nuwave\\Lighthouse\\Schema\\Types\\Scalars\\DateTime")
"A date string with format `Y-m-d`, e.g. `2011-05-23`."
scalar Date @scalar(class: "Nuwave\\Lighthouse\\Schema\\Types\\Scalars\\Date")

type Query {
    users: [User!]! @paginate(type: "paginator" model: "App\\User")
    user(id: ID @eq): User @find(model: "App\\User")
}

type Mutation {
    login(input: LoginInput @spread): AuthPayload! @field(resolver: "Joselfonseca\\LighthouseGraphQLPassport\\GraphQL\\Mutations\\Login@resolve")
    refreshToken(input: RefreshTokenInput @spread): RefreshTokenPayload! @field(resolver: "Joselfonseca\\LighthouseGraphQLPassport\\GraphQL\\Mutations\\RefreshToken@resolve")
    logout: LogoutResponse! @field(resolver: "Joselfonseca\\LighthouseGraphQLPassport\\GraphQL\\Mutations\\Logout@resolve")
    forgotPassword(input: ForgotPasswordInput! @spread): ForgotPasswordResponse! @field(resolver: "Joselfonseca\\LighthouseGraphQLPassport\\GraphQL\\Mutations\\ForgotPassword@resolve")
    updateForgottenPassword(input: NewPasswordWithCodeInput @spread): ForgotPasswordResponse! @field(resolver: "Joselfonseca\\LighthouseGraphQLPassport\\GraphQL\\Mutations\\ResetPassword@resolve")
    register(input: RegisterInput @spread): RegisterResponse! @field(resolver: "Joselfonseca\\LighthouseGraphQLPassport\\GraphQL\\Mutations\\Register@resolve")
    socialLogin(input: SocialLoginInput! @spread): AuthPayload! @field(resolver: "Joselfonseca\\LighthouseGraphQLPassport\\GraphQL\\Mutations\\SocialLogin@resolve")
    verifyEmail(input: VerifyEmailInput! @spread): AuthPayload! @field(resolver: "Joselfonseca\\LighthouseGraphQLPassport\\GraphQL\\Mutations\\VerifyEmail@resolve")
    updatePassword(input: UpdatePassword! @spread): UpdatePasswordResponse! @field(resolver: "Joselfonseca\\LighthouseGraphQLPassport\\GraphQL\\Mutations\\UpdatePassword@resolve") @guard(with: ["api"])
}

type User {
    id: ID!
    name: String!
    email: String!
    created_at: DateTime!
    updated_at: DateTime!
}
```
Lastly we need to install the GraphQL playground we will use to send queries and mutations to our GraphQL server, for that run the following command

```bash
composer require mll-lab/laravel-graphql-playground
```

This is ready for tenting now!

## Testing the auth mutations

Let’s start by running the PHP dev server

```
php artisan serve
```

This will open the port 8000 in our localhost for us to run the project, let’s navigate to http://127.0.0.1:8000/graphql-playground and you should see the playground like this

![](/assets/images/playground-1.png)

Now let’s create a user using a simple console command so we can log in using our graphql API. In the routes/console.php file enter

```php
Artisan::command('user', function () {
    \App\User::create([
        'name' => 'Jose Fonseca',
        'email' => 'myemail@email.com',
        'password' => bcrypt('123456789qq')
    ]);
})->describe('Create sample user');
```

Then run the command in the console

```shell
php artisan user
```

Now that we have a user to test with, lets run the following mutation in the GraphQL Playground

```graphql
mutation {
  login(input: {
    username: "myemail@email.com",
    password: "123456789qq"
  }) {
    access_token
    refresh_token
    expires_in
    token_type
    user {
      id
      email
      name
      created_at
      updated_at
    }
  }
}
```

This should give you the following response

```json
{
  "data": {
    "login": {
      "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6Ijk3NDE3MTI1YjBmNjQyZDNkZDljMDkwOGJiNTM2NDQ5YTYwZWU5ZTQ0ZTBkYzEzY2E5M2FhNGFjODI5ZWRiNzkwMWZhZmY5NzVhZTc5MjRiIn0.eyJhdWQiOiIyIiwianRpIjoiOTc0MTcxMjViMGY2NDJkM2RkOWMwOTA4YmI1MzY0NDlhNjBlZTllNDRlMGRjMTNjYTkzYWE0YWM4MjllZGI3OTAxZmFmZjk3NWFlNzkyNGIiLCJpYXQiOjE1NjE0MDkyMDMsIm5iZiI6MTU2MTQwOTIwMywiZXhwIjoxNTkzMDMxNjAzLCJzdWIiOiIxIiwic2NvcGVzIjpbXX0.CZOpXN0mFHXAQexpA4-FPMOcV-bdZRJD7zUK7guzethg9KUMrALhzgwfQZJv3w74Gnj54aAbdswHBwZEt9DREyEer7Ht0c8108amOllPqb-ydLET1RL1oYCE9H9vvUK0ZafLp09pMrXKot6UcViGOy97KF7YilAvaFfyGxSOmTTZyn0noe9F2ztIOPd3u9XuPuTR5yL-NqHufTTtyJkdQ2xPo03bF4tRfpMXQ5prnIJi4rxmBkpASwMwVraL4lVSZg_9STWMxXWWFdvmXydkNUtQSAftQyHmwMy33OOTxRtFRDN_1Y9wW7U9okVRM-gindkx0o_EB7ekcP1mvHc2PwxwWPMfFxezex98wYX3jTo8y7CN4vDrgUdXqrKkFc6JzwBgn8q_f7c5SzbzxR824h6ujFzSgMaUk_8zKtHX_qgDqaqPVzTebazQ0Pu9PNoYcCkQi5bNldCGuuaMsMxz3H-CWstR4_pAj9_jeKdvC5MA0OkQ30b3RlSmhSqb65LfZEU-y3wG62FKHD49JxBOpPh_Ga8SOQvfOCIL3SzURX9uOvSgcprQqLBYkhJNJC0gobAFgrKHbDhrBVvGH5U4BbIPVX-gnhR44aoyIf8sXFJQkPJJ7-p8HrCqDqjahrlDXsf4DRZGaVJJFhX2VcWAkCcQA2yJ1LSS1XpNU5oL-x4",
      "refresh_token": "def502004531ba2a174546464239e59c3d07332f8aec44bf95db59ba578bcb58c1986dd494d887c79282daf7379d2959b35816e07245ad237e5fa5e6b3ea1ff19ca0058aa82abc4d0abad427eb045b894c7591e4e248d684ea0c6772011ac1723d3d63805e0e148ec0875277072e39d9ecafcbe55d29b28607dc3ea9f85bdc0ca88aadca41b97e8f061b9b326dc922708f6826aee7e2e3296891e923fd9b5386b4c99d46e9832cb8e191c63bb31394a3dc92a8fc1b907925e9c1888d8b0c4d8c30ea330785dd429623e93979670856fdb5bf80c6c8cac08dc7d040d796e535aa082e241dd38b4ab7a4b0055d554cd85888533c76af594bed20dddcded751bfecd0ffdb741169f204c732cef0e2c49c26ec81de9dd8099b5ab1a481283f70c9685a11ca7018c259688601359722aee0f527c95b8b48062be81d414ce3e10a334d8b5c4f64c25b248c0b76bc40596b99580e38310e7d3a4e82c910276645101d821f",
      "expires_in": 31622400,
      "token_type": "Bearer",
      "user": {
        "id": "1",
        "email": "myemail@email.com",
        "name": "Jose Fonseca",
        "created_at": "2019-06-24 20:43:53",
        "updated_at": "2019-06-24 20:43:53"
      }
    }
  }
}
```

That is IT! you can now use the token in your GraphQL API!

## Testing a protected query

Let’s modify the users query to require authentication with passport, for that open the graphql/schema.graphql file and update the users query with the following

```graphql
users: [User!]! @guard(with: ["api"]) @paginate(type: "paginator" model: "App\\User")
```

Now try to run the a users query

```js
{
  users(count: 20){
    data {
      id
      email
    }
  }
}
```
This should give you an authentication error like the following

```json
{
  "errors": [
    {
      "debugMessage": "Unauthenticated.",
      "message": "Internal server error",
      "extensions": {
        "category": "internal"
      },
      "locations": [
        {
          "line": 2,
          "column": 3
        }
      ],
      "path": [
        "users"
      ],
      "trace": [
      ...
}
```
This means the query needs to have the access token in the request. To do that in the GraphQL Playground add the authorization header with the token we generated before

![](/assets/images/playground-2.png)

Re run the query and you should have the result just fine!

![](/assets/images/playground-3.png)

As you can see the query is protected and you can now use authentication in your GraphQL API.

Now go a head and try the rest of the mutations available to like refresh the token or reset a password.

If you like this little package please start it in Github and let me know if you run into any issues.

Happy Coding!
