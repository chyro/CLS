<?php
define('BASE_DIR', dirname(__DIR__) . '/');
define('APP_DIR', BASE_DIR . 'app/');
define('BASE_URL', rtrim(dirname($_SERVER["SCRIPT_NAME"])) . '/');
define('API_URL', rtrim(dirname(dirname($_SERVER["SCRIPT_NAME"]))) . '/');
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Watchlist</title>
<!-- TODO: Styles -->
<link rel="manifest" href="<?php echo BASE_URL; ?>manifest.json" />
</head>
<body>

<pre>
This app might...
- display the movie calendar
- display the notifications (e.g. friend requests, recommended movies, etc) - probably the most important => check the API regularly, push notifications
- allow setting watchlist movies to watched movies
</pre>
<div id="view-watched"></div>
<div id="view-watchlist"></div>
<div id="view-recommended"></div>

<script>
var base_url = <?php echo json_encode(BASE_URL); ?>;
var api_url = <?php echo json_encode(API_URL); ?>;

//Async init:
// simple: https://davidwalsh.name/javascript-loader
// complex: https://github.com/systemjs/systemjs
// Angular: System.import(base_url + "main.js").then(function() { MovieApp.init({apiUrl: api_url}); });

//Sync init:
</script>
<script src="./main.js"></script>
<script>
MovieApp.init({apiUrl: api_url});
</script>

</body>
</html>
