<?php
	/**
	 * Created by PhpStorm.
	 * User: Celsius Marketing DD
	 * Date: 7/29/2019
	 * Time: 3:17 PM
	 */
	
	namespace Classes\Cache;
	
	class CacheMechanism implements CacheInterface {
		
		/**
		 * @var string
		 */
		private $cacheFolder;
		private $fullPath;
		
		public function __construct( string $cacheFolder ) {
			
			$this->cacheFolder = $cacheFolder;
			
		}
		
		/**
		 * Store a mixed type value in cache for a certain amount of seconds.
		 * Allowed values are primitives and arrays.
		 *
		 * @param string $key
		 * @param mixed $value
		 * @param int $duration Duration in seconds
		 *
		 * @return mixed
		 */
		public function set( string $key, $value, int $duration ) {
			
			$timeSec = $this->getTime();
			
			//convert duration to seconds
			$timeToAdd = $duration * 60;
			//raw value to check against
			$ttdRaw = $timeSec + $timeToAdd;
			//for developers
			$ttdReadable = date( 'Y-m-d H:i:s', $ttdRaw );
			
			if ( ! file_exists( $this->fullPath ) ) {
				file_put_contents( $this->fullPath, '' );
			}
			
			if ( ! is_writable( $this->fullPath ) ) {
				throw new \Exception( sprintf( 'Unable to write to %s.', $this->fullPath ) );
			}
			
			//We don't include private data for security (No Address)
			$body = array (
				"values"     => $value,
				"attributes" => [
					'key'         => $key,
					'expRaw'      => $ttdRaw,
					'expReadable' => $ttdReadable,
				],
			);
			
			$json = json_encode( $body );
			
			file_put_contents( $this->fullPath, $json, LOCK_EX );
			
		}
		
		private function getTime() {
			
			//get current time
			$time = date( 'Y-m-d H:i:s' );
			//convert datetime to seconds
			$timeSec = strtotime( $time );
			
			return $timeSec;
		}
		
		/**
		 * Retrieve stored item.
		 * Returns the same type as it was stored in.
		 * Returns null if entry has expired.
		 *
		 * @param string $key
		 *
		 * @return mixed|null
		 */
		public function get( string $key ) {
			
			$timeSec = $this->getTime();
			
			//We name the file with the $key because it has no meaning
			//If we were storing multiple data sets in here we could approach this differently.
			$this->fullPath = sprintf( DOCUMENT_ROOT . '%s/%s.json', $this->cacheFolder, $key );
			
			if ( ! is_writable( $this->fullPath ) ) {
				throw new \Exception( sprintf( 'Unable to write to %s.', $this->fullPath ) );
			}
			
			$cachedData = json_decode( file_get_contents( $this->fullPath ), true );
			
			if ( $timeSec > $cachedData[ 'attributes' ][ 'expRaw' ] ) {
				
				file_put_contents( $this->fullPath, '', LOCK_EX );
				
				//Date Expired
				return "null";
				
			}
			
			return $cachedData;
		}
	}
	