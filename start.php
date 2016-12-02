<?php

elgg_register_event_handler('init', 'system', 'gc_fedsearch_gsa_init');

function gc_fedsearch_gsa_init() {
	// strip out all the (broken) hyperlink so that the GSA doesn't recursively create indices
	elgg_register_plugin_hook_handler('view', 'output/longtext', 'entity_url');
}


function entity_url($hook, $type, $return, $params) {
	
	// do this only for the gsa-crawler
	//if (strcmp('gsa-crawler',strtolower($_SERVER['HTTP_USER_AGENT'])) == 0)  {

		/*blog pages bookmarks file discussion*/
		$filter_entity = array('blog', 'pages');
		$context = get_context();

		// check to see if the entity contains title and description, then it must be some kind of blog, files, etc..
		if ($context && in_array($context, $filter_entity)) {
			$url = explode('/',$_SERVER['REQUEST_URI']);
			$entity = get_entity($url[4]);
			
			// english body text
			$description = new DOMDocument();
			$description->loadHTML($entity->description);
			$links = $description->getElementsByTagName('a');
			for ($i = $links->length - 1; $i >= 0; $i--) {
				$linkNode = $links->item($i);
				$lnkText = $linkNode->textContent;
				$newTxtNode = $description->createTextNode($lnkText);
				$linkNode->parentNode->replaceChild($newTxtNode, $linkNode);
			}
			$return = $description->textContent."<br/><br/>";


			// french body text
			$description->loadHTML($entity->description2);
			$links = $description->getElementsByTagName('a');
			for ($i = $links->length - 1; $i >= 0; $i--) {
				$linkNode = $links->item($i);
				$lnkText = $linkNode->textContent;
				$newTxtNode = $description->createTextNode($lnkText);
				$linkNode->parentNode->replaceChild($newTxtNode, $linkNode);
			}
			$return .= $description->textContent;	
		}
	//}
   
	return $return;
}

