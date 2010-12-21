<?
/**
 * A class to handle the YouTube playlist object.
 * @since	20101103, hafner
 */

require_once 'Zend/Loader.php';
Zend_Loader::loadClass('Zend_Gdata_Photos');
Zend_Loader::loadClass( 'Zend_Gdata_Photos_AlbumQuery' );
Zend_Loader::loadClass( 'Zend_Gdata_Photos_PhotoQuery' );

class PicasaAlbum
{
	/**
	 * Instance of the Zend_Gdata_Photos class.
	 * @var YouTube object
	 */
	protected $m_picasa;
	
	/**
	 * Instance of the common object.
	 * @var	Common
	 */
	protected $m_common;
	
	/**
	 * Username for this class.
	 */
	//photonorthwest
	const USERNAME = "colehafner";
	
	/**
	 * Initializes the Zend_Gdata_Photos object for this class.
	 * @since	20101103, hafner
	 */
	function __construct()
	{
		$this->m_common = new Common();
		$this->m_picasa = new Zend_Gdata_Photos();
		$this->m_picasa->setMajorProtocolVersion(2);
	}
	
	public function getAlbumList()
	{
		$return = array();
		$all_albums = $this->m_picasa->getUserFeed( self::USERNAME );
		
		foreach ( $all_albums as $i => $entry ) 
		{
			$return[$i]['id'] = $entry->gphotoId->text;
			$return[$i]['title'] = $entry->title->text;
		}
		
		return $return;
		
	}//getAlbumList()
	
	/** 
	 * Returns a collection of playlist objects for the current user.
	 * @return 	Zend_Gdata_Photos_PlaylistFeed object.
	 * @since	20101103, hafner
	 */
	public function getAlbum( $selector, $value, $vars = array() )
	{
		//setup query
		$img_max = ( array_key_exists( 'img_max', $vars ) ) ? $vars['img_max'] : "800u";
		$query = new Zend_Gdata_Photos_AlbumQuery();
		$query->setUser( self::USERNAME );
		$query->setImgMax( $img_max );
		
		//specify album
		if( $selector == "title" )
		{
			$query->setAlbumName( self::convertAlbumName( $value ) );
		}
		else 
		{
			$query->setAlbumId( $value );
		}
		
		//get feed
		return $this->m_picasa->getAlbumFeed( $query );
				
	}//getAllAlbums()
	
	/**
	 * Returns info for every photo in the album resource.
	 * @return	array
	 * @param	resource		$album_feed		result of  $this->m_service->getAlbumFeed()
	 * @mod	20100403
	 */
	public function getAlbumSummary( $album_feed, $vars = array() ) 
	{
		//1 indexed for data grid feed
		$i = 1;
		
		//collect data
		foreach( $album_feed as $pe ) 
		{
			//get photo summary
			$return[$i] = $this->getPhotoSummary( &$pe, $vars );
			$i++;
		}
		
		return $return;
		
	}//getAlbumData()
	
	/**
	 * Returns photo URL for photoEntry object passed on success, NULL otherwise.
	 * @return	mixed
	 * @param	Zend_Gdata_Photos_PhotoEntry		$photo_entry		Pointer to a PhotoEntry object
	 * @param	string							$type				The type of URL to retrieve. enum( 'full', 'thumb' )
	 * @mod	20100403
	 */
	public function getPhotoUrl( &$entry, $type = "full", $vars = array() ) 
	{
		
		$return = FALSE;
		$type = strtolower( trim( $type ) );
		
		if( $type == "full" ) 
		{
			//get photo url
			if( $entry->getMediaGroup()->getContent() != null ) 
			{
				$media_content_array = $entry->getMediaGroup()->getContent();
				$return = $media_content_array[0]->getUrl();
			}
		}
		else if ( $type == "thumb" )
		{
			//get thumb url
			if( $entry->getMediaGroup()->getThumbnail() != null ) 
			{
				$media_thumb_array = $entry->getMediaGroup()->getThumbnail();
				$key = ( array_key_exists( "thumb_key", $vars ) ) ? $vars['thumb_key'] : 0;
				$return = $media_thumb_array[$key]->getUrl();
			}
			
		}//if type == thumb
		
		return $return;
		
	}//getPhotoUrl()
	
