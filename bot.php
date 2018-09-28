<?php
/*
copyright @ medantechno.com
Modified @ Farzain - zFz
2017

*/

require_once('./line_class.php');
require_once('./unirest-php-master/src/Unirest.php');

$channelAccessToken = '8Ryaf6ulY1+ehr4/hNItyUTVzMxRKX1t/eNt46EuJ8OvpjhIcG/NtqcHEZr7Z/KRr6LiWm5JcEo2oSj2MwsredA31E9FIYVxR+IdGzP7RBrsonATFDHHLBZ32XRKA+bzABnuFywzSV6iAKnanC0cFwdB04t89/1O/w1cDnyilFU='; //sesuaikan 
$channelSecret = '5cd2bd7d45323f47bbedb6e32b03075f';//sesuaikan

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
// identifikasi mata uang
if ($pesan_datang[1] == 'USD'){
	$mataUang = "Dollar Amerika Serikat"
}else if($pesan_datang[1] == 'AUD'){
	$mataUang = "Dollar Australia"
}else if($pesan_datang[1] == 'CAD'){
	$mataUang = "Dollar Kanada"
}else if($pesan_datang[1] == 'CHF'){
	$mataUang = "Franc Swiss"
}else if($pesan_datang[1] == 'CNY'){
	$mataUang = "Yuan China"
}else if($pesan_datang[1] == 'DKK'){
	$mataUang = "Krone Denmark"
}else if($pesan_datang[1] == 'EUR'){
	$mataUang = "Euro"
}else if($pesan_datang[1] == 'GBP'){
	$mataUang = "Poundsterling German"
}else if($pesan_datang[1] == 'HKD'){
	$mataUang = "Dollar Hongkong"
}else if($pesan_datang[1] == 'JPY'){
	$mataUang = "Yen Japan"
}else if($pesan_datang[1] == 'NZD'){
	$mataUang = "Dollar New Zealand"
}else if($pesan_datang[1] == 'SAR'){
	$mataUang = "Riyal Saudi Arabia"
}else if($pesan_datang[1] == 'SEK'){
	$mataUang = "Krona Swedia"
}else if($pesan_datang[1] == 'SGD'){
	$mataUang = "Dollar Singapore"
}	
if (count($pesan_datang) > 2) {
    for ($i = 2; $i < count($pesan_datang); $i++) {
        $options .= '+';
        $options .= $pesan_datang[$i];
    }
}

#-------------------------[Function]-------------------------#
function proKurs($keyword) {
    $uri = "http://www.adisurya.net/kurs-bca/get?MataUang=" . $keyword;

    $response = Unirest\Request::get("$uri");

    $json = json_decode($response->raw_body, true);
//	if ($json['message']['code'] == 200){
    $result = "KURS MATA UANG ";
	$result .= "\n";
	$result .= $matauang 
//	$result	. "(";
//	$result .= $keyword . ")";
	$result .= "\n\nWaktu Efektif : ";
	$result .= $json['LastUpdate'];
	$result .= "\nHarga Jual : ";
	$result .= number_format($json['Data'][$keyword]['Jual']);
	$result .= "\nHarga Beli : ";
	$result .= number_format($json['Data'][$keyword]['Beli']);

    return $result;
}
#-------------------------[Function]-------------------------#

# require_once('./src/function/search-1.php');
# require_once('./src/function/download.php');
# require_once('./src/function/random.php');
# require_once('./src/function/search-2.php');
# require_once('./src/function/hard.php');

//show menu, saat join dan command /menu
if ($type == 'join' || $command == '/menu') {
    $text = "Assalamualaikum Agan, untuk mendapatkan Nilai Mata uang, silahkan ketik\n\n KURS <Kode Mata Uang>\n\nnanti aku informasikan nilai mata uangnya ya?? ^_^";
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
	    if ($command == 'KURS') {

        $result = proKurs($options);
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
