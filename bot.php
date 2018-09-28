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


if (count($pesan_datang) > 2) {
    for ($i = 2; $i < count($pesan_datang); $i++) {
        $options .= '+';
        $options .= $pesan_datang[$i];
    }
}

#-------------------------[Function]-------------------------#
function proKurs($keyword) {
    $uri = "http://www.adisurya.net/kurs-bca/get?MataUang=" . $keyword;
// identifikasi mata uang
/*
if ($keyword == 'USD'){
	$mataUang = "Dollar Amerika Serikat"
}else if($keyword == 'AUD'){
	$mataUang = "Dollar Australia"
}else if($keyword == 'CAD'){
	$mataUang = "Dollar Kanada"
}else if($keyword == 'CHF'){
	$mataUang = "Franc Swiss"
}else if($keyword == 'CNY'){
	$mataUang = "Yuan China"
}else if($keyword == 'DKK'){
	$mataUang = "Krone Denmark"
}else if($keyword == 'EUR'){
	$mataUang = "Euro"
}else if($keyword == 'GBP'){
	$mataUang = "Poundsterling German"
}else if($keyword == 'HKD'){
	$mataUang = "Dollar Hongkong"
}else if($keyword == 'JPY'){
	$mataUang = "Yen Japan"
}else if($keyword == 'NZD'){
	$mataUang = "Dollar New Zealand"
}else if($keyword == 'SAR'){
	$mataUang = "Riyal Saudi Arabia"
}else if($keyword == 'SEK'){
	$mataUang = "Krona Swedia"
}else if($keyword == 'SGD'){
	$mataUang = "Dollar Singapore"
}
*/
    $response = Unirest\Request::get("$uri");

    $json = json_decode($response->raw_body, true);
//	if ($json['message']['code'] == 200){
        $result = "KURS MATA UANG";
// 	$result .= $matauang;
//	$result .= "\n";
//	$result .= $matauang 
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
