<?php session_start();?>
<html>
<head>
<link href="bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet">
<script src="bootstrap/3.3.6/js/bootstrap.min.js"></script> 
<script type="text/javascript" src="bootstrap/3.3.6/js/jquery.min.js"></script>
<script src="http://www.youtube.com/player_api"></script> 
<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    

<style>




#div0 {
  border-radius: 15px;
  border: 2px solid #000000;
  padding: 20px; 
  width: 800px;
}

.spinner {
  margin: 100px auto 0;
  width: 70px;
  text-align: center;
}

.spinner > div {
  width: 18px;
  height: 18px;
  background-color: #333;

  border-radius: 100%;
  display: inline-block;
  -webkit-animation: sk-bouncedelay 1.4s infinite ease-in-out both;
  animation: sk-bouncedelay 1.4s infinite ease-in-out both;
}

.spinner .bounce1 {
  -webkit-animation-delay: -0.32s;
  animation-delay: -0.32s;
}

.spinner .bounce2 {
  -webkit-animation-delay: -0.16s;
  animation-delay: -0.16s;
}

@-webkit-keyframes sk-bouncedelay {
  0%, 80%, 100% { -webkit-transform: scale(0) }
  40% { -webkit-transform: scale(1.0) }
}

@keyframes sk-bouncedelay {
  0%, 80%, 100% { 
    -webkit-transform: scale(0);
    transform: scale(0);
  } 40% { 
    -webkit-transform: scale(1.0);
    transform: scale(1.0);
  }
}


#div1 {
  border-radius: 15px;
  border: 2px solid #00bfff ;
  padding: 20px; 
  width: 1000px;
  height:900px;
}


#progress-bar{
    position: relative;
    width: 300px;
    height: 20px;
    cursor: pointer;
}


#initial{
display:none;
}

#playbtns{
display:none;
}
#loading{
display:none;
}
#avatar_wrapper{
display:none;
}

#video {

  width: 480px;
  height: 270px;

  background-color: transparent;
  pointer-events: none;
  
}
#video1 {

  width: 480px;
  height: 270px;

  background-color: transparent;
  pointer-events: none;
  
}
</style>

<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Create and manage video caption tracks</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet">
<script src="js/ie-emulation-modes-warning.js"></script>
<link href="https://fonts.googleapis.com/css?family=Baloo" rel="stylesheet"> 
<link href="css/custom.css" rel="stylesheet">
<script type="text/javascript" src="js/jquery.min.js"></script>
<script src="bootstrap/3.3.6/js/bootstrap.min.js"></script> 

<meta http-equiv="Access-Control-Allow-Origin" content="*">
<meta http-equiv="Access-Control-Allow-Methods" content="GET">
<link rel="stylesheet" href="css/cwasa.css">
<script type="text/javascript" src="avatar_files/allcsa.js"></script>


<link rel="stylesheet" href="font-awesome/4.7.0/css/font-awesome.min.css">
<script type="text/javascript">

var TUavatarLoaded = false;


var avatarbusy = false;

</script>
</head>
<body onload="CWASA.init();">

	

<script>

var SigmlData;
var lookup = {};

$(document).ready(function() {

	var loadingTout = setInterval(function() { 
      if(TUavatarLoaded) {
        clearInterval(loadingTout);
        //console.log("Avatar loaded successfully");

        setTimeout(function() {
         
          
          $(".divCtrlPanel").hide();
          $("#avatar_wrapper").hide();
		   $("#leftSide").hide();
		   //$("#div2").show();
			
        }, 2000);
      }
  }, 2000);
	
	
	


  

  $.getJSON( "SignFiles/signdump.json", function( data ) {
    SigmlData = data;

    // make the lookup table
    for (i = 0, len = SigmlData.length; i < len; i++) {
        lookup[SigmlData[i].w] = SigmlData[i].s;
    }
  });

});  




</script>


<?php
  require_once __DIR__ . '/vendor/autoload.php';
  //session_start();
  
  $htmlBody = <<<END

<div id="div2" style="margin: 0;
    padding: 0;
    text-align: center;">
<form method="POST" enctype="multipart/form-data">
	
   <div id="div0" style="margin: 0 auto;
    text-align: center;
    ">
			<br>
		<label>YouTube URL :</label>
		<br>
	
           
     <input type="url" name="website" id="website" required  style="width: calc(100% - 102px);max-width: 600px;" />
		<br>
		<br>
		
		
    <input type="submit" class="btn btn-danger" value="Search" id="search">
	<br>
	<br>
   </div>
</form>

</div>

  





