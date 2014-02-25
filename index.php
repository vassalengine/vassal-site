<?php echo '<?xml version="1.0" encoding="UTF-8"?>' ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head profile="http://www.w3.org/2005/10/profile">
  <link rel="stylesheet" type="text/css" href="/css/style.css"/>
  <link rel="stylesheet" type="text/css" href="/css/site.css"/>
  <link rel="alternate" type="application/rss+xml" title="RSS" href="/news/feed/"/>
  <link rel="icon" type="image/png" href="/images/VASSAL.png"/>
  <title>VASSAL</title>

  <!--[if lte IE 9]>
    <script src="/js/IE9.js" type="text/javascript">IE7_PNG_SUFFIX=".png";</script>
  <![endif]-->
</head>
<body>
<?php include('inc/header.php'); ?>

<div id="content">
  <div class="errorbox">
    <p><b>We've moved to a new host!</b> The site should be much faster now, but we might have broken things in the process. If you encounter problems, please tell the <a href="mailto:webmaster@nomic.net">webmaster</a> so they may be fixed.</p>
  </div>

  <div class="content_box_left">
    <div id="about">
      <h1>What is <acronym>Vassal</acronym>?</h1>
      <p><acronym>Vassal</acronym> is a game engine for building and playing online adaptations of board games and card games. Play live on the Internet or by email. <acronym>Vassal</acronym> runs on all platforms, and is free, open-source software.</p>

      <p><a href="/about.php">Learn more</a> about <acronym>Vassal</acronym>.</p>

      <table class="screenshot">
        <tbody>
          <tr>
            <td><a href="/images/screenshot1.png"><img src="/thumbs/screenshot1.png" alt="screenshot"/></a></td>
            <td><a href="/images/screenshot2.png"><img src="/thumbs/screenshot2.png" alt="screenshot"/></a></td>
          </tr>
        </tbody>
      </table>
    </div>

    <div id="download">
      <h1>Get <acronym>Vassal</acronym></h1>
      <p><acronym>Vassal</acronym> is free, open-source software, created by volunteers. Our current release is 
        <?php 
          $version = trim(file_get_contents('util/release-stable'));
          echo $version;
        ?>.</p> 

      <div class="roadsign">
        <?php
          include('inc/download-detect.php');
          echo "<a href=\"$download_url\"><span>Download <acronym>Vassal</acronym><br/>$version$download_os</span><img src=\"/images/button.download.png\" alt=\"\" /></a>";
        ?>
      </div>
      <p>Or, <a href="/download.php">download <acronym>Vassal</acronym></a> for other operating systems.</p> 

      <div class="roadsign">
        <form id="donate" action="https://www.paypal.com/cgi-bin/webscr" method="post">
          <button type="submit" name="submit">
            <span>Support <acronym>Vassal</acronym></span>
            <img src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_LG.gif" alt="Donate"/>
          </button>
          <input type="hidden" name="cmd" value="_s-xclick"/>
          <input type="hidden" name="hosted_button_id" value="UQ943LRKHH7DL">
          <img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
        </form>
      </div>

      <p><acronym>Vassal</acronym> is free, but development and hosting are not. If you enjoy using <acronym>Vassal</acronym>, please consider helping us with these costs.</p>
    </div>
  
    <div id="status">
        <h1>Who's Playing?</h1>

        <p>Check the <a href="/status.php">server status</a> to see what games are being played right now.</p>
    </div>
  </div>

  <div class="content_box_right">
    <div id="news">
      <h1>Latest News <a href="/news/feed/"><img src="/images/feed-icon-14x14.png" alt="RSS feed"/></a></h1>

      <ul class="news">
      <?php include('inc/headlines.php'); ?>
      </ul>

      <div id="news-nav">
        <div id="news-submit">
          <em><a href="/news_submit.php">Submit News</a></em>
        </div>
  
        <div id="news-more">
          <em><a href="/news">&hellip;more news</a></em>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include('inc/footer.shtml'); ?>
</body>
</html>
