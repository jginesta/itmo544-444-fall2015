
<?php
session_start();
require 'vendor/autoload.php';
#$email = $_POST["email"];
#echo $email;


use Aws\Rds\RdsClient;
#$client = RdsClient::factory(array(
#'profile' => '<profile in your aws credentials file>',
$client = new Aws\Rds\RdsClient([
 'version' => 'latest',
 'region'  => 'us-east-1'
]);

$result = $client->describeDBInstances([
    'DBInstanceIdentifier' => 'mp1-jgl',
]);

$endpoint = "";
#print_r (getPath('DBInstances'));
#print_r (getPath('DBInstances/*/Endpoint/Address'));
#print_r (getPath('DBInstances/*/Endpoint/Address'));
$endpoint = $result['DBInstances'][0]['Endpoint']['Address'];

#foreach ($endpoint as $ep) {
    // Do something with the message
 #   echo "============". $ep . "================";
  #  $endpoint = $ep;
#}   
#foreach ($result->getPath('DBInstances/*/Endpoint/Address') as $ep) {
 #   // Do something with the message
  #  echo "============". $ep . "================";
   # $endpoint = $ep;
#}   
//echo "begin database";
$link = mysqli_connect($endpoint,"controller","letmein888","customerrecords") or die("Error " . mysqli_error($link));

/* check connection */
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}

//below line is unsafe - $email is not checked for SQL injection -- don't do this in real life or use an ORM instead
#$link->real_query("SELECT * FROM jgldata WHERE email = '$email'");
$link->real_query("SELECT * FROM jgldata");
$res = $link->use_result();
<!DOCTYPE html>
<html lang="de">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
	<title>Gallery</title>
	<link href='http://fonts.googleapis.com/css?family=Slabo+27px' rel='stylesheet' type='text/css'>
	<link href='../dist/simplelightbox.min.css' rel='stylesheet' type='text/css'>
	<link href='demo.css' rel='stylesheet' type='text/css'>
</head>
<body>
	<div class="container">
		<h1 class="align-center">Jessicas Gallery</h1>
		<div class="gallery">	
			<a href="images/image1.jpg" class="big"><img src="images/thumbs/thumb1.jpg" alt="" title="Beautiful Image" /></a>
			<a href="images/image2.jpg"><img src="images/thumbs/thumb2.jpg" alt="" title=""/></a>
			<a href="images/image3.jpg"><img src="images/thumbs/thumb3.jpg" alt="" title="Beautiful Image"/></a>
			<a href="images/image4.jpg"><img src="images/thumbs/thumb4.jpg" alt="" title=""/></a>
			<div class="clear"></div>
			
			<a href="images/image5.jpg"><img src="images/thumbs/thumb5.jpg" alt="" title=""/></a>
			<a href="images/image6.jpg"><img src="images/thumbs/thumb6.jpg" alt="" title=""/></a>
			<a href="images/image7.jpg" class="big"><img src="images/thumbs/thumb7.jpg" alt="" title=""/></a>
			<a href="images/image8.jpg"><img src="images/thumbs/thumb8.jpg" alt="" title=""/></a>
			<div class="clear"></div>
			
			<a href="images/image9.jpg" class="big"><img src="images/thumbs/thumb9.jpg" alt="" title=""/></a>
			<a href="images/image10.jpg"><img src="images/thumbs/thumb10.jpg" alt="" title=""/></a>
			<a href="images/image11.jpg"><img src="images/thumbs/thumb11.jpg" alt="" title=""/></a>
			<a href="images/image12.jpg"><img src="images/thumbs/thumb12.jpg" alt="" title=""/></a>
			<div class="clear"></div>
			
		</div>
	
		<br><br>
			
	</div>
	<div class="links">
	<?php
               
               	while ($row = $res->fetch_assoc()) 
               	{
               	echo '<a href="'. $row['s3rawurl'] .'" title="'. $row['filename'] .'" data-gallery ><img src="' . $row['s3rawurl'] . '" width="100" height="100" ></a>';  
                  }
               	$link->close();
                         
               ?>	
	</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
<script type="text/javascript" src="../dist/simple-lightbox.js"></script>
<script>
	$(function(){
		var gallery = $('.gallery a').simpleLightbox();
	});
