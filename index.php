<?php

	// check which player won
	function check_mahjong(&$roundinfo) {
		if($roundinfo["limit"]["won"]) { $roundinfo["winner"] = $roundinfo["limit"]["player"]; }
		else {
			$players =& $roundinfo["players"];
			for($i=0;$i<4;$i++) {
				$four = 0;
				$two = 0;
				// tile sets: you need five to win
				for($j=1; $j<6; $j++) {
					if ($players[$i]["sets"][$j]["tiles"] != "") {
						if (strlen($players[$i]["sets"][$j]["tiles"])>=3) $four++;
						if (strlen($players[$i]["sets"][$j]["tiles"])==2) $two++; }}
				// four need to be chow/pung/kong, one needs to be a pillow
				if ($four==4 && $two ==1) $roundinfo["winner"] = $i; } }
	}


	// calculate the scores for a round
	function calculate_scores(&$roundinfo) {
		// determine who won, and calculate everyone's tilepoints
		check_mahjong($roundinfo);

		// calculate everyone's tilepoints
		calculate_tilepoints($roundinfo);

		// solve all the who owes who how much equasions
		settle_scores($roundinfo);

		// update the winner and seenwind lists
		$winner = $roundinfo["winner"];
		if (count($roundinfo["winners"])==1 && $roundinfo["winners"][0] == "") { $roundinfo["winners"][0] = $roundinfo["players"][$winner]["name"]; } else { $roundinfo["winners"][] = $roundinfo["players"][$winner]["name"]; }
		if (count($roundinfo["seenwinds"])==1 && $roundinfo["seenwinds"][0] == "") { $roundinfo["seenwinds"][0] = $roundinfo["players"][$winner]["wind"]; } else { $roundinfo["seenwinds"][] = $roundinfo["players"][$winner]["wind"]; }
	}


	// we need the following bit in to form legal XML, and php interprets it as a code escape ^.^
	echo "<?";
?>DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Mahjong scoring page </title>
<link rel="stylesheet" href="mj-scoring.css" type="text/css"/>
<script language="javascript">
<!--

	var scorehtml = "-"

	function checkform() {

		var adds = false;
		var pl1 = document.getElementById("set1-1").value.length + document.getElementById("set2-1").value.length + document.getElementById("set3-1").value.length + document.getElementById("set4-1").value.length + document.getElementById("set5-1").value.length
		var pl2 = document.getElementById("set1-2").value.length + document.getElementById("set2-2").value.length + document.getElementById("set3-2").value.length + document.getElementById("set4-2").value.length + document.getElementById("set5-2").value.length
		var pl3 = document.getElementById("set1-3").value.length + document.getElementById("set2-3").value.length + document.getElementById("set3-3").value.length + document.getElementById("set4-3").value.length + document.getElementById("set5-3").value.length
		var pl4 = document.getElementById("set1-4").value.length + document.getElementById("set2-4").value.length + document.getElementById("set3-4").value.length + document.getElementById("set4-4").value.length + document.getElementById("set5-4").value.length

		var limit = document.mjform["limithand"];
		var limithand = (document.getElementById("limithands").value != "")

		for(var i = 0; i < 5; i++) { if (limit[i] != null && limit[i].checked && limithand) adds = true }

		// very simple verification that at least one player got mahjong
		if (pl1>13 || pl2>13 || pl3>13 || pl4>13) { adds = true }

		// if everything adds up, submit the form
		if (adds) { document.mjform.submit() } else { alert("No one has mahjong yet. While you may leave out non-scoring sets for the other players, please make sure that you have entered the winner's entire hand before hitting \"process hands\"."); }
	}

	function drawround() {
		document.draw.submit()
	}

	function resetflowerseasonnines() {
		for(var i=0; i<4; i++) {
			document.getElementById("f1"+i).checked = false;
			document.getElementById("f2"+i).checked = false;
			document.getElementById("f3"+i).checked = false;
			document.getElementById("f4"+i).checked = false;
			document.getElementById("s1"+i).checked = false;
			document.getElementById("s2"+i).checked = false;
			document.getElementById("s3"+i).checked = false;
			document.getElementById("s4"+i).checked = false;
			document.getElementById("ninetile"+i).checked = false;
		}
	}


	function setSuit(event, origin) {
		// IE vs. Gecko/Opera
		if(window.event) { keynum = event.keyCode } else if(event.which) { keynum = event.which }
		var key = String.fromCharCode(keynum)
		var iid = origin.id
		var rkeypressed = false
		if(key=='B' && event.shiftKey) {
			document.getElementById(iid+"-bamboo").checked = true
			rkeypressed=true }
		else if(key=='C' && event.shiftKey) {
			document.getElementById(iid+"-characters").checked = true
			rkeypressed=true }
		else if(key=='D' && event.shiftKey) {
			document.getElementById(iid+"-dots").checked = true
			rkeypressed=true }
		// remove the just typed letter
		if (rkeypressed==true) {
			var str = document.getElementById(iid).value
			var rstr = str.substring(0,(str.length-1))
			document.getElementById(iid).value = rstr }
	}

	function moveFocus(s,i)
	{
		var sid = ""
		if(s<5)  { sid = "set" + (s+1) + "-" + i }
		if(s==5 && i<4) { sid = "set" + 1 + "-" + (i+1) }
		if(s==5 && i==4) { sid = "set1-1" }
//		if(s==5 && i==4) { sid = "submitbutton" }
		document.getElementById(sid).focus()
	}
