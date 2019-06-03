<?php

date_default_timezone_set('America/Los_Angeles');

$scheme = (($_SERVER['HTTPS'] ?? 'off') !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost:8080';

require __DIR__.'/autoload.php';

// Nymph's configuration.
$nymphConfig = [
  'driver' => 'PostgreSQL',
  'PostgreSQL' => [
    'host' => getenv('POSTGRES_HOST'),
    'port' => getenv('POSTGRES_PORT'),
    'database' => getenv('POSTGRES_DATABASE'),
    'user' => getenv('POSTGRES_USER'),
    'password' => getenv('POSTGRES_PASSWORD')
      ?: trim(file_get_contents(getenv('POSTGRES_PASSWORD_FILE')))
  ]
];

\Nymph\Nymph::configure($nymphConfig);

// Nymph PubSub's configuration.
\Nymph\PubSub\Server::configure([
  'port' => ((int) getenv('PUBSUB_PORT')) ?? 8080,
  'entries' => [
    (getenv('PUBSUB_SCHEME') ?: 'ws').'://'.getenv('PUBSUB_HOST').'/'
  ]
]);

// uMailPHP's configuration.
\uMailPHP\Mail::configure([
  'site_name' => 'Nymph App Template',
  'site_link' => $scheme.'://'.$host.'/',
  'master_address' => 'noreply@example.com',
  'testing_mode' => true,
  'testing_email' => 'root@localhost', // TODO(hperrin): what should this be?
]);


// Tilmeld's configuration.
\Tilmeld\Tilmeld::configure([
  'app_url' => $scheme.'://'.$host.'/',
  'setup_url' => $scheme.'://'.$host.'/user/',
  'email_usernames' => true,
  'verify_redirect' => $scheme.'://'.$host.'/',
  'jwt_secret' => base64_decode(
      getenv('TILMELD_SECRET')
        ?: trim(file_get_contents(getenv('TILMELD_SECRET_FILE')))
  ),
  'jwt_expire' => 60*60*24*182, // About 6 months
  'jwt_renew' => 60*60*24*60 // About 2 months
]);
