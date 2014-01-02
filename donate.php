<?php echo '<?xml version="1.0" encoding="UTF-8"?>' ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head profile="http://www.w3.org/2005/10/profile">
  <link rel="stylesheet" type="text/css" href="/css/style.css"/>
  <link rel="stylesheet" type="text/css" href="/css/site.css"/>
  <link rel="icon" type="image/png" href="/images/VASSAL.png"/>
  <title>Support VASSAL</title>
</head>
<body>
<?php include('inc/header.php'); ?>

<div id="content">
  <div id="content_box_full">
    <h1>Support <acronym>Vassal</acronym></h1>
   
    <p><acronym>Vassal</acronym> is free, open-source software, created by volunteers. While <acronym>Vassal</acronym> is free, development and hosting are not. If you enjoy using <acronym>Vassal</acronym>, please consider helping us with these costs.</p>

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

    <p>Have you ever wondered just how much effort we've put into <acronym>Vassal</acronym>? Have a look a this project cost calculator to get an idea:</p>
    <div class="costbox">
      <script type="text/javascript" src="http://www.ohloh.net/p/27263/widgets/project_cocomo.js"></script>
    </div>
  </div>
</div>

<?php include('inc/footer.shtml'); ?>
</body>
</html>