-->
</script>
</head>

<body>

<?php

	if (!$_POST) {
?>
		<form action="index.php" method="post">
			<table>
				<tr>
					<td width="*%" align="center" valign="middle">
						<input type="hidden" name="turn" value="0"/>
						<input type="hidden" name="wind1" value="東"/>
						<input type="hidden" name="wind2" value="南"/>
						<input type="hidden" name="wind3" value="西"/>
						<input type="hidden" name="wind4" value="北"/>
						<input type="hidden" name="wotr" value="東"/>
						<input type="hidden" name="seenwinds" value=""/>
						<input type="hidden" name="winners" value=""/>
						<input type="hidden" name="points1" value="0"/>
						<input type="hidden" name="points2" value="0"/>
						<input type="hidden" name="points3" value="0"/>
						<input type="hidden" name="points4" value="0"/>
						<input type="hidden" name="scores1" value="0"/>
						<input type="hidden" name="scores2" value="0"/>
						<input type="hidden" name="scores3" value="0"/>
						<input type="hidden" name="scores4" value="0"/>

						<p>Please enter player names:</p>
						<table>
							<tr>
								<td>East:</td>
								<td><input type="text" name="player1"></td>
							</tr>
							<tr>
								<td>South:</td>
								<td><input type="text" name="player2"></td>
							</tr>
							<tr>
								<td>West:</td>
								<td><input type="text" name="player3"></td>
							</tr>
							<tr>
								<td>North:</td>
								<td><input type="text" name="player4"></td>
							</tr>
							<tr>
								<td>Ruleset:</td>
								<td>
									<select name="ruleset">
									    <option value="Chinese Classical">Chinese Classical</option>
									</select>
								</td>
							</tr>
							<tr>
								<td colspan="2">
									<p class="centered"><br/><input type="submit" name="submit" value="Start a new game"></p>
								</td>
							</tr>
						</table>
					</td>
					<td width="70%" class="bordered" style="padding-left: 1em; padding-right: 1em;">
					<?php
						 include("pregame.html");
						 include("legend.html");
					?>
					</td>
				</tr>
			</table>
		</form>
<?php
	}

	else {

		if (isset($_POST["draw"]))
		{
			$drawn==true;
		}
?>

	<form name="mjform" action="index.php" method="post">

<?php
		// general information
		$playernames = array();
		$points = array();
		$scores= array();
		$seenwinds = explode(",", $_POST["seenwinds"]);
		$winds = array();
		$winners = explode(",", $_POST["winners"]);
		$wotr = $_POST["wotr"];
		$ruleset = $_POST["ruleset"];
		$turn = $_POST["turn"];

		// include the proper algorithm
		if (false) { /* do nothing*/ }
		elseif ($ruleset == "Chinese Classical")	{ include("rules/chineseclassical.php"); }
		elseif ($ruleset == "Cantonese")		{ include("rules/cantonese.php"); }
		elseif ($ruleset == "Shanghai")			{ include("rules/shanghai.php"); }
		elseif ($ruleset == "Taiwan")			{ include("rules/taiwan.php"); }
		elseif ($ruleset == "Kong Kong")		{ include("rules/hongkong.php"); }

		// placeholder
		$roundinfo = array();

		// names, points, winds
		for($i=0;$i<4;$i++)
		{
			$c = $i + 1;
			$playernames[$i] = ($_POST["player$c"] == "") ? "player$c" : str_replace(" ","&nbsp;",$_POST["player$c"]);
			if ($turn==0) {
				$points = array(0=>array($start_score),
						1=>array($start_score),
						2=>array($start_score),
						3=>array($start_score)); }
			 else {
				$points[$i] = explode(",",$_POST["points$c"]); }
			$scores[$i] = explode(",",$_POST["scores$c"]);
			$winds[$i] = $_POST["wind$c"];
		}



		/*
			The scoring algorithm takes place here
		*/

		include("patterns.php");
		include("pattern-scores.php");

		// round number
		$roundinfo["turn"] = $turn;

		if ($turn>0)
		{
			// ninetile information
			$nte=-1;
			for($i=0;$i<4;$i++) { if(isset($_POST["ninetile".$i])) $nte=$i; }
			$roundinfo["ninetile"] = ($nte>=0)? $nte : false;

			$wfo=-1;
			for($i=0;$i<4;$i++) { if(isset($_POST["wrongfulout".$i])) $wfo=$i; }
			$roundinfo["wrongful"] = ($wfo>=0)? $wfo : false;

			// limit information
			$roundinfo["limit"]["won"] = ($_POST["limits"] != "");
			$roundinfo["limit"]["player"] = isset($_POST["limithand"]) ? $_POST["limithand"] : false;

			// win conditions
			$roundinfo["conditions"]["1stready"] = isset($_POST["1stready"]) ? true : false;
			$roundinfo["conditions"]["lasttile"] = isset($_POST["lasttile"]) ? true : false;
			$roundinfo["conditions"]["lastdiscard"] = isset($_POST["lastdiscard"]) ? true : false;
			$roundinfo["conditions"]["supplement"] = isset($_POST["supplement"]) ? true : false;
			$roundinfo["conditions"]["kongrob"] = isset($_POST["kongrob"]) ? true : false;
			$roundinfo["conditions"]["selfdrawn"] = isset($_POST["selfdrawn"]) ? true : false;
			$roundinfo["conditions"]["onlyout"] = isset($_POST["onlyout"]) ? true : false;
			$roundinfo["conditions"]["outonpair"] = isset($_POST["outpairs"]) ? true : false;
			$roundinfo["conditions"]["outonmajorpair"] = isset($_POST["outpairm"]) ? true : false;

			// important for making someone's limit hand - you pay for everyone
			$roundinfo["conditions"]["discarded"] = isset($_POST["discarded"]) ? $_POST["discarded"]: false;

			// flowers
			$roundinfo["flowers"][1] = isset($_POST["f1"]) ? $_POST["f1"] : false;
			$roundinfo["flowers"][2] = isset($_POST["f2"]) ? $_POST["f2"] : false;
			$roundinfo["flowers"][3] = isset($_POST["f3"]) ? $_POST["f3"] : false;
			$roundinfo["flowers"][4] = isset($_POST["f4"]) ? $_POST["f4"] : false;

			// seasons
			$roundinfo["seasons"][1] = isset($_POST["s1"]) ? $_POST["s1"] : false;
			$roundinfo["seasons"][2] = isset($_POST["s2"]) ? $_POST["s2"] : false;
			$roundinfo["seasons"][3] = isset($_POST["s3"]) ? $_POST["s3"] : false;
			$roundinfo["seasons"][4] = isset($_POST["s4"]) ? $_POST["s4"] : false;

			// wind of the round
			$roundinfo["wotr"] =& $wotr;

			// player information
			for($i=0;$i<4;$i++) {
				$roundinfo["players"][$i]["name"] = $playernames[$i];
				$roundinfo["players"][$i]["wind"] =& $winds[$i];
				$roundinfo["players"][$i]["outon"] = isset($_POST[$playernames[$i]."-outset"])? $_POST[$playernames[$i]."-outset"] : false;

				// tile sets
				for($j=1; $j<6; $j++) {
					$roundinfo["players"][$i]["sets"][$j]["tiles"] = strtolower($_POST[$playernames[$i]."-set".$j]);
					$roundinfo["players"][$i]["sets"][$j]["concealed"] = isset($_POST[$playernames[$i]."-set".$j."-conc"]) ? true : false;
					$roundinfo["players"][$i]["sets"][$j]["suit"] = isset($_POST[$playernames[$i]."-set".$j."-color"]) ? $_POST[$playernames[$i]."-set".$j."-color"] : "";
				}
			}

			$roundinfo["winds"] =& $winds;
			$roundinfo["points"] =& $points;
			$roundinfo["scores"] =& $scores;
			$roundinfo["winners"] =& $winners;
			$roundinfo["seenwinds"] =& $seenwinds;

			$roundinfo["patterns"] =& $patterns;
			$roundinfo["tilescores"] =& $tilescores;
			$roundinfo["multipliers"] =& $multipliers;

			// and now send it all off for processing by the proper ruleset
			calculate_scores($roundinfo);

			// update the winds
			update_winds($roundinfo);
		}

		/*
			end of scoring algorithm
		*/

		// now increase the turn count for the next round
		$turn++;


?>
				<input type="hidden" name="turn" value="<?php echo $turn; ?>"/>
				<input type="hidden" name="wotr" value="<?php echo $wotr; ?>"/>
				<input type="hidden" name="seenwinds" value="<?php echo implode(",",$seenwinds); ?>"/>
				<input type="hidden" name="winners" value="<?php echo implode(",",$winners); ?>"/>
				<input type="hidden" name="ruleset" value="<?php echo $_POST["ruleset"]; ?>"/>
<?php
		for($i = 0; $i<4; $i++)
		{
?>				<input type="hidden" name="wind<?php echo ($i+1); ?>" value="<?php echo $winds[$i]; ?>"/>
				<input type="hidden" name="player<?php echo ($i+1); ?>" value="<?php echo $playernames[$i]; ?>"/>
				<input type="hidden" name="points<?php echo ($i+1); ?>" value="<?php echo implode(",",$points[$i]); ?>"/>
				<input type="hidden" name="scores<?php echo ($i+1); ?>" value="<?php echo implode(",",$scores[$i]); ?>"/>
<?php
		}
?>
		<table width="100%">
			<tr>

<?php

		if ($turn > 0) {
?>
				<td>
					<div id="scores"></div>
						<table>
							<tr>
								<td/>
								<td/>
								<td class="bordered centered label"><?php echo $playernames[0]; ?></td>
								<td class="bordered centered label"><?php echo $playernames[1]; ?></td>
								<td class="bordered centered label"><?php echo $playernames[2]; ?></td>
								<td class="bordered centered label"><?php echo $playernames[3]; ?></td>
							</tr>
<?php
			// keeping score
			for($t=0; $t<$turn-1; $t++)
			{
?>
<?php
				if($t==$turn-2)
				{
?>
							<tr><td/></tr>
							<tr>
								<td valign="bottom" colspan="2" class="turnwind"><span style="cursor: pointer" onclick="view_scores()">scores</span></td>
<?php
					for($i=0;$i<4;$i++)
					{
?>
								<td class="bordered centered turnwind"><?php echo $roundinfo["players"][$i]["tilepoints"]; ?></td>
<?php
					}
?>
							</tr>
<?php
				}
?>
							<tr>
								<td><?php echo ($t+1); ?></td>
								<td class="bordered" align="right" width="4px"><span class="turnwind"><?php echo $seenwinds[$t]; ?></span></td>
<?php
				for($i=0;$i<4;$i++)
				{
?>
								<td class="bordered centered"><?php
									if ($winners[$t] == $playernames[$i]) echo "<b>";
									echo $scores[$i][$t+1];
									if ($winners[$t] == $playernames[$i]) echo "</b>";
								?></td>
<?php
				}
?>
							</tr>
<?php
			}
?>
							<tr><td/></tr>
							<tr>
								<td colspan="2"><?php if ($wotr == "done") { echo "<b>total</b>"; } else { echo "current"; } ?></td>
<?php
				for($i=0;$i<4;$i++)
				{
?>
								<td class="bordered centered"><?php echo $points[$i][count($points[$i])-1]; ?></td>
<?php
				}
?>
							</tr>
						</table>
					</div>		<!-- end of scores / score sheet div -->
				</td>
<?php
		}

		// current round information
?>
				<td>
					<table>
						<tr>
<?php

		for($i = 0; $i<4; $i++) {
?>
						<td class="bordered">
							<p class="centered bordered label"><?php
								if ($wotr == $winds[$i]) { echo "<b>"; }
								echo $winds[$i];
								if ($wotr == $winds[$i]) { echo "</b>"; }
								echo ": " . $playernames[$i];
							?></p>

							<p class="centered small smallcaps bordered">flowers and seasons</p>

							<table width="100%" class="centered">
								<tr>
									<td><div class="centered"><span class="flower-number">1.</span> <span class="flower">梅</span></div></td>
									<td><div class="centered"><span class="flower-number">2.</span> <span class="flower">蘭</span></div></td>
									<td><div class="centered"><span class="flower-number">3.</span> <span class="flower">菊</span></div></td>
									<td><div class="centered"><span class="flower-number">4.</span> <span class="flower">竹</span></div></td>
								</tr>
								<tr>
									<td><input type="radio" name="f1" id="f1<?php echo $i; ?>" value="<?php echo $playernames[$i]; ?>"/></td>
									<td><input type="radio" name="f2" id="f2<?php echo $i; ?>" value="<?php echo $playernames[$i]; ?>"/></td>
									<td><input type="radio" name="f3" id="f3<?php echo $i; ?>" value="<?php echo $playernames[$i]; ?>"/></td>
									<td><input type="radio" name="f4" id="f4<?php echo $i; ?>" value="<?php echo $playernames[$i]; ?>"/></td>
								</tr>
								<tr>
									<td><div class="centered"><span class="season-number">1.</span> <span class="season">春</span></div></td>
									<td><div class="centered"><span class="season-number">2.</span> <span class="season">夏</span></div></td>
									<td><div class="centered"><span class="season-number">3.</span> <span class="season">秋</span></div></td>
									<td><div class="centered"><span class="season-number">4.</span> <span class="season">冬</span></div></td>
								</tr>
								<tr>
									<td><input type="radio" name="s1" id="s1<?php echo $i; ?>" value="<?php echo $playernames[$i]; ?>"/></td>
									<td><input type="radio" name="s2" id="s2<?php echo $i; ?>" value="<?php echo $playernames[$i]; ?>"/></td>
									<td><input type="radio" name="s3" id="s3<?php echo $i; ?>" value="<?php echo $playernames[$i]; ?>"/></td>
									<td><input type="radio" name="s4" id="s4<?php echo $i; ?>" value="<?php echo $playernames[$i]; ?>"/></td>
								</tr>
							</table>

							<p class="centered small smallcaps bordered">tiles in hand</p>

							<table>
								<tr>
									<td/>
									<td class="centered">竹</td>
									<td class="centered">萬</td>
									<td class="centered">◎</td>
									<td/>
								</tr>
<?php
							for ($s=1;$s<6;$s++)
							{
								$lid = $s . "-" . ($i+1);
?>
								<tr>
									<td><input id="set<?php echo $lid; ?>" name="<?php echo $playernames[$i]; ?>-set<?php echo $s; ?>" type="text" size="4" onKeyUp="setSuit(event,this)"/></td>
									<td class="centered"><input id="set<?php echo $lid; ?>-bamboo" name="<?php echo $playernames[$i]; ?>-set<?php echo $s; ?>-color" type="radio" value="bamboo" onFocus="moveFocus(<?php echo $s ?>, <?php echo ($i+1) ?>)"/></td>
									<td class="centered"><input id="set<?php echo $lid; ?>-characters"name="<?php echo $playernames[$i]; ?>-set<?php echo $s; ?>-color" type="radio" value="characters" onFocus="moveFocus(<?php echo $s ?>, <?php echo ($i+1) ?>)"/></td>
									<td class="centered"><input id="set<?php echo $lid; ?>-dots"name="<?php echo $playernames[$i]; ?>-set<?php echo $s; ?>-color" type="radio" value="dots" onFocus="moveFocus(<?php echo $s ?>, <?php echo ($i+1) ?>)"/></td>
									<td class="concealed centered"><input type="checkbox" name="<?php echo $playernames[$i]; ?>-set<?php echo $s; ?>-conc" value="1" onFocus="moveFocus(<?php echo $s ?>, <?php echo ($i+1) ?>)"/></td>
								</tr>
<?php
							}
?>
								<tr>
									<td/>
									<td/>
									<td/>
									<td colspan="2" class="concealed centered" align="right"><span class="small">concealed</span></td>
								</tr>
							</table>

							<table width="100%">
								<tr>
									<td class="centered">
										<input type="radio" name="limithand" value="<?php echo $i; ?>" disabled="disabled"/> limit hand
									</td>
<!--
									<td  align="right" width="85%"class="small">discarded winning tile</td>
									<td><input type="radio" name="discard" value="<?php echo $playernames[$i]; ?>"/></td>
-->
								</tr>
							</table>
						</td>
<?php
		}
?>
					</tr>
					<tr>
						<?php
							for($i=0;$i<4; $i++)
							{
						?>
						<td align="center" class="turnwind">
							<input type="radio" name="ninetile<?php echo $i; ?>" id="ninetile<?php echo $i; ?>"/>nine tile error<br/>
							<input type="radio" name="wrongfulout<?php echo $i; ?>" id="wrongfulout<?php echo $i; ?>"/>wrongful out
						</td>
						<?php
							}
						?>
					</tr>
					<tr>
						<td colspan="4" class="centered turnwind">Scoring according to the <?php echo $ruleset; ?> rules</td>
					</tr>
					<tr>
						<td colspan="4">
							<table width="100%">
								<tr>
									<td class="centered bordered small">
										<input type="checkbox" name="1stready" value="1"/>first hand ready
										<input type="checkbox" name="lasttile" value="1"/>last tile
										<input type="checkbox" name="lastdiscard" value="1"/>last discard
										<input type="checkbox" name="supplement" value="1"/>dead wall tile
										<input type="checkbox" name="kongrob" value="1"/>kong rob
										<input type="checkbox" name="selfdrawn" value="1"/>self drawn
										<input type="checkbox" name="onlyout" value="1"/>one chance
										<input type="checkbox" name="outpairs" value="1" onclick="document.getElementById('mjp').disabled = (document.getElementById('mjp').disabled) ? false : true"/>pair
										<input type="checkbox" name="outpairm" value="1" id="mjp" disabled="disabled"/>major
									</td>
								</tr>
								<tr>
									<td  class="simplebordered" align="left"><?php render_limits(); ?></td>
								</tr>
								<tr><td/></tr>
<?php
		if ($wotr != "done")
		{
?>
								<tr>
									<td align="center">
										<input type="button" name="process" id="submitbutton" value="Process hands" onclick="checkform()" />
										<input type="button" name="resetfs" value="Clear flowers/seasons/nine tile" onclick="resetflowerseasonnines()"/>
										<input type="reset" name="reset" value="Clear everything" />
										<!--
										<input type="button" name="draw" value="Draw" onclick="drawround()"/>
										-->
									</td>
								</tr>
<?php
		}
?>
							</table>
						</td>
					</tr>
				</table>
			</td>
		</table>
	</form>

	<!-- draw form -->

	<form name="draw"action="index.php" method="post">
		<input type="hidden" name="draw" value="draw"/>
	</form>

<?php
	}

	if (isset($roundinfo) && $roundinfo["turn"]>0)
	{
?>
	<script language="javascript">

		// fill the scores div
		var html = "<?php
			$string1 = implode("<br/>",$roundinfo["players"][0]["scoring"]);
			$string2 = implode("<br/>",$roundinfo["players"][1]["scoring"]);
			$string3 = implode("<br/>",$roundinfo["players"][2]["scoring"]);
			$string4 = implode("<br/>",$roundinfo["players"][3]["scoring"]);
			$table = "<p class='centered smallcaps'>individual player scores for round ".$roundinfo["turn"]."</p><table width='100%'><tr><td class='bordered label'>".$roundinfo["players"][0]["name"]."</td><td class='bordered label'>".$roundinfo["players"][1]["name"]."</td><td class='bordered label'>".$roundinfo["players"][2]["name"]."</td><td class='bordered label'>".$roundinfo["players"][3]["name"]."</td></tr><tr><td class='bordered' valign='top'>$string1</td><td class='bordered' valign='top'>$string2</td><td class='bordered' valign='top'>$string3</td><td class='bordered' valign='top'>$string4</td></tr></table>";
			echo str_replace('"','\"',$table);
		?>"
		document.getElementById("scores").innerHTML = html

		function view_scores()
		{
			var divdisplay = document.getElementById("scores").style
			divdisplay.display = (divdisplay.display=="block")? "none" : "block";
		}
	</script>
<?php
	}
?>
</body>
</html>
