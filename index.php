<!DOCTYPE>

<HTML>

<head>

    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <!--<meta name="description" content="Welcome to cidzor">-->

    <title>/r/frugalmalefashion listener - cidzor</title>

    <link href="../../assets/style/style.css" rel="stylesheet" type="text/css" />
	
	<link href='http://fonts.googleapis.com/css?family=Roboto' rel='stylesheet' type='text/css'>
	<link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
	<link href='http://fonts.googleapis.com/css?family=Oswald' rel='stylesheet' type='text/css'>
	<link href='http://fonts.googleapis.com/css?family=Slabo+27px' rel='stylesheet' type='text/css'>
	
	<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
	<link rel="icon" href="/favicon.ico" type="image/x-icon">

	<script src="/assets/jquery-1.10.0.min.js"></script>
	
	<script>
	function playSound() {
		var sample = document.getElementById("tracker_alarm");
		sample.play();
		sample.Play();
	}
	
	var tick = 0;
	function tickUp() {
		if(tick == 30) tick = 0;
		if(tick == 5) getInfo();
		
		if(tick % 3 == 0) $("#running").animate({opacity: 0});
		else $("#running").animate({opacity: 1});
		
		tick++;
		setTimeout(function(){tickUp();}, 1000);
	}
	
	function getInfo() {
		var last = $(".tracker_entry_wrapper:first").attr('id');
		if(isNaN(last)) last = Math.floor(Date.now() / 1000)-60;
		var rand = Math.floor((Math.random() * 100) + 1);
		
		var get = $.ajax({
			url: "update.php?last="+last+"&r="+rand,
			success: function(result){
				$(result).filter("*").hide().prependTo("#tracker").fadeIn(1000);
				if(result != "") {
					playSound();
				}
			}
		});	
	}
	</script>

</head>



<body onload="tickUp()" class="tracker">
	<div id="tracker" style="text-align:center;">
		
		<audio id="tracker_alarm" style="display:none" src="bell.mp3" preload="auto"></audio>

		<div class="tracker_entry_wrapper" style="cursor:pointer" onclick="playSound()">
			Currently listening to reddit.com/r/frugalmalefashion.
			<br><br>
			About: This page will automatically update as new posts are made. No refreshes are required out of you. 
			With each new update, a message will pop up and a sound will be played. All you need to do is to keep this page open in 
			the background and keep an ear out for a distinct bell tolling.
			<br><br>
			<span id="running">listening...</span>
		</div>
		
		<div class="tracker_entry_wrapper">
			<!--omitted ad script-->
		</div>
		
		<br>

</div>
<br><br>
<br><br>
	
	
	
</div>


</body>

</html>
