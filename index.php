<?php echo '<?xml version="1.0" encoding="UTF-8"?>' ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head profile="http://www.w3.org/2005/10/profile">
  <link rel="stylesheet" type="text/css" href="/style.css"/>
  <link rel="alternate" type="application/rss+xml" title="RSS" href="" />
  <link rel="icon" type="image/png" href="/images/VASSAL.png"/>
  <title>VASSAL</title>
</head>
<body>
<div id="vassal-page">
  <div id="vassal-header">
    <div id="vassal-logo">
      <a href="/index.php"><img src="/images/header.png"/></a>
    </div>

    <div id="vassal-search">
    </div>

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

  <div id="vassal-navigation">
    <ul id="vassal-nav-list">
      <li><a href="download.html">Download</a></li>
  	  <li><a href="">Modules</a></li>
  	  <li><a href="">FAQ</a></li>
	    <li><a href="">Docs</a></li>
	    <li><a href="/forum/">Forum</a></li>
  	  <li><a href="/wiki/">Wiki</a></li>
  	  <li><a href="/bugzilla/">Tracker</a></li>
  	  <li><a href="news.php">News</a></li>
  	  <li><a href="">Help</a></li>
    </ul>
  </div>

  <div id="content">
    <div class="content_box_left">
      <div id="vassal-about">
        <h1>What is <span class="sc">Vassal</span>?</h1>
        <p><span class="sc">Vassal</span> is a game engine for building and playing online adaptations of board games and card games. It allows users to play in real time over the Internet, or by email. <span class="sc">Vassal</span> runs on all platforms, and is free, open-source software.</p>
        <p>Click <a href="foo">here</a> to learn more about <span class="sc">Vassal</span>.</p>

        <table class="screenshot">
          <tr>
            <td><a href="/images/screenshot1.png"><img src="/thumbs/screenshot1.png" /></a></td>
            <td><a href="/images/screenshot2.png"><img src="/thumbs/screenshot2.png" /></a></td>
          </tr>
        </table>
  
        <h1>What Games Are There?</h1>
        <p>Hundreds of boardgames have been converted for use with <span class="sc">Vassal</span>, so there's a good chance that you'll find the games you own in our <a href="">module library</a> already. If there is not yet a <span class="sc">Vassal</span> module for your favorite game, you can use the <span class="sc">Vassal</span> Editor to build your own module, and should you run into trouble, help is only a click away in our <a href="forum/">forum</a>.</p>

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
        <h1>Get <span class="sc">Vassal</span></h1>
        <p><span class="sc">Vassal</span> is free, open-source software, created by volunteers. Our current release is 3.1.0.</p> 
        <div class="dlbutton">
          <div>Download 3.1.0</div>
          <ul class="dllist">
            <li><a href="http://downloads.sourceforge.net/vassalengine/VASSAL-3.1.0-beta8-generic.zip">Linux</a></li>
            <li><a href="http://downloads.sourceforge.net/vassalengine/VASSAL-3.1.0-beta8-windows.exe">Mac OS</a></li>
            <li><a href="http://downloads.sourceforge.net/vassalengine/VASSAL-3.1.0-beta8-macosx.dmg">Windows</a></li>
          </ul>
        </div>

        <div class="dlbutton">
          <div>Support <span class="sc">Vassal</span></div>
          <a href=""><img src="/images/paypal_donate.png"/></a>
        </div>
        <p><span class="sc">Vassal</span> is free, but development and hosting are not. If you enjoy using <span class="sc">Vassal</span>, please consider helping us with these costs.</p>
      </div>

      <div id="vassal-help">
        <h1>Get Help</h1>
        <p>Need help using <span class="sc">Vassal</span>? Stuck while creating a module? If you can't find an answer in our extensive <a href="">documentation</a>, check our friendly <a href="forum/">forum</a> for help.</p>
      </div>

      <div id="vassal-contribute">
        <h1>Get Involved</h1>
        <p>The <span class="sc">Vassal</span> project is run by volunteers and makes progress by the efforts of volunteers. Is there a feature you'd like to see in the next release? Did you find a bug? Request that feature or report the bug <a href="bugzilla/">here</a>. Are you a programmer? We could use your help. Join us in the <a href="">developers' forum</a>. Not a programmer? Help us improve our <a href="">documentation</a>.</p>
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
            <li><a href=""><span class="sc">Vassal</span> 3.1.0 released</a></li>
            <li><a href=""><span class="sc">Vassal</span> has a new web site</a></li>
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
            <li><a href=""><span class="sc">Vassal</span> 3.1.0-beta8 released</a></li>
            <li><a href="">Zombies are eating my brain</a></li>
          </ul>
        </li>
        <li class="day">
          <div class="date">Jan<br/>25</div>
          <ul class="events">
            <li><a href=""><span class="sc">Vassal</span> 3.1.0-beta8 released</a></li>
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
<!--
  <div id="vassal-footer">
    <ul id="vassal-footer-list">
-->
<!--      <li>Copyright &copy; 2009 <a href="">VASSAL dev team</a></li>
      <li><a href="">License</a></li>
      <li><a href="">Donate</a></li>
      <li><a href="">Sitemap</a></li>
      <li><a href="">Search</a></li>
      <li><a href="">About</a></li>
    </ul>
    <ul>
-->
</div>
</body>
</html>
