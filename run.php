<?php

include('config.php');

echo "[+] Auto Commit and Push to Github \n\n";
echo "How many Commit do you want ? ";
$n = trim(fgets(STDIN));


$folder = "folder".rand();
mkdir($folder,0700);

$initGit = shell_exec('cd '.$folder.' && git init');

$remoteUrl = createRepo($token,$folder);

if($remoteUrl != null){
    for ($i=1 ; $i<= $n; $i++){
        echo'['.$i.']';
        makeCommit($folder);
    }
}

gitPushToRepo($folder,$username,$token,$remoteUrl);
echo "\n\n[SELESAI]\n\n";


function makeCommit($folder){
    $namafile = $folder.'/'.$folder.".txt";
    $file = fopen($namafile,"w+");
    fwrite($file,rand());
    fclose($file);
    
    shell_exec('cd '.$folder.' && git add . && git commit -m "commit '.rand().' "');
    echo " - [Make Commit]\n";
}

function gitPushToRepo($folder,$username,$token,$remoteUrl){
    shell_exec('cd '.$folder.' && git remote add origin '.$remoteUrl);
    $link = 'https://'.$username.':'.$token.'@github.com/'.$username.'/'.$folder;
    shell_exec('cd '.$folder.' && git push '.$link);
    echo "\n[Pushed To Github]";
}

function createRepo($token,$folder){
    $headers = array();
    $headers[] = 'Server: GitHub.com';
    $headers[] = 'Accept: */*';
    $headers[] = 'Accept-Language: en-US,en;q=0.5';
    $headers[] = 'Content-Type: application/json; charset=utf-8';
    $headers[] = 'X-GitHub-Media-Type: github.v3';
    $headers[] = 'X-RateLimit-Limit: 5000';
    $headers[] = 'Cache-Control: public, max-age=60, s-maxage=60';
    $headers[] = 'X-Content-Type-Options: nosniff';
    $headers[] = 'Vary: Accept';
    $headers[] = 'Authorization: token '.$token;
    
    $data = array(
        'name' => $folder,
        'private' => false,
    );
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api.github.com/user/repos");
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:82.0) Gecko/20100101 Firefox/82.0');
    $json = curl_exec($ch);
    $result = json_decode($json,true);

    if(isset($result['message'])) {
        echo $result['message'];
        return null;
    }else {
        echo "[Berhasil Buat Repo Di Github]\n\n";
        return $result['clone_url'];
    }
}


?>