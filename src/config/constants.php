<?php

return [
    'google_map_api_key' => env('GOOGLE_MAP_API_KEY'),
    'lat_regex' => '/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/',
    'long_regex' => '/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/',
];
