<?php
	// basic tile scores
	
	// pairs
	$tilescores["pair"]["wind"]["own"] = 2;
	$tilescores["pair"]["dragon"] = 2;

	// chows (generally zero points)
	$tilescores["chow"]["melded"] = 0;
	$tilescores["chow"]["concealed"] = 0;

	// pungs
	$tilescores["pung"]["simple"]["melded"] = 2;
	$tilescores["pung"]["simple"]["concealed"] = 4;
	$tilescores["pung"]["terminal"]["melded"] = 4;
	$tilescores["pung"]["terminal"]["concealed"] = 8;
	$tilescores["pung"]["wind"]["melded"] = 4;
	$tilescores["pung"]["wind"]["concealed"] = 8;
	$tilescores["pung"]["dragon"]["melded"] = 4;
	$tilescores["pung"]["dragon"]["concealed"] = 8;
	$tilescores["pung"]["honor"]["melded"] = 4;
	$tilescores["pung"]["honor"]["concealed"] = 8;

	// kongs
	$tilescores["kong"]["simple"]["melded"] = 8;
	$tilescores["kong"]["simple"]["concealed"] = 16;
	$tilescores["kong"]["terminal"]["melded"] = 16;
	$tilescores["kong"]["terminal"]["concealed"] = 32;
	$tilescores["kong"]["wind"]["melded"] = 16;
	$tilescores["kong"]["wind"]["concealed"] = 32;
	$tilescores["kong"]["dragon"]["melded"] = 16;
	$tilescores["kong"]["dragon"]["concealed"] = 32;
	$tilescores["kong"]["honor"]["melded"] = 16;
	$tilescores["kong"]["honor"]["concealed"] = 32;

	// flowers and seasons
	$tilescores["flower"] = 4;
	$tilescores["season"] = 4;

	// basic multipliers for special sets
	$multipliers["pung"]["wind"]["own"]  = 1;
	$multipliers["kong"]["wind"]["own"]  = 1;
	$multipliers["pung"]["wind"]["wotr"]  = 1;
	$multipliers["kong"]["wind"]["wotr"]  = 1;
	$multipliers["pung"]["dragon"] = 1;
	$multipliers["kong"]["dragon"] = 1;
?>