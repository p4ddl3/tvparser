<?php

/**
*Note : Currenty, this script download the target webpage and build the resulting display each time you call it. This job can be pretty slow.
*		I'm working on a way to cache the resulting HTML webpage. Thus, the rendering of the tv program will be as fast as lighting :)
*/


include('simple_html_dom.php');
echo '<meta http-equiv="content-type" content="text/html; charset=utf-8"></meta>';
echo '<link rel="stylesheet" type="text/css" href="ink.css">';
/**
*Download the web page containing the full tv program and put it into an array.
*/
function getContent()
{
	$html = file_get_html('http://www.programme-tv.net/programme/programme-tnt.html');
	//$html = file_get_html('http://www.programme-tv.net/programme/toutes-les-chaines/');
	//$html = file_get_html('http://www.programme-tv.net/programme/cable-adsl-satellite/');
	
	$channels = array();
	foreach($html->find('div[class=channel]') as $channel)
	{
		$channelTitle = substr($channel->children(0)->children(0)->title,13);
		$channelLogo = $channel->children(0)->children(0)->children(0)->src;
		$progId = 0;
		$channels[$channelTitle] = array();
		$channels[$channelTitle]['programs'] = array();
		$channels[$channelTitle]['logo'] = $channelLogo;
		foreach($channel->find('div[class=programme]') as $prog)
		{
			$program = array();
			$prog_title = $prog->children(0)->last_child()->title;
			$program['title'] = $prog_title;
			$program['image'] = $prog->children(0)->last_child()->children(0)->src;
			$metadata = $prog->children(1);
			$hour = $metadata->find('.prog_heure');
			$program['hour'] = $hour[0]->plaintext;
			$type = $metadata->find('.prog_type');
			$program['type'] = $type[0]->plaintext;
			
			//Creepy??? Well, that's PHP :)
			$channels[$channelTitle]['programs'][$progId] = $program;
			$progId++;
		}
	}
	return $channels;
}


/**
*(!)W.I.P.
*This function is in charge of displaying the whole thing in the prettiest way possible (depanding on my skills :O  )
*/
function prettyPrint($tvprogram)
{
echo '<div align="center"><table>';

foreach($tvprogram as $channelName => $channelInfos)
{
	echo '<tr><td><img src="'.$channelInfos['logo'].'"></img><td></tr>';
	echo '<tr>';
	foreach($channelInfos['programs'] as $program)
	{
		echo '
			<td>
				<table>
					<tr>
						<td><img src="'.$program['image'].'"/></td>
						<td>
							<table>
								<tr><td><b>'.$program['title'].'</b></td></tr>
								<tr><td><i>'.$program['type'].'</i></td></tr>
								<tr><td>'.$program['hour'].'</td></tr>
							</table>
						</td>
					</tr>
				</table>
				<hr/>
			</td>';
	}
	echo '</tr>';
}
echo '</table></div>';
}
$tvprogram = getContent();
prettyPrint($tvprogram);



?>

