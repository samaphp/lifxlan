<?php

namespace samaphp;

use samaphp\Connection\Socket;

Class LifxLan {

  /**
   * @var \samaphp\Connection\ConnectionInterface
   */
  protected $driver;

  public function __construct($driver = NULL)
  {
    $this->driver = $driver ?: new Socket();
  }

  public static function discover() {
    $lights = [
      [
        'label' => '',
        'ip' => '',
        'port' => '',
      ],
      [
        'label' => '',
        'ip' => '',
        'port' => '',
      ],
    ];

    return $lights;
  }

  public function connect($targeted_ip, $data) {
    $driver = $this->getDriver();
    return $driver->connect($targeted_ip, $data);
  }

  public function getDriver() {
    return $this->driver;
  }

}
