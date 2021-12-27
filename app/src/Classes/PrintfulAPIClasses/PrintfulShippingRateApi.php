<?php
	/**
	 * Created by PhpStorm.
	 * User: Celsius Marketing DD
	 * Date: 7/29/2019
	 * Time: 10:33 PM
	 */
	
	namespace Classes\Printful;
	
	use GuzzleHttp\Client;
	use GuzzleHttp\RequestOptions;
	
	class PrintfulShippingRateApi {
		
		private $client;
		private $apiInfo;
		private $key;
		
		public function __construct( $apiInfo ) {
			
			$this->key = $apiInfo[ 'apikey' ];
			
			$this->apiInfo = $apiInfo;
			
			$this->constructClient( 'https://api.printful.com/', $this->key );
			
		}
		
		private function constructClient( string $url, string $key ) {
			
			$this->key = base64_encode( $key );
			//Verify is set to false because I was having trouble with my local machine and guzzle verifying SSL certs
			$this->client = new Client( [
				'base_uri' => $url,
				'verify'   => false,
				'headers'  => [
					'Content-Type'  => 'application/json',
					'Authorization' => "Basic $this->key",
					'country_code'  => "US",
				],
			] );
			
		}
		
		public function requestPrintful() {
			
			$body = $this->constructBody();
			
			$response = $this->client->post( $this->apiInfo[ 'endpoint' ], [ RequestOptions::JSON => $body ] );
			
			$response = $response->getBody()->getContents();
			
			return $response;
		}
		
		private function constructBody() {
			
			return [
				'recipient' => [
					'address1'     => $this->apiInfo[ 'address' ],
					'country_code' => $this->apiInfo[ 'country' ],
				],
				'items'     => [
					[
						'variant_id' => $this->apiInfo[ 'product_id' ],
						'quantity'   => $this->apiInfo[ 'qty' ],
					],
				],
			];
		}
		
	}