END;

  
  $REDIRECT_URI = 'http://127.0.0.1/newapi/new.php';
  $TOKEN_FILE   = "token.txt";
  $SCOPES = "https://www.googleapis.com/auth/youtube.force-ssl";
  
  $client = new Google_Client();
  $client->setAuthConfig('credentials.json');
  // Incremental authorization
  $client->setIncludeGrantedScopes(true);
  // Allow access to Google API when the user is not present. 
  $client->setAccessType('offline');
  $client->setRedirectUri($REDIRECT_URI);
  $client->setScopes($SCOPES);
  
  if (isset($_GET['code']) && !empty($_GET['code'])) {
      try {
          // Exchange the one-time authorization code for an access token
          $accessToken = $client->fetchAccessTokenWithAuthCode($_GET['code']);
          
          // Save the access token and refresh token in local filesystem
          file_put_contents($TOKEN_FILE, json_encode($accessToken));
          
          $_SESSION['accessToken'] = $accessToken;
          header('Location: ' . filter_var($REDIRECT_URI, FILTER_SANITIZE_URL));
          exit();
      }
      catch (\Google_Service_Exception $e) {
          print_r($e);
      }
  }
  
  if (!isset($_SESSION['accessToken'])) {
      
      $token = @file_get_contents($TOKEN_FILE);
      
      if ($token == null) {
          
          // Generate a URL to request access from Google's OAuth 2.0 server:
          $authUrl = $client->createAuthUrl();
          
          // Redirect the user to Google's OAuth server
          header('Location: ' . filter_var($authUrl, FILTER_SANITIZE_URL));
          exit();
          
      } else {
          
          $_SESSION['accessToken'] = json_decode($token, true);
          
      }
  }
  
  $client->setAccessToken($_SESSION['accessToken']);
  
  /* Refresh token when expired */
  if ($client->isAccessTokenExpired()) {
      // the new access token comes with a refresh token as well
      $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
      file_put_contents($TOKEN_FILE, json_encode($client->getAccessToken()));
  }
  
  $youtube = new Google_Service_YouTube($client);
  
  if ($client->getAccessToken()) {
  // This code executes if the user enters an action in the form
  // and submits the form. Otherwise, the page displays the form above.
  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$website=isset($_POST['website']) ? $_POST['website'] : null;
    try {
		 

		  $website=($_POST['website']);
          preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $website, $match);
          $videoId = $match[1];
		  
		  
		  
		  
		  
		  
		  $filename = strval($videoId);
		  $ab=$filename.'.srt';
		  
		   $hostname="localhost"; 
		  $username="root";
		  $password="";
		  $conn=mysqli_connect($hostname,$username,$password,"isl_dataset");   
		  if(!$conn)
		  {
			die("Connection cannot be establish: " . mysqli_connect_error());
		  }
		  else
			{
						$result = mysqli_query($conn, "SELECT * FROM captionsdata WHERE caption_name='$ab'") or die("Error: " . mysqli_error($conn));
						$row=mysqli_fetch_array($result);
						
						if(mysqli_num_rows($result) == 1)
							{
								$b=$row['caption_path'];

									?>
							    <style type="text/css">#playbtns{
								display:block;
								}
								#div2{
								display:none;
								}
								
								</style>
													
								<?php	
							  
							}
							
							
							else{
							
								$captions = listCaptions($youtube, $videoId, $htmlBody);
							    if (empty($captions)) {
								$htmlBody .= "<br><h5 align='center'>Video caption tracks not available in Englsih.</h5>";
								 goto a;
							    } else{								  
								$firstCaptionId = $captions['id'];								
								downloadCaption($youtube, $firstCaptionId, $htmlBody, $videoId);
								$filename=strval($videoId);
							    $b='captions/'.$filename.'.srt';
								?>
							    <style type="text/css">#playbtns{
								display:block;
								}
								#div2{
								display:none;
								}
								
								</style>
													
								<?php	

								}	
							}
															
								
					          //  mysqli_close($conn);							
		    }
			
			
		  

	


			
			
