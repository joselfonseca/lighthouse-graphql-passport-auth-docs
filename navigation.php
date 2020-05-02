<?php

return [
    'Getting Started' => [
        'url' => 'docs/getting-started',
        'children' => [
        ],
    ],
    'Installation' => [
        'url' => 'docs/installation',
        'children' => [
        ],
    ],
    'Schema' => [
        'url' => 'docs/default-schema',
        'children' => [
        ],
    ],
    'Usage' => [
        'url' => 'docs/usage',
        'children' => [
            'Email verification' => [
                'url' => 'docs/usage/#email-verification'
            ],
            'Socialite integration' => [
                'url' => 'docs/usage/#socialite-integration'
            ],
            'Global Middleware' => [
                'url' => 'docs/usage/#global-middleware'
            ]
        ],
    ],
    'Customization' => [
        'url' => 'docs/customization',
        'children' => [
            'Customize the Schema' => [
                'url' => 'docs/customization/#customize-schema'
            ],
            'Customize resolvers' => [
                'url' => 'docs/customization/#customize-resolvers'
            ],
            'Ignore Migration' => [
                'url' => 'docs/customization/#ignore-migration'
            ]
        ]
    ],
    'Github' => 'https://github.com/joselfonseca/lighthouse-graphql-passport-auth',
];
