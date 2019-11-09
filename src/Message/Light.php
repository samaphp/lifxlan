<?php

namespace Samaphp\LifxLan\Message;

use Samaphp\LifxLan\LifxLanClass;
use Samaphp\LifxLan\DataWrapper;

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

  public function __construct($light_ip = FALSE, LifxLanClass $lifx_lan = NULL)
  {
    $this->lifx_lan = $lifx_lan ?: new LifxLanClass();
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

  /**
   * @param $color_name
   * @param bool $brightness Scale 0 to 100.
   * @param bool $force_power_effect If brightness 0 = off, brightness > 0 = on.
   *
   * @return string
   */
  public function setColorByName($color_name, $brightness = FALSE, $force_power_effect = TRUE) {
    $color_names = $this->colorNames();
    // If color name is not defined we will set Green color.
    if (!isset($color_names[$color_name])) {
      $color_name = 'green';
    }

    $color_codes = $color_names[$color_name];
    if (($brightness !== FALSE) && is_numeric($brightness) && ($brightness <= 100)) {
      // Getting the brightness value to send it to Lifx. (max = 65535).
      $color_codes[2] = ($brightness / 100) * 65535;
    }

    return $this->setColor($color_codes[0], $color_codes[1], $color_codes[2], $color_codes[3], $force_power_effect);
  }

  public function setColor($hue, $saturation, $brightness, $kelvin, $force_power_effect = FALSE) {
    $data_wrapper = new DataWrapper();
    $data_wrapper->protocol_header['type'] = 102;
    $this->payload['hue'] = $hue;
    $this->payload['saturation'] = $saturation;
    $this->payload['brightness'] = $brightness;
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

    $res = $this->lifx_lan->connect($this->light_ip, $packet);

    // We will auto switch on the light.
    if ($force_power_effect) {
      $this->setPowerStatus($brightness);
    }

    return $res;
  }

  /**
   * To power on or off the light.
   *
   * @param string $status
   *   This will be (on) or (off).
   *
   * @return mixed
   */
  public function setPowerStatus($status = 'on') {
    if ($status != 'off') {
      // Anything not off will be considered as on.
      $status = 'on';
      $status_value = 1;
    }
    else {
      // This is off.
      $status_value = 0;
    }

    $data_wrapper = new DataWrapper();
    // As per document the SetPower value is 117.
    // Source: https://lan.developer.lifx.com/v2.0/docs/light-messages#section-setpower-117
    $data_wrapper->protocol_header['type'] = 117;
    $this->payload['level'] = (($status_value * 100) / 100) * 65535;
    $this->payload['duration'] = 3;

    // Require a response.
    // $data_wrapper->frame_address['res_required'] = 1; //.
    $frame_address = $data_wrapper->frameAddressData();

    $protocol_header = $data_wrapper->protocolHeaderData();
    $payload = $data_wrapper->wrap(
      'vV',
      $this->payload['level'],
      $this->payload['duration']
    );

    $frame = $data_wrapper->frameData($frame_address, $protocol_header, $payload);

    $packet = $frame . $frame_address . $protocol_header . $payload;

    $res = $this->lifx_lan->connect($this->light_ip, $packet);
    // $parsed_result = $data_wrapper->unwrap('vlevel', $res); //.
    return $res;
  }

}
