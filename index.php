<?php echo '<?xml version="1.0" encoding="UTF-8"?>' ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head profile="http://www.w3.org/2005/10/profile">
  <link rel="stylesheet" type="text/css" href="/style.css"/>
  <link rel="stylesheet" type="text/css" href="/site.css"/>
  <link rel="alternate" type="application/rss+xml" title="RSS" href="" />
  <link rel="icon" type="image/png" href="/images/VASSAL.png"/>
  <title>VASSAL</title>
</head>
<body>
<div id="vassal-header">
  <div id="vassal-logo">
    <a href="/index.php"><img src="/images/header.png"/></a>
  </div>
  <?php virtual('/navigation.shtml'); ?>

  <div id="vassal-login">
<?php
  $key = $_COOKIE['VASSAL_login'];
  if (!empty($key)) { 
    require_once('sso/AuthDB.php');
  
    try {
      $auth = new AuthDB();
      $username = $auth->user_for_cookie($key);
      print '<a href="logout.php?returnto=/index.php">Log out</a>';
    }
    catch (ErrorException $e) {
      print '<a href="login.php?returnto=/index.php">Log in</a>';
    }
  }
  else {
    print '<a href="login.php?returnto=/index.php">Log in</a>';
  }
?>
  </div>
</div>

<div id="content">
  <div class="content_box_left">
    <div id="vassal-about">
      <h1>What is <acronym>Vassal</acronym>?</h1>
      <p><acronym>Vassal</acronym> is a game engine for building and playing online adaptations of board games and card games. It allows users to play in real time over the Internet, or by email. <acronym>Vassal</acronym> runs on all platforms, and is free, open-source software.</p>
      <p>Click <a href="foo">here</a> to learn more about <acronym>Vassal</acronym>.</p>

      <table class="screenshot">
        <tr>
          <td><a href="/images/screenshot1.png"><img src="/thumbs/screenshot1.png" /></a></td>
          <td><a href="/images/screenshot2.png"><img src="/thumbs/screenshot2.png" /></a></td>
        </tr>
      </table>
  
      <h1>What Games Are There?</h1>
      <p>Hundreds of boardgames have been converted for use with <acronym>Vassal</acronym>, so there's a good chance that you'll find the games you own in our <a href="">module library</a> already. If there is not yet a <acronym>Vassal</acronym> module for your favorite game, you can use the <acronym>Vassal</acronym> Editor to build your own module, and should you run into trouble, help is only a click away in our <a href="forum/">forum</a>.</p>

      <table class="screenshot">
        <tr>
          <td><a href="/images/screenshot2.png"><img src="/thumbs/screenshot2.png" /></a></td>
          <td><a href="/images/screenshot1.png"><img src="/thumbs/screenshot1.png" /></a></td>
        </tr>
      </table>
    </div>
  </div>

  <div class="content_box_right">
    <div id="vassal-download">
      <h1>Get <acronym>Vassal</acronym></h1>
      <p><acronym>Vassal</acronym> is free, open-source software, created by volunteers. Our current release is 3.1.0.</p> 
      <div class="dlbutton">
        <?php
          include 'inc/download-detect.php';
          echo "<a href=\"$download_url\"><span>Download <acronym>Vassal</acronym><br/>$version$download_os</span><img src=\"/images/button.download.png\"/></a>";
        ?>
      </div>
      <div class="dlbutton">
        <a href=""><span>Support <acronym>Vassal</acronym></span><img src="/images/paypal_donate.png"/></a>
      </div>
      <p><acronym>Vassal</acronym> is free, but development and hosting are not. If you enjoy using <acronym>Vassal</acronym>, please consider helping us with these costs.</p>
    </div>

    <div id="vassal-help">
      <h1>Get Help</h1>
      <p>Need help using <acronym>Vassal</acronym>? Stuck while creating a module? If you can't find an answer in our extensive <a href="">documentation</a>, check our friendly <a href="forum/">forum</a> for help.</p>
    </div>

    <div id="vassal-contribute">
      <h1>Get Involved</h1>
      <p>The <acronym>Vassal</acronym> project is run by volunteers and makes progress by the efforts of volunteers. Is there a feature you'd like to see in the next release? Did you find a bug? Request that feature or report the bug <a href="bugzilla/">here</a>. Are you a programmer? We could use your help. Join us in the <a href="">developers' forum</a>. Not a programmer? Help us improve our <a href="">documentation</a>.</p>
    </div>
  </div>

  <div class="content_box_full" id="vassal-news">
    <h1>Latest News <a href="news.php"><img src="/images/feed-icon-14x14.png"/></a></h1>
    <ul class="news">
      <li class="day">
        <div class="date">Feb<br/>1</div>
        <ul class="events">
          <li><a href="">This is a news item in the future</a></li>
        </ul>
      </li>
      <li class="day">
        <div class="date">Jan<br/>31</div>
        <ul class="events">
          <li><a href=""><acronym>Vassal</acronym> 3.1.0 released</a></li>
          <li><a href=""><acronym>Vassal</acronym> has a new web site</a></li>
          <li><a href="">This is a test news item</a></li>
          <li><a href="">No news is good news</a></li>
        </ul>
      </li>
      <li class="day">
        <div class="date">Jan<br/>30</div>
        <ul class="events">
          <li><a href="">Monsterpocalypse 1.01 released</a></li>
        </ul>
      </li>
      <li class="day">
        <div class="date">Jan<br/>26</div>
        <ul class="events">
          <li><a href=""><acronym>Vassal</acronym> 3.1.0-beta8 released</a></li>
          <li><a href="">Zombies are eating my brain</a></li>
        </ul>
      </li>
      <li class="day">
        <div class="date">Jan<br/>25</div>
        <ul class="events">
          <li><a href=""><acronym>Vassal</acronym> 3.1.0-beta8 released</a></li>
          <li><a href="">Zombies are eating my brain</a></li>
        </ul>
      </li>
      <li class="day">
        <div class="date">Jan<br/>24</div>
        <ul class="events">
          <li><a href="">Star Wars Tactics Version 1.4 released</a></li>
          <li><a href="">This is a very, very long news item, put here just to test what happens when we have a very, very long news item</a></li>
        </ul>
      </li>
    </ul>
    <em><a id="more-news" href="">...more news</a></em>
  </div>
</div>

<?php virtual('/footer.shtml'); ?>
</body>
</html>
