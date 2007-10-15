<?php
/*
Plugin Name:	EHT Translate
Plugin URI:		http://emiliogonzalez.sytes.net/index.php/2007/10/09/eht-translate-wordpress-plugin/
Description:	Make automatic translations of Wordpress pages.
Version:		0.1
Author:			Emilio Gonz&aacute;lez Monta&ntilde;a
Author URI:		http://emiliogonzalez.sytes.net

History:	0.1		First release.

How to use it: add this code in any part of your web page:

<php
if (function_exists ("EHTTranslate"))
{
    EHTTranslate ();
}
?>

*/

add_action ("admin_menu", "EHTTranslateAdminAddPages");

define ("EHT_TRANSLATE_PLUGIN_BASE", "/wp-content/plugins/eht-translate/");
define ("EHT_TRANSLATE_PLUGIN_BASE_IMAGES", EHT_TRANSLATE_PLUGIN_BASE . "images/");
define ("EHT_TRANSLATE_OPTION_LANGUAGE", "eht-translate-option-language");
define ("EHT_TRANSLATE_OPTION_SHOW_FRAME", "eht-translate-option-show-frame");
define ("EHT_TRANSLATE_FIELD_ACTION", "eht-translate-field-action");
define ("EHT_TRANSLATE_ACTION_UPDATE", "update");
define ("EHT_TRANSLATE_SHOW_FRAME_YES", "yes");
define ("EHT_TRANSLATE_SHOW_FRAME_NO", "no");
define ("EHT_TRANSLATE_DEFAULT_LANGUAGE", "en");
define ("EHT_TRANSLATE_DEFAULT_SHOW_FRAME", EHT_TRANSLATE_SHOW_FRAME_YES);

$languageNames = array ("ch" => "Chinese",
						"de" => "German",
						"en" => "English",
						"es" => "Spanish",
						"fr" => "French",
						"it" => "Italian",
						"ja" => "Japanese",
						"pt" => "Portuguese",
						"ru" => "Russian");
$flags = array ("ch" => "china.gif",
				"de" => "germany.gif",
				"en" => "england.gif",
				"es" => "spain.gif",
				"fr" => "france.gif",
				"it" => "italy.gif",
				"ja" => "japan.gif",
				"pt" => "portugal.gif",
				"ru" => "russia.gif");
$languagePairs = array ("ch" => array ("en"),
						"de" => array ("fr", "en"),
						"en" => array ("ch", "de", "es", "fr", "it", "ja", "pt", "ru"),
						"es" => array ("en"),
						"fr" => array ("de", "en"),
						"it" => array ("en"),
						"ja" => array ("en"),
						"pt" => array ("en"),
						"ru" => array ("en"));
						
function EHTTranslate ($print = true)
{
	global $languageNames, $languagePairs;

	$language = get_option (EHT_TRANSLATE_OPTION_LANGUAGE);
	$showFrame = get_option (EHT_TRANSLATE_OPTION_SHOW_FRAME);
	if ($language == "")
	{
		$language = EHT_TRANSLATE_DEFAULT_LANGUAGE;
	}
	if ($showFrame == "")
	{
		$showFrame = EHT_TRANSLATE_DEFAULT_SHOW_FRAME;
	}
	$showFrame = (($showFrame == "yes") ? true : false);
	
	$languages = $languagePairs[$language];

	if ($_SERVER["HTTPS"])
	{
		$url = "https://";
		if ($_SERVER["SERVER_PORT"] != 443)
		{
			$url .= ":" . $_SERVER["SERVER_PORT"];
		}
	}
	else
	{
		$url = "http://";
		if ($_SERVER["SERVER_PORT"] != 80)
		{
			$url .= ":" . $_SERVER["SERVER_PORT"];
		}
	}
	$url .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];

	$url = str_replace (":", "%3A", $url);
	$url = str_replace ("/", "%2F", $url);
	$url = str_replace ("&", "%26", $url);

	$text = "";
	$text .= "<ul>\n";
	$text .= "    <li><a lang=\"$language\" xml:lang=\"$language\" href=\"#\">" . EHTTranslateFlag ($language) . " " . $languageNames[$language] . "</a></li>\n";
	foreach ($languages as $languagePair)
	{
		$text .= "    <li><a lang=\"$languagePair\" xml:lang=\"$languagePair\" href=\"http://www.google.com/translate" . ($showFrame ? "_c" : "_p") . "?hl=$language&amp;ie=UTF8&amp;langpair=$language%7C$languagePair&amp;u=$url\" rel=\"nofollow\" target=\"_top\">" . EHTTranslateFlag ($languagePair) . " " . $languageNames[$languagePair] . "</a></li>\n";
	}
	$text .= "</ul>\n";
	$text .= "<small><a href=\"http://emiliogonzalez.sytes.net/index.php/2007/10/09/eht-translate-plugin-para-wordpress/\" target=\"_blank\">EHT Translate</a> by <a href=\"http://emiliogonzalez.sytes.net\" target=\"_blank\">Emilio</a></small><br>\n";

	if ($print)
	{
		echo $text;
	}
	
	return ($text);
}

