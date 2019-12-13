<?php

namespace Samaphp\LifxLan\Connection;

class Socket implements ConnectionInterface {

  private $timeout = 3;

  /**
   * Setting the connection timeout.
   *
   * @param $timeout
   *
   * @return $this
   */
  public function setTimeout($timeout) {
    $this->timeout = $timeout;
    return $this;
  }

  /**
   * Broadcasting the message.
   *
   * @param bool $targeted_ip
   * @param null $data
   * @param int $targeted_port
   *
   * @return bool|string|void|null
   */
  public function connect($targeted_ip = FALSE, $data = NULL, $targeted_port = 56700) {
    if (filter_var($targeted_ip, FILTER_VALIDATE_IP)) {
      $host = sprintf('udp://%s', $targeted_ip);
      $socket = fsockopen($host, $targeted_port, $errno, $errstr);
      fwrite($socket, $data);
      stream_set_timeout($socket, $this->timeout, 1);
      $data = fgets($socket);
      fclose($socket);
      return $data;
    }
    else {
      throw new \RuntimeException('Light IP is not valid.');
    }
  }

}
