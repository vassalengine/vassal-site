<!DOCTYPE html>
<html lang="en-US">
<head>
  <meta charset="UTF-8"/>
  <link rel="stylesheet" type="text/css" href="/css/style.css"/>
  <link rel="stylesheet" type="text/css" href="/css/site.css"/>
  <link rel="icon" type="image/png" href="/images/VASSAL.png"/>
  <title>Support VASSAL</title>
</head>
<body>
<?php include('inc/header.php'); ?>

<div id="content">
  <div id="content_box_full">
    <h1>Support <abbr>Vassal</abbr></h1>
   
    <p><abbr>Vassal</abbr> is free, open-source software, created by volunteers. While <abbr>Vassal</abbr> is free, development and hosting are not. If you enjoy using <abbr>Vassal</abbr>, please consider helping us with these costs.</p>

    <div class="roadsign">
      <form id="donate" action="https://www.paypal.com/cgi-bin/webscr" method="post">
        <button type="submit" name="submit">
          <span>Support <abbr>Vassal</abbr></span>
          <img src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_LG.gif" alt="Donate"/>
        </button>
        <input type="hidden" name="cmd" value="_s-xclick"/>
        <input type="hidden" name="hosted_button_id" value="UQ943LRKHH7DL">
      </form>
    </div>

    <p>Have you ever wondered just how much effort we've put into <abbr>Vassal</abbr>? Have a look a this project cost calculator to get an idea:</p>
    <div class="costbox">
      <script type="text/javascript" src="http://www.ohloh.net/p/27263/widgets/project_cocomo.js"></script>
    </div>
  </div>
</div>

<?php include('inc/footer.shtml'); ?>
</body>
</html>
