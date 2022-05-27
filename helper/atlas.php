<?php 
/* 
* Copyright (c) 2022 perniciousflier@gmail.com  
* This code is a part of the ochin project (https://github.com/ochin-space)
* The LICENSE file is included in the project's root. 
*/ 
function tilesList($path)
{
	if(file_exists($path) && is_dir($path))
	{
		$rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));
		$files = array(); 
		foreach ($rii as $file) {
			if ($file->isDir()){ 
				continue;
			}
			$files[] = trim(trim($file->getPathname(),$path),".png"); 
		}
		echo json_encode($files);
		exit();
	}
	else echo "false";
	exit();
}

function rrmdir($dir) 
{ 
	if (is_dir($dir)) { 
		$objects = scandir($dir);
		foreach ($objects as $object) { 
		if ($object != "." && $object != "..") { 
			if (is_dir($dir. DIRECTORY_SEPARATOR .$object) && !is_link($dir."/".$object))
				rrmdir($dir. DIRECTORY_SEPARATOR .$object);
			else
				unlink($dir. DIRECTORY_SEPARATOR .$object); 
			} 
		}
		rmdir($dir); 
	} 
}

//get the coordinates and return the tile numbers
function TileFromCoord($zoom, $lat, $lon)
{
	$x = floor((($lon+180)/360) * pow(2, $zoom));
	$y = floor((1 - log(tan(deg2rad($lat)) + 1 / cos(deg2rad($lat))) / pi()) / 2 * pow(2, $zoom));
	$url = array('z'=>$zoom,'x'=>$x,'y'=>$y);
	return $url;
}

//get the tile numbers and return the coordinates 
function CoordFromTile($zoom, $xtile, $ytile)
{
	$n = pow(2, $zoom);
	$lon_deg = $xtile / $n * 360.0 - 180.0;
	$lat_deg = rad2deg(atan(sinh(pi() * (1 - 2 * $ytile / $n))));
	return array('lat_deg'=>$lat_deg,'lon_deg'=>$lon_deg);
}

function downloadFromURL($serverMapURLn, $save_file_loc)
{							
	$ch = curl_init($serverMapURLn);
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
	curl_setopt($ch, CURLOPT_TIMEOUT, 300);
	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 8.0; Trident/4.0)');
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$raw_data = curl_exec($ch);
	curl_close($ch);
	$fp = fopen($save_file_loc, 'x');
	fwrite($fp, $raw_data);
	fclose($fp);
}

