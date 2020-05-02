---
title: Default Schema
description: Default schema in Lighthouse GraphQL Passport Auth
extends: _layouts.documentation
section: content
---

# Default schema {#default-schema}
By default the schema is defined internally in the package, once published it will be saved in `graphql/auth.graphql` and it looks like this:

```
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
    email: String! @rules(apply: ["required", "email"])
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
    password: String! @rules(apply: ["required", "confirmed", "min:8"])
    password_confirmation: String!
}


extend type Mutation {
    login(input: LoginInput @spread): AuthPayload! @field(resolver: "Joselfonseca\\LighthouseGraphQLPassport\\GraphQL\\Mutations\\Login@resolve")
    refreshToken(input: RefreshTokenInput @spread): RefreshTokenPayload! @field(resolver: "Joselfonseca\\LighthouseGraphQLPassport\\GraphQL\\Mutations\\RefreshToken@resolve")
    logout: LogoutResponse! @field(resolver: "Joselfonseca\\LighthouseGraphQLPassport\\GraphQL\\Mutations\\Logout@resolve")
    forgotPassword(input: ForgotPasswordInput! @spread): ForgotPasswordResponse! @field(resolver: "Joselfonseca\\LighthouseGraphQLPassport\\GraphQL\\Mutations\\ForgotPassword@resolve")
    updateForgottenPassword(input: NewPasswordWithCodeInput @spread): ForgotPasswordResponse! @field(resolver: "Joselfonseca\\LighthouseGraphQLPassport\\GraphQL\\Mutations\\ResetPassword@resolve")
    register(input: RegisterInput @spread): RegisterResponse! @field(resolver: "Joselfonseca\\LighthouseGraphQLPassport\\GraphQL\\Mutations\\Register@resolve")
    socialLogin(input: SocialLoginInput! @spread): AuthPayload @field(resolver: "Joselfonseca\\LighthouseGraphQLPassport\\GraphQL\\Mutations\\SocialLogin@resolve")
    verifyEmail(input: VerifyEmailInput! @spread): AuthPayload! @field(resolver: "Joselfonseca\\LighthouseGraphQLPassport\\GraphQL\\Mutations\\VerifyEmail@resolve")
    updatePassword(input: UpdatePassword! @spread): UpdatePasswordResponse! @field(resolver: "Joselfonseca\\LighthouseGraphQLPassport\\GraphQL\\Mutations\\UpdatePassword@resolve") @guard(with: ["api"])
}
```

In the configuration file you can now set the schema file to be used for the exported one like this:

```php
    /*
    |--------------------------------------------------------------------------
    | GraphQL schema
    |--------------------------------------------------------------------------
    |
    | File path of the GraphQL schema to be used, defaults to null so it uses
    | the default location
    |
    */
    'schema' => base_path('graphql/auth.graphql')
```

This will allow you to change the schema and resolvers if needed.
