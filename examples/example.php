<?php

// Don't miss to install needful libraries using Composer to generate
// autoload.php file.
require __DIR__ . '/../vendor/autoload.php';

use Samaphp\LifxLan\Message\Light;

// Example 1.
// Setting color by custom values.
// Insert your light IP.
$light_ip = '192.168.1.175';
$Light = new Light($light_ip);
// Setting a custom color.
// Max: 65535.
$hue = 25555;
// Max: 65535.
$saturation = 55555;
// Max: 65535.
$bright = 5000;
// Range 2500 to 9000.
$kelvin = 9000;
// $Light->setColor($hue, $saturation, $bright, $kelvin);

// Example 2.
// Setting the color by name.
// Insert your light IP.
$light_ip = '192.168.1.169';
$Light = new Light($light_ip);
// $res = $Light->setPowerStatus('on');
// $res = $Light->setPowerStatus('off');
$Light->setColorByName('white_warm', 40);

// List of pre-defined colors by this class to use with setColorByName().
print_r(array_keys($Light->colorNames()));
