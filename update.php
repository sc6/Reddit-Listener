<?

//connecting to database
//[omitted]

// Create connection
$conn = mysqli_connect($servername, $username, $password);

// Check connection
if (!$conn) {
die("Connection failed: " . mysqli_connect_error());
}


//get last update time
$query = //[omitted]
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
mysqli_free_result($result);

//if grace period has passed
if(time() > ($row['time'] + 30)) {
	
	//update time
	$query = //[omitted]
	mysqli_query($conn, $query);
	
	//get content
	$url = "http://www.reddit.com/r/frugalmalefashion/new/.json";
	$crawl = file_get_contents($url);
	$crawl = json_decode($crawl, true);
	
	$speed = 3; //number of expected posts per 30 seconds - should never reach, delete after debug
	for($i = 0; $i < 3; $i++) {
		//$id = $crawl["data"]["children"][$i]["data"]["id"];
		$url = $crawl["data"]["children"][$i]["data"]["url"];
		$author = $crawl["data"]["children"][$i]["data"]["author"];
		$num_comments = $crawl["data"]["children"][$i]["data"]["num_comments"];
		$permalink = "http://www.reddit.com/" . $crawl["data"]["children"][$i]["data"]["permalink"];
		$title = $crawl["data"]["children"][$i]["data"]["title"];
		$title = str_replace("'", "", $title);
		//$timestamp = $result["data"]["children"][$i]["data"]["created"];
		$domain = $crawl["data"]["children"][$i]["data"]["domain"];
		$image_source = $crawl["data"]["children"][$i]["data"]["preview"]["images"][0]["resolutions"][0]["url"];
	
		//check if already exists in database
		$query = //[omitted]
		$result = mysqli_query($conn, $query);
		if(mysqli_num_rows($result) != 0) break;
		mysqli_free_result($result);	
		
		//add to database (verified as non-duplicate)
		$query = //[omitted]
		//echo $query;
		mysqli_query($conn, $query);
		
		//print ?>
		<div class="tracker_entry_wrapper" id="<?=time()?>">
			<table><tr>
			<?if($image_source == "") {?>
			<td>
				<img class="tracker_entry_image" src="<?=$image_source?>"></img>
				<br><br>
			</td> 
			<?}?>
			<td>
				<span class="tracker_entry_title"><?=$title?></span>
				<br>
				<span class="tracker_entry_domain"><?=$domain?></span>
				<br><br>
				<strong><a href="<?=$url?>" style="font-size:16px;">direct link</a></strong> - <a href="<?=$permalink?>">comments (<?=$num_comments?>)</a>
				<span class="tracker_entry_author">
				submitted by <?=$author?>
				</span>
			</td></tr></table>
		</div>
		<br>
	<?
	}
} else {
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
?>
