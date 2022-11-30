<!DOCTYPE html>
<html lang="en">
<head>

  
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="See what's happening on the Vassal game server right now!">
  <title>Vassal Status</title>
  <link rel="icon" sizes="any" href="/favicon.ico">
  <link rel="icon" type="image/svg+xml" href="/icon.svg">
  <link rel="apple-touch-icon" href="/apple-touch-icon.png">
  <link rel="manifest" href="/site.webmanifest">
  <meta name="theme-color" content="#ffffff">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
  <link rel="stylesheet" type="text/css" href="/css/site.css"/>


  <script src="/js/mktree.js"></script>
  <script src="/js/status.js"></script>

  <link rel="stylesheet" type="text/css" href="/css/mktree.css"/>
  <link rel="stylesheet" type="text/css" href="/css/status.css"/>

</head>
<body>
  <!-- nav -->

  <header class="d-flex flex-wrap align-items-center justify-content-center justify-content-md-between px-2 py-2">
    <a href="/" class="d-flex align-items-center col-md-3 mb-2 mb-md-0 text-dark text-decoration-none">
      <img class="wordmark" src="/images/wordmark-path.svg" width="529" height="180" alt="Vassal">
    </a>

    <ul class="nav col-12 col-md-auto mb-2 justify-content-center mb-md-0">
      <li><a class="nav-link px-2 link-dark" href="/about.html"><svg class="svg-icon"><use xlink:href="#info-circle"></use></svg> About</a></li>
      <li>
        <a class="nav-link px-2 link-dark" href="https://discord.gg/GDdfq9AkfM"><svg class="svg-icon"><use xlink:href="#fab-discord"></use></svg> Chat</a>
      </li>
      <li><a class="nav-link px-2 link-dark" href="https://forum.vassalengine.org"><svg class="svg-icon"><use xlink:href="#landmark"></use></svg> Forum</a></li>
      <li><a class="nav-link px-2 link-dark" href="/wiki/Category:Modules"><svg class="svg-icon"><use xlink:href="#chess-knight"></use></svg> Modules</a></li>
      <li><a class="nav-link px-2 link-dark" href="/wiki"><svg class="svg-icon"><use xlink:href="#book-open"></use></svg> Documentation</a></li>
      <li><a class="nav-link px-2 link-dark" href="https://forum.vassalengine.org/c/news/17"><svg class="svg-icon"><use xlink:href="#bullhorn"></use></svg> News</a></li>
    </ul>
  </header>

  <div id="alert-bar" class="text-center">
    <h5 class="py-3">Vassal 3.6.8 is released! See the <a class="link-light" href="https://forum.vassalengine.org/t/vassal-3-6-8-released/75993">news</a> for details.</h5>
  </div>

  

  <div class="hero border-bottom mb-5">
    <div class="container-md px-5 py-5 mt-5">
      <div class="row mb-4">
        <div class="col text-center">
          <h1 class="display-4 fw-bold">Server Status</h1>
        </div>
      </div>
      <div class="row justify-content-center text-md-center">
        <div class="col-md-8">
          <p class="fs-5">Here you can see which modules are being used with the game server at present, and which modules have been used in the past day, week, and month.</p>
        </div>
      </div>
    </div>
  </div>

  <div class="container mb-5">
    <div class="row justify-content-center">
      <div class="col-md-6">
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

