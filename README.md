# System Checker

This PHP script can tell you wich browser and operating system you are using.
You can see this in action on my [website](http://philipdb.nl/systeminfo/).

Usage
-----
To use this, you first have to include 'system.php' and then you have to create a new class.
```php
include 'system.php'
  
$detected = new OS_BR()
```

Now you can get the information

```php
echo $detected->showInfo('browser'); // Shows the browser you are using
echo $detected->showInfo('version'); // Shows the version of the browser you are using
echo $detected->showInfo('os'); // Shows the os you are using
echo $detected->showInfo('all'); // Get an array with all information
```

Support
-------
Currently it can only detect the following browsers:
- Chrome
- Firefox
- Safari
- Opera
- Iceweasel
- Opera Mini
- Blackberry browser
- Android Webkit Browser (Stock Android Browser)
- Internet Explorer
- Edge

And this operating systems:
- Windows (2000+)
- Mac OS X
- Android
- Windows Phone
- IOS
- Blackberry OS
- Chrome OS
- Ubuntu
- Linux Mint
- Linux
- Unix