//function parsesrt($b)
{	  
			
		define('SRT_STATE_SUBNUMBER', 0);
		define('SRT_STATE_TIME',      1);
		define('SRT_STATE_TEXT',      2);
		define('SRT_STATE_BLANK',     3);

		$lines   = file($b);
		
		$subs    = array();
		$state   = SRT_STATE_SUBNUMBER;
		$subNum  = 0;
		$subText = '';
		$subTime = '';

		$html="";
		$elapsed=array();

		foreach($lines as $line) {
			switch($state) {
				case SRT_STATE_SUBNUMBER:
					$subNum = trim($line);
					$state  = SRT_STATE_TIME;
					break;

				case SRT_STATE_TIME:
					$subTime = trim($line);
					$state   = SRT_STATE_TEXT;
					break;

				case SRT_STATE_TEXT:
					if (trim($line) == '') {
						$sub = new stdClass;
						$sub->number = $subNum;
						list($sub->startTime, $sub->stopTime) = explode(' --> ', $subTime);
						
						
						
						$unformatted_start = $sub->startTime;
						$unformatted_end = $sub->stopTime;

						// Split into hh:mm:ss and milliseconds
						$start_array = explode(',', $unformatted_start);
						$end_array = explode(',', $unformatted_end);

						// Convert hh:mm:ss to DateTime
						$start  = new DateTime($start_array[0]);
						$end = new DateTime($end_array[0]);

						// Convert to time in seconds (PHP >=5.3 only)
						$start_in_seconds = $start->getTimestamp();
						$end_in_seconds = $end->getTimestamp();

						// Convert to milliseconds, then add remaining milliseconds
						$start_in_milliseconds = ($start_in_seconds * 1000) + $start_array[1];
						$end_in_milliseconds = ($end_in_seconds * 1000) + $end_array[1];

						// Calculate absolute value of the difference between start and end
						//$elapsed[] = abs($start_in_milliseconds - $end_in_milliseconds);
						
						$sub->startTimes = $start_in_milliseconds;
						$sub->stopTimes = $end_in_milliseconds;

					
						
						
						
						/*
						$splits =  explode(",",$sub->startTime);

						$sub->startTime = $splits[0];
						$sub->startMil = $splits[1];

						$splits =  explode(",",$sub->stopTime);
						
						$sub->stopTime = $splits[0];
						$sub->stopMil = $splits[1];
						*/
						
						$sub->text   = $subText;
						$subText     = '';
						$state       = SRT_STATE_SUBNUMBER;

						$subs[]      = $sub;
					} else {
						$subText .= $line;
					}
					break;
			}
		}

		if ($state == SRT_STATE_TEXT) {
			// if file was missing the trailing newlines, we'll be in this
			// state here.  Append the last read text and add the last sub.
			$sub->text = $subText;
			$subs[] = $sub;
		}

		//print_r(($subs));
		$txt=array();
		$time=array();
		$time1=array();

		$zero = "00:00:00";
		$zero  = new DateTime($zero);
		$start_in_seconds = $zero->getTimestamp();
		$start_in_milliseconds = ($start_in_seconds * 1000);





		$difference_in_second = ($subs[0]->startTimes) - ($start_in_milliseconds);
		array_push($time1,$difference_in_second);


		for ($x = 0; $x < sizeof($subs); $x++) {
			$txt[]=$subs[$x]->text;
			$difference_in_seconds = ($subs[$x]->stopTimes) - ($subs[$x]->startTimes);
			array_push($time,$difference_in_seconds);	
		} 

		for ($x = 1; $x < sizeof($subs); $x++) {
			$difference_in_secondss = ($subs[$x]->startTimes) - ($subs[$x-1]->stopTimes);
			array_push($time1,$difference_in_secondss);
		}
	
		$txt1=array();
		$newsig="";
		for ($x = 0; $x < sizeof($subs); $x++) {
			$newsig = preg_replace("/[^A-Za-z0-9]/",' ',$subs[$x]->text);
			$txt1[]=$newsig;
		} 



		  $hostname="localhost"; 
		  $username="root";
		  $password="";
		  $conn=mysqli_connect($hostname,$username,$password,"isl_dataset");   
		  if(!$conn)
		  {
			die("Connection cannot be establish: " . mysqli_connect_error());
		  }
		  else
			{						
									$sig="";
									$html="";
									$len=0;
									$img=array();
									for($i= 0;$i<sizeof($txt);$i++)
									{
										$html="";
										$sig=$txt[$i];
										$newsig = preg_replace("/[^A-Za-z0-9]/",' ',$sig);
										$len=strlen($newsig);
										for($y=0; $y<$len; $y++)
										{
											$result = mysqli_query($conn, "SELECT * FROM fingerspelling WHERE image_name='$newsig[$y]'") or die("Error: " . mysqli_error($conn));
											$row=mysqli_fetch_array($result);
											$html .= "<img src='".$row['image_path']."'>";  									
										}
										array_push($img,$html);
									}
			} 
			//mysqli_close($conn);
}

	
  }
    
     catch (Google_Service_Exception $e) {
      $htmlBody .= sprintf('<br><h5 align="center">Please Check the URL or try again after sometime.</h5>');
		
		  
		  
		 // '<p>A service error occurred: <code>%s</code></p>',htmlspecialchars($e->getMessage())
		  
		  
		  
    } catch (Google_Exception $e) {
      $htmlBody .= sprintf('<br><h5 align="center">Please Check the URL or try with differnt URL.</h5>');
		  
		  
		  //'<p>An client error occurred: <code>%s</code></p>',htmlspecialchars($e->getMessage())

    }
  }
  
}			
			


  a:


