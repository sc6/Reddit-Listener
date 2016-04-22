<?

//variables
$gracePeriod = 30; 		//number of seconds to wait until requesting reddit's server.
$speed = 3; 			//cap on new submissions displayed in a single update.

//connecting to database
$servername = "localhost";
$username = "root";
$password = "";

// Create connection
$conn = mysqli_connect($servername, $username, $password);

// Check connection
if (!$conn) 
	die("Connection failed: " . mysqli_connect_error());


//Get the most recent 'updated_at' in 'log'
$query = "SELECT updated_at FROM `reddit-listener`.log ORDER BY updated_at DESC LIMIT 1,1";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
mysqli_free_result($result);

//If grace period has passed, get new data from their server.
if(time() > ($row['updated_at'] + $gracePeriod))
{
	//update time
	//$query = //[omitted]
	//mysqli_query($conn, $query);
	
	//Check their page for new content.
	$url = "http://www.reddit.com/r/netsec/new/.json";
	$crawl = file_get_contents($url);
	$crawl = json_decode($crawl, true);
	
	for($i = 0; $i < 3; $i++)
	{
		//$id = $crawl["data"]["children"][$i]["data"]["id"];
		$url = $crawl["data"]["children"][$i]["data"]["url"];
		$author = $crawl["data"]["children"][$i]["data"]["author"];
		$num_comments = $crawl["data"]["children"][$i]["data"]["num_comments"];
		$permalink = "http://www.reddit.com/" . $crawl["data"]["children"][$i]["data"]["permalink"];
		$title = $crawl["data"]["children"][$i]["data"]["title"];
		$title = str_replace("'", "", $title);
		//$timestamp = $result["data"]["children"][$i]["data"]["created"];
		$domain = $crawl["data"]["children"][$i]["data"]["domain"];
		//$image_source = $crawl["data"]["children"][$i]["data"]["preview"]["images"][0]["resolutions"][0]["url"];
	
		//check if url already exists in our database
		$query = "SELECT * FROM `reddit-listener`.log WHERE url='$permalink'";	//TODO use prepared statements
		//echo $query;
		$result = mysqli_query($conn, $query);
		//var_dump($result);
		if(mysqli_num_rows($result) != 0) break;
		mysqli_free_result($result);	
		
		//add url to database (if this point is reached, there's no copy in database)
		$query = "INSERT INTO `reddit-listener`.log (url, updated_at) VALUES ('$permalink', " . time() . ")";
		//echo $query;
		mysqli_query($conn, $query);
		
		printPost($title, $domain, $url, $permalink, $author, $num_comments);
	}
} 
/*
else //Grace period has not passed.
{
	$lastTimestamp = $_GET['last'];
	//$lastTimestamp = 1435800234;
	
	//get all posts after last update (up to 10)
	$query = //[omitted]
	$result = mysqli_query($conn, $query);
	
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
		//print	?>
		<div class="tracker_entry_wrapper" id="<?=time()?>">
			<table><tr>
			<?if($image_source == "") {?>
			<td>
				<img class="tracker_entry_image" src="<?=$image_source?>"></img>
				<br><br>
			</td> 
			<?}?>
			<td>
				<span class="tracker_entry_title"><?=$row['title']?></span>
				<br>
				<span class="tracker_entry_domain"><?=$row['domain']?></span>
				<br><br>
				<strong><a href="<?=$row['url']?>" style="font-size:16px;">direct link</a></strong> - <a href="<?=$row['permalink']?>">comments</a>
				<span class="tracker_entry_author">
				submitted by <?=$row['author']?>
				</span>
			</td></tr></table>
		</div>
		<br>
	<?}
	mysqli_free_result($result);
}
*/





//Prints reddit post in HTML.
function printPost($title, $domain, $url, $permalink, $author, $num_comments)
{
	echo ""
	."<div class='tracker_entry_wrapper' id='" . time() . "'>"
	."		<table><tr>";

	/*
	if($image_source == "")
	{
	
	echo ""
	."		<td>"
	."			<img class='tracker_entry_image' src='$image_source'></img>"
	."			<br><br>"
	."		</td>";
	
	}
	*/
	
	echo ""
	."		<td>"
	."			<span class='tracker_entry_title'>$title</span>"
	."			<br>"
	."			<span class='tracker_entry_domain'>$domain</span>"
	."			<br><br>"
	."			<strong><a href='<?=$url?>' style='font-size:16px;'>direct link</a></strong> - <a href='$permalink'>comments ($num_comments)</a>"
	."			<span class='tracker_entry_author'>"
	."			submitted by $author"
	."			</span>"
	."		</td></tr></table>"
	."	</div>"
	."	<br>";
}

