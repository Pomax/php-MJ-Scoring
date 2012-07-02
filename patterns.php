<?php
	// basic patterns data
	
	// pair patterns
	$patterns["pair"]["simple"] = array("22","33","44","55","66","77","88");
	$patterns["pair"]["terminal"] = array("11","99");
	$patterns["pair"]["number"] = (array) array_merge($patterns["pair"]["simple"], $patterns["pair"]["terminal"]);
	$patterns["pair"]["dragon"] = array("cc","ff","pp");
	$patterns["pair"]["wind"] = array("ee","ss","ww","nn");
	$patterns["pair"]["honor"] = (array) array_merge($patterns["pair"]["dragon"], $patterns["pair"]["wind"]);
	$patterns["pair"]["all"] = (array) array_merge($patterns["pair"]["simple"], $patterns["pair"]["terminal"], $patterns["pair"]["honor"]);

	// chow is clearly a very limited set
	$patterns["chow"] = array("123","234","345","456","567","678","789");

	// pung patterns
	$patterns["pung"]["simple"] = array("222","333","444","555","666","777","888");
	$patterns["pung"]["terminal"] = array("111","999");
	$patterns["pung"]["number"] = (array) array_merge($patterns["pung"]["simple"], $patterns["pung"]["terminal"]);
	$patterns["pung"]["dragon"] = array("ccc","fff","ppp");
	$patterns["pung"]["wind"] = array("eee","sss","www","nnn");
	$patterns["pung"]["honor"] = (array) array_merge($patterns["pung"]["dragon"], $patterns["pung"]["wind"]);
	$patterns["pung"]["all"] = (array) array_merge($patterns["pung"]["simple"], $patterns["pung"]["terminal"], $patterns["pung"]["honor"]);

	// kong patterns
	$patterns["kong"]["simple"] = array("2222","3333","4444","5555","6666","7777","8888");
	$patterns["kong"]["terminal"] = array("1111","9999");
	$patterns["kong"]["number"] = (array) array_merge($patterns["kong"]["simple"], $patterns["kong"]["terminal"]);
	$patterns["kong"]["dragon"] = array("cccc","ffff","pppp");
	$patterns["kong"]["wind"] = array("eeee","ssss","wwww","nnnn");
	$patterns["kong"]["honor"] = (array) array_merge($patterns["kong"]["dragon"], $patterns["kong"]["wind"]);
	$patterns["kong"]["all"] = (array) array_merge($patterns["kong"]["simple"], $patterns["kong"]["terminal"], $patterns["kong"]["honor"]);

	// denotations
	$patterns["simple"] = array("2","3","4","5","6","7","8");
	$patterns["terminal"] = array("1","9");	
	$patterns["dragon"] = array("c","f","p");
	$patterns["wind"] = array("e","s","w","n");
	$patterns["honor"] = (array) array_merge($patterns["dragon"], $patterns["wind"]);
	$patterns["all"] = (array) array_merge($patterns["simple"], $patterns["terminal"], $patterns["honor"]);
?>