<?php
require_once __DIR__ . '/vendor/autoload.php';


use Kunnu\Dropbox\Dropbox;
use Kunnu\Dropbox\DropboxApp;

// Get your Tokens from Dropbox and update the three next variables
$authorizationToken = "AUTH_TOKEN";

$client_id = "APP_KEY";

$client_secret = "APP_SECRET";

//Configure Dropbox Application
//$app = new DropboxApp("client_id", "client_secret", "access_token");

$app = new DropboxApp($client_id, $client_secret, $authorizationToken);


//Configure Dropbox service
$dropbox = new Dropbox($app);

$listFolderContents = $dropbox->listFolder("/Images");

//Fetch Items
$items = $listFolderContents->getItems();

//All Items
$all = $items->all();

// Array to store image file names
$imgs = []; 

foreach ($all as $key => $value) {
    $imgs[] = $value->getDataProperty('path_lower');
}

sort($imgs);  // sorting by ascending name

$count = count($imgs);  // number of images

//var_dump($imgs); 

$links = [];

$alts = [];  // for img alt attribute

foreach ($imgs as $img) {
    $temporaryLink = $dropbox->getTemporaryLink($img);
    // Add Link to array
    $links[] = $temporaryLink->getLink();

    $alts[] = substr( rtrim($img, ".png"), strpos($img, "_") + 1); 	
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dropbox</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.1/css/bulma.min.css">
    <script type="text/javascript" src="https://www.dropbox.com/static/api/2/dropins.js" id="dropboxjs"
            data-app-key="APP_KEY"></script>
            <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
<style>

.carousel{
    background-color:#2a2a2a;
    margin-top: 20px;
}
.carousel-item{
    text-align: center;
    min-height: 280px; /* Prevent carousel from being distorted if for some reason image doesn't load */
}
.bs-example{
	margin: 20px;
}
img {
    margin-top: 20px;
  max-height:200px;
}

</style>
</head>
<body>

<section class="section">
    <div class="container">
        <h1 class="title has-text-primary">Dropbox</h1>

        <div class="tile is-ancestor">
            <div class="tile is-parent">
                <article class="tile is-child box">
                    <h2 class="title">Carrousel</h2>
                    <div id="openDropbox"></div>
                    <article class="message is-success" id="selected-link">
                        <div class="message-header">
                            <p>Success: Selected Link</p>
                        </div>
                        <div class="message-body">
                            <a href="" id="link"></a>
                        </div>
                    </article>
                </article>

            </div>
        </div>



        <div class="bs-example">

        
        <div id="myCarousel" class="carousel slide" data-ride="carousel">
        <!-- Carousel indicators -->
        <ol class="carousel-indicators">
            <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
            <?php 
           for ($i = 1; $i < $count; $i++) {
              echo "<li data-target='#myCarousel' data-slide-to='".$i."'></li>\n";
           } 
        ?>

        </ol>
        <!-- Wrapper for carousel items -->
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="<?= $links[0] ?>" alt="<?= $alts[0] ?>">
            </div>

            <?php
           for ($i = 1; $i < $count; $i++) {    
              echo "<div class='carousel-item'>\n";
              echo "<img src='$links[$i]' alt='$alts[$i]'>\n";
               echo "</div>\n";
           }
        ?>
        </div>
        <!-- Carousel controls -->
        <a class="carousel-control-prev" href="#myCarousel" data-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </a>
        <a class="carousel-control-next" href="#myCarousel" data-slide="next">
            <span class="carousel-control-next-icon"></span>
        </a>
    </div>
</div>

</div>


</section>

<script>

    let selectedLink = document.getElementById("selected-link");
    selectedLink.style.display = "none";
    options = {

        // Required. Called when a user selects an item in the Chooser.
        success: function(files) {
            selectedLink.style.display = "block";
            let link = document.getElementById('link');
            link.innerHTML = files[0].link;
            link.href = files[0].link;
        },

        // Optional. Called when the user closes the dialog without selecting a file
        // and does not include any parameters.
        cancel: function() {

        },

        // Optional. "preview" (default) is a preview link to the document for sharing,
        // "direct" is an expiring link to download the contents of the file. For more
        // information about link types, see Link types below.
        linkType: "preview", // or "direct"

        // Optional. A value of false (default) limits selection to a single file, while
        // true enables multiple file selection.
        multiselect: false, // or true

        // Optional. This is a list of file extensions. If specified, the user will
        // only be able to select files with these extensions. You may also specify
        // file types, such as "video" or "images" in the list. For more information,
        // see File types below. By default, all extensions are allowed.
        extensions: ['.pdf', '.doc', '.docx', '.png'],

        // Optional. A value of false (default) limits selection to files,
        // while true allows the user to select both folders and files.
        // You cannot specify `linkType: "direct"` when using `folderselect: true`.
        folderselect: false, // or true

        // Optional. A limit on the size of each file that may be selected, in bytes.
        // If specified, the user will only be able to select files with size
        // less than or equal to this limit.
        // For the purposes of this option, folders have size zero.
        //sizeLimit: 1024, // or any positive number
    };

    var button = Dropbox.createChooseButton(options);
    document.getElementById("openDropbox").appendChild(button);

</script>
</body>
</html>