<?php echo '<?xml version="1.0" encoding="UTF-8"?>' ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head profile="http://www.w3.org/2005/10/profile">
  <link rel="stylesheet" type="text/css" href="/css/style.css"/>
  <link rel="stylesheet" type="text/css" href="/css/site.css"/>
  <link rel="alternate" type="application/rss+xml" title="RSS" href="/news_rss.php" />
  <link rel="icon" type="image/png" href="/images/VASSAL.png"/>
  <title>VASSAL</title>

  <!--[if lte IE 9]>
    <script src="/js/IE9.js" type="text/javascript">IE7_PNG_SUFFIX=".png";</script>
  <![endif]-->

  <script type="text/javascript" src="/js/mktree.js"></script>
  <script type="text/javascript" src="/js/status.js"></script>

  <link rel="stylesheet" type="text/css" href="/css/mktree.css"/>
  <link rel="stylesheet" type="text/css" href="/css/status.css"/>
</head>
<body> 
<?php include('inc/header.php'); ?>

<div id="content">
<div class="content_box_full">

<h1>Server Status</h1>

<p>Here you can see which modules are being used with the game server at present, and which modules have been used in the past day, week, and month.</p>

<div class="status_box">
<ul class="tab_menu">

<?php

$when = isset($_GET['when']) ? $_GET['when'] : 'current';

switch ($when) {
case 'current':
  echo <<<END
  <li class="showing"><a href="?when=current">Current</a></li>
  <li><a href="?when=day">Day</a></li>
  <li><a href="?when=week">Week</a></li>
  <li><a href="?when=month">Month</a></li>
END;
  break;
case 'day':
  echo <<<END
  <li><a href="?when=current">Current</a></li>
  <li class="showing"><a href="?when=day">Day</a></li>
  <li><a href="?when=week">Week</a></li>
  <li><a href="?when=month">Month</a></li>
END;
  break;
case 'week':
  echo <<<END
  <li><a href="?when=current">Current</a></li>
  <li><a href="?when=day">Day</a></li>
  <li class="showing"><a href="?when=week">Week</a></li>
  <li><a href="?when=month">Month</a></li>
END;
  break;
case 'month':
  echo <<<END
  <li><a href="?when=current">Current</a></li>
  <li><a href="?when=day">Day</a></li>
  <li><a href="?when=week">Week</a></li>
  <li class="showing"><a href="?when=month">Month</a></li>
END;
  break;
default:
  throw new ErrorException('Unrecognized when: ' . $when);
}
  
?>
<li><img id="toggle" src="/images/green-plus.png" alt="Expand"/></li>
</ul>
<div class="tab_contents">
<ul class="mktree" id="stree">

<?php

# connect to the SQL server
require_once('util/vserver-config.php');

$dbh = mysql_connect(SQL_HOST, SQL_USERNAME, SQL_PASSWORD);
if (!$dbh) {
  throw new ErrorException('Cannot connect to MySQL server: ' . mysql_error());
}

if (!mysql_select_db(SQL_DB, $dbh)) {
  throw new ErrorException('Cannot select database: ' . mysql_error());
}

$query = 'SELECT module_name, game_room, player_name FROM connections ';

switch ($when) {
case 'current':
  # get the set of rows having the most recent timestamp
  $query .= 'WHERE time = (SELECT MAX(time) FROM connections) ';
  break;
case 'day':
  $query .= 'WHERE DATEDIFF(NOW(), time) <= 1 ';
  break;
case 'week':
  $query .= 'WHERE DATEDIFF(NOW(), time) <= 7 '; 
  break;
case 'month':
  $query .= 'WHERE DATEDIFF(NOW(), time) <= 30 ';
  break;
default:
  throw new ErrorException('Unrecognized when: ' . $when);
}

$query .= 'ORDER BY module_name, game_room, player_name';

$r = mysql_query($query, $dbh);
if (!$r) {
  throw new ErrorException('SELECT failed: ' . mysql_error());
}

$tree = array();

while (($row = mysql_fetch_row($r))) {
  $tree[$row[0]][$row[1]][] = $row[2]; 
}

# NB: root is liClosed to prevent the tree from being
# rendered as expanded before all the parts are loaded
echo sprintf(
  "<li id=\"root\" class=\"liClosed\">VASSAL (%d)\n",
  mysql_num_rows($r)
);

echo ' <ul>', "\n";

$first = true;
foreach ($tree as $module => $rooms) {
  echo sprintf(
    " <li%s>%s (%d)\n",
    $first ? ' id="first_module"' : '',
    htmlspecialchars($module, ENT_QUOTES),
    count($rooms, COUNT_RECURSIVE) - count($rooms)
  );
  echo '  <ul>', "\n";

  $first = false;

  foreach ($rooms as $room => $players) {
    echo sprintf(
      "   <li>%s (%d)\n",
      htmlspecialchars($room, ENT_QUOTES),
      count($players)
    );
    echo '    <ul>', "\n";

    foreach ($players as $player) {
      echo '     <li>', htmlspecialchars($player, ENT_QUOTES), "</li>\n";
    }

    echo '    </ul>', "\n";
    echo '   </li>', "\n";
  }

  echo '  </ul>', "\n";
  echo ' </li>', "\n";
}
?>

</ul>
</li>
</ul>
</div>
</div>
</div>
</div>

<?php include('inc/footer.shtml'); ?>
</body>
</html>
