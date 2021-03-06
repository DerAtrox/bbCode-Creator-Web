<?php
// MUSTACHE ENGINE
require 'assets/src/Mustache/Autoloader.php';
Mustache_Autoloader::register();

$m = new Mustache_Engine(array(
    'loader' => new Mustache_Loader_FilesystemLoader(dirname(__FILE__) . '/views/', array('extension' => '.html')),
    'partials_loader' => new Mustache_Loader_FilesystemLoader(dirname(__FILE__) . '/views/', array('extension' => '.html')),
    'cache' => dirname(__FILE__) . '/cache',
));


// LANGUAGE
$locale = json_decode(file_get_contents("locales/en.json"), true);
if (isset($_COOKIE["lang"])) {
    $lang_code = $_COOKIE["lang"];

    if ($lang_code != "en") {
        if (file_exists("locales/" . $lang_code . ".json")) {
            $locale = array_replace_recursive($locale, json_decode(file_get_contents("locales/" . $lang_code . ".json"), true));
        }
    }
}

// VERSION
if(strpos(strtolower($_SERVER["HTTP_HOST"]), "dev.creator") !== false) {
    $version = "dev" . filemtime(__FILE__);
} else {
    $raw = trim(file_get_contents('version'));
    $version = "<a href='https://github.com/DerAtrox/bbCode-Creator-Web/commit/" . $raw . "'>" . $raw . "</a>";
}

// DATA
$data = new stdClass();

$data->lang = $locale;
$data->hidecookie = isset($_COOKIE["hide_cookie"]) ? $_COOKIE["hide_cookie"] == 1 : false;
$data->hidejumbo = isset($_COOKIE["hide_jumbo"]) ? $_COOKIE["hide_jumbo"] == 1 : false;

$locales = array();
foreach(array_diff(scandir("locales"), array('..', '.')) as $file) {
    array_push($locales, array('name' => str_replace('.json', '', $file)));
}
$data->locales = $locales;
$data->version = $version;

// PAGE
if (!isset($_GET["page"])) {
    $page = "home";
} else {
    $page = $_GET["page"];
}

switch ($page) {
    case "home":
        $data->page_home = true;
        break;
    case "settings":
        $data->page_settings = true;
        break;
    default:
        $data->page_notfound = true;
        break;
}

echo $m->render("main", $data);