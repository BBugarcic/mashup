<?php

    require(__DIR__ . "/../includes/config.php");

	// split given string in $_GET["geo"] to smaller strings
	$keywords = preg_split("/[\s,]+/", $_GET["geo"]);

	// number of elements in keywords array	
	$keysize = sizeof($keywords);
	// numerically indexed array of places
    $places = [];

    // search database for places matching $_GET["geo"]
	$search = query("SELECT * FROM places WHERE MATCH (place_name, admin_name1, postal_code, admin_code1) AGAINST (? IN BOOLEAN MODE)", $_GET["geo"]);

	// number of matching with data in multidimensional array returned from database 
	$match = 0;
 	
	// iterate through search array
	// comparing data with keywords
	// counting number of matching ($match)
	// adding elements into new array ($places)
	foreach($search as $rows)
	{	
		$match = 0;	
		foreach ($rows as $column => $val)
		{	
			for ($i = $keysize - 1; $i >= 0; $i--)
			{				
				if (strcmp($keywords[$i], $val) === 0)
				{
					$match++;
					if ($match === $keysize)
					{		
						$places[] = $rows;
					}
				}
			}
		}
	}

	if (empty($places))
	{
		// numerically indexed array that will have two words as first element		
		$new_key = [];
		// string with space character
		$space = " ";
		// concatinate first two elements of keywords array with space between strings 	
		$new_key[] = $keywords[0].$space.$keywords[1];
		
		// taking part of keywords array after frist two elements
		$key_slice = array_slice($keywords, 2);

		// adding new elements to new_key array
		for ($i = 0, $j = sizeof($key_slice); $i < $j; $i++)
		{
			$new_key[] = $key_slice[$i];
		}

		$new_keysize = sizeof($new_key);

		// iterate through search array
		// comparing data with keywords
		// counting nubmer of matching ($match)
		// adding elements into new array ($places)
		foreach($search as $rows)
		{	
			$match = 0;	
			foreach ($rows as $column => $val)
			{	
				for ($i = $new_keysize - 1; $i >= 0; $i--)
				{				
					if (strcmp($new_key[$i], $val) === 0)
					{
						$match++;
						if ($match === $new_keysize)
						{	
						//	echo "new if match: $match\n";
							$places[] = $rows;
						}
					}
				}
			}
		}		
	}
	
	if (empty($places))
	{
		// numerically indexed array that will have three words as first element		
		$new_key = [];
		
		// string with space character
		$space = " ";

		// concatinate first two elements of keywords array with space between strings 		
		$new_key[] = $keywords[0].$space.$keywords[1];
		
		// taking part of keywords array after first two elements
		$key_slice = array_slice($keywords, 3);

		// adding new elements 
		for ($i = 0, $j = sizeof($key_slice); $i < $j; $i++)
		{
			$new_key[] = $key_slice[$i];
		}

		$new_keysize = sizeof($new_key);

		// iterate through search array
		// comparing data with keywords
		// counting nubmer of matching ($match)
		// adding elements into new array ($places)
		foreach($search as $rows)
		{	
			$match = 0;	
			foreach ($rows as $column => $val)
			{	
				for ($i = $new_keysize - 1; $i >= 0; $i--)
				{				
					if (strcmp($new_key[$i], $val) === 0)
					{
						$match++;
						if ($match === $new_keysize - 1)
						{	
							$places[] = $rows;
						}
					}
				}
			}
		}		
	}

    // output places as JSON (pretty-printed for debugging convenience)
    header("Content-type: application/json");
    print(json_encode($places, JSON_PRETTY_PRINT));

?>
