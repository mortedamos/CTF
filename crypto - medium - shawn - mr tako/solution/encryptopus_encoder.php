<?php

// RUN FROM COMMAND LINE
// php encryptopus_encoder.php "text to encode goes here"

error_reporting( E_ALL );

function encryptopus_encode($plain) {

	// given text, encode in encryptopus
	
	// variable declare
		$debug = 0;	
		$separator = "\n"; // between each RGB set
		$keep_unsupported_characters = 0;
		$supported_characters = "ABCDEFGHIJKLMNOPQRSTUVWXYZ!?=+"; // + =
	// end variable declare
	
	if($debug) {
		echo("Calling encryptapus_encode with plain \"$plain\".\n");
	}
	
	// build translation arrays
		// arms
		$arm1 = array("N","C","J","W","9");
		$arm2 = array("Z","5","0","B","R");
		$arm3 = array("X","L","D","I","S");
		$arm4 = array("A","Q","F","2","Y");
		$arm5 = array("O","U","T","?","4");
		$arm6 = array("G","M","V","6","=");
		$arm7 = array("H","P","3","7","!");
		$arm8 = array("1","E","K","+","8");
		
		// main_keys
		$mk_arr = array("r","g","b");
		
		// red
		$r = array();
		$r[1] = $arm1;
		$r[2] = $arm2;
		$r[3] = $arm3;
		$r[4] = $arm4;
		$r[5] = $arm5;
		$r[6] = $arm6;
		$r[7] = $arm7;
		$r[8] = $arm8;	
		
		$mk_arr["r"] = $r;
		
		// green
		$g = array();
		$g[1] = $r[4];
		$g[2] = $r[6];
		$g[3] = $r[2];
		$g[4] = $r[1];
		$g[5] = $r[7];
		$g[6] = $r[3];
		$g[7] = $r[8];
		$g[8] = $r[5];
		
		$mk_arr["g"] = $g;
		
		// blue
		$b = array();
		$b[1] = $r[3];
		$b[2] = $r[8];
		$b[3] = $r[5];
		$b[4] = $r[4];
		$b[5] = $r[6];
		$b[6] = $r[2];
		$b[7] = $r[1];
		$b[8] = $r[7];	
	
		$mk_arr["b"] = $b;
	
	// END build translation arrays	
	
	// plain text adjustments
		$plain = strtoupper( trim($plain) );

		// replacements
			// replace space with +
			$plain = str_replace(" ","+", $plain);
		
			// replace . with =
			$plain = str_replace(".","=", $plain);
		// end replacements
		
		// $supported_characters = "ABCDEFGHIJKLMNOPQRSTUVWXYZ!?=+";
		$pattern = '/([^A-Z0-9\?\!\+\?=])/i';
		$plain = preg_replace($pattern, "", $plain);
	
		echo("Adjusted Plaintext is \"$plain\".\n");
	
	// end plain text adjustments
	
	$plain_arr = str_split($plain);	
	$ciphertext = "";
	
	foreach($plain_arr as $char_pos => $char) {
		
		
		if($debug) {
			echo("Encoding char \"$char\".\n");
		}
		$encoded = 0;
	
		// select which main key to use (rgb)
		// cycle through r, g, and b to make it look like some sort of graphical color value set
			if( (!isset($key_pick)) || ($key_pick >= 2) ) {
				$key_pick = 0;
			} else {
				$key_pick = $key_pick + 1;
			}
			
			switch($key_pick) {
				case "0":
					$use_mk = "r";
				break;
				case "1":
					$use_mk = "g";
				break;
				case "2":
					$use_mk = "b";
				break;
				default:
					echo("ERROR: unknown key_pick value \"$key_pick\".\n");
					$use_mk = "r";
				break;
			}
		
			if($debug) {
				echo("Encrypting using MK \"$use_mk\".\n");
			}

		$armkey_arr = $mk_arr[$use_mk];
	
		// encode using selected MK
		foreach($armkey_arr as $arm_num => $arm_arr) {
			if($debug) {
				echo("Looking for $char in key \"$use_mk\" arm \"$arm_num\"...\n");
				print_r($arm_arr);
				echo("\n");
			}
			$pos = array_search($char, $arm_arr);
			if($pos !== FALSE) {
				$cipher_char = $arm_arr[$pos];
				if($debug) {
					echo("Found char \"$char\" matched to key \"$use_mk\" arm \"$arm_num\" at position \"$pos\", being cipher char \"$cipher_char\"\n");
				}
				
				$ciphered_char = strtoupper($use_mk) . ($arm_num) . ($pos+1);
				$ciphertext .= $ciphered_char;
				$encoded = 1;
				
			} else {
				if($debug) {
					echo("Character \"$char\" was not found on \"$use_mk\" arm \"$arm_num\".\n");
				}
			}
		}
	
		if($keep_unsupported_characters) {
			if($encoded < 1) {
				$ciphertext .= $char;
			}
		}
		
		if($use_mk == "b") {
			$ciphertext .= $separator;
		}
	}

	// pad out to make an even number of RGB values
	if(strlen($ciphertext) > 0) {
		if($use_mk == "r") {
			$ciphertext .= "G00" . "B00";
		}
		if($use_mk == "g") {
			$ciphertext .= "B00";
		}
	}
	
	if( substr($ciphertext, -1) == $separator ) {
		$ciphertext = substr($ciphertext, 0, -1);
	}

	
	return $ciphertext;

}



if(isset($argv[1])) {
	$arg = $argv[1];
	
	echo("GIVEN: $arg.\n");
	
	$encoded = encryptopus_encode($arg);
	
	echo($encoded);
	
} else {
	echo("No command line argument given.");
}
	


?>