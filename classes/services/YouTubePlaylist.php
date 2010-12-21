<?
/**
 * A class to handle the YouTube playlist object.
 * @since	20101103, hafner
 */

require_once 'Zend/Loader.php';
Zend_Loader::loadClass('Zend_Gdata_YouTube');

class YouTubePlaylist
{
	/**
	 * Instance of the Zend_Gdata_YouTube class.
	 * @var YouTube object
	 */
	protected $m_you_tube;
	
	/**
	 * Username for this class.
	 */
	const USERNAME = "madnesschill";
	//fysiel@gmail.com
	
	/**
	 * Initializes the Zend_Gdata_YouTube object for this class.
	 * @since	20101103, hafner
	 */
	function __construct()
	{
		$this->m_you_tube = new Zend_Gdata_Youtube();
		$this->m_you_tube->setMajorProtocolVersion(2);
	}
	
	/** 
	 * Returns a collection of playlist objects for the current user.
	 * @return 	Zend_Gdata_Youtube_PlaylistFeed object.
	 * @since	20101103, hafner
	 */
	public function getAllPlaylists()
	{
		return $this->m_you_tube->getPlaylistListFeed( self::USERNAME );
		
	}//getPlaylistFeedSummary()
	
	/** 
	 * Returns an array of video entry summaries ( result of $this->getVideoFeedSummary() ) for current playlist id if valid, FALSE otherwise.
	 * @return 	mixed
	 * @since	20101103, hafner
	 * @param	string			$playlist_id		alpha numeric id for playlist
	 */
	public function getPlaylistFeedFromId( $playlist_id )
	{
		if( strlen( $playlist_id ) < 1 )
		{
			return FALSE;
		}
		
		//Yeah.
		$base_url = "http://gdata.youtube.com/feeds/api/playlists";
		$playlist_video_feed = $this->m_you_tube->getVideoFeed( $base_url . "/" . $playlist_id );
		
		return $this->getVideoFeedSummary( $playlist_video_feed );
		
	}//getPlaylistFeedFromId()
	
	/**
	 * Returns an array of 'title' => title of the playlist, 'vids' => array of video entry summaries ( result of $this->getVideoFeedSummary() )
	 * @return	array 
	 * @since	20101103, hafner
	 * @param	Zend_Gdata_Youtube_PlaylistEntry		$playlist_entry		playlist object
	 */
	public function getPlaylistSummary( &$playlist_entry )
	{
		$playlist_url = $playlist_entry->getPlaylistVideoFeedUrl();
		$playlist_video_feed = $this->m_you_tube->getVideoFeed( $playlist_url );
		
		return array( 
			'title' => $playlist_entry->getTitleValue(), 
			'vids' => $this->getVideoFeedSummary( $playlist_video_feed ) 
		);
		
	}//getPlaylistSummary()
	
	/**
	 * Returns an array of video enrty summaries ( result of $this->getVideoEntrySummary() )
	 * @return	array
	 * @since	20101103, hafner
	 * @param	Zend_Gdata_Youtube_VideoFeed		$video_feed			video feed object
	 */
	public function getVideoFeedSummary( &$video_feed )
	{
		$i = 1;
		$return = FALSE;
		
		foreach ( $video_feed as $video_entry ) 
		{
			$return[$i] = $this->getVideoEntrySummary( $video_entry );
			$i++;
	  	}
	  	
	  	return $return;
	  	
	}//getVideoFeedSummary()
	
	/**
	 * Returns an array of data about the current video entry if exists, FALSE otherwise.
	 * @return	mixed
	 * @since	20101103, hafner
	 * @param	Zend_Gdata_Youtube_VideoEntry		$video_entry		instance of the video_entry class
	 */
	public function getVideoEntrySummary( $video_entry ) 
	{
		if( !is_object( $video_entry ) )
		{
			return FALSE;
		}
		
		return array (
			'id' => $video_entry->getVideoId(),
			'title' => $video_entry->getVideoTitle(),
			'desc' => $video_entry->getVideoDescription(),
			'url_watch' => $video_entry->getVideoWatchPageUrl(),
			'url_flash' => $video_entry->getFlashPlayerUrl(),
			'duration' => $video_entry->getVideoDuration(),
			'total_views' => $video_entry->getVideoViewCount(),
			'rating' => $video_entry->getVideoRatingInfo(),
			'record_date' => $video_entry->getVideoRecorded(),
			'url_mobile' => $this->getVideoEntryMobileUrl( $video_entry ),
			'thumbs' => $this->getVideoEntryThumbnailUrlCollection( $video_entry )
		);
		
	}//printVideoEntrySummary()
	
	/**
	 * Returns a collection of thumbnail urls for the current video entry if exists, FALSE otherwise.
	 * @return	mixed
	 * @since	20101103, hafner
	 * @param	Zend_Gdata_Youtube_VideoEntry		$video_entry		instance of the video_entry class
	 */
	public function getVideoEntryThumbnailUrlCollection( $video_entry )
	{
		//get thumbnails
		$video_thumbnails = $video_entry->getVideoThumbnails();
		
		if( !is_array( $video_thumbnails ) || 
			count( $video_thumbnails ) == 0 )
		{
			return FALSE;
		}
	
		$i = 0;
		$return = array();
		foreach($video_thumbnails as $video_thumbnail) 
		{
			$return[$i]['time'] = $video_thumbnail['time'];
			$return[$i]['url_img'] = $video_thumbnail['url'];
			$return[$i]['height'] = $video_thumbnail['height'];
			$return[$i]['width'] = $video_thumbnail['width'];
			
			$i++;
		}
		
		return $return;
		
	}//getVideoEntryThumbnailUrlCollection()
	
	/**
	 * Returns the mobile URL of the current video entry if exists, FALSE otherwise.
	 * @return	mixed
	 * @since	20101103, hafner
	 * @param	Zend_Gdata_Youtube_VideoEntry		$video_entry		instance of the video_entry class
	 */
	public function getVideoEntryMobileUrl( $video_entry )
	{
		if( !is_array( $video_entry->mediaGroup->content ) ||
			count( $video_entry->mediaGroup->content ) == 0 )
		{
			return FALSE;
		}
		
		$return = array();
		//object directly to retrieve its 'Mobile RSTP link' child
		foreach ( $video_entry->mediaGroup->content as $content ) 
		{
			if ( $content->type === "video/3gpp" ) 
			{
				return $content->url;
			}
			
	  	}//loop through media content
	  	
	}//getVideoEntryMobileUrl()
	
}//class YouTubePlaylist()