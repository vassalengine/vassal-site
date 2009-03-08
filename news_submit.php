<?php
require_once('sso/ssolib.php');

$title = 'Submit VASSAL News';

# check whether this was a submission attempt
if (empty($_POST)) {
  print_top($title);
  print_form('', '');
  print_bottom();
  exit;
}

$key = $_COOKIE['VASSAL_login'];

# sanitize the input
$headline = addslashes($_POST['headline']);
$text = addslashes($_POST['text']);

try {
  # check for blank headline
  if (empty($headline)) {
    throw new ErrorException('Empty headline.');
  }

  # check for blank text
  if (empty($text)) {
    throw new ErrorException('Empty text.');
  }

  # check cookie
   if (empty($key)) {
    throw new ErrorException('No key.');
  }

  require_once('sso/AuthDB.php');  

  $auth = new AuthDB();
  $username = $auth->user_for_cookie($key);


  require_once('sso/NewsDB.php');

  $news = new NewsDB();

  $query = "INSERT INTO news (headline, text) VALUES ('$headline', '$text')";
  $news->write($query);

  print_top($title);
  echo '<p>Item submitted.</p>';
  print_bottom();
  exit;
}
catch (ErrorException $e) {
  print_top($title);
  warn($e->getMessage());
  print_form($headline, $text);
  print_bottom();
  exit;
}

function print_form($headline, $text) {
  print <<<END
<form class="sso_form" action="/news_submit.php" method="post">
  <fieldset>
    <legend>Submit News Item</legend>
    <table>
      <tr>
        <th><label for="headline">Headline:</label></th>
        <td><input type="text" id="headline" name="headline" value="$headline" /></td>
      </tr>
      <tr>
        <th><label for="text">Text:</label></th>
        <td><textarea id="text" name="text" cols="40" rows="10">$text</textarea></td>
      </tr>
      <tr>
        <td></td>
        <td><input type="submit" name="submit" id="submit" value="Submit" /></td>
      </tr>
    </table>
  </fieldset>
</form>
END;
}


?>
