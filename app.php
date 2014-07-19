<?php
$id = trim(file_get_contents('userid.txt'));
$url_req = "https://api.stackexchange.com/2.2/users/{$id}/favorites?order=desc&sort=added&site=stackoverflow";

$query = trim($argv[1]);

$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $url_req);
curl_setopt($curl, CURLOPT_ENCODING, "");
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); 
$resp = curl_exec($curl);
curl_close($curl);

$data = json_decode($resp, true);

$xml = <<<'EOD'
  <?xml version="1.0"?>
  <items>
EOD;

foreach ($data["items"] as $question){
  $url = $question["link"];
  $title = $question["title"];
  $tags = $question["tags"];
  $tag_list = implode(",", $tags);
  $term_list = "$title $tag_list";
  $query_matched = stripos($term_list, $query);

  if ( !($query_matched === false) ){
    $xml .= "<item arg=\"$url\">\n";
    $xml .= "<title>$title</title>\n";
    $xml .= "<subtitle>$url</subtitle>\n";
    $xml .= "<icon>icon.png</icon>\n";
    $xml .= "</item>\n";
  }
}

$xml .="</items>";
echo $xml;
