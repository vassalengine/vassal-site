<?php echo '<?xml version="1.0" encoding="UTF-8"?>' ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head profile="http://www.w3.org/2005/10/profile">
  <link rel="stylesheet" type="text/css" href="/style.css"/>
  <link rel="stylesheet" type="text/css" href="/css/site.css"/>
  <link rel="alternate" type="application/rss+xml" title="RSS" href="/news_rss.php" />
  <link rel="icon" type="image/png" href="/images/VASSAL.png"/>
  <title>VASSAL</title>
</head>
<body>
<?php include('inc/header.php'); ?>

<div id="content">
  <div class="content_box_left">
    <div id="about">
      <h1>What is <acronym>Vassal</acronym>?</h1>
      <p><acronym>Vassal</acronym> is a game engine for building and playing online adaptations of board games and card games. Play live on the Internet or by email. <acronym>Vassal</acronym> runs on all platforms, and is free, open-source software.</p>

      <p><a href="/about.php">Learn more</a> about <acronym>Vassal</acronym>.</p>

      <table class="screenshot">
        <tbody>
          <tr>
            <td><a href="/images/screenshot1.png"><img src="/thumbs/screenshot1.png" alt="screenshot"></a></td>
            <td><a href="/images/screenshot2.png"><img src="/thumbs/screenshot2.png" alt="screenshot"></a></td>
          </tr>
        </tbody>
      </table>
    </div>

    <div id="download">
      <h1>Get <acronym>Vassal</acronym></h1>
      <p><acronym>Vassal</acronym> is free, open-source software, created by volunteers. Our current release is 
        <?php 
          $version = file_get_contents('util/release-current');
          echo $version;
        ?>
      .</p> 

      <div class="dlbutton">
        <?php
          include('inc/download-detect.php');
          echo "<a href=\"$download_url\"><span>Download <acronym>Vassal</acronym><br/>$version$download_os</span><img src=\"/images/button.download.png\" alt=\"\" /></a>";
        ?>
      </div>
      <div class="dlbutton">
        <a target="_blank" href="https://www.paypal.com/us/cgi-bin/webscr?cmd=_flow&amp;SESSION=gS5hJbXNrYzfhrwcaHyrBsSqND22MpcGz07W_M7D94YX9wq1CFH4rbdTb7e&amp;dispatch=5885d80a13c0db1fa798f5a5f5ae42e779d4b5655493f6171509c5b2ec019b86"><span>Support <acronym>Vassal</acronym></span><img src="/images/paypal_donate.png" alt="" /></a>
      </div>
      <p><acronym>Vassal</acronym> is free, but development and hosting are not. If you enjoy using <acronym>Vassal</acronym>, please consider helping us with these costs.</p>
    </div>
  </div>

  <div class="content_box_right">
    <div id="news">
      <h1>Latest News <a href="/news_rss.php"><img src="/images/feed-icon-14x14.png" alt="RSS feed"></a></h1>

<?php

$rows = array();

try {
  require_once('sso/NewsDB.php');

  $news = new NewsDB();
  $limit = 10;
  $query = "SELECT id, DATE_FORMAT(date,\"%b\") AS month, DAYOFMONTH(date) AS day, headline FROM news ORDER BY date DESC LIMIT $limit";
  
  $rows = $news->read_all($query);
}
catch (ErrorException $e) {
  warn($e->getMessage());
}

if (count($rows) > 0) {
  echo '<ul>';

  $month = '';
  $day = 0;

  # print each item
  foreach ($rows as $item) {
    if ($day != $item['day'] || $month != $item['month']) {
      if ($day != 0) echo '</ul></li>';

      echo '<li class="day"><div class="date">',
        $item['month'], '<br/>', $item['day'], '</div><ul class="events">';

      $day = $item['day'];
      $month = $item['month'];
    }

    echo '<li><a href="/news.php#_', $item['id'], '">',
      $item['headline'], '</a></li>';
  }

  echo '</ul></li></ul>';
}
?>
      <div class="news-nav">
        <em><a href="/news.php">...more news</a></em>
      </div>
    </div>
  </div>
</div>

<?php include('inc/footer.shtml'); ?>
</body>
</html>