function updateLocalMap($path, $serverMapURL,  $newTiles, $tiles2remove, $deleteAllcheck, $downloadSubcheck)
{
	$tiles = JSON_decode($newTiles);
	$tiles2remove = JSON_decode($tiles2remove);
	$nSubTiles2Dwn = 4;
	$nSubTiles2Del = 64;
	
	if(!file_exists($path)) mkdir($path, 0755);	//check if map folder exists and eventually create it
		
	$diskFree = intval((disk_free_space($path)*100)/disk_total_space($path));
	//download the selected tiles and the ones over them with lower zoom factor
	foreach($tiles as $tile)
	{
		$coord = explode("/", $tile);	//divide the string zoom/lat/lon
		//fputs($debug, $coord[0]."/".$coord[1]."/".$coord[2]."\n");
		
		if($downloadSubcheck=='true')	//download the tiles at zoomlevel+1 also
		{	
			$xyoffset=0;
			//$SubZTile00 = array('z'=>$coord[0],'x'=>$coord[1],'y'=>$coord[2]);			
			for($zoom=$coord[0]+1;$zoom<=20;$zoom++)//download all tiles on the higher zoom layers
			{
				$ntiles = pow(2,($zoom-$coord[0]));
				$xyoffset = intdiv($nSubTiles2Dwn,2);
				$SubZTile00 = TileFromCoord($zoom, $coord[1], $coord[2]); //get the name of the tile00 from zoom and coordinates
				for($x=0;$x<$nSubTiles2Dwn;$x++)
				{
					for($y=0;$y<$nSubTiles2Dwn;$y++)
					{
						$filename = $SubZTile00['z']."/".($SubZTile00['x']+$x-$xyoffset)."/".($SubZTile00['y']+$y-$xyoffset);
						//file location folder
						$save_file_loc = $path."/".$filename.".png";
						
						//fputs($debug, $filename."\n");
						if(!file_exists($save_file_loc)) //downlaod the file if does not exist already
						{
							//create directories
							$dir=$path."/".$SubZTile00['z']."/".($SubZTile00['x']+$x-$xyoffset);
							if(!file_exists($dir)&& !is_dir($dir)) mkdir($dir, 0755, true); //create the dirs if does not exist already
							
							$serverMapURLn = str_replace("{z}/{x}/{y}",$filename,$serverMapURL);	//replace "{z}/{x}/{y}.png" from the server url with the name of the tile
							//fputs($debug, $serverMapURLn."\n\n");					
							//file_put_contents($save_file_loc, file_get_contents($serverMapURLn));	
							if($diskFree>30) downloadFromURL($serverMapURLn, $save_file_loc);
							else 
							{
								echo ("Not all tiles has been downloaded since you only have ".$diskFree."% of available disk space!");
								exit();
							}
						}
					}
				}
			}
		}		
		//download also the upper tiles (less zoomed to zoom9)
		for($zoom=$coord[0];$zoom>=9;$zoom--)
		{
			$url = TileFromCoord($zoom,$coord[1],$coord[2]);	//generate the url of the tile fom the coordinates and the zoom level
			$filename = $url['z']."/".$url['x']."/".$url['y'];
			//file location folder
			$save_file_loc = $path."/".$filename.".png";
			
			//fputs($debug, $url['z']."/".$url['x']."/".$url['y']."\n");
			if(!file_exists($save_file_loc)) //downlaod the file if does not exist already
			{
				//create directories
				$dir=$path."/".$url['z']."/".$url['x'];
				if(!file_exists($dir)&& !is_dir($dir)) mkdir($dir, 0755, true); //create the dirs if does not exist already
				
				$serverMapURLn = str_replace("{z}/{x}/{y}",$filename,$serverMapURL);	//replace "{z}/{x}/{y}.png" from the server url with the name of the tile
				//fputs($debug, $serverMapURLn."\n\n");					
				//file_put_contents($save_file_loc, file_get_contents($serverMapURLn));	
				if($diskFree>30) downloadFromURL($serverMapURLn, $save_file_loc);
				else 
				{
					echo ("Not all tiles has been downloaded since you only have ".$diskFree."% of available disk space!");
					exit();
				}
			}
		}		
	}
	
	//delete the selected (to delete) tiles and the ones with higher zoom factor
	foreach($tiles2remove as $tile)
	{
		$coord = explode("/", $tile);	//divide the string zoom/lat/lon
		$url = TileFromCoord($coord[0],$coord[1],$coord[2]);	//generate the url of the tile fom the coordinates and the zoom level
		$filename = $path."/".$url['z']."/".$url['x']."/".$url['y'].".png";
		//fputs($debug,$filename."\n\n");
		if (file_exists($filename)) unlink($filename);
		
		if($deleteAllcheck=='true')
		{	
			$xystart=0;	
			for($zoom=$coord[0];$zoom<=20;$zoom++)//remove all tiles on the higher zoom layers
			{
				$xystart = intdiv($nSubTiles2Del,2);	//calculate the zero point of the 64x64 area to delete
				$SubZTile = TileFromCoord($zoom, $coord[1], $coord[2]); //get the tile numebers from zoom and coordinates
				//fputs($debug,$path."/".$SubZTile['z']."/".($SubZTile['x'])."/".($SubZTile['y']).".png"."\n");
				for($x=0;$x<$nSubTiles2Del;$x++)
				{
					for($y=0;$y<$nSubTiles2Del;$y++)
					{
						$filename = $path."/".$SubZTile['z']."/".($SubZTile['x']+$x-$xystart)."/".($SubZTile['y']+$y-$xystart).".png";
						if (file_exists($filename)) unlink($filename);
					}
				}
			}
		}
	}
	//fclose($debug);
	exit();
}
?>