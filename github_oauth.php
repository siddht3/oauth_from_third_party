<?php

	require('http.php');
	require('oauth_client.php');
    require('_db_connect.php');
	$client = new oauth_client_class;
	$client->debug = false;
	$client->debug_http = true;
	$client->server = 'github';
	$client->redirect_uri = 'http://'.$_SERVER['HTTP_HOST'].
		dirname(strtok($_SERVER['REQUEST_URI'],'?')).'/login_with_github.php';

	$client->client_id = ''; $application_line = __LINE__;
	$client->client_secret = '';

	if(strlen($client->client_id) == 0
	|| strlen($client->client_secret) == 0)
		die('Please go to github applications page '.
			'https://github.com/settings/applications/new in the API access tab, '.
			'create a new client ID, and in the line '.$application_line.
			' set the client_id to Client ID and client_secret with Client Secret. '.
			'The Callback URL must be '.$client->redirect_uri);

	/* API permissions
	 */
	$client->scope = 'user:email';
	if(($success = $client->Initialize()))
	{
		if(($success = $client->Process()))
		{
			if(strlen($client->authorization_error))
			{
				$client->error = $client->authorization_error;
				$success = false;
			}
			elseif(strlen($client->access_token))
			{
				$success = $client->CallAPI(
					'https://api.github.com/user',
					'GET', array(), array('FailOnAccessError'=>true), $user);
			}
		}
		$success = $client->Finalize($success);
	}
	if($client->exit)
		exit;
	if($success)
	{
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>github OAuth client results</title>
</head>
<body>
<?php
		echo '<h1>', HtmlSpecialChars($user->login),
			' , you have logged in successfully with github!</h1>';
		//echo '<pre>', HtmlSpecialChars(print_r($user, 1)), '</pre>';

$ip = $_SERVER['REMOTE_ADDR'];
$_login=$user->login;
$_id=$user->id;
$_au=$user->avatar_url;
$_url=$user->url;
$_hurl=$user->html_url;
$_email=$user->email;
$sql = "INSERT INTO `git` (`serial`,`ip`, `user`, `date`, `time`,`id`,`avatar_url`,`url`,`html_url`,`email`) VALUES (NULL,'$ip','$_login', CURRENT_DATE(), CURRENT_TIME(),'$_id','$_au','$_url','$_hurl','$_email')";


if (mysqli_query($mysqli, $sql))
 {
   echo "true";
} else {
    echo "Error: " . $sql . "<br>" . mysqli_error($mysqli);
}
$conn->close();			

?>
</body>
</html>
<?php
	}
	else
	{
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>OAuth client error</title>
</head>
<body>
<h1>OAuth client error</h1>
<pre>Error: <?php echo HtmlSpecialChars($client->error); ?></pre>
</body>
</html>
<?php
	}

?>