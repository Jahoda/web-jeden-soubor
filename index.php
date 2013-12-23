<!doctype html>
<meta charset="utf-8">
<title>Archiv</title>
<meta name="robots" content="noindex,nofollow,nosnippet,noarchive">
<meta name="googlebot" content="noindex,nofollow,nosnippet,noarchive">
<style>
/* CSS pravidla, případně readfile('soubor.css'); */
</style>

<body>
	<div class="hlavicka">
		<p>Hlavička webu
	</div>
	
	<div class="obsah" id="obsah">
	  <h1>Úvodní stránka</h1>
	  <p>Bude nahrazeno obsahem přes JS.</p>
	</div>

	<div class="stranky" id="stranky">&nbsp;</div>

	</div>

	<div class="paticka">
		<p>Patička</p>
	</div>
<script>
var stranky = {

<?php
//SQL connect
define('SQL_HOST', 'localhost');
define('SQL_DBNAME', 'db');
define('SQL_USERNAME' ,'root');
define('SQL_PASSWORD', '');

$dsn = 'mysql:dbname='.SQL_DBNAME.';host='.SQL_HOST.'';
$user = SQL_USERNAME;
$password = SQL_PASSWORD;

try {
    $pdo = new PDO($dsn, $user, $password);
} catch (PDOException $e) {
    die('Connection failed: ' . $e->getMessage());
}

$sql = $pdo->prepare("SET NAMES utf8");
$sql->execute();

$sql = $pdo->prepare("
SELECT `url_slug`, `title`, `description`, text_html
FROM `pages`
WHERE `status` = '1'
ORDER BY `last_modification` DESC
");

function data_uri($filename) {
  $url = "http://jecas.cz"; // adresa stránky
	$filename = (preg_match("@^(" . $url . "|http)@", $filename)) ? $filename : ($url . $filename);
  $data = base64_encode(file_get_contents($filename));
  return "data:image/png;base64,$data";
}

function rozbehatScript($text) {
	$text = str_replace(array("<script>", "<\/script>"), array('<scri"+"pt>', '</scr"+"ipt>'), $text);
	return $text;
}

function vycistit($text) {
	$text = preg_replace("~(<a href\=['|\"])/([a-z0-9#-]*)([\"|'])~", "$1javascript:zobrazitStranku('$2')$3", $text);
	$search = '/(<img\s+src=["\'])([^"\']+)(["\']\s+[^>]+>)/';
	$text = preg_replace_callback($search, create_function(
	        '$matches',
	        'return $matches[1] . data_uri($matches[2]) . $matches[3];'
	    ), $text);
	return $text;
}


$sql->execute() or die(print_r($sql->errorInfo(), true));

$zaznamy = $sql->fetchAll();

foreach ($zaznamy as $zaznam) {
	echo '"' . $zaznam["url_slug"] . 
	  '" : { nadpis : ' . json_encode(htmlspecialchars($zaznam["title"])) . 
	    ', popis: ' . json_encode($zaznam["description"]) . 
	    ', obsah : ' . rozbehatScript(json_encode(vycistit($zaznam['text_html']))) . 
	   '}, ';
}  	

?>
};


function insertAndExecute(id, text)
  {
    domelement = document.getElementById(id);
    domelement.innerHTML = text;
    var scripts = [];

    ret = domelement.childNodes;
    for ( var i = 0; ret[i]; i++ ) {
      if ( scripts && nodeName( ret[i], "script" ) && (!ret[i].type || ret[i].type.toLowerCase() === "text/javascript") ) {
            scripts.push( ret[i].parentNode ? ret[i].parentNode.removeChild( ret[i] ) : ret[i] );
        }
    }

    for(script in scripts)
    {
      evalScript(scripts[script]);
    }
  }
  function nodeName( elem, name ) {
    return elem.nodeName && elem.nodeName.toUpperCase() === name.toUpperCase();
  }
  function evalScript( elem ) {
    data = ( elem.text || elem.textContent || elem.innerHTML || "" );

    var head = document.getElementsByTagName("head")[0] || document.documentElement,
    script = document.createElement("script");
    script.type = "text/javascript";
    script.appendChild( document.createTextNode( data ) );
    head.insertBefore( script, head.firstChild );
    head.removeChild( script );

    if ( elem.parentNode ) {
        elem.parentNode.removeChild( elem );
    }
  }

function vsechnyClanky() {
	var vystup = "<h2>Stránky</h2>";
	for (stranka in stranky) {
		vystup += "<h3><a href='javascript:zobrazitStranku(\"" + stranka + "\")'>" + stranky[stranka].nadpis + "</a></h3>";
	};
	document.getElementById("stranky").innerHTML = vystup;
}

function zobrazitStranku(url) {
	if (!stranky[url]) {
		alert("Dostupné pouze online na jecas.cz");
		return;
	}
	document.title = stranky[url].nadpis;
	insertAndExecute('obsah', "<h1>" + stranky[url].nadpis + "</h1><p>" + stranky[url].popis + "</p>" + stranky[url].obsah);
	ukazky();
	window.scrollTo(0, 0);
}


vsechnyClanky();
</script>