</script>
</body>
</html>	

            <a href="http://upload.wikimedia.org/wikipedia/commons/thumb/6/6b/Oxbow_Bend_outlook_in_the_Grand_Teton_National_Park.jpg/800px-Oxbow_Bend_outlook_in_the_Grand_Teton_National_Park.jpg">
                <img 
                    src="http://upload.wikimedia.org/wikipedia/commons/thumb/6/6b/Oxbow_Bend_outlook_in_the_Grand_Teton_National_Park.jpg/100px-Oxbow_Bend_outlook_in_the_Grand_Teton_National_Park.jpg",
                    data-big="http://upload.wikimedia.org/wikipedia/commons/thumb/6/6b/Oxbow_Bend_outlook_in_the_Grand_Teton_National_Park.jpg/1280px-Oxbow_Bend_outlook_in_the_Grand_Teton_National_Park.jpg"
                    data-title="Oxbow Bend outlook"
                    data-description="View over the Snake River to the Mount Moran with the Skillet Glacier."
                >
            </a>
            <a href="http://upload.wikimedia.org/wikipedia/commons/thumb/c/c9/Hazy_blue_hour_in_Grand_Canyon.JPG/800px-Hazy_blue_hour_in_Grand_Canyon.JPG">
                <img 
                    src="http://upload.wikimedia.org/wikipedia/commons/thumb/c/c9/Hazy_blue_hour_in_Grand_Canyon.JPG/100px-Hazy_blue_hour_in_Grand_Canyon.JPG",
                    data-big="http://upload.wikimedia.org/wikipedia/commons/thumb/c/c9/Hazy_blue_hour_in_Grand_Canyon.JPG/1280px-Hazy_blue_hour_in_Grand_Canyon.JPG"
                    data-title="Hazy blue hour"
                    data-description="Hazy blue hour in Grand Canyon. View from the South Rim."
                >
            </a>
            <a href="http://upload.wikimedia.org/wikipedia/commons/thumb/f/f4/2909_vallon_moy_res.jpg/800px-2909_vallon_moy_res.jpg">
                <img 
                    src="http://upload.wikimedia.org/wikipedia/commons/thumb/f/f4/2909_vallon_moy_res.jpg/100px-2909_vallon_moy_res.jpg",
                    data-big="http://upload.wikimedia.org/wikipedia/commons/thumb/f/f4/2909_vallon_moy_res.jpg/1280px-2909_vallon_moy_res.jpg"
                    data-title="Haute Severaisse valley"
                    data-description="View of Haute Severaisse valley and surrounding summits from the slopes of Les Vernets."
                >
            </a>
            <a href="http://upload.wikimedia.org/wikipedia/commons/thumb/4/46/Bohinjsko_jezero_2.jpg/800px-Bohinjsko_jezero_2.jpg">
                <img 
                    src="http://upload.wikimedia.org/wikipedia/commons/thumb/4/46/Bohinjsko_jezero_2.jpg/100px-Bohinjsko_jezero_2.jpg",
                    data-big="http://upload.wikimedia.org/wikipedia/commons/thumb/4/46/Bohinjsko_jezero_2.jpg/1280px-Bohinjsko_jezero_2.jpg"
                    data-title="Bohinj lake"
                    data-description="Bohinj lake (Triglav National Park, Slovenia) in the morning."
                >
            </a>
            <a href="http://upload.wikimedia.org/wikipedia/commons/thumb/3/32/Bowling_Balls_Beach_2_edit.jpg/800px-Bowling_Balls_Beach_2_edit.jpg">
                <img 
                    src="http://upload.wikimedia.org/wikipedia/commons/thumb/3/32/Bowling_Balls_Beach_2_edit.jpg/100px-Bowling_Balls_Beach_2_edit.jpg",
                    data-big="http://upload.wikimedia.org/wikipedia/commons/thumb/3/32/Bowling_Balls_Beach_2_edit.jpg/1280px-Bowling_Balls_Beach_2_edit.jpg"
                    data-title="Bowling Balls"
                    data-description="Mendocino county, California, USA."
                >
            </a>
        </div>

        
    </div>

    <script>

    // Load the classic theme
    Galleria.loadTheme('js/galleria.classic.min.js');

    // Initialize Galleria
    Galleria.run('#galleria');

    </script>
    </body>
</html>




