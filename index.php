<!DOCTYPE html>
<html lang="en-US">
<head>
  <?php include('inc/head.shtml'); ?>
  <link rel="alternate" type="application/rss+xml" title="RSS" href="/news/feed/"/>
  <title>VASSAL</title>
</head>
<body>
<?php include('inc/header.php'); ?>

<div id="content">
  <div class="content_box_left">
    <div id="about">
      <h1>What is <abbr>Vassal</abbr>?</h1>
      <p><abbr>Vassal</abbr> is a game engine for building and playing online adaptations of board games and card games. Play live on the Internet or by email. <abbr>Vassal</abbr> runs on all platforms, and is free, open-source software.</p>

      <p><a href="/about.php">Learn more</a> about <abbr>Vassal</abbr>.</p>

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
      <h1>Get <abbr>Vassal</abbr></h1>
      <p><abbr>Vassal</abbr> is free, open-source software, created by volunteers. Our current release is 
        <?php 
          $version = trim(file_get_contents('util/release-stable'));
          echo $version;
        ?>.</p> 

      <div class="roadsign">
        <?php
          include('inc/download-detect.php');
          echo "<a href=\"$download_url\"><span>Download <abbr>Vassal</abbr><br/>$version$download_os</span><img src=\"/images/button.download.png\" alt=\"\" /></a>";
        ?>
      </div>
      <p>Or, <a href="/download.php">download <abbr>Vassal</abbr></a> for other operating systems.</p> 

      <div class="roadsign">
        <form id="donate" action="https://www.paypal.com/cgi-bin/webscr" method="post">
          <button type="submit" name="submit">
            <span>Support <abbr>Vassal</abbr></span>
            <img src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_LG.gif" alt="Donate"/>
          </button>
          <input type="hidden" name="cmd" value="_s-xclick"/>
          <input type="hidden" name="hosted_button_id" value="UQ943LRKHH7DL"/>
        </form>
      </div>

      <p><abbr>Vassal</abbr> is free, but development and hosting are not. If you enjoy using <abbr>Vassal</abbr>, please consider helping us with these costs.</p>
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