try {
  # connect to the SQL server
  require_once('util/vserver-config.php');

  $dbh = mysqli_connect(SQL_HOST, SQL_USERNAME, SQL_PASSWORD, SQL_DB);
  if (mysqli_connect_errno()) {
    throw new RuntimeException('Connection failed: ' . mysqli_connect_error());
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
    throw new RuntimeException('Unrecognized when: ' . $when);
  }

  $query .= 'ORDER BY module_name, game_room, player_name';

  $r = mysqli_query($dbh, $query);
  if (!$r) {
    throw new RuntimeException('SELECT failed: ' . mysqli_error($dbh));
  }

  $tree = array();

  while (($row = mysqli_fetch_row($r))) {
    $tree[$row[0]][$row[1]][] = $row[2];
  }

  # NB: root is liClosed to prevent the tree from being
  # rendered as expanded before all the parts are loaded
  echo sprintf(
    "<li id=\"root\" class=\"liClosed\">Vassal (%d)\n",
    mysqli_num_rows($r)
  );

  mysqli_free_result($r);

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
}
catch (Exception $e) {
  echo sprintf("            <li>Exception: %s</li>\n", $e->getMessage());
}
?>

              </ul>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>



  <!-- footer -->

  <div class="container border-top pt-5">
    <footer class="flex-wrap align-items-center">
      <article class="row">
        <a class="nav-link link-dark col-md d-flex align-items-start justify-content-center" href="https://discord.gg/GDdfq9AkfM">
          <img class="footer-icon float-start mx-2" src="/images/Discord-Logo-Black.svg" width="71" height="55" alt="">
          <div>
            <h5 class="mb-0">Vassal on Discord</h5>
            <p>Chat with friends</p>
          </div>
        </a>

        <a class="nav-link link-dark col-md d-flex align-items-start justify-content-center" href="https://github.com/vassalengine/vassal">
          <img class="footer-icon float-start mx-2" src="/images/github-icon.svg" width="256" height="250" alt="">
          <div>
            <h5 class="mb-0">Vassal on GitHub</h5>
            <p>We write code here</p>
          </div>
        </a>

        <a class="nav-link link-dark col-md d-flex align-items-start justify-content-center" href="/status.php">
          <img class="footer-icon float-start mx-2" src="/images/server-svgrepo-com.svg" width="512" height="512" alt="">
          <div>
            <h5 class="mb-0">Server Status</h5>
            <p>Who's playing now?</p>
          </div>
        </a>
      </article>

      <div id="colophon" class="text-center pt-5">
        <div class="row text-muted">
          <p class="col mb-0">Copyright © 2009–2022 The Vassal Team</p>
        </div>
        <div class="row text-muted">
          <p class="col">
            <a class="link-secondary text-decoration-none" href="/status.php">Status</a> | <a class="link-secondary text-decoration-none" href="/contact.html">Contact</a> | <a class="link-secondary text-decoration-none" href="/about.html#donate">Donate</a>
          </p>
        </div>
      </div>
    </footer>
  </div>

  <div class="svg-sprites">
    <div class="fontawesome">
      <svg xmlns="http://www.w3.org/2000/svg">
        <symbol id="book-open" viewBox="0 0 576 512">
          <path d="M542.22 32.05c-54.8 3.11-163.72 14.43-230.96 55.59-4.64 2.84-7.27 7.89-7.27 13.17v363.87c0 11.55 12.63 18.85 23.28 13.49 69.18-34.82 169.23-44.32 218.7-46.92 16.89-.89 30.02-14.43 30.02-30.66V62.75c.01-17.71-15.35-31.74-33.77-30.7zM264.73 87.64C197.5 46.48 88.58 35.17 33.78 32.05 15.36 31.01 0 45.04 0 62.75V400.6c0 16.24 13.13 29.78 30.02 30.66 49.49 2.6 149.59 12.11 218.77 46.95 10.62 5.35 23.21-1.94 23.21-13.46V100.63c0-5.29-2.62-10.14-7.27-12.99z"></path>
        </symbol>
        <symbol id="bullhorn" viewBox="0 0 576 512">
          <path d="M576 240c0-23.63-12.95-44.04-32-55.12V32.01C544 23.26 537.02 0 512 0c-7.12 0-14.19 2.38-19.98 7.02l-85.03 68.03C364.28 109.19 310.66 128 256 128H64c-35.35 0-64 28.65-64 64v96c0 35.35 28.65 64 64 64h33.7c-1.39 10.48-2.18 21.14-2.18 32 0 39.77 9.26 77.35 25.56 110.94 5.19 10.69 16.52 17.06 28.4 17.06h74.28c26.05 0 41.69-29.84 25.9-50.56-16.4-21.52-26.15-48.36-26.15-77.44 0-11.11 1.62-21.79 4.41-32H256c54.66 0 108.28 18.81 150.98 52.95l85.03 68.03a32.023 32.023 0 0 0 19.98 7.02c24.92 0 32-22.78 32-32V295.13C563.05 284.04 576 263.63 576 240zm-96 141.42l-33.05-26.44C392.95 311.78 325.12 288 256 288v-96c69.12 0 136.95-23.78 190.95-66.98L480 98.58v282.84z"></path>
        </symbol>
        <symbol id="chess-knight" viewBox="0 0 384 512">
          <path d="M19 272.47l40.63 18.06a32 32 0 0 0 24.88.47l12.78-5.12a32 32 0 0 0 18.76-20.5l9.22-30.65a24 24 0 0 1 12.55-15.65L159.94 208v50.33a48 48 0 0 1-26.53 42.94l-57.22 28.65A80 80 0 0 0 32 401.48V416h319.86V224c0-106-85.92-192-191.92-192H12A12 12 0 0 0 0 44a16.9 16.9 0 0 0 1.79 7.58L16 80l-9 9a24 24 0 0 0-7 17v137.21a32 32 0 0 0 19 29.26zM52 128a20 20 0 1 1-20 20 20 20 0 0 1 20-20zm316 320H16a16 16 0 0 0-16 16v32a16 16 0 0 0 16 16h352a16 16 0 0 0 16-16v-32a16 16 0 0 0-16-16z"></path>
        </symbol>
        <symbol id="home" viewBox="0 0 576 512">
          <path d="M280.37 148.26L96 300.11V464a16 16 0 0 0 16 16l112.06-.29a16 16 0 0 0 15.92-16V368a16 16 0 0 1 16-16h64a16 16 0 0 1 16 16v95.64a16 16 0 0 0 16 16.05L464 480a16 16 0 0 0 16-16V300L295.67 148.26a12.19 12.19 0 0 0-15.3 0zM571.6 251.47L488 182.56V44.05a12 12 0 0 0-12-12h-56a12 12 0 0 0-12 12v72.61L318.47 43a48 48 0 0 0-61 0L4.34 251.47a12 12 0 0 0-1.6 16.9l25.5 31A12 12 0 0 0 45.15 301l235.22-193.74a12.19 12.19 0 0 1 15.3 0L530.9 301a12 12 0 0 0 16.9-1.6l25.5-31a12 12 0 0 0-1.7-16.93z"></path>
        </symbol>
        <symbol id="info-circle" viewBox="0 0 512 512">
          <path d="M256 8C119.043 8 8 119.083 8 256c0 136.997 111.043 248 248 248s248-111.003 248-248C504 119.083 392.957 8 256 8zm0 110c23.196 0 42 18.804 42 42s-18.804 42-42 42-42-18.804-42-42 18.804-42 42-42zm56 254c0 6.627-5.373 12-12 12h-88c-6.627 0-12-5.373-12-12v-24c0-6.627 5.373-12 12-12h12v-64h-12c-6.627 0-12-5.373-12-12v-24c0-6.627 5.373-12 12-12h64c6.627 0 12 5.373 12 12v100h12c6.627 0 12 5.373 12 12v24z"></path>
        </symbol>
        <symbol id="landmark" viewBox="0 0 512 512">
          <path d="M501.62 92.11L267.24 2.04a31.958 31.958 0 0 0-22.47 0L10.38 92.11A16.001 16.001 0 0 0 0 107.09V144c0 8.84 7.16 16 16 16h480c8.84 0 16-7.16 16-16v-36.91c0-6.67-4.14-12.64-10.38-14.98zM64 192v160H48c-8.84 0-16 7.16-16 16v48h448v-48c0-8.84-7.16-16-16-16h-16V192h-64v160h-96V192h-64v160h-96V192H64zm432 256H16c-8.84 0-16 7.16-16 16v32c0 8.84 7.16 16 16 16h480c8.84 0 16-7.16 16-16v-32c0-8.84-7.16-16-16-16z"></path>
        </symbol>
        <symbol id="fab-discord" viewBox="0 0 448 512">
          <path d="M297.216 243.2c0 15.616-11.52 28.416-26.112 28.416-14.336 0-26.112-12.8-26.112-28.416s11.52-28.416 26.112-28.416c14.592 0 26.112 12.8 26.112 28.416zm-119.552-28.416c-14.592 0-26.112 12.8-26.112 28.416s11.776 28.416 26.112 28.416c14.592 0 26.112-12.8 26.112-28.416.256-15.616-11.52-28.416-26.112-28.416zM448 52.736V512c-64.494-56.994-43.868-38.128-118.784-107.776l13.568 47.36H52.48C23.552 451.584 0 428.032 0 398.848V52.736C0 23.552 23.552 0 52.48 0h343.04C424.448 0 448 23.552 448 52.736zm-72.96 242.688c0-82.432-36.864-149.248-36.864-149.248-36.864-27.648-71.936-26.88-71.936-26.88l-3.584 4.096c43.52 13.312 63.744 32.512 63.744 32.512-60.811-33.329-132.244-33.335-191.232-7.424-9.472 4.352-15.104 7.424-15.104 7.424s21.248-20.224 67.328-33.536l-2.56-3.072s-35.072-.768-71.936 26.88c0 0-36.864 66.816-36.864 149.248 0 0 21.504 37.12 78.08 38.912 0 0 9.472-11.52 17.152-21.248-32.512-9.728-44.8-30.208-44.8-30.208 3.766 2.636 9.976 6.053 10.496 6.4 43.21 24.198 104.588 32.126 159.744 8.96 8.96-3.328 18.944-8.192 29.44-15.104 0 0-12.8 20.992-46.336 30.464 7.68 9.728 16.896 20.736 16.896 20.736 56.576-1.792 78.336-38.912 78.336-38.912z"></path>
        </symbol>
      </svg>
    </div>
  </div>
</body>
</html>
