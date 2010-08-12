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

$key = isset($_COOKIE['VASSAL_login']) ? $_COOKIE['VASSAL_login'] : '';

$headline = isset($_POST['headline']) ? $_POST['headline'] : '';
$text = isset($_POST['text']) ? $_POST['text'] : '';

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

  submit_story($username, $headline, $text);

  print_top($title);
  echo '<p>Your news item has been submitted. It will appear in the news feed after approval by the news editor.</p>';
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

#
# This is a shim for submitting items to phpns
#
function submit_story($username, $headline, $text) {
  $ch = curl_init();

  $cfile = tempnam('/tmp', 'cookies');

  # login
  $url = 'http://localhost/news/login.php?do=p';

  require_once('util/newsbot-config.php');

  $data = array(
    'username' => NEWSBOT_USERNAME,
    'password' => NEWSBOT_PASSWORD,
    'remember' => 0
  );

  curl_setopt_array($ch, array(
    CURLOPT_URL            => $url,
    CURLOPT_HEADER         => false,  # don't need it
    CURLOPT_RETURNTRANSFER => true,   # prevent printing
    CURLOPT_COOKIEJAR      => $cfile,
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => $data,
    CURLOPT_FOLLOWLOCATION => false   # 302 is expected result
  ));

  curl_exec($ch);
  if (curl_errno($ch) != 0) {
    throw new ErrorException('curl: ' . curl_error($ch));
  }
  
  # phpns redirects to index.php on success, so check for 302
  $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  if ($code != 302) {
    throw new ErrorException("login failed: $code");
  }

  # post an article
  $url = 'http://localhost/news/article.php?do=p';

  $data = array(
    'article_title'    => $headline,
#    'article_subtitle' => '',
    'article_cat'      => 'all',
    'article_text'     => $text,
#    'article_exptext'  => '',
#    'image'            => '',
#    'start_date'       => '',
#    'end_date'         => '',
#    'acchecked'        => '0',
#    'achecked'         => '0',
  );

  curl_setopt_array($ch, array(
    CURLOPT_URL            => $url,
    CURLOPT_HEADER         => false,  # don't need it
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_COOKIEFILE     => $cfile,
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => $data,
    CURLOPT_FOLLOWLOCATION => false
  ));

  $result = curl_exec($ch);
  if (curl_errno($ch) != 0) {
    throw new ErrorException('curl: ' . curl_error($ch));
  }

  # check that posting succeeded 
  if (strpos($result, 'Article Success') === false) {
    throw new ErrorException('posting failed');
  }

  # logout
  $url = 'http://localhost/news/login.php?do=logout';

  curl_setopt_array($ch, array(
    CURLOPT_URL            => $url,
    CURLOPT_HEADER         => false,  # don't need it
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_COOKIEFILE     => $cfile,
    CURLOPT_HTTPGET        => true,
    CURLOPT_FOLLOWLOCATION => false   # 302 is expected result
  ));

  $result = curl_exec($ch);
  if (curl_errno($ch) != 0) {
    throw new ErrorException('curl: ' . curl_error($ch));
  }

  # cleanup
  if (!unlink($cfile)) {
    die("failed to delete $cfile");
  }

  curl_close($ch);
}

?>
