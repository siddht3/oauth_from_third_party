 <?php
$servername = "localhost";   //put your server name here,sql of course
$username = "admin";  //your username that can acess the sql server and database
$password = "YOSHI";  //your sql password
$dbname = "gapi";   //database name
$mysqli = new mysqli($servername, $username, $password,$dbname);
if ($mysqli->connect_error) 
{
    die("Connection failed: " . $conn->connect_error);
} 
else
{
 //echo "sucessfully connected to db ".$dbname;
}
?>
