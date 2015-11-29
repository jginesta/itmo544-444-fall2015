<html>
<center>
<head><title>Hello app</title>
</head>
<body style="background-color:LightSkyBlue">
<font face="Comic sans MS" size="6">Welcome to the Imagicka App</font><br />
<font face="Comic sans MS" size="4">Choose to upload the picture or to view the gallery</font><br />
<font face="Comic sans MS" size="3"> Don't forget to subscribe!</font><br /></center>
</html>

<?php
 session_start();
// If the read mode is enabled from the introspection, then only the user can view the gallery
if(strlen($_POST['read'])==0){
 ?>

<html>

<!-- The data encoding type, enctype, MUST be specified as below -->
<form enctype="multipart/form-data" action="result.php" method="POST">
    <!-- MAX_FILE_SIZE must precede the file input field -->
    <input type="hidden" name="MAX_FILE_SIZE" value="3000000" />
    <!-- Name of input element determines name in $_FILES array -->
    Send this file: <input name="userfile" type="file" /><br />
Enter Email of user: <input type="email" name="email"><br />
Enter Phone of user (1-XXX-XXX-XXXX): <input type="phone" name="phone">
<input type="submit" value="Send File" />
</form>

<?php
}
 ?>

<hr />

<!-- The data encoding type, enctype, MUST be specified as below -->
<form enctype="multipart/form-data" action="gallery.php" method="POST">
Enter Email of user for gallery to browse: <input type="email" name="email">
<input type="submit" value="Load Gallery" />
</form>

<form enctype="multipart/form-data" action="MP2Subscription.php" method="POST">
Enter Email of user to subscribe: <input type="text" name="email"/><input type="submit" value="Subscribe">
</form>

<a href= "introspection.php"> Backup your database! <a/>
</body>
</html>


















