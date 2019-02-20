<?php

  include('simple_html_dom.php');

  $dodexUkPrefix = "https://www.dodax.co.uk/en-gb/music-cds-dvds-vinyl/";
  $dodexDePrefix = "https://www.dodax.de/de-de/music-cds-dvds-vinyl/";
  $dodexNlPrefix = "https://www.dodax.nl/nl-nl/music-cds-dvds-vinyl/";
  $dodexEsPrefix = "https://www.dodax.es/es-es/music-cds-dvds-vinyl/";
  $dodexAtPrefix = "https://www.dodax.at/de-at/music-cds-dvds-vinyl/";
  $dodexItPrefix = "https://www.dodax.it/it-it/music-cds-dvds-vinyl/";
  $dodexPlPrefix = "https://www.dodax.pl/pl-pl/music-cds-dvds-vinyl/";

  $albums = array(
  	"classic-rock/alt-j-relaxer-dpR2QSF17M4QK/",
  	"pop/arcade-fire-everything-now-dpRGDSG4QLSHE/",
  	"pop/arcade-fire-neon-bible-dp88IKU1HHBG6/",
  	"pop/arcade-fire-put-your-money-on-me-dpRHELSR1P2VF/",
  	"pop/arcade-fire-suburbs-dpARF0ST9O8AI/"
  ); 

  
  $emailBody =  $emailBody . "<html>";
  $emailBody =  $emailBody . "<head>";
  $emailBody =  $emailBody . "<link rel='stylesheet' type='text/css' href='style.css'/>";
  $emailBody =  $emailBody . "</head>";
  $emailBody =  $emailBody . "<body>";
  $emailBody =  $emailBody . "<table class='paleBlueRows'><thead><tr>";
  $emailBody =  $emailBody . "<th width='30%'><strong>Artist</strong></th><th width='50%'><strong>Album</strong></th>";
  $emailBody =  $emailBody . "<th width='20%'><strong>Cheapest Price</strong></th>";
  $emailBody =  $emailBody . "</style></thead></tr>";

  $html = new simple_html_dom();
    
  foreach ($albums as $album) {
    $cheapestPrice = 0.00;
    $cheapestUrl = "";

    /* UK */
    $string = getSource($dodexUkPrefix . $album);
    $html->load($string);

    $artistName = strtoupper(trim($html->find('p[class="info_options hidden-md-down"]', 0)));
    $albumName = strtoupper(trim($html->find('h2[class="product_title font_bold"]', 0)->plaintext));
    
    $gbpPrice = get_currency(trim(str_replace(",",".",str_replace("£", "", $html->find('span[class="current_price font_bold"]', 0)->plaintext))),"GBP");
    $gbpPriceConverted = get_currency($gbpPrice,"GBP");
    if(($gbpPrice != "") && ($cheapestPrice == 0.00 || $cheapestPrice > $gbpPriceConverted)) {
    	$cheapestPrice = $gbpPriceConverted;
    	$cheapestUrl = "<a href='" . $dodexUkPrefix . $album . "' target=_blank>";
    }

    /* DE */
    $string = getSource($dodexDePrefix . $album);
    $html->load($string);
    $dePrice = trim(str_replace(",",".",str_replace("€ ", "", $html->find('span[class="current_price font_bold"]', 0)->plaintext)));
    if(($dePrice != "") && ($cheapestPrice == 0.00 || $cheapestPrice > $dePrice)) {
      $cheapestPrice = $dePrice;
    	$cheapestUrl = "<a href='" . $dodexDePrefix . $album . "' target=_blank>";
    }

    /* NL */
    $string = getSource($dodexNlPrefix . $album);
    $html->load($string);
    $nlPrice = trim(str_replace(",",".",str_replace("€ ", "", $html->find('span[class="current_price font_bold"]', 0)->plaintext)));
    if(($nlPrice != "") && ($cheapestPrice == 0.00 || $cheapestPrice > $nlPrice)) {
      $cheapestPrice = $nlPrice;
    	$cheapestUrl = "<a href='" . $dodexNlPrefix . $album . "' target=_blank>";
    }

    /* ES */
    $string = getSource($dodexEsPrefix . $album);
    $esPrice = trim(str_replace(",",".",str_replace("€ ", "", $html->find('span[class="current_price font_bold"]', 0)->plaintext)));
    if(($esPrice != "") && ($cheapestPrice == 0.00 || $cheapestPrice > $esPrice)) {
      $cheapestPrice = $esPrice;
    	$cheapestUrl = "<a href='" . $dodexEsPrefix . $album . "' target=_blank>";
    }

    /* AT */
    $string = getSource($dodexAtPrefix . $album);
    $html->load($string);
    $atPrice = trim(str_replace(",",".",str_replace("€ ", "", $html->find('span[class="current_price font_bold"]', 0)->plaintext)));
    if(($atPrice != "") && ($cheapestPrice == 0.00 || $cheapestPrice > $atPrice)) {
      $cheapestPrice = $atPrice;
    	$cheapestUrl = "<a href='" . $dodexAtPrefix . $album . "' target=_blank>";
    }

    /* IT */
    $string = getSource($dodexItPrefix . $album);
    $html->load($string);
    $itPrice = trim(str_replace(",",".",str_replace("€ ", "", $html->find('span[class="current_price font_bold"]', 0)->plaintext)));
    if(($itPrice != "") && ($cheapestPrice == 0.00 || $cheapestPrice > $itPrice)) {
      $cheapestPrice = $itPrice;
    	$cheapestUrl = "<a href='" . $dodexItPrefix . $album . "' target=_blank>";
    }

    /* PL */
    $string = getSource($dodexPlPrefix . $album);
    $html->load($string);
    $plPrice = trim(str_replace(",",".",str_replace(" zł", "", $html->find('span[class="current_price font_bold"]', 0)->plaintext)));
    $plPriceConverted = get_currency($plPrice,"PLN");
    if(($plPrice != "") && ($cheapestPrice == 0.00 || $cheapestPrice > $plPriceConverted)) {
      $cheapestPrice = $plPriceConverted;
    	$cheapestUrl = "<a href='" . $dodexPlPrefix . $album . "' target=_blank>";
    }

    $row = "<tr>";
    $row = $row . "<td>" . $artistName . "</td>";
    $row = $row . "<td>" . $albumName . "</td>";
    $row = $row . "<td><strong>" . $cheapestUrl . "&euro;" . $cheapestPrice . "</a></strong></td>";
    $row = $row . "</tr>";
    $emailBody = $emailBody . $row;
  }

  $emailBody = $emailBody . "</table></body></html>";
  
  $to = '<email address here>';
  $send = '<email address here>';
  $subject = 'Dodex Vinyl Price Update';

  $headers = 'From: Dodax Price Checker <' . $to . ">\r\n" ;
  $headers .='Reply-To: '. $to . "\r\n" ;
  $headers .='X-Mailer: PHP/' . phpversion();
  $headers .= "MIME-Version: 1.0\r\n";
  $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";   
  mail($send, $subject, $emailBody, $headers);
  	
  function get_currency($amount,$fromCurrency)  {
    $fxRate = 0.00;
    if($fromCurrency == "GBP") {
       $fxRate = 1.14;
    }
    else if($fromCurrency == "PLN") {
       $fxRate = 0.23;
    }
    $total = $fxRate * $amount;
    return number_format($total, 2, '.', '');
  }

  function getSource($website) {
    return file_get_contents($website);
  }

?>