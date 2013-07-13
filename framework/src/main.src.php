<?php
/**
 * XOMBO
 * main.src.php default function that gets called when a web application warms up
 *
 * @license Apache License V2 <http://www.apache.org/licenses/LICENSE-2.0.html>
 * @author Nolte Burke
 * 	
 * 	Copyright 2013 Nolte Burke
 * 	
 * 	Licensed under the Apache License, Version 2.0 (the "License");
 * 	you may not use this file except in compliance with the License.
 * 	You may obtain a copy of the License at
 * 	
 * 		http://www.apache.org/licenses/LICENSE-2.0
 * 	
 * 	Unless required by applicable law or agreed to in writing, software
 * 	distributed under the License is distributed on an "AS IS" BASIS,
 * 	WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * 	See the License for the specific language governing permissions and
 * 	limitations under the License.
 */
function main () {
	if (count (XOMBO\response::getAll ()) == 0)
		XOMBO\response::factory (new XOMBO\request ())->setError (404, "No results to display");

	header ("Content-Type: " . (array_key_exists ("json", $_REQUEST) ? "application/javascript" : (array_key_exists ("html", $_REQUEST) ? "text/html" : "application/xhtml+xml")));

	ob_start ();
	// output of result sets
	$callback = array_key_exists ("json", $_REQUEST) && array_key_exists ("callback", $_REQUEST) ? $_REQUEST["callback"] : NULL;
	echo !array_key_exists ("json", $_REQUEST) ? "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\n" : (is_string ($callback) ? $callback . " (" : "");
	if (array_key_exists ("html", $_REQUEST) && !array_key_exists ("json", $_REQUEST))
		$xsl = call_user_func (
					(
						is_model ((array_key (XOMBO\response::getAll (), 0)->getRequest ()->getNamespace () == "" ? "" : array_key (XOMBO\response::getAll (), 0)->getRequest ()->getNamespace () . "\\") . array_key (XOMBO\response::getAll (), 0)->getRequest ()->getClass ()) ? 
							(array_key (XOMBO\response::getAll (), 0)->getRequest ()->getNamespace () == "" ? "" : array_key (XOMBO\response::getAll (), 0)->getRequest ()->getNamespace () . "\\") . array_key (XOMBO\response::getAll (), 0)->getRequest ()->getClass () . "Controller"
							 : 
							(array_key (XOMBO\response::getAll (), 0)->getRequest ()->getNamespace () == "" ? "" : array_key (XOMBO\response::getAll (), 0)->getRequest ()->getNamespace () . "\\") . array_key (XOMBO\response::getAll (), 0)->getRequest ()->getClass ()
					)
					 . "::xsl"
				, TRUE);

	if (count (XOMBO\response::getAll ()) == 1) {
		$response = array_pop (XOMBO\response::getAll ());
		if (!array_key_exists ("json", $_REQUEST)) echo "<?xml-stylesheet type=\"text/xsl\" href=\"/" . $response->getRequest ()->getClass () . "/xsl\" ?>\n";
		echo $response;
	} else {
		$first = TRUE;
		echo !array_key_exists ("json", $_REQUEST) ? "<?xml-stylesheet type=\"text/xsl\" href=\"/" . array_key (XOMBO\response::getAll (), 0)->getRequest ()->getClass () . "/xsl\" ?>\n<responses>\n" : "[";
		while ($r = XOMBO\response::shift ()) {
			if ($first) $first = FALSE;
			else echo !array_key_exists ("json", $_REQUEST) ? "" : ",";
			echo $r;
		}
		echo !array_key_exists ("json", $_REQUEST) ? "</responses>" : "]";
	}

	echo is_string ($callback) ? ");" : "";

	if (array_key_exists ("html", $_REQUEST) && !array_key_exists ("json", $_REQUEST)) {
		$xsl = preg_replace_callback (
			'/(<xsl:[a-zA-Z0-9\-\_\.]+ select=\"document\(\')([a-zA-Z0-9\.\-\_\/]+)(\'\))/',
			create_function ('$matches', 'return preg_match (\'/(.*)(\.xml)$/\', $matches[2]) ? $matches[1] . $_SERVER[\'DOCUMENT_ROOT\'] . $matches[2] . $matches[3] : $matches[1] . "http" . (!empty ($_SERVER[\'HTTPS\']) ? "s" : "") . "://" . $_SERVER[\'HTTP_HOST\'] . $matches[2] . "' . (defined ("SID") ? "?" . SID : "") . '" . $matches[3];'),
			$xsl
		);
		$merge = new XSLTProcessor();
		$xsldoc = new DOMDocument();
		$xsldoc->loadXML($xsl);
		$merge->importStyleSheet($xsldoc);
		$xmldoc = new DOMDocument();
		$xmldoc->loadXML(ob_get_clean ());
		echo "<!DOCTYPE html>\n" . $merge->transformToXML($xmldoc) . (array_key_exists ("debug", $_REQUEST) ? "<!-- rendered on server //-->" : "");
	}
	return 0;
}

exit (main ());