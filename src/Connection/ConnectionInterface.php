<?php

namespace Samaphp\LifxLan\Connection;

/**
 * Interface ConnectionInterface
 */
interface ConnectionInterface
{
  /**
   * Send the data.
   *
   * @param $data
   * @param $targeted_ip
   * @param $targeted_port
   *
   * @return void
   */
  public function connect($targeted_ip = FALSE, $data = NULL, $targeted_port = FALSE);
}