function listCaptions(Google_Service_YouTube $youtube, $videoId, &$htmlBody) {
  // Call the YouTube Data API's captions.list method to retrieve video caption tracks.
  $captions = $youtube->captions->listCaptions("snippet", $videoId);
  $encaption="";

 // $htmlBody .= "<h3>Video Caption Tracks</h3><ul>";
  foreach ($captions as $caption) {
	if($caption['snippet']['language'] == "en"){
    //$htmlBody .= sprintf('<li>%s(%s) in %s language</li>', $caption['snippet']['name'],
      //  $caption['id'],  $caption['snippet']['language']);
		$encaption=$caption;
		
	}
  }
  //$htmlBody .= '</ul>';

  return $encaption;
}


 

function downloadCaption(Google_Service_YouTube $youtube, $captionId, &$htmlBody, $videoId) {
    // Call the YouTube Data API's captions.download method to download an existing caption.
    $captionResouce = $youtube->captions->download($captionId, array(
        'tfmt' => "srt",
        'alt' => "media"
    ));

    //$htmlBody .= "<h2>Downloaded caption track</h2><ul>";
   // $htmlBody .= sprintf('<li>%s</li>',
	$response=$captionResouce->getBody()->getContents();
   // $htmlBody .= '</ul>';
    $filename = strval($videoId);
	$myfile = fopen("captions/".$filename.".srt", "w");
	//$myfile = fopen("testfile.srt", "w");
	fwrite($myfile, $response);
    fclose($myfile);
	$hostname="localhost"; 
		 $username="root";
		  $password="";
		  $conn1=mysqli_connect($hostname,$username,$password,"isl_dataset");   
		 if(!$conn1)
		  {
			die("Connection cannot be establish: " . mysqli_connect_error());
		  }
		 else
			{
				$a1=$filename.'.srt';
			  $b1='captions/'.$filename.'.srt';
				$sql = "INSERT INTO captionsdata (caption_name, caption_path) VALUES ('$a1','$b1' )";
				mysqli_query($conn1, $sql);
			}
			mysqli_close($conn1);
}

?>



  <?=$htmlBody?>
  
  
  
  <div id="playbtns" style="margin: 0; padding: 0; text-align: center;">
  
		<button id="hider" type="button" class="btn btn-danger btn-lg">Back To HomePage</button>
		<br>
		<br>
		<br>
		<br>
		<br>
		<button id="hider1" type="button" class="btn btn-success btn-lg">Play Video With Finger Spelling</button>
		<br>
		<br>
		<button id="hider2"  type="button" class="btn btn-primary btn-lg">Play Video In Sign Language</button>
		<br>
		<br>
  
  </div>
  
  
  
  
  <div id="initial" style="margin: 0; padding: 0; text-align: center; display:none;">
		
		<div style="width:1000px; margin:0 auto;">
		<br>
		<div id="div1" style="text-align: center;">
		<br>
		
		<iframe id="video"  src="https://www.youtube.com/embed/<?php echo $videoId; ?>?modestbranding=1&rel=0&color=white&controls=0&mute=1&enablejsapi=1&html5=1&cc_load_policy=3" frameborder="0" allowTransparency="true"></iframe>
		
		<br>
		<br>
	
		<div class="buttons">
		<button class="btn btn-primary" id="play-button" >PLAY</button>
		<button style="display:none;" class="btn btn-primary" id="pause-button">PAUSE</button>
		<button style="display:none;" class="btn btn-primary" id="stop-button">STOP</button>
		<i id="mute-toggle" class="material-icons">volume_off</i>
		<input id="volume-input" type="range" max="100" min="0">
		
		<br>
		<br>
		
		<select id="speed">
                <option>0.25</option>
                <option>0.5</option>
                <option selected="selected">1</option>
            </select>
			<button style="" class="btn btn-primary" id="slow-button">Speed</button>
			<br>
			<br>
			<div id="para" style="display:none">
		<p ><span id="current-time"></span> / <span id="duration"></span></p>
		<br>
		<input type="range" id="progress-bar" value="0">
		</div>
		<br>
		
		<br>
		
	    </div>
		
		
	
		
		
		<!-- <button id="btnplay" type="button" class="btn btn-danger btn-lg">Play</button> -->
		 <br>
		 
		 
		 
		<p id='changeText'></p>
		<br>
		<div id='changeImg'></div>
		</div>
		<br>
		<br>
		</div>
		
		
		</div>






  
  
  
  

  
  
  <br>
  <br>
  <br>

  
  
  	<div style="text-align:center">


	<div class="spinner" style="display:none;">
	  <div class="bounce1"></div>
	  <div class="bounce2"></div>
	  <div class="bounce3"></div>
	</div>
	<p id="wait" style="display:none;" >Please Wait ...Loading Resourses</p>
</div>
  

	

