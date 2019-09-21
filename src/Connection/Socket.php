<?php

namespace samaphp\Connection;

class Socket implements ConnectionInterface {

  public function connect($targeted_ip = FALSE, $data = NULL, $targeted_port = 56700) {
    if (filter_var($targeted_ip, FILTER_VALIDATE_IP)) {
      $host = sprintf('udp://%s', $targeted_ip);
      $socket = fsockopen($host, $targeted_port, $errno, $errstr);
      fwrite($socket, $data);
      stream_set_timeout($socket, 2, 0);
      $data = fgets($socket);
      fclose($socket);
      return $data;
    }
    else {
      throw new \RuntimeException('Light IP is not valid.');
    }
  }

}
