<?php return array(
    'root' => array(
        'name' => 'wpmpw/plugin',
        'pretty_version' => 'dev-main',
        'version' => 'dev-main',
        'reference' => 'd7fc196b42c7918802a73247f555aed74e9e73b3',
        'type' => 'wordpress-plugin',
        'install_path' => __DIR__ . '/../../',
        'aliases' => array(),
        'dev' => true,
    ),
    'versions' => array(
        'psr/http-message' => array(
            'pretty_version' => '1.1',
            'version' => '1.1.0.0',
            'reference' => 'cb6ce4845ce34a8ad9e68117c10ee90a29919eba',
            'type' => 'library',
            'install_path' => __DIR__ . '/../psr/http-message',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
        'roave/security-advisories' => array(
            'pretty_version' => 'dev-latest',
            'version' => 'dev-latest',
            'reference' => '70eb886a27427421cf1bd612067810c9fb1cbb5c',
            'type' => 'metapackage',
            'install_path' => NULL,
            'aliases' => array(
                0 => '9999999-dev',
            ),
            'dev_requirement' => true,
        ),
        'wpmpw/plugin' => array(
            'pretty_version' => 'dev-main',
            'version' => 'dev-main',
            'reference' => 'd7fc196b42c7918802a73247f555aed74e9e73b3',
            'type' => 'wordpress-plugin',
            'install_path' => __DIR__ . '/../../',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
    ),
);