function EHTTranslateFlag ($languageCode)
{
	global $flags;
	
	$flag = "<img src=\"" . EHT_TRANSLATE_PLUGIN_BASE_IMAGES . $flags[$languageCode] . "\">";

	return ($flag);
}

function EHTTranslateAdminAddPages ()
{
	add_options_page ('EHT Translate', 'EHT Translate', 8, 'eht-translate-options', 'EHTTranslateAdminOptions');
}

function EHTTranslateAdminOptions ()
{
	global $languageNames;
	
	$action = $_POST[EHT_TRANSLATE_FIELD_ACTION];
	if ($action == EHT_TRANSLATE_ACTION_UPDATE)
	{
		$language = $_POST[EHT_TRANSLATE_OPTION_LANGUAGE];
		$showFrame = $_POST[EHT_TRANSLATE_OPTION_SHOW_FRAME];
	}
	else
	{
		$language = get_option (EHT_TRANSLATE_OPTION_LANGUAGE);
		$showFrame = get_option (EHT_TRANSLATE_OPTION_SHOW_FRAME);
	}
	
	if ($language == "")
	{
		$language = EHT_TRANSLATE_DEFAULT_LANGUAGE;
		$action = EHT_TRANSLATE_ACTION_UPDATE;
	}
	if ($showFrame == "")
	{
		$showFrame = EHT_TRANSLATE_DEFAULT_SHOW_FRAME;
		$action = EHT_TRANSLATE_ACTION_UPDATE;
	}
	
	if ($action == EHT_TRANSLATE_ACTION_UPDATE)
	{
        update_option (EHT_TRANSLATE_OPTION_LANGUAGE, $language);
        update_option (EHT_TRANSLATE_OPTION_SHOW_FRAME, $showFrame);
        echo "<div class=\"updated\">The options have been updated.</div>\n";
	}

	echo "<div class=\"wrap\">\n";
	echo "<h2>EHT Translate</h2>\n";
	echo "<form method=\"post\" action=\"" . str_replace( '%7E', '~', $_SERVER['REQUEST_URI']) . "\">\n";
	echo "<input type=\"hidden\" name=\"" . EHT_TRANSLATE_FIELD_ACTION . "\" value=\"" . EHT_TRANSLATE_ACTION_UPDATE . "\">\n";
	echo "<p>Base language of your web page:</p><p><blockquote>\n";
	foreach ($languageNames as $languageCode=>$languageName)
	{
		echo "<input type=\"radio\" name=\"" . EHT_TRANSLATE_OPTION_LANGUAGE . "\" value=\"" . $languageCode . "\"";
		if ($languageCode == $language)
		{
			echo " checked";
		}
		echo "> " . EHTTranslateFlag ($languageCode) . " $languageName ($languageCode)<br>\n";
	}
	echo "</blockquote></p>\n";
	echo "<p>Show Google frame:</p><p><blockquote>\n";
	echo "<input type=\"radio\" name=\"" . EHT_TRANSLATE_OPTION_SHOW_FRAME . 
		 "\" value=\"" . EHT_TRANSLATE_SHOW_FRAME_YES . "\"" . 
		 (($showFrame == EHT_TRANSLATE_SHOW_FRAME_YES) ? " checked" : "") . "> Show frame<br>\n";
	echo "<input type=\"radio\" name=\"" . EHT_TRANSLATE_OPTION_SHOW_FRAME . 
		 "\" value=\"" . EHT_TRANSLATE_SHOW_FRAME_NO . "\"" . 
		 (($showFrame == EHT_TRANSLATE_SHOW_FRAME_YES) ? "" : " checked") . "> Don't show frame<br>\n";
	echo "</blockquote></p>\n";
	echo "<p class=\"submit\">\n";
	echo "<input type=\"submit\" value=\"Update Options\">\n";
	echo "</p>\n";
	echo "</form>\n";
	echo "</div>\n";
	echo "<p align=\"center\">Plugin <a href=\"http://emiliogonzalez.sytes.net/index.php/2007/10/09/eht-translate-plugin-para-wordpress/\" target=\"_blank\">EHT Translate</a> - Created by <a href=\"http://emiliogonzalez.sytes.net\" target=\"_blank\">Emilio Gonz&aacute;lez Monta&ntilde;a</a></p>\n";
}

?>
