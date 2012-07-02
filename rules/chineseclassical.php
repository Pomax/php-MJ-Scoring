<!--

	Chinese classical rules:

	starting points: 2000

	hand limit: 1000

	TILE POINTS:

		multipliers :

			own flower + season		: 1
			all flowers or seasons		: 1
			all flowers and seasons		: 3

		multiplier hands:

			little winds				: 1		three pung/kong and a pair of winds, any other pung/kong
			big winds				: 5		pung/kongs of all winds, any other pair


	WINNING HAND ONLY:

		winning					: 10
		self drawn					: 10
		one chance hand				: 2
		out on a pair				: 2

		multipliers :

			out on the last tile		: 1
			out on the last discard		: 1
			out on a dead wall tile		: 1
			out by robbing kong		: 1
			ready on first turn		: 1

		limit hands:

			heavenly hand			100%		east, going out on the dealt hand
			earthly hand			100%		not east, going out on east's first discard

			all kong				100%
			all honors				100%
			all terminal				100%

			kong on kong			100%		out on dead wall tile from a second kong that turn
			13 wins				100%		13 wins as dealer
			all green				100%		normal hand with only-green bamboo tiles and green dragons
			three scholars			100%		pungs of all three dragons, random pung and a pair
			nine gates				100%		same suit 111, 2345678, 999, using any one numeral to complete - concealed, final may be drawn
			nine gates impure		100%		same suit 111, 2345678, 999, any one numeral to complete, completed by forming the gate pattern instead
			squirming snake			100%		111, 234, 567, 88, 999 one suit - allowed to meld
			fully conceal suit			100%		all tiles from one suit, no honors
			thirteen orphans			100%		1,9 of each suit, n,e,s,w,c,f,p and any of these to complete
			hidden treasure			100%		fully concealed pung/kong hand - cannot be formed with a discard

			moon					100%		last tile/last discard, dots 1
			plum blossom			100%		out on dead wall tile, dots 5
			scratching pole			100%		out by robbing kong, bamboo 2

	Score settling:

		Everyone pays the winner the number of tilepoints the winner had - east pays double, or if the winner is east, receives double from everyone
		The losers pay each other the difference in their points - if east did not win, east pays and receives double


	add 9tile
	double for
-->

