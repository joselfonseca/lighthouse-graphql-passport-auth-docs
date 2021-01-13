@extends('_layouts.master')

@section('body')
    <section class="container max-w-6xl mx-auto px-6 py-10 md:py-12">
        <div class="flex flex-col-reverse mb-10 lg:flex-row lg:mb-24">
            <div class="flex mt-8">
                <div class="w-2/3">
                    <h1 id="intro-docs-template">{{ $page->siteName }}</h1>

                    <h2 id="intro-powered-by-jigsaw" class="font-light mt-4">{{ $page->siteDescription }}</h2>
                    <div class="flex my-10">
                        <a href="/docs/getting-started" title="{{ $page->siteName }} getting started" class="bg-blue-500 hover:bg-blue-600 font-normal text-white hover:text-white rounded mr-4 py-2 px-6">Get Started</a>

                        <a href="https://github.com/joselfonseca/lighthouse-graphql-passport-auth" title="Github" class="bg-gray-400 hover:bg-gray-600 text-white font-normal hover:text-white mr-4 rounded py-2 px-6">Github</a>

                        <a href="/tutorials/getting-started/" title="{{ $page->siteName }} tutorial" class="bg-blue-500 hover:bg-blue-600 font-normal text-white hover:text-white rounded mr-4 py-2 px-6">Tutorial</a>
                    </div>
                </div>
                <div class="flex justify-end w-1/3">
                    <img src="/assets/img/safe.svg" class="h-64">
                </div>
            </div>
        </div>
    </section>
    <section class="w-full bg-blue-900">
        <div class="container max-w-6xl mx-auto px-6 py-10 md:py-12">
            <div class="flex flex-row">
                <div class="w-1/2 p-3">
                    <h4 class="text-white text-center">Get started fast</h4>
                    <div class="text-gray-400">
                        Get your Authentication up and running for your GraphQL API with passport and mutations ready to use
                    </div>
                </div>
                <div class="w-1/2 p-3">
                    <h4 class="text-white text-center">Extend based on your needs</h4>
                    <div class="text-gray-400">
                        Extend or replace the mutations to fit your use case, use your ouw resolvers and take control over your authentication
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="w-full">
        <div class="container max-w-6xl mx-auto px-6 py-10 md:py-12">
            <h4 class="text-blue-900 text-center">Install the package</h4>
            <div class="text-gray-400">
                <pre>
                    <code class="language-bash">
                        composer require joselfonseca/lighthouse-graphql-passport-auth
                    </code>
                </pre>
            </div>
            <h4 class="text-blue-900 text-center">Get this default Schema for authentication with Laravel Passport</h4>
            <div class="text-gray-400">
                <pre>
                    <code class="language-graphql">
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
                    </code>
                </pre>
            </div>
            <div class="w-full text-center">
                <a href="/docs/getting-started" title="{{ $page->siteName }} getting started" class="bg-blue-500 hover:bg-blue-600 font-normal text-white hover:text-white rounded mr-4 py-2 px-6">Get Started</a>
            </div>
        </div>
    </section>
@endsection
