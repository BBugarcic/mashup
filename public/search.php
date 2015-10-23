<?php

    require(__DIR__ . "/../includes/config.php");

	// decode the URL ($_GET["geo"])
	// breaking url-strng into an array containing keywords
	// removing whitespaces from the end and beginning of words (strings)
	$param = array_map('trim', explode(",", urldecode($_GET["geo"])));
	
	// removing "US" parametar from key, every address is in US
	if (($us = array_search("US", $param)) !== false)
	{
		unset($param[$us]);
	}
	
	// building sql query
	$sql = "SELECT * FROM places WHERE ";
	
	for ($i = 0, $j = sizeof($param); $i < $j; $i++)
	{ 
		// if key is numeric, assume it is a postal code
		if (is_numeric($param[$i]))
		{
			$sql .= "(postal_code='$param[$i]')";		
		}   	
		else
		{		
			$sql .= "(place_name='$param[$i]' OR admin_name1='$param[$i]' OR admin_code1 LIKE '$param[$i]')";
	
			if ($i < $j - 1) 
			{
				$sql .= " AND ";
			}		
		}
	}
	
	$places = query($sql);
	
	// if user do not enter comma after first word
	// breaking elements of parametar array to new array
	if (empty($places))
	{	
		// building sql query with new parameters
		$sql = "SELECT * FROM places WHERE ";

		foreach($param as $key => $val)	
		{	
			$new_param = explode(" ", $val);
			
			//for ($i = 0, $j = sizeof($new_param); $i < $j, $i++)
			//{
	
				for ($i = 0, $j = sizeof($new_param); $i < $j; $i++)
				{ 
					// if key is numeric, assume it is a postal code
					if (is_numeric($new_param[$i]))
					{
						$sql .= "(postal_code='$new_param[$i]')";		
					}   	
					else
					{		
						$sql .= "(place_name='$new_param[$i]' OR admin_name1='$new_param[$i]' OR admin_code1 LIKE '$new_param[$i]')";
	
						if ($i < $j - 1) 
						{
							$sql .= " AND ";
						}		
					}
				}
		}		
				
		$places = query($sql);
	}

    // output places as JSON (pretty-printed for debugging convenience)
    header("Content-type: application/json");
    print(json_encode($places, JSON_PRETTY_PRINT));

?>