	public function getPhotoWidth( &$entry, $type = "full" )
	{
		if( $type == "full" ) 
		{
			//get photo url
			if( $entry->getMediaGroup()->getContent() != null ) 
			{
				$media_content_array = $entry->getMediaGroup()->getContent();
				$return = $media_content_array[0]->getWidth();
			}
		}
		else if ( $type == "thumb" )
		{
			//get thumb url
			if( $entry->getMediaGroup()->getThumbnail() != null ) 
			{
				$media_thumb_array = $entry->getMediaGroup()->getThumbnail();
				$return = $media_thumb_array[0]->getWidth();
			}
			
		}//if type == thumb
		
		return $return;
		
	}//getPhotoWidth()
	
	public function getPhotoHeight( &$entry, $type = "full" )
	{
		if( $type == "full" ) 
		{
			//get photo url
			if( $entry->getMediaGroup()->getContent() != null ) 
			{
				$media_content_array = $entry->getMediaGroup()->getContent();
				$return = $media_content_array[0]->getHeight();
			}
		}
		else if ( $type == "thumb" )
		{
			//get thumb url
			if( $entry->getMediaGroup()->getThumbnail() != null ) 
			{
				$media_thumb_array = $entry->getMediaGroup()->getThumbnail();
				$return = $media_thumb_array[0]->getHeight();
			}
			
		}//if type == thumb
		
		return $return;
		
	}//getPhotoHeight()
	
	public function getAlbumPhotoUrlSummary( $album_feed, $type = "" )
	{
		$return = array();
		
		switch( strtolower( trim( $type ) ) )
		{
			case "thumb":
				foreach( $album_feed as $pe )
				{
					$return[] = $this->getPhotoUrl( $pe, "thumb" );
				}
				break;
				
			case "full":
				foreach( $album_feed as $pe )
				{
					$return[] = $this->getPhotoUrl( $pe, "full" );
				}
				break;
				
			default:
				foreach( $album_feed as $pe )
				{
					$return[] = $this->getPhotoUrl( $pe, "thumb" );
					$return[] = $this->getPhotoUrl( $pe, "full" );
				}
				break;
		}
		
		return $return;
		
	}//getAlbumPhotoUrlSummary()
	
	public function getAllPics( $pic_type = "" )
	{
		$return = array();
		$album_list = $this->getAlbumList();
		
		foreach( $album_list as $i => $info )
		{
			$feed = $this->getAlbum( "id", $info['id'] );
			$photo_feed = $this->getAlbumPhotoUrlSummary( $feed, $pic_type );
			$return = array_merge( $return, $photo_feed );
		}
		
		return $return;
		
	}//getAllPics()
	