<?php

	// minor bit of general information
	$limit_score = 1000;
	$start_score = 2000;

	// check if this is a limit hand
	function render_limits()
	{
	?>
			<script language="javascript">
			<!--
				function endisablegroup(state) {
					var radiogroup = document.mjform["limithand"]
					for (var i=0; i<5; i++) {
						radiogroup[i].disabled = state
					}
				}

			-->
			</script>
			limits hands:
			<select name="limits" id="limithands">
				<option onclick="endisablegroup(true)" selected="selected"></option>
				<option onclick="endisablegroup(false)">heavenly hand (dealer goes out on first tile)</option>
				<option onclick="endisablegroup(false)">earthly hand (non-dealer goes out on dealer's first discard)</option>
				<option onclick="endisablegroup(false)">all kong (four kongs and a pair)</option>
				<option onclick="endisablegroup(false)">all terminal (normal hand composed of terminals)</option>
				<option onclick="endisablegroup(false)">all honors (normal hand, no suit tiles)</option>
				<option onclick="endisablegroup(false)">kong on kong (going out by forming a kong, then another kong and then drawing your mahjong tile)</option>
				<option onclick="endisablegroup(false)">13 orphans (1 and 9 of each suit, C, F, P, E, N, S, W and a tile that forms a pair with any of these)</option>
				<option onclick="endisablegroup(false)">13 dealer wins (draws count as wins for this limit)</option>
				<option onclick="endisablegroup(false)">all green (normal hand composed of only green-face bamboo tiles [2,3,4,6,8] and green dragons)</option>
				<option onclick="endisablegroup(false)">three scholars (pungs of each dragon)</option>
				<option onclick="endisablegroup(false)">nine gates (concealed same suit 111, 2-6, 999 and a tile to form a pair; pair tile may be a discard)</option>
				<option onclick="endisablegroup(false)">fully concealed suit (normal one suit no honor hand, fully concealed)</option>
				<option onclick="endisablegroup(false)">hidden treasure (concealed pung/kong hand, only self drawn out allowed)</option>
				<option onclick="endisablegroup(false)">moon from the bottom of the ocean (out on the last tile or discard, tile being dots 1)</option>
				<option onclick="endisablegroup(false)">plum blossom on the roof (out on a supplementary tile, tile being dots 5)</option>
				<option onclick="endisablegroup(false)">cat scratching a carrying pole (out by robbing the bamboo 2 kong)</option>
			</select>
	<?php
	}


	// calcuate the tilepoints for each player
	function calculate_tilepoints(&$roundinfo) {
		global $limit_score;
		$winner = $roundinfo["winner"];
		for($p=0;$p<4;$p++) {
			$player =& $roundinfo["players"][$p];
			$won = ($p == $winner);
			if ($won) {
				if ($roundinfo["limit"]["won"]) {
					$player["scoring"][] = "limit hand";
					$player["tilepoints"] = $limit_score;
				}
				 else {
					$player["tilepoints"] = 0;
					$player["tilepoints"] += calculate_basic_tilepoints($player, $roundinfo, $won);
					$player["tilepoints"] *= calculate_doubles($player, $roundinfo, $won); } }

			else{
				$player["tilepoints"] = 0;
				$player["tilepoints"] += calculate_basic_tilepoints($player, $roundinfo, $won);
				$player["tilepoints"] *= calculate_doubles($player, $roundinfo, $won); }

			// curb tilepoint score to limit
			if ($player["tilepoints"] >$limit_score) $player["tilepoints"] = $limit_score;

			$player["scoring"][] = "<b>total points: ".$player["tilepoints"]. "</b>";

			// set up a score entry for use in the settle_scores function
			$player["score"] = 0; }
	}

	// calculate basic tile scores
	function calculate_basic_tilepoints(&$player, &$roundinfo, $won) {
		$patterns =& $roundinfo["patterns"];
		$tilescores =& $roundinfo["tilescores"];
		$player["scoring"][] = "[basic tile points]";

		$tilepoints = 0;

		if ($won) {
			$setpoints = 10;
			$player["scoring"][] = "$setpoints: winning";
			$tilepoints += $setpoints; }

		for ($s=1;$s<6;$s++) {
			$concealed = $player["sets"][$s]["concealed"];
			$tiles = str_split($player["sets"][$s]["tiles"]);
			sort($tiles);
			$set = implode("",$tiles);

			if($set == "") { continue; }

			$setecho = "";
			$scs = array();
			// score the set - pung?
			if (in_array($set, $patterns["pung"]["all"])) {
				if (in_array($set, $patterns["pung"]["simple"])) {
					$setpoints = ($concealed)? $tilescores["pung"]["simple"]["concealed"] : $tilescores["pung"]["simple"]["melded"];
					$setecho .= "$setpoints: "; if ($concealed) { $setecho .= "concealed "; } $setecho .= "pung simples [$set]";
					$tilepoints += $setpoints;
				}
				elseif (in_array($set, $patterns["pung"]["terminal"])) {
					$setpoints = ($concealed)? $tilescores["pung"]["terminal"]["concealed"] : $tilescores["pung"]["terminal"]["melded"];
					$setecho .= "$setpoints: "; if ($concealed) { $setecho .= "concealed "; } $setecho .= "pung terminals [$set]";
					$tilepoints += $setpoints;
				}
				elseif (in_array($set, $patterns["pung"]["dragon"])) {
					$setpoints = ($concealed)? $tilescores["pung"]["dragon"]["concealed"] : $tilescores["pung"]["dragon"]["melded"];
					$setecho .= "$setpoints: "; if ($concealed) { $setecho .= "concealed "; } $setecho .= "pung dragons [$set]";
					$tilepoints += $setpoints;
				}
				elseif (in_array($set, $patterns["pung"]["wind"])) {
					$setpoints = ($concealed)? $tilescores["pung"]["wind"]["concealed"] : $tilescores["pung"]["wind"]["melded"];
					$setecho .= "$setpoints: "; if ($concealed) { $setecho .= "concealed "; } $setecho .= "pung winds [$set]";
					$tilepoints += $setpoints;
				}
			}

			// score the set - kong?
			elseif (in_array($set, $patterns["kong"]["all"])) {
				if (in_array($set, $patterns["kong"]["simple"])) {
					$setpoints = ($concealed)? $tilescores["kong"]["simple"]["concealed"] : $tilescores["kong"]["simple"]["melded"];
					$setecho .= "$setpoints: "; if ($concealed) { $setecho .= "concealed "; } $setecho .= "kong simples [$set]";
					$tilepoints += $setpoints;
				}
				elseif (in_array($set, $patterns["kong"]["terminal"])) {
					$setpoints = ($concealed)? $tilescores["kong"]["terminal"]["concealed"] : $tilescores["kong"]["terminal"]["melded"];
					$setecho .= "$setpoints: "; if ($concealed) { $setecho .= "concealed "; } $setecho .= "kong terminals [$set]";
					$tilepoints += $setpoints;
				}
				elseif (in_array($set, $patterns["kong"]["dragon"])) {
					$setpoints = ($concealed)? $tilescores["kong"]["dragon"]["concealed"] : $tilescores["kong"]["dragon"]["melded"];
					$setecho .= "$setpoints: "; if ($concealed) { $setecho .= "concealed "; } $setecho .= "kong dragons [$set]";
					$tilepoints += $setpoints;
				}
				elseif (in_array($set, $patterns["kong"]["wind"])) {
					$setpoints = ($concealed)? $tilescores["kong"]["wind"]["concealed"] : $tilescores["kong"]["wind"]["melded"];
					$setecho .= "$setpoints: "; if ($concealed) { $setecho .= "concealed "; } $setecho .= "kong winds [$set]";
					$tilepoints += $setpoints;
				}
			}

			// score the set - pair?
			elseif (in_array($set, $patterns["pair"]["all"])) {
				$scs = array();
				if (in_array($set, $patterns["pair"]["dragon"])) {
					$setpoints = $tilescores["pair"]["dragon"];
					$player["valuepair"] = true;
					$scs[] = "$setpoints: pair dragons [$set]";
					$tilepoints += $setpoints;
				}
				if ($set == str_replace(array("東", "南", "西", "北"), array("ee","ss","ww","nn"), $player["wind"])) {
					$setpoints = $tilescores["pair"]["wind"]["own"];
					$player["valuepair"] = true;
					$scs[] = "$setpoints: pair of own winds [$set]";
					$tilepoints += $setpoints;
				}
				if ($set == str_replace(array("東", "南", "西", "北"), array("ee","ss","ww","nn"), $roundinfo["wotr"])) {
					$setpoints = $tilescores["pair"]["wind"]["own"];
					$player["valuepair"] = true;
					$scs[] = "$setpoints: pair of wind of the round [$set]";
					$tilepoints += $setpoints;
				}
			}

			if ($setecho != "") $player["scoring"][] = $setecho . "";
			if (count($scs) > 0) $player["scoring"] = array_merge($player["scoring"],$scs);
		}

		// score flowers/season
		for($f=1;$f<5;$f++) {
			if($roundinfo["flowers"][$f] == $player["name"]) {
				$setpoints = $tilescores["flower"];
				$player["scoring"][] = "$setpoints: flower $f";
				$tilepoints += $setpoints;
			}
			if($roundinfo["seasons"][$f] == $player["name"]) {
				$setpoints = $tilescores["season"];
				$player["scoring"][] = "$setpoints: season $f";
				$tilepoints += $setpoints;
			}
		}

		// special winning conditions
		if ($won) {
			if ($roundinfo["conditions"]["selfdrawn"]) {
				$setpoints = 2;
				$player["scoring"][] = "$setpoints: self drawn out";
				$tilepoints += $setpoints;
			}
			if ($roundinfo["conditions"]["onlyout"]) {
				$setpoints = 2;
				$player["scoring"][] = "$setpoints: only chance out";
				$tilepoints += $setpoints;
			}
			if ($roundinfo["conditions"]["outonpair"]) {
				$setpoints = 2;
				$player["scoring"][] = "$setpoints: out on a pair";
				$tilepoints += $setpoints;
			}
			if ($roundinfo["conditions"]["outonmajorpair"]) {
				$setpoints = 2;
				$player["scoring"][] = "$setpoints: out on a major pair";
				$tilepoints += $setpoints;
			}
		}

		$player["scoring"][] =  ($tilepoints>0) ? "<i>basic tile points: $tilepoints</i>" : "<i>no basic tile points</i>";

		return $tilepoints;
	}


	// calculate basic tile scores
	function calculate_doubles(&$player, &$roundinfo, $won) {
		$patterns =& $roundinfo["patterns"];
		$doubles =& $roundinfo["doubles"];
		$player["scoring"][] = "[doubles]";

		$double = 0;
		$fullyconcealed = true;
		$clean = "";
		$chow = true;
		$pung = true;
		$honors = false;
		$lbwinds = array();
		for ($s=1;$s<6;$s++) {
			$concealed = $player["sets"][$s]["concealed"];
			$suit = $player["sets"][$s]["suit"];
			$tiles = str_split($player["sets"][$s]["tiles"]);
			sort($tiles);
			$set = implode("",$tiles);

			// don't process set if there is nothing
			if ($set =="") { continue; }

			// check for concealed hand
			$fullyconcealed = $fullyconcealed && $concealed;

			// check chow hand (not a chow hand if the pair is a value-pair
			if(in_array($set, $patterns["pung"]["all"]) || in_array($set, $patterns["kong"]["all"]) || (in_array($set, $patterns["pair"]["honor"]) && isset($player["valuepair"]))) { $chow = false; }

			// check pung hand
			if(in_array($set, $patterns["chow"])) { $pung = false; }

			// check for pung doubles
			if (in_array($set, $patterns["pung"]["dragon"])) {
				$setdouble = 1;
				$player["scoring"][] = "$setdouble: pung dragons";
				$double += $setdouble;
			}

			elseif (in_array($set, $patterns["pung"]["wind"])) {
				// own wind or wotr?
				$ownpung = str_replace(array("東", "南", "西", "北"), array("eee","sss","www","nnn"), $player["wind"]);
				$wotrpung = str_replace(array("東", "南", "西", "北"), array("eee","sss","www","nnn"), $roundinfo["wotr"]);
				if ($set == $ownpung) {
					$setdouble = 1;
					$player["scoring"][] = "$setdouble: pung of own winds";
					$double += $setdouble;
				}
				if ($set == $wotrpung) {
					$setdouble = 1;
					$player["scoring"][] = "$setdouble: pung of the wind of the round";
					$double += $setdouble;
				}
			}

			// check for kong doubles
			elseif (in_array($set, $patterns["kong"]["dragon"])) {
				$setdouble = 1;
				$player["scoring"][] = "$setdouble: kong dragons";
				$double += $setdouble;
			}

			elseif (in_array($set, $patterns["kong"]["wind"])) {
				// own wind or wotr?
				$ownkong = str_replace(array("東", "南", "西", "北"), array("eeee","ssss","wwww","nnnn"), $player["wind"]);
				$wotrkong = str_replace(array("東", "南", "西", "北"), array("eeee","ssss","wwww","nnnn"), $roundinfo["wotr"]);
				if ($set == $ownkong) {
					$setdouble = 1;
					$player["scoring"][] = "$setdouble: kong of own winds";
					$double += $setdouble;
				}
				if ($set == $wotrkong) {
					$setdouble = 1;
					$player["scoring"][] = "$setdouble: kong of the wind of the round";
					$double += $setdouble;
				}
			}

			// clean check
			if ($clean !== false && $clean == "") { $clean = $suit; } elseif ($suit != "" && $clean != $suit) { $clean = false; }

			// honors check
			if (in_array($set, $patterns["pair"]["honor"]) || in_array($set, $patterns["pung"]["honor"]) || in_array($set, $patterns["kong"]["honor"])) { $honors = true; }

			// little/big winds?
			if (in_array($set, $patterns["pair"]["wind"]) || in_array($set, $patterns["pung"]["wind"]) || in_array($set, $patterns["kong"]["wind"])) { $lbwinds[] = $set; }
		}

		// little/big wind bonus
		if(count($lbwinds) > 3) {
			$pk = 0;
			$pr = 0;
			foreach($lbwinds as $set) { if(strlen($set)>2) { $pk++; } elseif (strlen($set)==2) { $pr++; } }
			// little wind
			if ($pk==3 && $pr==1) {
				$setdouble = 1;
				$player["scoring"][] = "$setdouble: little winds";
				$double += $setdouble;
			}
			// big wind!
			if ($pk==4) {
				$setdouble = 5;
				$player["scoring"][] = "$setdouble: big winds";
				$double += $setdouble;
			}
		}

		// season/flowers check
		$flw = array("梅","蘭","菊","竹");
		$ssn = array("春","夏","秋","冬");
		switch($player["wind"]) {
			case("東"):	$ownflower = "梅"; $ownseason = "春"; break;
			case("南"):	$ownflower = "蘭"; $ownseason = "夏"; break;
			case("西"):	$ownflower = "菊"; $ownseason = "秋"; break;
			case("北"):	$ownflower = "竹"; $ownseason = "冬"; break; }
		$flowers = array();
		$seasons = array();
		for($f=1; $f<5;$f++) {
			if ($roundinfo["flowers"][$f] == $player["name"]) { $flowers[] = $flw[$f-1]; }
			if ($roundinfo["seasons"][$f] == $player["name"]) { $seasons[] = $ssn[$f-1]; } }

		if(in_array($ownflower, $flowers) && in_array($ownseason, $seasons) && count($flowers)+count($seasons)<8) {
			$setdouble = 1;
			$player["scoring"][] = "$setdouble: own flower and season";
			$double += $setdouble;
		}
		if( (count($flowers) == 4 && count($seasons) < 4) || (count($seasons) == 4 && count($flowers) < 4)) {
			$setdouble = 1;
			$player["scoring"][] = "$setdouble: all flowers or seasons";
			$double += $setdouble;
		}
		if(count($seasons) == 4 && count($flowers) == 4) {
			$setdouble = 3;
			$player["scoring"][] = "$setdouble: all flowers and seasons";
			$double += $setdouble;
		}


		// doubles for the winner only
		if ($won) {
			// fully concealed hand
			if($fullyconcealed) {
				$setdouble = 1;
				$player["scoring"][] = "$setdouble: fully concealed hand";
				$double += $setdouble;
			}

			// chow hand
			if($chow) {
				$setdouble = 1;
				$player["scoring"][] = "$setdouble: chow hand";
				$double += $setdouble;
			}

			// pung hand
			if($pung) {
				$setdouble = 1;
				$player["scoring"][] = "$setdouble: pung hand";
				$double += $setdouble;
			}

			// clean hand
			if ($clean != false) {
				// clean hand with honors
				if ($honors) {
					$setdouble = 1;
					$player["scoring"][] = "$setdouble: one suit with honors";
					$double += $setdouble;
				}
				// properly clean hand
				if (!$honors) {
					$setdouble = 3;
					$player["scoring"][] = "$setdouble: one suit only";
					$double += $setdouble;
				}
			}

			// special conditions
			if ($roundinfo["conditions"]["lasttile"]) {
				$setdouble = 1;
				$player["scoring"][] = "$setdouble: out on the last tile";
				$double += $setdouble;
			}

			if ($roundinfo["conditions"]["lastdiscard"]) {
				$setdouble = 1;
				$player["scoring"][] = "$setdouble: out on the last discard";
				$double += $setdouble;
			}

			if ($roundinfo["conditions"]["supplement"]) {
				$setdouble = 1;
				$player["scoring"][] = "$setdouble: out on a supplement tile";
				$double += $setdouble;
			}

			if ($roundinfo["conditions"]["kongrob"]) {
				$setdouble = 1;
				$player["scoring"][] = "$setdouble: out by robbing a kong";
				$double += $setdouble;
			}

			if ($roundinfo["conditions"]["1stready"]) {
				$setdouble = 1;
				$player["scoring"][] = "$setdouble: ready on first hand";
				$double += $setdouble;
			}
		}

		$player["scoring"][] = ($double>0) ?  "<i>total doublings: $double</i>" : "<i>no doubles</i>";

		return pow(2,$double);
	}



	// settle the payment to the winner, and payment amongst the rest
	function settle_scores(&$roundinfo) {
		$showscores = false;
		global $start_score;

		$roundinfo["scoring"][] = "settling scores";

		// first settle payment to the winner
		$winner = $roundinfo["winner"];
		$winplayer =& $roundinfo["players"][$winner];

		// check for nine-tile error made by someone
		if($roundinfo["ninetile"] !== false)
		{
			// this player pays for everyone!
			$seriousloser =& $roundinfo["players"][$roundinfo["ninetile"]];
			$factor = ($winplayer["wind"] == "東" || $player["wind"] == "東") ? 2 : 1;
			$difference = 3 * $factor * $winplayer["tilepoints"];

			$winplayer["score"] += $difference;
			$seriousloser["score"] -= $difference;
			$seriousloser["scoring"][] = "<br/><b><i>pays winner for everyone,<br/>due to nine tile error</i></b>";
		}
		elseif($roundinfo["wrongful"] !== false)
		{
			// this player pays 300 to all players
			$seriousloser =& $roundinfo["players"][$roundinfo["wrongful"]];
			for($i=0;$i<4;$i++) {
			  if($i!=$roundinfo["wrongful"]) {
			    $roundinfo["players"][$i]["score"] += 300;
			    $roundinfo["players"][$i]["scoring"] = "<br/><b>wrongful out points</b>"; }}
			$roundinfo["players"][$roundinfo["wrongful"]]["score"] -= 900;
		}
		else
		{
			// without a nine tile error, all players pay the winner
			for($p=0;$p<4;$p++) {
				$player =& $roundinfo["players"][$p];
				if ($p != $winner) {
					$factor = ($winplayer["wind"] == "東" || $player["wind"] == "東") ? 2 : 1;
					$difference = $factor * $winplayer["tilepoints"];
					$roundinfo["scoring"][] = $player["name"]."(".$player["wind"].") pays ".$winplayer["name"]." (".$winplayer["wind"]."): ".$difference."";

					$winplayer["score"] += $difference;
					$player["score"] -= $difference; } }
		}

		// next, settle payment amongst the losers
		if($roundinfo["wrongful"] === false) {
      for($p1=0;$p1<4;$p1++) {
        for($p2=$p1+1;$p2<4;$p2++) {
          $player1 =& $roundinfo["players"][$p1];
          $player2 =& $roundinfo["players"][$p2];
          $factor = ($player1["wind"] == "東" || $player2["wind"] == "東") ? 2 : 1;
          if ($p1 == $winner || $p2 == $winner) { continue; }
          else {
            $difference = $factor * ($player1["tilepoints"] - $player2["tilepoints"]);
            $roundinfo["scoring"][] = $player2["name"]."(".$player2["wind"].") pays ".$player1["name"]."(".$player1["wind"]."): ". $difference."";
            $player1["score"] += $difference;
            $player2["score"] -= $difference; } } } }

		// change points based on the score
		$scores =& $roundinfo["scores"];
		$points =& $roundinfo["points"];
		for($i=0;$i<4;$i++) {
			$scores[$i][] = $roundinfo["players"][$i]["score"];
			$points[$i][] = $points[$i][count($points[$i])-1] + $roundinfo["players"][$i]["score"]; }
	}



	// update the winds if the deal passed and the winner was not east, and the wotr if wind was 北 and east did not win.
	function update_winds(&$roundinfo) {
		$winnerwind = $roundinfo["players"][$roundinfo["winner"]]["wind"];
		if($winnerwind != "東" || $roundinfo["wrongful"] !== false) {
			// rotate wind of the round
			$wotr = $roundinfo["wotr"];
			if ($roundinfo["players"][3]["wind"] == "東") {
				switch($wotr) {
					case("東") : $roundinfo["wotr"] = "南"; break;
					case("南") : $roundinfo["wotr"] = "西"; break;
					case("西") : $roundinfo["wotr"] = "北"; break;
					case("北") : $roundinfo["wotr"] = "done"; break; } }

			// rotate player winds
			for($i=0;$i<4;$i++) {
				$wind = $roundinfo["winds"][$i];
				switch($wind) {
					case("東") : $roundinfo["winds"][$i] = "北"; break;
					case("南") : $roundinfo["winds"][$i] = "東"; break;
					case("西") : $roundinfo["winds"][$i] = "南"; break;
					case("北") : $roundinfo["winds"][$i] = "西"; break; } } }
	}
?>