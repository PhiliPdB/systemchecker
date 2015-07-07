<?php

class OS_BR {

    private $agent = "";
    private $info = array();

    function __construct() {
        // Get user agent and detect operating system and browser
        $this->agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : NULL;
        $this->getOS();
        $this->getFormFactor();
        $this->getBrowser();
    }

    function getBrowser() { // Detect browser
        $browser = array("Iceweasel"            =>  "/Iceweasel.(\d+.\d+.\d+|\d+.\d+)/i",
                         "Opera Mini"           =>  "/Opera Mini.(\d+.\d+.\d+)/i",
                         "Opera"                =>  "/(OPR.(\d+.\d+.\d+.\d+|\d+.\d+)|(Opera (\d+.\d+)|Opera))/i",
                         "Firefox"              =>	"/Firefox.(\d+.\d+.\d+|\d+.\d+)/i",
                         "Firefox for IOS"      =>  "/Fxi.(\d+.\d+)/i",
                         "Internet Explorer"    =>	"/(MSIE (\d+)|rv:(\d+))/i",
                         "Edge"					=>	"/Edge.(\d+)/i",
                         "Steam Game Overlay"   =>  "/Valve Steam GameOverlay/i",
                         "Google Chrome"        =>	"/chrome.(\d+.\d+.\d+.\d+)/i",
                         "Nintendo Browser"     =>  "/NintendoBrowser.(\d+.\d+)/i",
                         "Safari"				=>	"/Safari/i"
                         ); // Only support for this browsers

        foreach($browser as $key => $value) {
            if(preg_match($value, $this->agent)) {
                switch ($key) {
                    case 'Safari': // First look if it is a webkit based browser
                        if (preg_match("/Tizen/i", $this->info['Operating System'])) {
                            $this->info = array_merge($this->info,array("Browser" => "Tizen Browser"));
                            $this->info = array_merge($this->info,array("Version" => ""));
                        } elseif (preg_match("/Android/i", $this->info['Operating System'])) {
                            $this->info = array_merge($this->info,array("Browser" => "Android Browser"));
                            $this->info = array_merge($this->info,array("Version" => ""));
                        } elseif (preg_match("/Blackberry/i", $this->info['Operating System'])) {
                            $this->info = array_merge($this->info,array("Browser" => "Blackberry Browser"));
                            $this->info = array_merge($this->info,array("Version" => ""));
                        } elseif (preg_match("/SymbianOS/i", $this->info['Operating System'])) {
                            $this->info = array_merge($this->info,array("Browser" => "Nokia Web Browser"));
                            $this->info = array_merge($this->info,array("Version" => ""));
                        } else {
                            $this->info = array_merge($this->info,array("Browser" => $key));
                            $this->info = array_merge($this->info,array("Version" => $this->getVersion($key, $value, $this->agent)));
                        }
                        break;
                    
                    case 'Firefox': // Check if is FirefoxOS
                        $this->info = array_merge($this->info,array("Browser" => $key));
                        $this->info = array_merge($this->info,array("Version" => $this->getVersion($key, $value, $this->agent)));
                        if ($this->info['Operating System'] == "Unknown" && $this->info['Form Factor'] == "Mobile")
                            $this->info['Operating System'] = "FirefoxOS";
                        break;

                    default:
                        $this->info = array_merge($this->info,array("Browser" => $key));
                        $this->info = array_merge($this->info,array("Version" => $this->getVersion($key, $value, $this->agent)));
                        break;
                }
                break;
            } else {
                if ($this->info['Operating System'] == "Ubuntu Touch") {
                    $this->info = array_merge($this->info,array("Browser" => "Ubuntu Web Browser"));
                    $this->info = array_merge($this->info,array("Version" => ""));
                } else {
                    $this->info = array_merge($this->info,array("Browser" => "Unknown"));
                    $this->info = array_merge($this->info,array("Version" => ""));
                }
            }
        }
        return $this->info['Browser'];
    }

