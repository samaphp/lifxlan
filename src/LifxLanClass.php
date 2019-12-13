<?php

namespace Samaphp\LifxLan;

use Samaphp\LifxLan\Connection\Socket;

Class LifxLanClass {

  /**
   * @var \Samaphp\LifxLan\Connection\ConnectionInterface
   */
  protected $driver;
  private $timeout = 3;

  public function __construct($driver = NULL)
  {
    $this->driver = $driver ?: new Socket();
  }

  public function setTimeout($timeout) {
    $this->timeout = $timeout;
    return $this;
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
    return $driver->setTimeout($this->timeout)->connect($targeted_ip, $data);
  }

  public function getDriver() {
    return $this->driver;
  }

}
