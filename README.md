# lifxlan
LifxLan protocol. (Work in progress)

Note: This library is not ready for use in production. And does not cover all API capabilities. I just built it for my self to 

**Setting light color by name:**
```php
$light_ip = '192.168.1.x';
$Light = new Light($light_ip);
$brightness = 40;
$Light->setColorByName('white_warm', $brightness);

// Set light power on.
$Light->setPowerStatus('on');

// Set light power off.
$Light->setPowerStatus('off');
```
