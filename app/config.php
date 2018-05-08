<?php

// You can set this to your own time zone.
date_default_timezone_set('America/Los_Angeles');

/*
 * You don't need to edit below here. It is set up to work inside your Docker
 * container.
 */

require __DIR__.'/autoload.php';

$filename = '/db/nymph.db';

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
    ['entries' => ['ws://'.getenv('PUBSUB_HOST').'/']]
);

// uMailPHP's configuration.
\uMailPHP\Mail::configure([
  'site_name' => 'Nymph App Template',
  'site_link' => 'http://localhost:8080/',
  'master_address' => 'noreply@example.com',
  'testing_mode' => true,
  'testing_email' => 'hperrin@localhost', // TODO(hperrin): what should this be?
]);


// Tilmeld's configuration.
\Tilmeld\Tilmeld::configure([
  'app_url' => 'http://localhost:8080/',
  'setup_url' => 'http://localhost:8080/user/',
  'email_usernames' => true,
  'verify_redirect' => 'http://localhost:8080/',
  'jwt_secret' => base64_decode(
      file_get_contents(getenv('TILMELD_SECRET_FILE'))
  )
]);