    function getOS() { // Get operating system
        $OS = array("Tizen"             =>  "/Tizen (\d+.\d+)/i",
                    "Android"           =>  "/(Android.(\d+.\d+.\d+|\d+.\d+)|Android)/i",
                    
                    "Windows"           =>  "/Windows/i",

                    "Ubuntu"            =>  "/Ubuntu/i",
                    "Linux Mint"        =>  "/Linux Mint/i",
                    "OpenSUSE"          =>  "/OpenSUSE/i",
                    "Fedora"            =>  "/Fedora/i",
                    "Debian"            =>  "/Debian/i",
                    "Linux"             =>  "/Linux/i",
                    "Unix"              =>  "/Unix/i",
                    
                    "Chrome OS"         =>	"/CrOS/i",
                    
                    "Mac OS X"          =>  "/Mac OS X (\d+.\d+.\d+|\d+.\d+)/i",
                    "IOS"			    =>	"/(CPU OS (\d+.\d+.\d+|\d+.\d+)|CPU iPhone OS (\d+.\d+.\d+|\d+.\d+))/i",
                    
                    "Blackberry OS 10"	=>	"/BB10/i",
                    "Blackberry OS"	    =>	"/Blackberry/i",
                    
                    "SymbianOS"         =>  "/SymbianOS.(\d+.\d+)/i",

                    "Playstation"       =>  "/Playstation \d..?(\d+.\d+)/i"
                    ); // Only support for these operating systems

        foreach($OS as $key => $value) {
            if (preg_match($value, $this->agent)) {
            	switch ($key) {
                    // For some operating systems you can also get the version numbers
                    case 'Android':
                        preg_match_all($value, $this->agent, $matches);
                        $version = $matches[2][0] ? $matches[2][0] : "";
                        $this->info = array_merge($this->info, array("Operating System" => $key . " " . $version));
                        break;

            		case 'Windows':
            			$windows_versions = array("Windows 8.1"		=>	"/Windows NT 6.3/i",
            									  "Windows 8"		=>	"/Windows NT 6.2/i",
            									  "Windows 7"		=>	"/Windows NT 6.1/i",
            									  "Windows Vista"	=>	"/Windows NT 6.0/i",
            									  "Windows XP"		=>	"/Windows NT 5.[12]/i",
            									  "Windows 2000"	=>	"/Windows NT 5.0/i",
            									  "Windows"			=>	"/Windows NT (\d+.\d+)/i",
            									  "Windows Phone"	=>	"/Windows Phone (\d+.\d+)/i"
            									  );

            			foreach ($windows_versions as $windows => $reg) {
            				if (preg_match($reg, $this->agent)) {
            					switch ($windows) {
            						case 'Windows':
            						case 'Windows Phone':
            							preg_match_all($reg, $this->agent, $matches);
            							$version = $matches[1][0];

            							$this->info = array_merge($this->info, array("Operating System" => $windows . " " . $version));
            							break;
            						
            						default:
            							$this->info = array_merge($this->info, array("Operating System" => $windows));
            							break;
            					}
                                break;
            				}
            			}
            			break;
            		
            		case 'Ubuntu':
                        if (preg_match("/Mobile|Tablet/i", $this->agent)) $this->info = array_merge($this->info, array("Operating System" => "Ubuntu Touch"));
                        else $this->info = array_merge($this->info, array("Operating System" => $key));
                        break;

            		case 'Mac OS X':
            			preg_match_all($value, $this->agent, $matches);
            			$version = str_replace("_", ".", $matches[1][0]);
            			$this->info = array_merge($this->info, array("Operating System" => $key . " " . $version));
            			break;

            		case 'IOS':
            			preg_match_all($value, $this->agent, $matches);
            			$version = $matches[2][0] ? str_replace("_", ".", $matches[2][0]) : str_replace("_", ".", $matches[3][0]);
            			$this->info = array_merge($this->info, array("Operating System" => $key . " " . $version));
            			break;

            		case 'Blackberry':
            			preg_match_all("/value.(\d+.\d+)/i", $this->agent, $matches);
            			$version = $matches[1][0];
            			$this->info = array_merge($this->info, array("Operating System" => $key . " " . $version));
            			break;

                    case 'Playstation':
                        preg_match_all($value, $this->agent, $matches);
                        $version = $matches[2][0];
                        $this->info = array_merge($this->info, array("Operating System" => $key . " " . $matches[1][0] . " OS " . $version));
                        break;

            		default:
            			preg_match_all($value, $this->agent, $matches);
                        $version = str_replace("_", ".", $matches[1][0]);
                        $this->info = array_merge($this->info, array("Operating System" => $key . " " . $version));
            			break;
            	}
                break;
            } else $this->info = array_merge($this->info,array("Operating System" => "Unknown"));
        }
        return $this->info['Operating System'];
    }

    function getVersion($browser, $search, $string) { // Get the browser version
        $browser = $this->info['Browser'];
        $version = "";
        $browser = strtolower($browser);
        preg_match_all($search,$string,$match);
        switch($browser) {
            case "opera": 
                if ($match[2][0]) $version = $match[2][0];
                elseif ($match[4][0]) $version = $match[4][0];
                else {
                    preg_match_all("/version.(\d+.\d+)/i", $this->agent, $matches);
                    $version = $matches[1][0];
                }
                break;

            case "safari":
            	preg_match_all("/version.(\d+.\d+.\d+|\d+.\d+)/i", $this->agent, $matches);
            	$version = $matches[1][0];
            	break;

            case "opera":
                if ($match[2][0]) $version = $match[2][0];
                else {
                    preg_match_all("/version.(\d+.\d+)/i", $this->agent, $matches);
                    $version = $matches[1][0];
                }
                break;

            case 'internet explorer': $version = $match[2][0] ? $match[2][0] : $match[3][0];
            break;

            default: $version = $match[1][0];
            break;
        }
        return $version;
    }

    function getFormFactor() {
        $formFactor = array("Mobile"            =>  "/Mobi/i",
                            "Tablet"            =>  "/Tablet/i",
                            "Desktop"           =>  "/Desktop/i",
                            "TV"                =>  "/TV/i",
                            "Game console"      =>  "/Xbox|Playstation|Wii/i",
                            "Portable console"  =>  "/3DS|DSi|Playstation (Vita|Portable)/i"
                            );

        foreach ($formFactor as $key => $value) {
            if (preg_match($value, $this->agent)) {
                $this->info = array_merge($this->info, array("Form Factor" => $key));
                break;
            } else {
                if (preg_match("/Android/i", $this->info['Operating System'])) $this->info = array_merge($this->info, array("Form Factor" => "Tablet"));
                elseif (preg_match("/IOS/i", $this->info['Operating System'])) {
                    if (preg_match("/iPhone/i", $this->agent)) $this->info = array_merge($this->info, array("Form Factor" => "Mobile"));
                    else $this->info = array_merge($this->info, array("Form Factor" => "Tablet"));
                }
                else $this->info = array_merge($this->info, array("Form Factor" => "Unknown"));
            }
        }
    }

    function showInfo($switch) { // Show the info
        $switch = strtolower($switch);
        switch($switch) {
            case "browser": return $this->info['Browser'];
            break;

            case "os": return $this->info['Operating System'];
            break;

            case "version": return $this->info['Version'];
            break;

            case "form": return $this->info['Form Factor'];
            break;

            case "all" : return array($this->info["Version"], 
                $this->info['Operating System'], $this->info['Browser']);
                break;

            default: return "Unknown";
            break;

        }
    }
}

 ?>
