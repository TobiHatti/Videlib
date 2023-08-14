<?php 
require("lib/connect.php");
require("lib/wrapsql.php");
$revision = date('Y-m-d').rand();
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">

    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">

	<meta name="description" content="Character lexicon and manager for fiction, fantasy or RP, with several ways to manage and organize characters"/>

	<meta property="og:site_name" content="Vide" />
	<meta property="og:description" content="Character lexicon and manager for fiction, fantasy or RP, with several ways to manage and organize characters" />
	<meta property="og:image" content="/files/VideLogo.png">
	<meta property="og:image:type" content="image/png">
	<meta property="og:image:width" content="1024">
	<meta property="og:image:height" content="1024">
	<meta property="og:image:alt" content="Vide Logo" />
	<meta property="og:title" content="Vide" />
	<meta property="og:type" content="website" />
	<meta property="og:url" content="https://vide.endev.at" />
	<meta property="og:determiner" content="a" />
	<meta property="og:locale" content="en_US" />
	
	<meta name="twitter:card" content="summary" />
	<meta name="twitter:title" content="Vide" />
	<meta name="twitter:description" content="Character lexicon and manager for fiction, fantasy or RP, with several ways to manage and organize characters" />
	<meta name="twitter:image" content="/files/VideLogo.png" />
	<meta name="twitter:image:alt" content="Vide Logo" />

	<title>Vide</title>
	<link rel="icon" type="image/x-icon" href="/files/favicon.ico">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
	<link rel="stylesheet" href="/css/form.css?<?= $revision ?>">
	<link rel="stylesheet" href="/css/layout.css?<?= $revision ?>">
	<link rel="stylesheet" href="/css/particles.css?<?= $revision ?>">
	<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

	<script src="/js/accordion.js?<?= $revision ?>"></script>
	<script src="/js/script.js?<?= $revision ?>"></script>
</head>
<body>
	<div id="particleContainer"></div>
	<header>
		<h1>Vide</h1>
	</header>
	<main></main>
	<div class="modalWrapper modClose" style="display: none;">
		<div class="modalBlur"></div>
		<div class="modal">
			<div class="modalContent"></div>
		</div>
	</div> 
	<script src="/js/events.js?<?= $revision ?>"></script>
</body>
</html>