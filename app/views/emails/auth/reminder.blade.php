<!DOCTYPE html>
<html lang="en-US">
  <head>
    <meta charset="utf-8">
  </head>
  <body>
    <h2>SeAT Password Reset</h2>

    <div>
      To reset your password, complete this form: {{ URL::action('RemindersController@getReset', array($token)) }}.
    </div>
  </body>
</html>