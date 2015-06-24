<?php
	/**
	 * Swap names here in for format of:
	 *		"OLD-NAME" => "New-Name",
	 *   
	 *	Note: Make sure there's a comma at the end of the line
	 *	Note: OLD-NAME must be all caps (must match what is in the SMS)
	 */




	 $name_swap = array(
		//Add names bellow:
		"ROSE-PENNISI" => "Rose-Pennisi, T.",
		"LOVELESS-MOR" => "Loveless-Morris, J.",
		"HOLLAND-OHER" => "Holland-O'Hern, C.",
		"CALLAHAN-MCC" => "Callahan-McCain, T.",
		"CHASE-DEITRI" => "Chase-Deitrich, D.",
		"KORSCHINOWSK" => "Korschinowski, C.",
		"LINGENFELTER" => "Lingenfelter, R.",
		"FELCH" => "Felch, L.",
		"LEWANDOWSKI" => "Carson-Lewandowski, D.",
		"SWORD" => "Sword, Y.",
		"WESTBERRY" => "Westberry, C.",
		"BAHRT" => "Bahrt, D.",
		"LAZARUS" => "Lazarus, B.",
		"CONDON" => "Condon, J.",
		"COOPER" => "Cooper, L.",
		"HERNANDEZ" => "Hernandez, K.",
		"MUSSON" => "Musson",
		"ANDERSON" => "Anderson",
		"LARGENT" => "Largent",
		"HENDRICKSON" => "Hendrickson, A.",

		
		//Do not modify bellow:
		"EOF" => "EOF"
	 );
	 
	 /**
	 * Force item numbers to different admin_units:
	 *		"item #" => "admin_unit",
	 *
	 *	For example:
	 *	
	 *		"0523" => "4",
	 *   
	 *	Note: Make sure there's a comma at the end of the line
	 */
	 $force_item  = array(
		//Add classes below:
		/* 904: IBEST NAC; 903: IBEST Chemical Dependency; 902: IBEST CAD */
		
		// Fall 2014 alterations
		"BA02" => "BA", // BASMO
		"BA12" => "BA", // BASMO
		"BF01" => "BA", // Continuing Ed
		"BF61" => "BA", // Continuing Ed
		
		"BF11" => "-1", // Continuing Ed
		"BF01" => "-1", // Continuing Ed
		"JT01" => "-1", // Continuing Ed
		"JT03" => "-1", // Continuing Ed
		"BF21" => "-1", // Continuing Ed
		"BF31" => "-1", // Continuing Ed
		"BF41" => "-1", // Continuing Ed
		"BF51" => "-1", // Continuing Ed
		"BF61" => "-1", // Continuing Ed
		"CH40" => "-1", // Continuing Ed
		"CH04" => "-1", // Continuing Ed
		"CH50" => "-1", // Continuing Ed
		"CH05" => "-1", // Continuing Ed
		"CH41" => "-1", // Continuing Ed
		"CH14" => "-1", // Continuing Ed
		"CH15" => "-1", // Continuing Ed
		"CH51" => "-1", // Continuing Ed
		"CHCW" => "-1", // Continuing Ed
		"CH45" => "-1", // Continuing Ed
		"CH54" => "-1", // Continuing Ed
		"CH30" => "-1", // Continuing Ed
		"CH03" => "-1", // Continuing Ed
		"CHSL" => "-1", // cancelled
		"CHMC" => "-1", // cancelled
		"CHMB" => "-1", // cancelled
		"CHMA" => "-1", // cancelled
		"CHMH" => "-1", // cancelled
		"CHMF" => "-1", // cancelled
		"CHMG" => "-1", // cancelled
		"CHML" => "-1", // cancelled
		"CHMM" => "-1", // cancelled
		"CHMK" => "-1", // cancelled
		"CHSS" => "-1", // cancelled
		"CHSC" => "-1", // cancelled
		"CHML" => "-1", // cancelled
		"CHWK" => "-1", // cancelled
		"CHWM" => "-1", // cancelled
		"CHWA" => "-1", // cancelled
		"CHWB" => "-1", // cancelled
		"CHWC" => "-1", // cancelled
		"CHWH" => "-1", // cancelled
		"CHWF" => "-1", // cancelled
		"CHWG" => "-1", // cancelled
		"CHWP" => "-1", // cancelled
		"CHWR" => "-1", // cancelled
		"CHWQ" => "-1", // cancelled
		"CHMQ" => "-1", // cancelled
		"CHMP" => "-1", // cancelled
		"CHMR" => "-1", // cancelled
		"CHWL" => "-1", // cancelled
		"IESH" => "-1", // Continuing Ed
		"ZZZZ" => "-1", // Continuing Ed
		"53XX" => "-1", // Continuing Ed
		"CHNA" => "-1", // hidden fall quarter 2014
		"CHAS" => "-1", // hidden fall quarter 2014
		"CHSA" => "-1", // hidden fall quarter 2014
		"5B72" => "-1", // hidden fall quarter 2014
		"5BA2" => "-1", // hidden fall quarter 2014
		"IT00" => "-1", // hidden fall quarter 2014
		"BF42" => "-1", // hidden fall quarter 2014
		"YYYY" => "-1", // hidden fall quarter 2014
		"BF12" => "-1", // hidden fall quarter 2014
		
		
		// Winter 2015 alterations
		"631R" => "902",
		"631C" => "902",
		"631T" => "902",
		"631M" => "902",
		"632N" => "902",
		"632A" => "902",
		"632P" => "902",
		"632D" => "902",
		"632Y" => "902",
		"632B" => "902",
		
		"241H" => "903",
		"241V" => "903",
		"241B" => "903",
		"241G" => "903",
		"242L" => "903",
		"242C" => "903",
		"242R" => "903",		
		"242D" => "903",
		"242P" => "903",
		"243T" => "903",
		"243F" => "903",
		"243M" => "903",	
		
		"NS3F" => "904",
		"NS3C" => "904",
			
		"5703" => "57", // retail only

		"BA02" => "BA", // BASMO
		"BA12" => "BA", // BASMO
		"BA23" => "BA",

		"BF01" => "BA", // Continuing Ed
		"BF61" => "BA", // Continuing Ed	

		"CHCD" => "-1", // doesn't need to show on the schedule
		"CHNA" => "-1", // doesn't need to show on the schedule
		"5B73" => "-1", // doesn't need to show on the schedule
		"5BA3" => "-1", // doesn't need to show on the schedule
		"5BD3" => "-1", // doesn't need to show on the schedule
		"1305" => "-1", // doesn't need to show on the schedule	
		"68J3" => "-1", // cancelled
		// "47Q3" => "-1", // cancelled
		// "47R3" => "-1", // cancelled
		"3683" => "-1", // cancelled
		"36A3" => "-1", // cancelled
		"3693" => "-1", // cancelled
		"36B3" => "-1", // cancelled
		"5B23" => "212", // NWCTHS
		"0542" => "104", // NWCTHS
		"0542" => "104", // NWCTHS
		"MQ43" => "212", // Adult high school
		"MQ53" => "212", // Adult high school
	
		"MQ83" => "118", //physics
		"0566" => "118", //physics
	
		"47N3" => "-1", //cancelled
		
		"5203" => "-1", //cancelled
		"5213" => "-1", //cancelled
		"5223" => "-1", //cancelled
		"5233" => "-1", //cancelled
		"3033" => "-1", //cancelled

		"5BCC" => "5D", // ADHS
		"5B64" => "5D", // ADHS
		"5B44" => "5D", // ADHS
		"5B54" => "5D", // ADHS
		"5BG4" => "5D", // ADHS
		"5B84" => "5D", // ADHS
		"5B24" => "5D", // ADHS
		"5B74" => "5D", // ADHS
		"5BA4" => "5D", // ADHS
		"5BD4" => "5D", // ADHS
		"5B34" => "-1", // ADHS
		"5W44" => "104", // ALP
		"0540" => "115", // ALP
		"0541" => "115", // ALP
		"MQ94" => "-1", // high school
		"MQA4" => "-1", // high school
		"MQB4" => "-1", // high school
		"MQC4" => "-1", // high school
		"0576" => "-1", // cancelled
		"2PB4" => "-1", // cancelled
		"4114" => "-1", // cancelled
		"4144" => "-1", // cancelled
		"5W48" => "115", // ALP
		"0556" => "115", // ALP
		"0557" => "115", // ALP
		"0540" => "103", // ALP
		"0541" => "103", // ALP




		
		
		
		
		
		//Do not modify bellow:
		"EOF" => "EOF"
	 );
?>