<div style="width: 900px; margin:0 auto; padding: 0; text-align: center;">

	<div  id="avatar_wrapper" style=" text-align: center;display:none; float:left;">
	



    <div id="div3" style="margin: 0; padding: 0; text-align: center;">
      <div class="CWASAPanel av0" style="pointer-events: none;" align="center">
        <div class="divAv av0 " style="pointer-events: none;" >
          <canvas class="canvasAv av0" style="pointer-events: none;" width="374" height="403"></canvas>
        </div> 
      </div>
      <div id="currentWord" class="alert alert-warning" style="width:374"></div>
    </div>
	<br>
	<div id="pic" style="display:none;"></div>
	<br>
		<br>

	<br>
		<br>
			<br>
		<br>
	
    </div>	
		
		
  <div id="vid" style="width:500px; display:none; float:right;">
  
  	<iframe id="video1"  src="https://www.youtube.com/embed/<?php echo $videoId; ?>?modestbranding=1&rel=0&color=white&controls=0&mute=1&enablejsapi=1&html5=1&cc_load_policy=3" frameborder="0" allowTransparency="true"></iframe>
	
	<br>
			<br>
		<p id="para1" style="display:none;"><span id="current-time1"></span> / <span id="duration1"></span></p>
		<br>
	
			    <div id="leftSide" style="display: none;">

            
            <button type="button" id="playeng" class="btn btn-primary">Play</button> <button type="button" id="pauseng" class="btn btn-primary">Pause</button> <button type="button" id="stopeng" class="btn btn-primary">Stop</button><i id="mute-toggle1" class="material-icons">volume_off</i>
		<input id="volume-input1" type="range" max="100" min="0">


         
           </div>
  
  
  </div>
  
</div>

	
		
		


<script src="ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type="text/javascript">

		var txt2 = <?php echo json_encode($txt1); ?>;
		var tme = <?php echo json_encode($time); ?>;
		var tme1 = <?php echo json_encode($time1); ?>;
		var txt = <?php echo json_encode($txt); ?>;
		var image = <?php echo json_encode($img); ?>;
		
