<!DOCTYPE html>
<html lang="en-US">
	<head>
		<meta charset="utf-8">
	</head>
	<body>
		<h2>SeAT Account Creation</h2>

		<div>
			Your email address has been used to register for a new SeAT account.
			To complete the registration, please click this link {{ URL::action('RegisterController@getActivate', array($user_id, $activation_code)) }}.
		</div>
	</body>
</html>