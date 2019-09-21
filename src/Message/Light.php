<?php

namespace samaphp\Message;

use samaphp\LifxLan;
use samaphp\DataWrapper;

Class Light {

  private $lifx_lan;
  private $light_ip;
  public $payload = [
    // Unsigned 8-bit integer.
    'reserved' => 0,
    // Unsigned 16-bit integer.
    'hue' => 0,
    // Unsigned 16-bit integer.
    'saturation' => 0,
    // Unsigned 16-bit integer.
    'brightness' => 0,
    // Unsigned 16-bit integer.
    'kelvin' => 0,
  ];

  public function __construct($light_ip = FALSE, LifxLan $lifx_lan = NULL)
  {
    $this->lifx_lan = $lifx_lan ?: new LifxLan();
    $this->light_ip = $light_ip;
    if (!filter_var($this->light_ip, FILTER_VALIDATE_IP)) {
      throw new \RuntimeException('Light IP is not valid.');
    }
  }

  public function colorNames() {
    return [
      'white' => [65535, 0, 65535, 4500],
      'white_cold' => [65535, 0, 65535, 8000],
      'white_cool' => [65535, 0, 65535, 5000],
      'white_warm' => [65535, 0, 65535, 3200],
      'white_hot' => [65535, 0, 65535, 2500],
      'red' => [65535, 65535, 65535, 9000],
      'purple' => [50000, 65535, 65535, 9000],
      'blue' => [40000, 65535, 65535, 9000],
      'cyan' => [30000, 65535, 65535, 9000],
      'yellow' => [10000, 65535, 65535, 9000],
      'green' => [15000, 65535, 65535, 9000],
      'orange' => [6500, 65535, 65535, 9000],
    ];
  }

  public function setColorByName($color_name) {
    $color_names = $this->colorNames();
    // If color name is not defined we will set Green color.
    if (!isset($color_names[$color_name])) {
      $color_name = 'green';
    }

    $color_codes = $color_names[$color_name];
    return $this->setColor($color_codes[0], $color_codes[1], $color_codes[2], $color_codes[3]);
  }

  public function setColor($hue, $saturation, $bright, $kelvin) {
    $data_wrapper = new DataWrapper();
    $data_wrapper->protocol_header['type'] = 102;
    $this->payload['hue'] = $hue;
    $this->payload['saturation'] = $saturation;
    $this->payload['brightness'] = $bright;
    $this->payload['kelvin'] = $kelvin;

    $frame_address = $data_wrapper->frameAddressData();
    $protocol_header = $data_wrapper->protocolHeaderData();
    $payload = $data_wrapper->wrap(
      'CvvvvV',
      0,
      $this->payload['hue'],
      $this->payload['saturation'],
      $this->payload['brightness'],
      $this->payload['kelvin'],
      100
    );

    $frame = $data_wrapper->frameData($frame_address, $protocol_header, $payload);

    $packet = $frame . $frame_address . $protocol_header . $payload;

    return $this->lifx_lan->connect($this->light_ip, $packet);
  }
}
