<?php

$hostname = 'confluence.atlassian.com';
$credent = 'Your username and password are encrypted'; // You can also transfer a token. Do not forget to change the authorization type from Basic to Bearer.

if (file_exists(__DIR__.'/config_local.php')) {
    include __DIR__.'/config_local.php';
}

$req = 'mcafee'; // your search request
$pageOrAttach = 'page'; // 'page' or 'attachment'
$textOrTitle = 'title'; // 'text' or 'title'
$reqw = urlencode($req);
$conflUrl = "https://$hostname";
$symbols1 = "%22";
$symbols2 = "%22";
$star = "*";
$linkReq = "{$symbols1}{$reqw}{$star}{$symbols2}";
$q = 'cql=space=sdesk%20and%20type=' . $pageOrAttach . '%20and%20' . $textOrTitle . '%20~%20' . $linkReq  . '*?';
//print $q . '<br>';

$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_URL => "https://$hostname/rest/api/content/search?$q",
    CURLOPT_SSL_VERIFYHOST => false,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_HEADER => 0,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 10,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "GET",
    CURLOPT_POSTFIELDS => "",
    CURLOPT_HTTPHEADER => array(
        "Accept: */*",
        "Authorization: Basic $credent",
        "Cache-Control: no-cache",
        "Connection: keep-alive",
        "Content-Type: application/json",
        "Host: $hostname",
        "accept-encoding: gzip, deflate",
        "User-Agent: Routing Search/beta2.0",
        "cache-control: no-cache",
    ),
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
    echo "cURL Error #:" . $err;
} else {
    $object = json_decode($response, true);
}

$results = array_filter($object['results']);
if(!empty($results)) {
    foreach ($results as $result) {
        $urlss = "{$conflUrl}{$result['_links']['webui']}";
        print "<div class='confluenceResult'>
                    <div class='confluenceResultTitle'>
                        <a href='$urlss' target='_blank'>$result[title]</a>
                    </div>
                    <div class='confluenceResultType'>
                      Тип:  $result[type]
                    </div>
                    <div class='confluenceResultUrl'>";
        print   $conflUrl . $result['_links']['webui'];
        print   "</div>";

        print "<div class='confluenceResultBody'>";
        print $result['excerpt'] . "...";
        print "</div>";
        print "</div>";
    }
} else {
    print "<div class='notFound'>Not found! Try changing the query. You can search for an incomplete word.</div>";
}
