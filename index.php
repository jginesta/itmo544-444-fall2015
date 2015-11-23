<?php session_start(); ?>
<html>
<head><title>Hello app</title>
</head>
<body style="background-color:LemonChiffon">

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
<hr />
<!-- The data encoding type, enctype, MUST be specified as below -->
<form enctype="multipart/form-data" action="gallery.php" method="POST">
Enter Email of user for gallery to browse: <input type="email" name="email"><br />
<input type="submit" value="Load Gallery" />
</form>

<form enctype="multipart/form-data" action="MP2Subscription.php" method="POST">
Enter Email of user to subscribe: <input type="text" name="email"/><br />
Confirm subscription <input type="submit" value="Subscribe">
</form>

</body>
</html>


