var input="";
var data="";





 for(var i=0;i < txt2.length;i++){
	//input = $("#engtext").val().trim().replace(/\r?\n/g, ' '); // change newline to space while reading
	input=input+txt2[i]
  }
  inputs=input.trim().replace(/\r?\n/g, ' '); // change newline to space while reading
  var res = inputs.split(" ");
  
  function useless(value){
	  return value != "";
  }
  
  var resnew=res.filter(useless);
  res=resnew;
		
		
		
		var time=[];
		for(var i=0;i < tme.length;i++){
		    var t = parseInt(tme[i]);
			var p = t;
			time.push(p);
		}
		
		var time1=[];
		for(var i=0;i < tme1.length;i++){
		    var t = parseInt(tme1[i]);
			var p = t;
			time1.push(p);
		}


		
		
		
		
		
		
		
		
		document.getElementById("changeText").innerHTML = "";
		
			

	var cnt = 0;
		var j=1;





		var stop2 =true;
		var start = true;
		var a=true;
	
			
	
	var cleartimer=0;
	
	
	
	
	
	
	


	
	
	
	
	
	
	
	
	
	
	
		
		
		// global variable for the player
	var player;
	var player1;
	// this function gets called when API is ready to use
	function onYouTubePlayerAPIReady() {
		
		
	  // create the global player from the specific iframe (#video)
	  player = new YT.Player('video', {
	    events: {
	      // call this function when player is ready to use
	      'onReady': onPlayerReady
	    }
	  });
	  
	  	  player1 = new YT.Player('video1', {
	    events: {
	      // call this function when player is ready to use
	      'onReady': onPlayerReady
	    }
	  });
	  
	  
	}

	function onPlayerReady(event) {
		
	
		
		    // Update the controls on load
    updateTimerDisplay();
	 updateProgressBar();


    // Clear any old interval.
    //clearInterval(time_update_interval);

    // Start interval to update elapsed time display and
    // the elapsed part of the progress bar every second.
    time_update_interval = setInterval(function () {
        updateTimerDisplay();
		 updateProgressBar();

    }, 1000)
	
	
	
	
	
		
		
		
		
		
		
			 player1.setPlaybackRate(0.25);
		
		var playButton1 = document.getElementById("playeng");
	  playButton1.addEventListener("click", function() {
		 console.log("Started parsing");
		 $(this).hide();
	     $("#pauseng").show();
         $("#stopeng").show();
		 $("#para1").show();
         stop1=true;
		   playsub();
		   
		  player1.playVideo();
		 
	 
		  
	  });
	  
	  var pauseButton1 = document.getElementById("pauseng");
	  pauseButton1.addEventListener("click", function() {
		  $(this).hide();
		  $("#playeng").show();
		  $("#stopeng").hide();
		  stop1=false;
		  stop=true;
	    player1.pauseVideo();
	  });
	  
	  
	  	  var stopButton1 = document.getElementById("stopeng");
	  stopButton1.addEventListener("click", function() {
		 stop=false;
			stop1=false;
		  $(this).hide();
		  $("#pauseng").hide();
		  $("#playeng").show();
		  $("#para1").hide();
		
		 
	      player1.stopVideo();
	  });
	  
	  
	  		$('#mute-toggle1').on('click', function() {
    var mute_toggle1 = $(this);

    if(player1.isMuted()){
        player1.unMute();
        mute_toggle1.text('volume_up');
    }
    else{
        player1.mute();
        mute_toggle1.text('volume_off');
    }
});


 $('#volume-input1').on('change', function () {
    player1.setVolume($(this).val());
});
		
		
		
		
		
		
		
		
		
		
		
	  // bind events
	  var playButton = document.getElementById("play-button");
	  playButton.addEventListener("click", function() {
		  stop2 =true;
		  start = true;
		  updateTimerDisplay();
	 updateProgressBar();


    // Clear any old interval.
    //clearInterval(time_update_interval);

    // Start interval to update elapsed time display and
    // the elapsed part of the progress bar every second.
    time_update_interval = setInterval(function () {
        updateTimerDisplay();
		 updateProgressBar();

    }, 1000)
	
		  $("#play-button").hide();
		  $("#stop-button").show();
		  $("#pause-button").show();
		  $("#para").show();
		  if(a == true){
			   cnt=0;
		  j=1;
		 		setTimeout(function() {
			
		if(time1[0]==0){
			document.getElementById("changeText").innerHTML = txt[0];
			document.getElementById("changeImg").innerHTML = image[0];
			run();
		}else{			
		   setTimeout(function() {
		   document.getElementById("changeText").innerHTML = txt[0];
		   document.getElementById("changeImg").innerHTML =image[0] ;
		   run();
		  }, time1[0]);  
		}
		},1500);
		a=false;
	    player.playVideo();
	 }else{
		 document.getElementById("changeText").innerHTML = "";
			document.getElementById("changeImg").innerHTML ="" ;
		
	start = true;
		  stop2=true;
		  player.playVideo();
		   run();
	 }
		  
	  });
	  
	  var pauseButton = document.getElementById("pause-button");
	  pauseButton.addEventListener("click", function() {
		  $("#play-button").show();
		  $("#stop-button").hide();
		  $("#pause-button").hide();
		  stop2=false;
		  start=true;
	    player.pauseVideo();
	  });

	  var stopButton = document.getElementById("stop-button");
	  stopButton.addEventListener("click", function() {
		  $("#play-button").show();
		  $("#stop-button").hide();
		  $("#pause-button").hide();
		  $("#para").hide();
		  stop2=false;
		  start=false;
		 
	      player.stopVideo();
	  });
	  
	  
	  
	  
		$('#mute-toggle').on('click', function() {
    var mute_toggle = $(this);

    if(player.isMuted()){
        player.unMute();
        mute_toggle.text('volume_up');
    }
    else{
        player.mute();
        mute_toggle.text('volume_off');
    }
});


 $('#volume-input').on('change', function () {
    player.setVolume($(this).val());
});
	  
	  var slowButton = document.getElementById("slow-button");
	  slowButton.addEventListener("click", function() {
		 // clearTimeout(cleartimer);
		  var slow=$("#speed").val();
		  slow=parseFloat(slow);

	  if(slow == 0.5){
		  time=[];
		  time1=[];

		  for(var i=0;i < tme.length;i++){
		    var t = parseInt(tme[i]);
			var p = t*2;
			time.push(p);
		
		}
				
	
		for(var i=0;i < tme1.length;i++){
		    var t = parseInt(tme1[i]);
			var p = t*2;
			time1.push(p);
			
		}
		  
	  }
	  
	  
	    if(slow == 0.25){
			 time=[];
		  time1=[];
			   

		  for(var i=0;i < tme.length;i++){
		    var t = parseInt(tme[i]);
			var p = t*4;
			time.push(p);
			
		}
		

		for(var i=0;i < tme1.length;i++){
		    var t = parseInt(tme1[i]);
			var p = t*4;
			time1.push(p);
			
		}
		  
	  }
	  
	    if(slow == 1){
			 time=[];
		  time1=[];

		  for(var i=0;i < tme.length;i++){
		    var t = parseInt(tme[i]);
			var p = t;
			time.push(p);
		}
		

		for(var i=0;i < tme1.length;i++){
		    var t = parseInt(tme1[i]);
			var p = t;
			time1.push(p);
		}
		  
	  }
	  		  player.setPlaybackRate(slow);
		 
	
	  });
	  
	  
	  
	  
	  
	  
	  
	  		
		function formatTime(time){
    time = Math.round(time);

    var minutes = Math.floor(time / 60),
    seconds = time - minutes * 60;

    seconds = seconds < 10 ? '0' + seconds : seconds;

    return minutes + ":" + seconds;
}


function updateTimerDisplay(){
    // Update current time text display.
    $('#current-time1').text(formatTime( player1.getCurrentTime() ));
    $('#current-time').text(formatTime( player.getCurrentTime() ));
    $('#duration1').text(formatTime( player1.getDuration() ));
    $('#duration').text(formatTime( player.getDuration() ));
  }
		

// This function is called by initialize()
function updateProgressBar(){
    // Update the value of our progress bar accordingly.
    $('#progress-bar').val((player.getCurrentTime() / player.getDuration()) * 100);
}

	  
	  
	  
	
	  
	  
	  
	}

	// Inject YouTube API script
	var tag = document.createElement('script');
	tag.src = "https://www.youtube.com/player_api";
	var firstScriptTag = document.getElementsByTagName('script')[0];
	firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

