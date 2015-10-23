<?php

    require(__DIR__ . "/../includes/config.php");

	// decode the URL ($_GET["geo"])
	$url = urldecode($_GET["geo"]);
	
	// replacing commas with spaces, and double spacies with one space
	$words = str_replace("  ", " ", (str_replace(",", " ", $url)));

	$param = explode(" ", $words);

	// building sql query
	$sql = "SELECT * FROM places WHERE ";
	
	for ($i = 0, $j = sizeof($param); $i < $j; $i++)
	{ 
		// if string is numeric, assume it is a postal code
		if (is_numeric($param[$i]))
		{
			$sql .= "(postal_code='$param[$i]')";		
		}   	
		else
		{		
			$sql .= "(place_name LIKE '%$param[$i]%' OR admin_name1 LIKE '%$param[$i]%' OR admin_code1='$param[$i]')";
	
			if ($i < $j - 1) 
			{
				$sql .= " AND ";
			}		
		}
	}

	// take data from database
	$search = query($sql);
	
	$param_size = sizeof($param);
	$places = [];
	
	// iterate through search array
	// comparing data with parametars
	// counting number of matching ($match)
	// adding elements into new array ($places)
	foreach($search as $rows)
	{	
		$match = 0;	
		foreach ($rows as $column => $val)
		{								
			for ($i = $param_size - 1; $i >= 0; $i--)
			{					
				if (strcmp($param[$i], $val) === 0)
				{
					$match++;
					if ($match === $param_size)
					{		
						$places[] = $rows;
					}					
				}			
				if (strpos($val, $param[$i]. " ") !== 0)
				{
					$match++;
					if ($match === $param_size)
					{		
						$places[] = $rows;
					}
				}
				
			}
		}
	}

    // output places as JSON (pretty-printed for debugging convenience)
    header("Content-type: application/json");
    print(json_encode($places, JSON_PRETTY_PRINT));

?>
