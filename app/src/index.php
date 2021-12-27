<?php
	/**
	 * Created by PhpStorm.
	 * User: Celsius Marketing DD
	 * Date: 7/29/2019
	 * Time: 3:10 PM
	 */
	
	require 'vendor/autoload.php';
	//Requiring settings.php for transparency and to ensure functionality.
	require_once 'settings.php';
	
	use Classes\Cache\CacheMechanism;
	use Classes\Cache\CacheInterface;
	use Classes\Printful as Printful;
	
	//I used this array instead of const inside of the cache class because in a prod environment client would dbe passing us this data.
	
	$staticInfo = array (
		'address'    => '11025 Westlake Dr, Charlotte, North Carolina, 28273',
		'country'    => 'US',
		'product_id' => '7679',
		'qty'        => '2',
		'endpoint'   => '/shipping/rates',
		'apikey'     => '77qn9aax-qrrm-idki:lnh0-fm2nhmp0yca7',
	);
	
	class Execute {
		
		private $cacheMechanism;
		private $key;
		private $shippingInfo;
		private $data;
		
		public function __construct( CacheInterface $cacheMechanism, $data ) {
			
			//Timezone set in /src/settings.php
			date_default_timezone_set( TIMEZONE );
			
			$this->cacheMechanism = $cacheMechanism;
			
			$this->data = $data;
			//stripping any characters that could become out of place from the address if used on a live environment to remove the chance of the key coming out differently each time
			$prepKey = preg_replace( '/[^a-zA-Z0-9]/', '', $this->data[ 'address' ] );
			
			$this->setKey( $prepKey );
			
			$cachedData = $this->cacheMechanism->get( $this->key );
			
			if ( ! isset( $cachedData ) || ! $cachedData || $cachedData === 'null' ) {
				echo "Expired";
				
				$requestPrint = new Printful\PrintfulShippingRateApi( $this->data );
				
				$this->shippingInfo = $requestPrint->requestPrintful();
				
				//ReadMe Comment Here
				//print shipping info on initial api call
				//print_r($this->shippingInfo);
				
			} else {
				
				print_r( $cachedData[ 'values' ] );
				
				return;
			}
			
			$this->cacheMechanism->set( $this->key, $this->shippingInfo, DURATION );
			
		}
		
		private function setKey( string $str ) {
			
			$this->key = md5( $str );
		}
		
	}
	
	$cacheMechanism = new CacheMechanism( CACHE_FOLDER );
	$ini            = new Execute( $cacheMechanism, $staticInfo );