	public static function convertAlbumName( $name )
	{
		$return = $name;
		
		if( strpos( $name, " " ) !== FALSE )
		{
			$name = ucwords( $name );
			$return = str_replace( array( "(", ")", " " ), array( "", "", "" ), $name );
		}
		
		return $return;
		
	}//convertAlbumName()
	
	
	public function getHtml( $cmd, $vars = array() )
	{
		switch( strtolower( trim( $cmd ) ) )
		{
			case "grid":
				$records = $vars['feed'];
				$items_per_row = ( array_key_exists( "items_per_row", $vars ) ) ? $vars['items_per_row'] : 3;
				$img_class = ( array_key_exists( "img_class", $vars ) ) ? $vars['primary_class'] : "thumb_dimmed";
				$num_items = ( is_array( $records ) ) ? count( $records ) : 0;
				$num_rows = ceil( $num_items / $items_per_row );
				
				$body = '
					<table class="thumb_menu_grid">
					';
				
				if( $num_items > 0 )
				{
				
					for( $i = 0; $i < $num_rows; $i++ )
					{
						$body .= '
						<tr>			
								';
							
						for( $j = 1; $j <= $items_per_row; $j++ )
						{
						
							$key = $j + ( $items_per_row * $i );
							
							if( $key > $num_items )
							{
								break;
							}
							
							$item = $records[$key];
							$orientation = ( $item['width_full'] > $item['height_full'] ) ? 'landscape' : 'portrait';
							$orientation_tag = 'orientation="' . $orientation . '"';
							
							$body .= '
							<td>
								<div class="thumb_holder" use_jquery="1">
									<a href="#' . $vars['album_name'] . '-' .  $item['photo_id'] . '">
										<img src="' . $item['url_thumb'] . '" alt="' . $item['title'] . '" class="album_thumb ' . $img_class . '" full_src="' . $item['url_full'] . '" ' . $orientation_tag . ' />
									</a>
								</div>
							</td>
						';	
						}
						$body .= '
						</tr>
					';	
								
					}		
				}
				else
				{
					$body .= '
						<tr>
							<td>
								<div style="position:relative;margin-top:145px;text-align:center;line-height:1.8em;color:#FF0000;">
									There are 0 photos in this album. Check back later...
								</div>
							</td>
						</tr>
						';
				}
				
				$body .= '
				</table>
				';
				
				$return = array( 'body' => $body );
				break;
				
			case "album_list":
				$body = '';
				$albums = $this->getAlbumList();
				
				$body = '
				<ul class="album_list">
				';
				foreach( $albums as $i => $info )
				{
					$link = $this->m_common->makeLink( array( 'v' => "portfolio", 'sub' => "album", 'id1' => self::convertAlbumName( $info['title'] ) ) );
					
					$body .= '
					<li>
						<div class="album_loader" id="album_loader_' . $i . '">
						</div>
						<div class="album_title">				
							<a href="' . $link . '" class="album_link" album_num="' . $i . '">
								' . $info['title'] . '
							</a>
						</div>
					</li>
					';
				}
				
				$body .= '
				</ul>
				';
				
				$return = array( 'body' => $body );
				break;
				
			case "all_pics_grid":
				$records = $vars['feed'];
				$items_per_row = ( array_key_exists( "items_per_row", $vars ) ) ? $vars['items_per_row'] : 3;
				$img_class = ( array_key_exists( "img_class", $vars ) ) ? $vars['primary_class'] : "thumb_dimmed";
				
				$num_items = ( is_array( $records ) ) ? count( $records ) : 0;
				$num_rows = ceil( $num_items / $items_per_row );
				$body = '';
				
				if( $num_items > 0 )
				{
					$body = '
					<table class="thumb_menu_grid_no_spacing">
					';
				
					for( $i = 0; $i < $num_rows; $i++ )
					{
						$body .= '
						<tr>			
								';
							
						for( $j = 1; $j <= $items_per_row; $j++ )
						{
						
							$key = $j + ( $items_per_row * $i );
							
							if( $key > $num_items )
							{
								break;
							}
							
							$item = $records[$key];
							$orientation = ( $item['width_full'] > $item['height_full'] ) ? 'landscape' : 'portrait';
							$orientation_tag = 'orientation="' . $orientation . '"';
							
							$body .= '
							<td>
								<div class="thumb_holder" use_jquery="0">
									<img src="' . $item . '" class="' . $img_class . '" />
								</div>
							</td>
						';	
						}
						$body .= '
						</tr>
						';
					}
					
					$body .= '
					</table>
					';
				}
				
				$return = array( 'body' => $body );
				break;
				
			case "featured_grid":
				
				$show_paginator = FALSE;
				$body = '
				<div class="featured_no_pics">
					This album has 0 pictures.
				</div>
				';
				
				if( count( $vars['feed'] ) > 0 )
				{
					$body = '';
					$show_paginator = TRUE;
					
					foreach( $vars['feed'] as $i => $item )
					{
						$body .= '
						<div class="featured_thumb_holder">
							<a href="' . $item['url_full'] . '">
								<img src="' . $item['url_thumb'] . '" class="thumb_dimmed" />
							</a>
						</div>
						';
					}
				}
				
				$return = array( 'body' => $body, 'show_paginator' => $show_paginator );
				break;
		}
		
		return $return;
		
	}//getHtml()
	
	public function getPhoto( $album_name, $photo_id )
	{
		$query = new Zend_Gdata_Photos_PhotoQuery();
		$query->setUser( self::USERNAME );
		$query->setAlbumName( $album_name );
		$query->setPhotoId( $photo_id );
		
		return $this->m_picasa->getPhotoEntry( $query );
		
	}//getPhoto()
	
	public function getPhotoSummary( &$pe, $vars = array() )
	{
		return array( 
			'title' => $pe->title->text,
			'album_id' => $pe->getGphotoAlbumId()->getText(),
			'photo_id' => $pe->getGphotoId()->getText(),
			'summary' => $pe->getSummary()->getText(),
			'url_full' => $this->getPhotoUrl( &$pe, "full" ),
			'url_thumb' => $this->getPhotoUrl( &$pe, "thumb", $vars ),
			'width_full' => $this->getPhotoWidth( &$pe, "full" ),
			'height_full' => $this->getPhotoHeight( &$pe, "full" ),
			'width_thumb' => $this->getPhotoWidth( &$pe, "thumb" ),
			'height_thumb' => $this->getPhotoHeight( &$pe, "thumb" ) 
		);
		
	}//getPhotoSummary()
	
}//class PicasaAlbum