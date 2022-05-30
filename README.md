![Alt text](images/ochin_logo.png?raw=true&=200x "ochin_web")
<h1>ochin_web - Local Atlas</h1>
<p>The "Local Atlas" software addon is designed to be a part of the <a href="https://github.com/ochin-space/ochin_web">ochin_web</a> project.
<p>This tool is intended to simplify the download process of the geographic maps.</p>
The needs to have local maps arises from the possibility of using the device in offline mode, therefore without the possibility of downloading the tiles in runtime mode.</p>
<p>
<h3>How to use the addon</h3>

![Alt text](images/atlas.png?raw=true&=200x "Local Atlas")
In order to use this addon it's necessary to have the internet connection, it's possible to do so by connecting the wifi module to an online access point.
To download the maps locally, proceed as follows:
<ul><li>select the map style from the servers list</li>
<li>find the geographic area of interest on the map</li>
<li>find the zoom level that contains the whole area in a single tile</li>
<li>select the tile</li>
<li>go to the next zoom level and select all the tiles above the area</li>
<li> repeat the previous point up to the maximum zoom level desired</li>
<li>start the download process</li></ul>
</p>
<h3>Getting around the map</h3>
By means of the selection bar it is possible to select one of the map servers present in the list. Each sever provide a map based on a different style.
<p>To add a server to the list it is necessary to modify the "helper/defaultMapServer.json" file present in the software folder.</p>
<p>To move the map, simply drag it while holding down the left mouse button.
<p>To zoom on the map you can use the "+" and "-" buttons at the top left of the map.It is also possible to unlock the zoom function with the mouse wheel by making a single click with the left button. To lock the zoom function make a single click again.</p>
<p>From the search field it is possible to position the map on a specific area on the planet.</p>
<h3>Select and download the tiles</h3>
<p>Once the map has been positioned on the area at the desired zoom level, it is possible to select the tiles to download by double clicking on them and they will turn yellow. This means that they will be downloaded at the next update of the local atlas.</p>
<p>When you select a tile at a zoom level greater than 9, the tiles above it at a lower zoom level, up to level 9, are automatically downloaded as well.
Tiles that are already present in the local atlas have a green color.
To delete one or more tiles (green) from the local atlas, simply double-click on them and they will turn red. This means that they will be deleted at the next update of the local atlas.</p>

![Alt text](images/selections.png?raw=true&=200x "Tiles selections")
<p>It is possible to download the same tiles from different servers, the selection of tiles to update (download or delete) refers only to the selected server.</p>
<p>To start the update process you need to press the "Update the Local Atlas" button and a modal window will open. From this modal window it is possible to select two additional features:</p>
<ul><li>Delete All</li>
<li>Download All</li></ul>
<p>If you choose to "Delete All", all the tiles under the red ones that you've selected will be also removed.</p>
<p>Since the number of subtiles could be huge, the "Delete All" will only remove area of ​​64x64 tiles on each sublayer.
If you choose to "Download All", you will also download an area of ​​4x4 tiles on every Zoomlayer up to 20.</p>
<p>The number of tiles to be downloaded automatically is limited to 4x4 at each zoom level, to avoid downloading a very large number of unnecessary tiles.</p>
<p>Since you're zooming deep in a small area, every zoomed area will be centered around the point you clicked (the coordinates and not just the tile). This means that, for example, if you have selected a tile at zoom level 10 by clicking near the top left corner of the same, the 4x4 areas at subsequent zoom levels will be centered around that point and not in the center of the tile at zoom level 10.</p>

<h3>How to install the "Local Atlas" addon on ochin_web software</h3>
<p>The "Addons Manager" inside of the ochin_web software, is a web tool that has the purpose of managing additional software packages to the ochin_web environment.</p>
<p>By clicking on the "Upload a new Addon" button it is possible to upload a new addon in ".zip" format. Each new addon loaded will be inserted into the table assigned to it, as foreseen by its "install.xml" file.</p>
<pEach addon can be managed by means of the "edit" button. By pressing "edit" a modal diaolog box will be opened.</p>
<p>From the modal dialog you can enable or disable the addon. If the addon is disabled, it remains in the system but is not displayed in the topbar.
By means of a select bar it is possible to choose whether to assign the addon to the "Applications", "Configuration", "Development" or "Topbar". In the last case, the addon will be shown on the zero level in the topbar. From the "name" field you can change the name that will be displayed in the menu, while "folder name" is the path where the addon will be saved, inside of the "/var/www/html/ochin/addons/" folder.</p>
<p>By clicking the "Delete" button, the folder that contain the addon will be delated and the addon removed from the addons database.</p>


<h3>DISCLAIMER</h3>
...part of the DISCLAIMER.md doc present in the root of the project...
<i><ul><li>This software is based on the use of an Apache webserver and is in fact a website. Unlike a normal public website, it must be able to perform operations on the Operating System files with advanced administrator rights. For this to happen, the software uses a background service that exchanges information with the webserver and manages the system files (limited only to the necessary files). In this way the user "www-data", who manages the webserver, does not have direct access to the system. However, this implies an implicit reduction in the security level of the system. The software itself allows the user to perform advanced operations that can compromise the stability of the system. With the aim of increasing security, potentially dangerous operations are limited to local access (from IPs present in the same subnet of the webserver). That said, it is highly inadvisable to make the web interface public, due to the risks associated with having a public website that has access, even if indirect, to the operating system. For these reasons, before using this software it is necessary to understand well what are the objectives for which it was created and its limits.</li>
<li>The software is meant to become a web development platform for Raspberry Pi board-based devices. The basic structure of the software allows you to manage services, the hardware configuration of the Raspberry pi board, kernel modules, networking boards and more. Addons can be installed on the base structure to provide advanced features. The software is therefore suitable for projects where the Raspberry Pi board is used to manage a hardware device, such as a robot or IOT devices. In this case the sw ochin_web is used as a graphical interface to manage the machine, accessible only to the developer or the user of the robot.</li></ul></i>