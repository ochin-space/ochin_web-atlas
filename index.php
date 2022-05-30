<?php 
/* 
* Copyright (c) 2022 perniciousflier@gmail.com  
* This code is a part of the ochin project (https://github.com/ochin-space)
* The LICENSE file is included in the project's root. 
*/ 
session_start();
require 'helper/Config.php';
require 'helper/atlas.php';

if(isset($_SESSION["loggedin"]) && ($_SESSION["loggedin"]==true)) 
{
	if(!isset($_SESSION["mapServer"])) $_SESSION["mapServer"] = 0;
	
    if(isset($_POST['tilesList'])) {
		$_SESSION["mapServer"] = $_POST['mapIndex'];
        tilesList(Config::atlas_tiles_path."/".$_POST['mapName']);
    }
    if(isset($_POST['updateLocalMap'])) {
        updateLocalMap(Config::atlas_tiles_path."/".$_POST['mapName'],$_POST['serverMapURL'],$_POST['newTiles'],$_POST['tiles2remove'],$_POST['deleteAllcheck'],$_POST['downloadSubcheck']);
    }
	
	function getSelectHTML()
	{
		$content = file_get_contents(Config::atlas_tiles_path."/defaultMapServer.json");
		$json = json_decode($content);	
		$select = "";
		for($i=0;$i<sizeof($json->defaultMapServer);$i++)
		{
			$select = $select.'<option>'.$json->defaultMapServer[$i]->name.'</option>';
		}
		return $select;
	}
		
	function getSelectJson()
	{
		$content = file_get_contents(Config::atlas_tiles_path."/defaultMapServer.json");
		return json_encode($content);
	}
?>

<!DOCTYPE html>
<html>
<head>
<link href="css/loader.css" rel="stylesheet">
<script type="text/javascript" src=<?php echo Config::jQueryPath;?>></script> 
<!-- Required meta tags for Bootstrap 5-->
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- Bootstrap CSS -->
<link href=<?php echo Config::bootstrapCSSpath;?> rel="stylesheet">
<!-- Bootstrap js -->
<script src=<?php echo Config::bootstrapJSpath;?>></script>
<!-- Leaflet CSS -->
<link href=<?php echo Config::leafletCSSpath;?> rel="stylesheet">
<!-- Leaflet js -->
<script src=<?php echo Config::leafletJSpath;?>></script>
<!--link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" integrity="sha512-xodZBNTC5n17Xt2atTPuE1HxjVMSvLVW9ocqUKLsCC5CXdbqCmblAshOMAS6/keqq/sMZMZ19scR4PsZChSR7A==" crossorigin=""-->
<!--script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js" integrity="sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA==" rossorigin=""-->
<?php include Config::topbar_path;?>
<title>öchìn</title>
</head>
    <body style="background-color:#f2f2f2;" onload="selectMapServer(<?php echo $_SESSION["mapServer"];?>)">
        <div class="container-xl">
			<div class="row">	
				<div class="col-sm-10">			
					<div class=" display-2 text-dark mt-2 mb-4 font-weight-normal text-center">Local Atlas</div>
				</div>
				<div class="col-sm-2 ">		
					<div class="d-flex justify-content-end mb-2 mt-5" >
						<button type="button" class="d-flex me-4" name="info" style="background-color: Transparent; border: none;"  title="Click for info about this page" data-bs-toggle='modal' data-bs-target='#infoModal'"><img  width="40" height="40" src="icons/info.png"></button>
					</div>
				</div>  
				<div id="loader" class=""></div>	
			</div>      				
			<div class="d-grid gap-2 mb-4">
				<div class="row align-items-center">
					<div class="col col-sm-3">
						<div class="input-group ">
							<button type='button' class='btn btn-sm border secondary' title="Search a location" onclick="searchLocation()">Search</button>
							<input type="text" class="form-control" id="srcLocation" placeholder="Insert the location to search">
						</div>
					</div>				
					<div class="col col-sm-3">
						<select id="MapSelect" class="form-select" onchange="MapSelectOnChange(this)">
							<option selected>Select a Map Server from the list</option>
							<?php echo getSelectHTML();?>
						</select>
					</div>
					<div class="col col-sm-2">
						<button type='button' class='btn btn-sm btn-primary me-4' data-bs-toggle='modal' title="Click to update the local atlas" data-bs-target='#updateModal'>Update the Local Atlas</button>
					</div>	
				</div>
			</div>
			<div class="row p-3 rounded-2" style="background-color:white;">
				<div class="d-flex align-items-center justify-content-center">
					<div id="mapBox" class="my-2" style="height: 800px; width: 100%"></div>
				</div>
			</div>
        </div>


        <div class="modal fade" id="updateModal" tabindex="-1" aria-labelledby="updateModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="updateModalLabel">Are you sure you want to update the local atlas?</h5>
                    </div>
                    <div class="modal-body">
						<p>You will download to the local atlas the new tiles (in yellow) and the local tiles selected (in red) will be removed.</p>
						<p style="color: red;">Attention!</p><p> If you choose to "Delete All", all the tiles under the red ones that you've selected will be also removed!</p>
						<p>Since the number of subtiles to check could be huge, the "Delete All" will remove area of 64x64 tiles on each sublayer.</p>
						<p>If you choose to "Download All", you will also download an area of 4x4 tiles on every Zoomlayer up to 20.</p>
						<p>Since you're zooming deep in a small area, every zoomed area will be centered around the point you clicked (the coordinates and not just the tile).</p>
                    </div>
                    <div class="modal-footer">
						<div class="row">
							<div class="form-check  form-switch">
							  <label class="form-check-label" for="downloadSub" id="downloadSubLabel">Download All the tiles under the selected in yellow.</label>
							  <input class="form-check-input" type="checkbox" id="downloadSub">
							</div>
							<div class="form-check  form-switch">
							  <label class="form-check-label" for="deleteAll" id="deleteAllLabel">Delete All the tiles under the selected in red.</label>
							  <input class="form-check-input" type="checkbox" id="deleteAll">
							</div>
						</div>
						<div class="row">
							<button type='button' class="btn btn-primary col m-3" data-bs-dismiss="modal" onclick="updateLocalMap()">Update</button> 
							<button type="button" class="btn btn-secondary col m-3" data-bs-dismiss="modal">Cancel</button></div>
						</div>
                    </div>
                </div>
            </div>
        </div>
		
		<div class="modal fade" id="infoModal" tabindex="-1" aria-labelledby="infoModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-lg modal-dialog-centered" >
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="editModalLabel">Local Atlas guide</h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					</div>
					<div class="modal-body"  style="height: 80vh; overflow-y: auto;">
						<p><?php include('info.html'); ?></p>
					</div>					
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
					</div>
				</div>
			</div>
		</div>
    </body>
</html>

<script>
var map;
var osm;
var serverMapURL;
var localtiles = [];
var newtiles = [];
var newtilesCoord = [];
var tiles2remove = [];
var tiles2removeCoord = [];

document.getElementById("srcLocation").onkeypress = function(event){
	if (event.keyCode == 13 || event.which == 13){
		searchLocation();
	}
};

function searchLocation()
{
	$.get(location.protocol + '//nominatim.openstreetmap.org/search?format=json&q='+document.getElementById('srcLocation').value, function(data){
		map.flyTo(new L.LatLng(data["0"]["lat"], data["0"]["lon"]),16);
    });	
}

function localtilesList(mapName, mapIndex)
{
	localtiles = [];
	document.getElementById('loader').innerHTML = '<div class="loader"></div>';	
    $.ajax({
        type : "POST",  //type of method
        url  : "index.php",  //your page
        data: "tilesList=1&mapName="+mapName+"&mapIndex="+mapIndex,
        success: function(msg)
        {
			if(msg!="false")
			{
				localtiles = JSON.parse(msg);
			}
			//clean the map and redraw the layers
			if(map)
			{	
				map.off();
				map.remove(); 
			}
			createMap(<?php echo "'".Config::defaultLatitude."'";?>, <?php echo "'".Config::defaultLongitude."'";?>,serverMapURL);
			document.getElementById('loader').innerHTML = '<div class=""></div>';
			document.getElementById('MapSelect').selectedIndex = mapIndex+1;
        },
		error: function(){}
    });
}

function updateLocalMap()
{
	document.getElementById('loader').innerHTML = '<div class="loader"></div>';	
	deleteAllcheck = document.getElementById('deleteAll').checked;
	downloadSubcheck = document.getElementById('downloadSub').checked;
	JSONnewtiles = JSON.stringify(newtilesCoord);
	JSONremovetiles = JSON.stringify(tiles2removeCoord);
    $.ajax({
        type : "POST",  //type of method
        url  : "index.php",  //your page
		processData: false,
        data: "updateLocalMap=1&mapName="+mapName+"&serverMapURL="+serverMapURL+"&newTiles="+JSONnewtiles+"&tiles2remove="+JSONremovetiles+"&deleteAllcheck="+deleteAllcheck+"&downloadSubcheck="+downloadSubcheck,
        success: function(msg)
        {
            location.reload(true);
        },
        error: function() { }
    });
}

function localtileCheck(z,x,y)
{
    var passing = false;
	if(localtiles.length>0)
	{
		localtiles.forEach(function(element) {
			if(element.includes(z) && element.includes(x) && element.includes(y)) {
				passing = true;
			}
		});
	}
    return passing;
}

function newtilesList(tilename,lat,lon,zoom)
{
	isSelected = false;
	isLocal = false;
	toRemove = false;
	//check if the tile is present in local atlas
	localtiles.forEach(function(element,index) {
		if(element.includes(tilename)) {	//if it's prtesent in local add it to the remove list
			isLocal = true;
		}
	});
	//check if the tile is in the new tile selected list
	newtiles.forEach(function(element,index) {
		if(element.includes(tilename)) {	//if it's in the list remove it from the list
			newtiles_index = index;			
			isSelected = true;
		}
	});
	//check if the tile is in the remove tile list
	tiles2remove.forEach(function(element,index) {
		if(element.includes(tilename)) {	//if it's in the list remove it from the list
			toRemove_index = index;					
			toRemove = true;
		}
	});
	
	if(isLocal && toRemove)	//already in local atlas and in the remove list -> remove it from the remove list
	{
		tiles2remove.splice(toRemove_index, 1);
		tiles2removeCoord.splice(toRemove_index, 1);	
	}
	else if(isLocal && !toRemove)	//already in local atlas and not in the remove list -> add it to the remove list
	{
		tiles2remove.push(tilename);
		tiles2removeCoord.push(zoom+"/"+lat+"/"+lon);
	}
	else if(!isLocal && isSelected)	//not in local atlas and is in the download list -> remove it from the upload list
	{
		newtiles.splice(newtiles_index, 1);
		newtilesCoord.splice(newtiles_index, 1);	
	}
	else if(!isLocal && !isSelected) //not in local atlas and not in the download list -> add it to the upload list
	{		
		newtiles.push(tilename);
		newtilesCoord.push(zoom+"/"+lat+"/"+lon);
	}
}

function newtileCheck(z,x,y)
{
    var passing = false;
	if(newtiles.length>0)
	{
		newtiles.forEach(function(element) {
			if(element.includes(z) && element.includes(x) && element.includes(y)) {
				passing = true;
			}
		});
	}
    return passing;
}

function removetileCheck(z,x,y)
{
    var passing = false;
	if(tiles2remove.length>0)
	{
		tiles2remove.forEach(function(element) {
			if(element.includes(z) && element.includes(x) && element.includes(y)) {
				passing = true;
			}
		});
	}
    return passing;
}

if (typeof(Number.prototype.toRad) === "undefined") {
  Number.prototype.toRad = function() {
    return this * Math.PI / 180;
  }
}

function getTileURL(lat, lon, zoom) {
    var xtile = parseInt(Math.floor( (lon + 180) / 360 * (1<<zoom) ));
    var ytile = parseInt(Math.floor( (1 - Math.log(Math.tan(lat.toRad()) + 1 / Math.cos(lat.toRad())) / Math.PI) / 2 * (1<<zoom) ));
    return "" + zoom + "/" + xtile + "/" + ytile;
}

function selectMapServer(i)
{
	var json = JSON.parse(<?php echo getSelectJson(); ?>);
	serverMapURL = json.defaultMapServer[i].url1;
	mapName = json.defaultMapServer[i].name;
	mapName = mapName.replace (/\./g, "_");
	localtilesList(mapName, i);
}

function MapSelectOnChange(selectBox)
{
	if(selectBox.selectedIndex>0) selectMapServer(selectBox.selectedIndex-1);
}

L.GridLayer.ColorGrid = L.GridLayer.extend({
  createTile: function (coords) {
	if(localtileCheck(coords.z,coords.x,coords.y) && !removetileCheck(coords.z,coords.x,coords.y))	//tile present in the local atlas and not to remove
	{
		const tile = document.createElement('div');
		tile.style.outline = '1px solid white';
		tile.style.fontWeight = 'normal';
		tile.style.fontSize = '10pt';
		tile.style.background = 'rgba(0, 255, 0, 0.2)'; //loaded green
		tile.innerHTML = [coords.z, coords.x, coords.y].join('/');
		return tile;
	}
	else if(newtileCheck(coords.z,coords.x,coords.y))	//tile selected to download
	{
		const tile = document.createElement('div');
		tile.style.outline = '1px solid white';
		tile.style.fontWeight = 'normal';
		tile.style.fontSize = '10pt';
		tile.style.background = 'rgba(255, 255, 0, 0.2)'; //to load yellow
		tile.innerHTML = [coords.z, coords.x, coords.y].join('/');
		return tile;
	}
	else if(removetileCheck(coords.z,coords.x,coords.y))	//tile present in the local atlas and selected to remove
	{
		const tile = document.createElement('div');
		tile.style.outline = '1px solid white';
		tile.style.fontWeight = 'normal';
		tile.style.fontSize = '10pt';
		tile.style.background = 'rgba(255, 0, 0, 0.2)'; //to load red
		tile.innerHTML = [coords.z, coords.x, coords.y].join('/');
		return tile;
	}
	
	const tile = document.createElement('div');
	tile.style.outline = '1px solid white';
	tile.innerHTML = [coords.z, coords.x, coords.y].join('/');
	return tile;
  },
});

L.gridLayer.ColorGrid = function (opts) {
  return new L.GridLayer.ColorGrid(opts);
};	

function createMap(lat,lon,url)
{
	map = new L.Map("mapBox", {center: new L.LatLng(lat,lon), zoom: 11, scrollWheelZoom: false});
	map.doubleClickZoom.disable(); 
	osm = new L.TileLayer(url,{
		maxNativeZoom:21,
		maxZoom:21
	});		
	map.options.minZoom = 9;
	
	map.eachLayer(function (layer) {
		map.removeLayer(layer)
	}); 
	map.addLayer(osm);
	map.addLayer(L.gridLayer.ColorGrid());	
	
	map.on('click', function() {
		if (map.scrollWheelZoom.enabled()) 
		{
			map.scrollWheelZoom.disable();
		}
		else 
		{
			map.scrollWheelZoom.enable();
		}
	});
	
	map.on('dblclick', function(e) {
		var newtile = getTileURL(e.latlng.lat, e.latlng.lng, map.getZoom());
		//select/deselect a tile
		newtilesList(newtile,e.latlng.lat, e.latlng.lng, map.getZoom());
		//clean the map and redraw the layers
		map.eachLayer(function (layer) {
			map.removeLayer(layer)
		}); 
		
		map.addLayer(osm);
		map.addLayer(L.gridLayer.ColorGrid());
	})
}

</script>
<?php } else header("Location:../../login.php"); ?>
