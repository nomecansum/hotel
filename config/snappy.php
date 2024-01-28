<?php

// return [

//     /*
//     |--------------------------------------------------------------------------
//     | Snappy PDF / Image Configuration
//     |--------------------------------------------------------------------------
//     |
//     | This option contains settings for PDF generation.
//     |
//     | Enabled:
//     |    
//     |    Whether to load PDF / Image generation.
//     |
//     | Binary:
//     |    
//     |    The file path of the wkhtmltopdf / wkhtmltoimage executable.
//     |
//     | Timout:
//     |    
//     |    The amount of time to wait (in seconds) before PDF / Image generation is stopped.
//     |    Setting this to false disables the timeout (unlimited processing time).
//     |
//     | Options:
//     |
//     |    The wkhtmltopdf command options. These are passed directly to wkhtmltopdf.
//     |    See https://wkhtmltopdf.org/usage/wkhtmltopdf.txt for all options.
//     |
//     | Env:
//     |
//     |    The environment variables to set while running the wkhtmltopdf process.
//     |
//     */
    
//     'pdf' => [
//         'enabled' => true,
//         'binary' => env('BIN_SNAPPY_PDF', 'xvfb-run -a wkhtmltopdf  --enable-local-file-access'),
//         'timeout' => 500,
//         'options' => ['load-error-handling'=>'ignore','load-media-error-handling'=>'ignore','enable-local-file-access'=>true],
//         'env'     => [],
//     ],
    
//     'image' => [
//         'enabled' => true,
//         'binary' => env('BIN_SNAPPY_IMG', 'xvfb-run -a wkhtmltoimage'),
//         'timeout' => 500,
//         'options' => [],
//         'env'     => [],
//     ],

// ];

if(PHP_OS == 'WINNT') {
    return [
        'pdf' => [
            'enabled' => true,
            'binary' => env('BIN_SNAPPY_PDF', '"C:\Program Files\wkhtmltopdf\bin\wkhtmltopdf"'),
            'timeout' => 1000,
            'is-remote-enabled' => true,
            'keep-relative-links' => true,
            'enable-javascript' => true,
            'window-status' => 'ready',
            'margin-left' => 20,
            'margin-right' => 20,
            'enable-internal-links' => true,
            'enable-local-file-access' => true,
            'options' => [
                'zoom' => 1,
                'load-error-handling' => 'ignore',
                'load-media-error-handling' => 'ignore',
                'enable-local-file-access' => true,
                'page-size' => 'A4'
            ],
            'env' => [],
        ],
        'image' => [
            'enabled' => true,
            'binary' => env('BIN_SNAPPY_IMG', 'xvfb-run -a wkhtmltoimage'),
            'timeout' => 500,
            'options' => [],
            'env' => [],
        ]
    ];
}
else
{
    return [
        'pdf' => [
            'enabled' => true,
            'binary' => env('BIN_SNAPPY_PDF', '"C:\Program Files\wkhtmltopdf\bin\wkhtmltopdf"'),
            'timeout' => 1000,
            'is-remote-enabled' => true,
            'keep-relative-links' => true,
            'enable-javascript' => true,
            'window-status' => 'ready',
            'margin-left' => 20,
            'margin-right' => 20,
            'enable-internal-links' => true,
            'enable-local-file-access' => true,
            'options' => [
                'load-error-handling' => 'ignore',
                'load-media-error-handling' => 'ignore',
                'enable-local-file-access' => true,
                //'page-size' => 'A4',
                'disable-smart-shrinking' => true,
                'zoom'  => 0.8
            ],
            'env' => [],
        ],
        'image' => [
            'enabled' => true,
            'binary' => env('BIN_SNAPPY_IMG', 'xvfb-run -a wkhtmltoimage'),
            'timeout' => 500,
            'options' => [],
            'env' => [],
        ]
    ];
}
