<?php

date_default_timezone_set('America/Los_Angeles');

$scheme = (($_SERVER['HTTPS'] ?? 'off') !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost:8080';

require __DIR__.'/autoload.php';

$filename = getenv('SQLITE_DATABASE_FILE') ?: '/db/nymph.db';

// Nymph's configuration.
$nymphConfig = [
  'driver' => 'SQLite3',
  'SQLite3' => [
    'filename' => $filename
  ]
];

if (!file_exists($filename)) {
  // Create the DB.
  \Nymph\Nymph::configure($nymphConfig);
  \Nymph\Nymph::disconnect();
}

if (!getenv('PUBSUB_HOST')) {
  $nymphConfig['SQLite3']['open_flags'] = \SQLITE3_OPEN_READONLY;
}

\Nymph\Nymph::configure($nymphConfig);

// Nymph PubSub's configuration.
\Nymph\PubSub\Server::configure(
  [
    'port' => ((int) getenv('PUBSUB_PORT')) ?? 8080,
    'entries' => [
      (getenv('PUBSUB_SCHEME') ?: 'ws').'://'.getenv('PUBSUB_HOST').'/'
    ]
  ]
);

// uMailPHP's configuration.
\uMailPHP\Mail::configure(
  [
    'site_name' => 'Nymph App Template',
    'site_link' => $scheme.'://'.$host.'/',
    'master_address' => 'noreply@example.com',
    'testing_mode' => true,
    'testing_email' => 'root@localhost', // TODO(hperrin): what should this be?
  ]
);


// Tilmeld's configuration.
\Tilmeld\Tilmeld::configure(
  [
    'app_url' => $scheme.'://'.$host.'/',
    'setup_url' => $scheme.'://'.$host.'/user/',
    'email_usernames' => true,
    'verify_redirect' => $scheme.'://'.$host.'/',
    'jwt_secret' => base64_decode(
      getenv('TILMELD_SECRET')
        ?: trim(file_get_contents(getenv('TILMELD_SECRET_FILE')))
    ),
    'jwt_expire' => 60 * 60 * 24 * 182, // About 6 months
    'jwt_renew' => 60 * 60 * 24 * 60 // About 2 months
  ]
);
