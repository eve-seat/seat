<!DOCTYPE html>
<html lang="en-US">
  <head>
    <meta charset="utf-8">
  </head>
  <body>
    <h2>SeAT Account Creation</h2>

    <div>
      <p>
        Your email address has been used to register for a new SeAT account.
        To complete the registration, please click this link <br>
        {{ URL::action('RegisterController@getActivate', array($activation_code)) }}
      </p>
      <p>
        If you did not sign up for this service, then you may safely ignore this email.
      </p>

    </div>
  </body>
</html>