$('#progress-bar').on('mousedown', function (e) {
	stop2=false;
	start=false;
	var ab = 0;
	var b = 0;
	var c = false;
	var d = 0;
	var e = 0;
	clearTimeout(cleartimer);
});


$('#progress-bar').on('mouseup touchend', function (e) {
	cnt=0;
	j=0;
	
	var i=1;
	
	
		
	a=true;
		


	 stop2=false;
	 start=false;
clearTimeout(cleartimer);
    // Calculate the new time for the video.
    // new time in seconds = total duration in seconds * ( value of range input / 100 )
    var newTime = player.getDuration() * (e.target.value / 100);

    // Skip video to new time.
    player.seekTo(newTime);
	
	document.getElementById("changeText").innerHTML = "";
	document.getElementById("changeImg").innerHTML ="" ;
	
	var ab = Math.trunc( newTime*1000 );
	var b = 0;
	var c = false;
	var d = 0;
	var e = 0;

	for (i = 0; i < ((time.length*2)); i++) {
		 if((ab < b) || (ab==b))
		 {
			if(d==e){
				var g = (e-1);//get value of e
				c=true;
				var f=b-time[d-1];
				b=0;
				break;
			}else{
				var g = (d-1);//get value of d
				c=false;
				var f=b-time1[e-1];
				b=0;
				break;
			}
		 }else if(c==false){
			 b= b+time1[e];
			 e= e+1;
			 c= true;
		 }else if(c==true){
			 b = b +time[d];
			 d = d+1;
			 c = false;
		 } 	
     }
	 stop2=true;
	 start=true;
	 if(c==true){
			
		  j = g+1;
		 cnt = g;
		 //alert(cnt);
		 //alert(j);
		  if(cnt== -1){
			 j=0;
			 cnt =0;
		 }
		 document.getElementById("changeText").innerHTML = "";
		 document.getElementById("changeImg").innerHTML = "";
		 clearTimeout(cleartimer);
		 setTimeout(function() {
			clearTimeout(cleartimer);
		 run();
		 }, (ab-f));  
	 }else{
		
		  j = g+1;
	     cnt = g;
		 if(cnt== -1){
			 j=0;
			 cnt=0;
		 }
		 //alert(cnt);
		 //alert(j);
		 document.getElementById("changeText").innerHTML = txt[j];
		 document.getElementById("changeImg").innerHTML = image[j];
		 clearTimeout(cleartimer);
		 setTimeout(function() {
			 clearTimeout(cleartimer);
		 run();
		  }, (ab-f)); 
	 }
});	
		

		
		
		
		
		
	
		
		
		

	
	
		
		function run() {
			clearTimeout(cleartimer);
			if(stop2==true){
		cleartimer =  window.setTimeout(function() {

		  if(time1[j]== 0){
			  document.getElementById("changeText").innerHTML = txt[j];
			  document.getElementById("changeImg").innerHTML = image[j] ;
			  j++;
			  
		  }else{
			  document.getElementById("changeText").innerHTML = "";
			  document.getElementById("changeImg").innerHTML ="" ;
			  setTimeout(function() {
			  document.getElementById("changeText").innerHTML = txt[j];
			  document.getElementById("changeImg").innerHTML = image[j];
			  j++;
			 }, time1[j]);  
			}
			cnt++;
			if (cnt < txt.length-1) {	
						run();
				}		
			 }, time[cnt]);
		}
		
		else if(start == false){
			clearTimeout(cleartimer);
			for(var i=0; i < cleartimer ; i+=1) { 
						clearTimeout(i);
					}
			
			document.getElementById("changeText").innerHTML = "";
			document.getElementById("changeImg").innerHTML ="" ;
			clearInterval(cleartimer);
			
			console.log("in stop mode");
			a=true;
			start=true;
			stop2=true;
			return;
			
		}else{
			clearTimeout(cleartimer);
				for(var i=0; i < cleartimer ; i+=1) { 
						clearTimeout(i);
					}
				clearInterval(cleartimer);
				console.log("in pause mode");
				return;
  }
			
		}	
	
		
		if(cnt == txt.length-1){
			
			document.getElementById("changeText").innerHTML = "";
			document.getElementById("changeImg").innerHTML ="";
			
		}

























		
		var stop =true;
		var stop1 =true;
		
		$("#hider").click(function(){
			$(this).hide();
			$("#initial").hide();
			$("#div2").show();
			$("#leftSide").hide();
			$("#playbtns").hide();
			$("#avatar_wrapper").hide();
			$("#vid").hide();
			$(".spinner").hide();
		   $("#wait").hide();
		    stop =false;
		    stop1 =false;
		
	  });	
	  
	  
	  
	  $("#hider1").click(function(){
		  document.getElementById("changeText").innerHTML = "";
			document.getElementById("changeImg").innerHTML ="";
			
		   $(this).hide();
		   $("#stopeng").click();
		   $("#initial").show();
		   $("#hider2").show();
		   $("#avatar_wrapper").hide();
		   $("#vid").hide();
		   $("#leftSide").hide();
		   $(".spinner").hide();
		   $("#wait").hide();
		     stop =false;
		     stop1 =false;
     });
	
	 $("#hider2").click(function(){
		$("#stop-button").click();
		  $(this).hide();
         stop=true;
		 $("#initial").hide();
		   $("#hider1").show();
		   $(".spinner").show();
		   $("#wait").show();
		   console.log("should be displayed");
		   $("#currentWord").text("");
		  $("#hider").attr("disabled", true);
		  $("#hider1").attr("disabled", true);
		 setTimeout(function(){
		   $(this).hide();
		   $("#hider").attr("disabled", false);
		   $("#hider1").attr("disabled", false);
		   $(".spinner").hide();
		   $("#wait").hide();
		   $("#avatar_wrapper").show(); 
		   $("#vid").show(); 
		   $("#leftSide").show();
		   $("#playeng").show();
		   $("#pauseng").hide();
		   $("#stopeng").hide();
		 },2000);
		   
     });
	 
	
	
		
		
			
		
	
	
	
	
	
	
	var count=0;
  

  
  
  function playsub(){
	  //$(".txtLogSpeed").val("+2.0");
	  if(stop1==true){
	var playtimeout1 =  window.setTimeout(function() {
	 input = res[count];
     if(input.length == 0)
       return;
     input = input.toLocaleLowerCase();
     $.getJSON( "lemmatizer/lemstem.php?l=" + input, function(data) {
    

   
     playseq = Array();
     for(i = 0; i < data.length; i++)
	 { playseq.push(data[i]);}
	 
    
     if(data.length > 0) {
      var playtimeout = setInterval(function() {

          if(playseq.length == 0 || data.length == 0) {
            clearInterval(playtimeout);
            
            avatarbusy=false;
            return;
          }

          if(avatarbusy == false) {
            avatarbusy = true; 

            word2play = playseq.shift();    
     
            if(lookup[word2play]==null) {
       
              avatarbusy=false;
              
           var string ="";
              
			 for (var i = 0; i <word2play.length; i++){
				 var add=".png'>";
				 var sub= "<img src=\'images\\";
				 string = string +sub+word2play[i] + add; 
			 }
			 $("#pic").show();
			 document.getElementById("currentWord").innerHTML = word2play;
			 document.getElementById("pic").innerHTML =string;
			 window.setTimeout(function() {
				$("#pic").hide();
			 },1200);
			 return;
			 
            

            } else {
			
              data2play = lookup[word2play];
              console.log(word2play);
			  //$(".txtLogSpeed").val("+2.0");
              $("#currentWord").text(word2play);
              $(".txtaSiGMLText").val(data2play);
              $(".bttnPlaySiGMLText").click();
			  
			  
            }
          } 
      }, 1500);
    
	 }
  });
	 
  count++;
			if (count < res.length) {	
						playsub();
				}		
	 }, 1800);
  
  }
  else if(stop==false){
	  count=0;
	  clearInterval(playtimeout);
	  clearInterval(playtimeout1);
	  console.log("in stop mode");
	  return;
  }else{
	  
	  clearInterval(playtimeout);
	  clearInterval(playtimeout1);
	 console.log("in pause mode");
	  return;
  }
  }
  
  
  
  
  
  
  
  
 
/*
var input="";
  $("#playeng").click(function() {
  $(this).hide();
  $("#pauseng").show();
  $("#stopeng").show();
  stop1=true;
  console.log("Started parsing");
  playsub();
});


  $("#pauseng").click(function(){
		  $(this).hide();
		  $("#playeng").show();
		  $("#stopeng").hide();
		  stop1=false;
		  stop=true;
	
		});
		
		$("#stopeng").click(function(){
			stop=false;
			stop1=false;
		  $(this).hide();
		  $("#pauseng").hide();
		  $("#playeng").show();
		
	
		});
	
*/













		
		
		
		
</script>




</body>
</html>