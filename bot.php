<?php

require_once('./line_class.php');
require_once('./unirest-php-master/src/Unirest.php');

$channelAccessToken = '0HTAZoDwTrJ7sCb1XZVFPuDqkt/HF0DPDW1GY8NWtf9X6x9Ko/+WHbiGBOqfItiQQrYFv9Dsi8CD/2uA/etHuH2Dqn2kDGYCzSGjwuB5g6vh48GMR/3pYeOlUUPqgSgNs/HmVa/JQknlsZEs5lWzMAdB04t89/1O/w1cDnyilFU='; //sesuaikan 
$channelSecret = 'f0c73f23492125096da452b0ee3f6965';//sesuaikan

$client = new LINEBotTiny($channelAccessToken, $channelSecret);

$userId 	= $client->parseEvents()[0]['source']['userId'];
$groupId 	= $client->parseEvents()[0]['source']['groupId'];
$replyToken = $client->parseEvents()[0]['replyToken'];
$timestamp	= $client->parseEvents()[0]['timestamp'];
$type 		= $client->parseEvents()[0]['type'];

$message 	= $client->parseEvents()[0]['message'];
$messageid 	= $client->parseEvents()[0]['message']['id'];

$profil = $client->profil($userId);

$pesan_datang = explode(" ", strtoupper($message['text']));

$command = $pesan_datang[0];
$options = $pesan_datang[1];



if (count($pesan_datang) > 2) {
    for ($i = 2; $i < count($pesan_datang); $i++) {
        $options .= '+';
        $options .= $pesan_datang[$i];
    }
}

/*
if ($kata1 != "HALAL" or $kata1 != "HELP" or $kata1 != "HALO" or $kata1 != "HALO," or $kata1 != "HAI," or $kata1 != "HAI"){
		$balas = array(
            	'replyToken' => $replyToken,
            	'messages' => array(
                	array(
                    	'type' => 'text',
                    	'text' => 'Bakso Bakso...?? ^_^ '.$kata1
                		)
            		)
        	);
}
*/
if ($command <> 'HALAL' || $command <> 'HALO'|| $command <> 'HAI'){
		$balas = array(
            	'replyToken' => $replyToken,
            	'messages' => array(
                	array(
                    	'type' => 'text',
                    	'text' => 'Maaf, maksudnya gmn ya kak?? ketik Help untuk bantuan ya?? ^_^'
                		)
            		)
        	);
	}



#-------------------------[Function]-------------------------#
function muiHalal($keyword) {
    $uri = "https://sites.google.com/macros/exec?service=AKfycbx_-gZbLP7Z2gGxehXhWMWDAAQsTp3e3bmpTBiaLuzSDQSbIFWD&menu=nama_produk&query=" . $keyword;
// identifikasi mata uang

    $response = Unirest\Request::get("$uri");

    $json = json_decode($response->raw_body, true);
	if ($json['status'] == 'success'){
 	$result = "Halo Gan, berikut produk yg ditemukan :";
		for ($i=0; $i<10; $i++){
		$result .= "\nNama Produk : ";
		$result .= $json['data'][$i]['title'];
		$result .= "\nNo. Sertifikat : ";
		$result .= $json['data'][$i]['nomor_sertifikat'];
		$result .= "\nProdusen : ";
		$result .= $json['data'][$i]['produsen'];
		$result .= "\nBerlaku Sampai : ";
		$result .= $json['data'][$i]['berlaku_hingga'];
		$result .= "\n";
		}
	}

    return $result;
}
#-------------------------[Function]-------------------------#

# require_once('./src/function/search-1.php');
# require_once('./src/function/download.php');
# require_once('./src/function/random.php');
# require_once('./src/function/search-2.php');
# require_once('./src/function/hard.php');

//show menu, saat join dan command /menu
if ($type == 'join' || $command == 'MENU') {
    $text = "Assalamualaikum Agan, untuk mendapatkan Nilai Mata uang, silahkan ketik\n\n KURS <Kode Mata Uang>\n\nnanti aku informasikan nilai mata uangnya ya?? ^_^";
    $balas = array(
        'replyToken' => $replyToken,
        'messages' => array(
            		array(
	    		'type' => 'image',
    			'originalContentUrl' => 'https://example.com/original.jpg',
    			'previewImageUrl' => 'https://example.com/preview.jpg'
				)
		)
    );
}
if ($command == 'HALO' || $command == 'HAI' || $command == 'HALO,' || $command == 'HAI,' ||$command == 'SELAMAT') {
    $aray = ["Halo kakak.. Apa Kabar","Selamat Pagi.. Ayo Semangat","Hai Kakak, Gimana Kabarnya","Halo.. Halo.. Halo.."];
    shuffle($aray);
    $text = array_shift($aray);
    $balas = array(
        'replyToken' => $replyToken,
        'messages' => array(
            array(
                'type' => 'text',
                'text' => $text
            )
        )
    );
}
//pesan bergambar
if($message['type']=='text') {
	if ($command == 'HALAL') {

        $result = muiHalal($options);
        $balas = array(
            'replyToken' => $replyToken,
            'messages' => array(
                array(
                    'type' => 'text',
                    'text' => $result
                )
            )
        );
    }
/* else if ($command == 'HALO'){
	$balas = array(
        'replyToken' => $replyToken,
        'messages' => array(
              	array(
               	'type' => 'text',
               	'text' => 'Halo.. Ada yg bisa dibantu ?? ^_^'
        	)
           )
       	);
}*/
}else if($message['type']=='sticker')
{	
	$balas = array(
							'replyToken' => $replyToken,														
							'messages' => array(
								array(
										'type' => 'text',									
										'text' => 'Makasih Kak Stikernya ^_^'										
									
									)
							)
						);
						
}
if (isset($balas)) {
    $result = json_encode($balas);
//$result = ob_get_clean();

    file_put_contents('./balasan.json', $result);


    $client->replyMessage($balas);
}
?>
