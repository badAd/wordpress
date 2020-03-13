<?php

// $resiteURL = // Get this from the database
$resiteURL = 'https://badad.one/VYGfZF0SOn3XyifpXG9jukCgGUTYAMG00ANEsmo5y2LKfnOhYHMAS1GVEoGcPDfqlQFyycnS3nPMkCpenwnEpQ5J4qinimMVqqPAZveaasVUWUpMfJW2Z0695bCjYPxV5LccXHJouwX3lBj5JTOP44ufd9dNHBnreJLKDoY1QpXQ7r1dtVpeT5keWSOUchtNeORhSBAJRBY7giuSNSfNGqDOKqx7ChUX4B2PBtCsFWlk6dJeWpoooC8L7bolELK/site.html';

function badadcred() {
  global $resiteURL;
  $content = '<p style="text-align: center;"><a class="badad_shortcode badad_gif" id="baVrtLnk1" title="Unannoying advertising" rel="nofollow" href="' . $resiteURL . '"><img class="aligncenter" id="baVrtImg1" alt="badAad.one" src="' . plugins_url() . '/badad/assets/badadcred.gif" /></a></p>';
  return $content;
}

add_shortcode('badadcred', 'badadcred');

function badadcredTXT() {
  global $resiteURL;
  $content = '<hr class="badad_shortcode badad_txt badad_hr_top"><p style="text-align: center;"><a id="baVrtLnk1" title="Unannoying advertising" rel="nofollow" href="' . $resiteURL . '"><b>badAd.one</b></a></p><hr class="badad_shortcode badad_txt badad_hr_bot">';
  return $content;
}

add_shortcode('badadcredtxt', 'badadcredTXT');

//function ads(int $num, string $align, bool $cred, bool $hit) {}
