<?php

namespace App;

use GuzzleHttp\Client;

$client = new Client(['base_uri' => 'http://www.rcksld.com/']);

$response = $client->get('GetNowShowing.json');

