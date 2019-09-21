<?php

namespace Samaphp\LifxLan;

class DataWrapper {

  public $frame = [
    'size' => 0, //uint16_t
    // Protocol number: must be 1024. (uint16_t).
    'protocol' => 1024,
    // Message includes a target address: must be one (1). (bool).
    'addressable' => 0,
    // Determines usage of the Frame Address target field. (bool).
    'tagged' => 0,
    // Message origin indicator: must be zero (0). (uint8_t).
    'origin' => 0,
    // Source identifier: unique value set by the client, used by responses.
    // (uint32_t).
    'source' => 0,
  ];

  public $frame_address = [
    // 6 byte device address (MAC address) or zero (0) means all devices.
    // The last two bytes should be 0 bytes. (uint8_t[8]).
    'target' => 0,
    // Must all be zero (0). (uint8_t[6]).
    'reserved' => [0,0,0,0,0,0],
    // Response message required. (bool).
    'res_required' => 0,
    // Acknowledgement message required. (bool).
    'ack_required' => 0,
    // Reserved. ().
    'reserved2' => "\x00\x00\x00\x00\x00\x00",
    // Wrap around message sequence number. (uint8_t).
    'sequence' => 0,
  ];

  public $protocol_header = [
    // Reserved. (uint64_t).
    'reserved' => 0,
    // Message type determines the payload being used. (uint64_t).
    'type' => 0,
    // Reserved.
    'reserved2' => 0,
  ];

  public function __construct()
  {

  }

  public function frameData($frame_address, $protocol_header, $payload)
  {
    $otap = 0b00000000000000;
    // Tagged.
    $otap = $otap ^  0b10000000000000;
    // Addressable.
    $otap = $otap ^  0b01000000000000;

    $pnum = $this->frame['protocol'];
    $otap = $otap ^ $pnum;

    $message_size = 8 +  strlen($frame_address) + strlen($protocol_header) + strlen($payload);
    $message_size -= 12;

    // Source identifier: unique value set by the client, used by responses.
    $source_identifier = mt_rand(655455, 999999);
    return $this->wrap(
      'vvV',
      $message_size,
      $otap,
      $source_identifier
    );
  }

  public function frameAddressData()
  {
    $bits =  0b00000000;

    if ($this->frame_address['res_required']) $bits = $bits ^ 0b00000010;
    if ($this->frame_address['ack_required']) $bits = $bits ^ 0b00000001;

    return $this->wrap("CCCCCCCCCCCCCCCC",
      0,0,0,0,0,0,0,0,
      0,0,0,0,0,0,
      $bits,
      0
    );
  }

  public function protocolHeaderData()
  {
    return $this->wrap("Pvv",
      $this->protocol_header['reserved'],
      $this->protocol_header['type'],
      $this->protocol_header['reserved2']
    );
  }

  public function wrap()
  {
    $data = func_get_args();
    // return array_walk($data, 'pack');
    return call_user_func_array('pack', $data);
  }
}
