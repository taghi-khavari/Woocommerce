<?php
add_filter( 'woocommerce_currency_symbol', 'weblandtk_symbols', 30 , 2);
function weblandtk_symbols( $currency_symbol , $currency ){
  $currency_symbol = 'USD $';
  return $currency_symbol;
}
