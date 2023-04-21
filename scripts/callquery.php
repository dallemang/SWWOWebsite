<?php



function haslash ($s) {return (strpos($s, "/")>0);};

function matchmime ($m1, $pat) {
 
    $m1s=explode("/", $m1);
    $pats=explode("/", $pat);
    if (array_values($pats)[0]=='*' or array_values($pats)[0]==array_values($m1s)[0]) {
       if (array_values($pats)[1]=='*' or array_values($pats)[1]==array_values($m1s)[1]) {
        return (true);
    }; return (false);};
    return (false);
}

$mimes=array (
"text/turtle" ,
"application/rdf+json" ,
"application/rdf+xml" ,
"text/plain" ,
"text/html"
);

$accepts=array_filter(preg_split ("/[;,]/", $_SERVER["HTTP_ACCEPT"]), "haslash");


$themime="";
foreach ($accepts AS $a) {
    if ($themime=="") {
        foreach ($mimes AS $m) {
            if ($themime=="") {
                if (matchmime ($m, $a)) {
                   $themime=$m  ;
                    }
                }   
            }   
        }
    }

$othermime=$themime;
if ($themime=="text/html" or $themime=="text/plain") {
    $othermime="text/turtle";
}

   
$data = '{"parameters": {"resource": "' . $_SERVER["SCRIPT_URI"] . '"}}';

$q = $_GET['q'];
$org = $_GET['org'];
$dataset = $_GET['dataset'];
$file = $_GET['file'];

$headers= array('Authorization: Bearer ' .  file_get_contents ('../../etc/champ') ,
                'Accept: application/json' 
                );


$call="https://api.data.world/v0/sql/".$org."/".$dataset."?query=".rawurlencode($q);


$ch = curl_init();
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_URL, $call);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$output = curl_exec($ch);

if (curl_errno($ch)) {
    $error_msg = curl_error($ch);
}
curl_close($ch);

    echo $output;

?>
