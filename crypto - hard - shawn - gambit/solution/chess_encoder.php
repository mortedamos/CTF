<?php


	if(isset($_GET["arg"])) {	
		$arg = $_GET["arg"];
	}



	function chess_encode($t) {

		// given a string $t, 
		// base64 encode it
		// then encode each character of the base64 encoded value with descriptive notation
		//		https://en.wikipedia.org/wiki/Descriptive_notation
		// and output it as a record of a chess game
		// as of 2017.05.04, this game will not make any sense

		/* 
		base64 index table mapped to an 8x8 (chessboard) grid:
			A	B	C	D	E	F	G	H
			I	J	K	L	M	N	O	P
			Q	R	S	T	U	V	W	X
			Y	Z	a	b	c	d	e	f
			g	h	i	j	k	l	m	n
			o	p	q	r	s	t	u	v
			w	x	y	z	0	1	2	3
			4	5	6	7	8	9	+	/


			note the lack of the padding character - ==. We will just keep these as they are.
			
		descriptive notation from white's POV (white makes the first move)

			QR8	QN8	QB8	Q8	K8	KB8	KN8	KR8
			QR7	QN7	QB7	Q7	K7	KB7	KN7	KR7
			QR6	QN6	QB6	Q6	K6	KB6	KN6	KR6
			QR5	QN5	QB5	Q5	K5	KB5	KN5	KR5
			QR4	QN4	QB4	Q4	K4	KB4	KN4	KR4
			QR3	QN3	QB3	Q3	K3	KB3	KN3	KR3
			QR2	QN2	QB2	Q2	K2	KB2	KN2	KR2
			QR1	QN1	QB1	Q1	K1	KB1	KN1	KR1
		
		descriptive notation from black's POV
		
			QR1	QN1	QB1	Q1	K1	KB1	KN1	KR1
			QR2	QN2	QB2	Q2	K2	KB2	KN2	KR2
			QR3	QN3	QB3	Q3	K3	KB3	KN3	KR3
			QR4	QN4	QB4	Q4	K4	KB4	KN4	KR4
			QR5	QN5	QB5	Q5	K5	KB5	KN5	KR5
			QR6	QN6	QB6	Q6	K6	KB6	KN6	KR6
			QR7	QN7	QB7	Q7	K7	KB7	KN7	KR7
			QR8	QN8	QB8	Q8	K8	KB8	KN8	KR8
		*/
		
		$debug = 0;
		$lr = "<br />\n";
		$noise = 1;
		
		if($debug) {
			echo("Given $t $lr");
		}
		
		$t_b64 = base64_encode($t);

		if($debug) {
			echo("Base64 encoded: $t_b64 $lr");
		}
		
		$b64_index_arr = array();
		$b64_index_arr[] = array("A","B","C","D","E","F","G","H");
		$b64_index_arr[] = array("I","J","K","L","M","N","O","P");
		$b64_index_arr[] = array("Q","R","S","T","U","V","W","X");
		$b64_index_arr[] = array("Y","Z","a","b","c","d","e","f");
		$b64_index_arr[] = array("g","h","i","j","k","l","m","n");
		$b64_index_arr[] = array("o","p","q","r","s","t","u","v");
		$b64_index_arr[] = array("w","x","y","z","0","1","2","3");
		$b64_index_arr[] = array("4","5","6","7","8","9","+","/");

		$wdn_index_arr = array();
		$wdn_index_arr[] = array("QR8","QN8","QB8","Q8","K8","KB8","KN8","KR8");
		$wdn_index_arr[] = array("QR7","QN7","QB7","Q7","K7","KB7","KN7","KR7");
		$wdn_index_arr[] = array("QR6","QN6","QB6","Q6","K6","KB6","KN6","KR6");
		$wdn_index_arr[] = array("QR5","QN5","QB5","Q5","K5","KB5","KN5","KR5");
		$wdn_index_arr[] = array("QR4","QN4","QB4","Q4","K4","KB4","KN4","KR4");
		$wdn_index_arr[] = array("QR3","QN3","QB3","Q3","K3","KB3","KN3","KR3");
		$wdn_index_arr[] = array("QR2","QN2","QB2","Q2","K2","KB2","KN2","KR2");
		$wdn_index_arr[] = array("QR1","QN1","QB1","Q1","K1","KB1","KN1","KR1");
		
		$bdn_index_arr = array();
		$bdn_index_arr[] = array("QR1","QN1","QB1","Q1","K1","KB1","KN1","KR1");
		$bdn_index_arr[] = array("QR2","QN2","QB2","Q2","K2","KB2","KN2","KR2");
		$bdn_index_arr[] = array("QR3","QN3","QB3","Q3","K3","KB3","KN3","KR3");
		$bdn_index_arr[] = array("QR4","QN4","QB4","Q4","K4","KB4","KN4","KR4");
		$bdn_index_arr[] = array("QR5","QN5","QB5","Q5","K5","KB5","KN5","KR5");
		$bdn_index_arr[] = array("QR6","QN6","QB6","Q6","K6","KB6","KN6","KR6");
		$bdn_index_arr[] = array("QR7","QN7","QB7","Q7","K7","KB7","KN7","KR7");
		$bdn_index_arr[] = array("QR8","QN8","QB8","Q8","K8","KB8","KN8","KR8");

		$piece_arr = array("K","Q","KR","QR","KB","QB","KN","QN","RP","RP","BP","BP","NP","NP","QP","KP");
		$pawns_arr = array("P","P","P","P","P","P","P","P");
		$piece_arr = array_merge($piece_arr, $pawns_arr); // because sometimes pawns arent referred to their specific position, just as P
		
		
		// go through each character of b64 encoded value and get board position
		$t_b64_arr = str_split($t_b64);
		$game_rounds = count($t_b64_arr);
		$game_turns = round($game_rounds/2,0);
		
		if($debug) {
			echo("Game turns: $game_turns $lr");
		}
		
		$padding = "";
		$turn = 0;
		$round = 0; // two turns, used to record a game
		$out = "";
		foreach($t_b64_arr as $position => $char) {
			
			if($char == "=") {
				$padding .= $char;
			} else {
				foreach($b64_index_arr as $row_pos => $row_arr) {
					$col_pos = array_search($char, $row_arr);
					if($col_pos !== FALSE) {
						// found it!
						break;
					}
				}

				if($debug) {
					echo("Found $char at row: $row_pos and col: $col_pos. <br />");
				}
				
				// for each board position get the corrosponding chess board notation
				if ($turn % 2 == 0) {
					// even turns - white
					if($debug) {
						echo("white turn... ");
					}	
					$chess_pos = $wdn_index_arr[$row_pos][$col_pos];
				} else {
					// odd turns - black
					if($debug) {
						echo("black turn... ");
					}
					$chess_pos = $bdn_index_arr[$row_pos][$col_pos];
				}
				
				if($debug) {
					echo("Chess notation is: $chess_pos $lr");
				}
			
				// assemble output
				
				// increment rounds
				if ($turn % 2 == 0) {
					$round++;
					$out .= "$round.";
				}

				// get random piece to move to this position and append
				$rand = rand(0,(count($piece_arr)-1));
				$piece = $piece_arr[$rand];
				$out .= " " . $piece . "-" . $chess_pos;
			
				// NOISE
				// sometimes pieces do things. add this in as noise
				if($noise) {
					
					// capture
						if($round > 3) {
							
							$capture_num = rand(0,$game_rounds); // note: 10 of 24 chance based on the Evergreen Game. About 40%.
							$capture_num = $capture_num + $round; // increase chance of captures as the game goes on								
							$cap_chance = ($capture_num / ($game_rounds)) * 100;
							if($debug) {
								echo("capture chance: $cap_chance $lr");
							}
							
							switch($cap_chance) {
								case ($cap_chance < 90):
									// no capture
								break;
								default:
									// capture
									$rand = rand(0,(count($piece_arr)-1));
									$cap_piece = $piece_arr[$rand];							
									$out .= " (" . $piece . "x" . $cap_piece . ")";
									
								break;
							}
						}
					// end capture
					
					// check
					if($round > (7+rand(0,5))) {
						
						$check_num = rand(0,$game_rounds);
						$check_num = $check_num + $round; // increase chance of check as the game goes on								
						$check_chance = ($check_num / ($game_rounds)) * 100;						
						
						if($debug) {
							echo("$round. chance to check is $check_chance... $lr");
						}
						
						switch($check_chance) {
							case ($check_chance < 120):
								// no capture
							break;
							default:
								// check
								$out .= " ch";
								
							break;
						}
					}	
				}
				// END NOISE
			
			
				if ($turn % 2 !== 0) {
					$out .= "$lr";
				}

				$turn++;
				
			}
			

			
		}

		// add padding back in as ?
		if($debug) {
			echo("padding: $padding $lr");
		}
		$num_pad = strlen($padding);
		if($num_pad > 0) {
			$out .= " ";
			for($i=0;$i<$num_pad;$i++) {
				$out .= "?";
			}
		}

		if($debug) {
			echo("$lr$lr$lr");
		}

		return $out;
		
	}

	$lr = "<br />\n";
	
	echo("Chess encoding \"$arg\"...$lr");
	echo("Base64 Encoded to \"" . base64_encode($arg) . "\"$lr");
	echo("Turning base64 into chess...$lr$lr");
	
	
	$encoded = chess_encode($arg);

		
	$chess_players_arr = array("Garry","Alexander","Magnus","Bobby","Anatoly","Vladimir","Mikhail","Dimitri","Vasily","Emanuel","Boris");
	$player_num = rand(0,(count($chess_players_arr)-1));
	$player_name = $chess_players_arr[$player_num];
	
	echo($player_name . ",$lr$lr");
	
	echo("I have encountered a difficult situation in my current match and turn to you and your expertise. What should my next move be?$lr$lr");
	echo("$encoded");
	echo("$lr");	
	
	
?>