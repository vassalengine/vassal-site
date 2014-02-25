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

  # check for header injection in headline
  if (preg_match('/\r|\n|%0A|%0D/i', $headline)) {
    throw new ErrorException('Headline contains prohibited characters.');
  }

  # check for blank text
  if (empty($text)) {
    throw new ErrorException('Empty text.');
  }

  # check cookie
  if (empty($key)) {
    throw new ErrorException('Please log in before submitting news.');
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
# This is a shim for submitting items to WordPress
#
function submit_story($username, $title, $text) {
  require_once('/usr/share/wordpress/wp-load.php');

  # check if the user exists in WordPress
  $uid = NULL;
  $user = get_user_by('login', $username);
  if (!$user) {
    # create the user in WordPress if not
    require_once('sso/UserDB.php');

    $udb = new UserDB();
    $result = $udb->search("uid=$username");

    $userdata = array(
      'user_pass'     => md5(microtime()),
      'user_login'    => $username,
      'user_email'    => $result[0]['mail'][0],
      'display_name'  => $username,
      'role'          => 'contributor'
    );

    $result = wp_insert_user($userdata);
    if (is_wp_error($result)) {
      throw new ErrorException(
        'Failed to create WordPress user: ' . $result->get_error_message()
      );
    }
    $uid = $result;
  }
  else {
    $uid = $user->ID;
  }

  # push the post to WordPress
  $post = array(
    'post_title'   => $title,
    'post_content' => $text,
    'post_status'  => 'pending',
    'post_type'    => 'post',
    'post_author'  => $uid
  );

  # NB: wp_insert_post is responsible for sanitizing title and text
  $result = wp_insert_post($post, TRUE);
  if (is_wp_error($result)) {
    throw new ErrorException(
      'Failed to post news item: ' . $result->get_error_message()
    );
  }

  $post_id = $result;

  # notify news editors
  $fields = array('user_email');
  $editors = array_merge(
    get_users(array('role' => 'Administrator', 'fields' => $fields)),
    get_users(array('role' => 'Editor', 'fields' => $fields))
  );

  $subject = 'New post: ' . $title;
  $body = "There is a new VASSAL news post to moderate:\n\n" .
          admin_url("post.php?post=$post_id&action=edit", 'https') . "\n\n" .
          "Title:\n\n" . $title . "\n\n" .
          "Text:\n\n" . $text . "\n";

  foreach ($editors as $e) {
    wp_mail($e->user_email, $subject, $body);
  }
}

?>
