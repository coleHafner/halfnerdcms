<?
/**
 * A class to handle a File record.
 * @since	20100618, hafner
 */
require_once( 'base/Authentication.php' );
require_once( "base/FormHandler.php" );
require_once( 'base/Section.php' );
require_once( "base/Common.php" );
require_once( 'base/View.php' );

class Article
{
	/**
	 * Instance of the Common class.
	 * @var	Common
	 */
	protected $m_common;
	
	/**
	 * Instance of the FormHandler class.
	 * @var	Form
	 */
	protected $m_form;
	
	/**
	 * PK of the File Record.
	 * @var	int
	 */
	protected $m_article_id;
	
	/**
	 * Id of the person who wrote this article.
	 * @var	int
	 */
	protected $m_user_id;
	
	/**
	 * Id of the section to which this article belongs.
	 * @var	int
	 */
	protected $m_section_id;
	
	/**
	 * Id of the view to which this article belongs.
	 * @var	int
	 */
	protected $m_view_id;
	
	/**
	 * Collection of file ids related to this article.
	 * @var	array
	 */
	protected $m_file_ids;
	
	/**
	 * Tile of the record.
	 * @var	string
	 */
	protected $m_title;
	
	/**
	 * Body of the record.
	 * @var	string
	 */
	protected $m_body;
	
	/**
	 * Tag string. Space separated tag string.
	 * @var	string
	 */
	protected $m_tag_string;
	
	/**
	 * Post timestamp of the record.
	 * @var	string
	 */
	protected $m_post_timestamp;
	
	/**
	 * Timestamp Raw.
	 * @var	int
	 */
	protected $m_post_timestamp_raw;
	
	/**
	 * Active flag.
	 * @var	boolean
	 */
	protected $m_active;
	
	/**
	 * Priority for current section/view.
	 * @var	int
	 */
	protected $m_priority;
	
	/**
	 * Array of linked objects.
	 * @var	array
	 */
	protected $m_linked_objects;
	
	/**
	 * Constructs the object.
	 * @since	20100618, hafner
	 * @return	State
	 * @param	int				$article_id			id of the current record
	 */
	public function __construct( $article_id, $objects = FALSE )
	{
		$this->m_common = new Common();
		$this->m_form = new FormHandler( 1 );
		$this->m_article_id = ( is_numeric( $article_id ) && $article_id > 0 ) ? $article_id : 0;
		$this->setMemberVars( $objects );
	}//constructor
	
	/**
	 * Sets the member variables for this class.
	 * Returns TRUE, always.
	 * @since	20100618, hafner
	 * @return	boolean
	 */
	public function setMemberVars( $objects )
	{
		//get member vars
		$sql = "
		SELECT 
			article_id,
			user_id,
			section_id,
			view_id,
			title,
			body,
			tag_string,
			post_timestamp,
			priority,
			active
		FROM 
			common_Articles
		WHERE 
			article_id = " . $this->m_article_id;
		
		$result = $this->m_common->m_db->query( $sql, __FILE__, __LINE__ );
		$row = ( $this->m_common->m_db->numRows( $result ) > 0 ) ? $this->m_common->m_db->fetchAssoc( $result ) : array();
		
		//set member vars
		$this->m_article_id = $row['article_id'];
		$this->m_user_id = $row['user_id'];
		$this->m_section_id = $row['section_id'];
		$this->m_view_id = $row['view_id'];
		$this->m_file_ids = $this->getFiles();
		$this->m_title = stripslashes( $row['title'] );
		$this->m_body = stripslashes( $row['body'] );
		$this->m_tag_string = $row['tag_string'];
		$this->m_post_timestamp =  ( $this->m_article_id > 0 ) ? $this->m_common->convertTimestamp( $row['post_timestamp'], TRUE ) : "";
		$this->m_post_timestamp_diff = $row['post_timestamp'];
		$this->m_priority = $row['priority'];
		$this->m_active = $this->m_common->m_db->fixBoolean( $row['active'] );
		$this->m_linked_objects = ( $objects ) ? $this->setLinkedObjects() : array(); 
		
		return TRUE;
		
	}//setMemberVars()
	
	/**
	* Get an array of data suitable to use in modify
	* @since 	20100618, hafner
	* @return 	array
	* @param 	boolean 		$fix_clob		whether or not to file member variables of CLOB type
	*/
	public function getDataArray( $fix_clob = TRUE ) 
	{
		return array(
			'article_id' => $this->m_article_id,
			'user_id' => $this->m_user_id,
			'section_id' => $this->m_section_id,
			'view_id' => $this->m_view_id,
			'title' => $this->m_title,
			'body' => $this->m_body,
			'tag_string' => $this->m_tag_string,
			'post_timestamp' => $this->m_post_timestamp,
			'priority' => $this->m_priority,
			'active' => $this->m_active
		);
		
	}//getDataArray()
	
	/**
	* Save with the current values of the instance variables
	* This is a wrapper to modify() to ease some methods of coding
	* @since 	20100618, hafner
	* @return	mixed
	*/
	public function save()
	{
		$input = $this->getDataArray();
		return $this->modify( $input, FALSE );
	}//save()
	
	/**
	 * Adds a new record.
	 * Returns ( int ) Id of record if form data is valid, ( string ) form error otherwise.
	 * @since	20100618,hafner
	 * @return	mixed
	 * @param	array				$input				array of input data
	 */
	public function add( $input )
	{
		$this->checkInput( $input, TRUE );
		
		if( !$this->m_form->m_error )
		{
			//only set upload_timestamp on add
			$req_fields = array( 
				'post_timestamp' => strtotime( date( "Y-m-d h:i:s" ) ), 
				'user_id' => 0, 
				'section_id' => 0, 
				'view_id' => 0
			);
			
			$input['article_id'] = $this->m_common->m_db->insertBlank( 'common_Articles', 'article_id', $req_fields );
			
			$this->m_article_id = $input['article_id'];
			$return = $this->m_article_id;
			$this->modify( $input, TRUE );
		}
		else
		{
			$return = $this->m_form->m_error;
		}
		
		return $return;
		
	}//add()
	
	/**
	 * Modifies a record.
	 * Returns ( int ) Id of record if form data is valid, ( string ) form error otherwise. 
	 * @since	20100618, hafner
	 * @return	mixed
	 * @param	array				$input				array of input data
	 * @param	boolean				$from_add			if we are adding a new record, from_add = TRUE, FALSE otherwise.
	 */
	public function modify( $input, $from_add )
	{
		if( !$from_add )
		{
			$this->checkInput( $input, FALSE );
		}

		if( !$this->m_form->m_error )
		{
			$sql = "
			UPDATE 
				common_Articles
			SET 
				title = '" . $this->m_common->m_db->escapeString( $input['title'] ) . "',
				body = '" .  $this->m_common->m_db->escapeString( $input['body'] ) . "',
				tag_string = '" . $this->m_common->m_db->escapeString( $input['tag_string'] ) . "',
				user_id = " . $input['user_id'] . ",
				section_id = " . $input['section_id'] . ",
				view_id = " . $input['view_id'] . ",
				priority = " . $this->getAutoPriority( $input['section_id'], $input['view_id'] ) . "
			WHERE 
				article_id = " . $this->m_article_id;
				
			$this->m_common->m_db->query( $sql, __FILE__, __LINE__ );
			
			$return = $this->m_article_id;
		}
		else
		{
			$return = $this->m_form->m_error;
		}
		
		return $return;
		
	}//modify()
	
	/**
	 * Modifies a record.
	 * Returns TRUE, always. 
	 * @since	20100618, hafner
	 * @return	mixed
	 * @param	array				$input				array of input data 
	 */
	public function delete( $deactivate = TRUE )
	{
		if( $deactivate )
		{
			$sql = "
			UPDATE common_Articles
			SET active = 0
			WHERE article_id = " . $this->m_article_id;
			$this->m_common->m_db->query( $sql, __FILE__, __LINE__ );
		}
		else
		{
			$sql = "
			DELETE
			FROM common_Articles
			WHERE article_id = " . $this->m_article_id;
			$this->m_common->m_db->query( $sql, __FILE__, __LINE__ );	
		}
		
		return $this->m_article_id;
		
	}//delete()
	
	/**
	 * Validates the form input for creating/modifying a new File record.
	 * Returns FALSE on success, error message string otherwise.
	 * @since	20100618, hafner
	 * @return	mixed
	 * @param	array			$input			array of data
	 * @param	boolean			$is_addition	if we are adding a new record, is_addition = TRUE, FALSE otherwise.			 
	 */
	public function checkInput( $input, $is_addition )
	{
		//check missing title
		if( !array_key_exists( "title", $input ) || 
			strlen( trim( $input['title'] ) ) == 0 ||
			$input['title'] == "Article Title" )
		{
			$this->m_form->m_error = "You must select a title.";
		}

		/*
		//check duplicate title
		if( $is_addition )
		{
			if( !$this->m_form->m_error )
			{
				$dup_check = array( 
					'table_name' => "common_Articles",
					'pk_name' => "article_id",
					'check_values' => array( 'title' => strtolower( $input['title'] ) )
				);
				
				if( is_numeric( $this->m_common->m_db->checkDuplicate( $dup_check ) ) )
				{
					$this->m_form->m_error = "That Title already exists";
				}
			}
			
		}
		*/
		//check missing body
		if( !$this->m_form->m_error )
		{
			if( !array_key_exists( "body", $input ) || 
				strlen( trim( $input['body'] ) ) == 0 ||
				$input['body'] == "Article Body" )
			{
				$this->m_form->m_error = "You must fill in the body.";
			}
		}
		
		//check existing auth_id
		if( !$this->m_form->m_error )
		{
			if( !array_key_exists( 'user_id', $input ) || 
				!is_numeric( $input['user_id'] ) ||
				$input['user_id'] == 0 )
			{
				$this->m_form->m_error = "You must provide a user id.";
			}
		}
		
		//check valid auth_id
		if( !$this->m_form->m_error )
		{
			$sql = "
			SELECT count(*)
			FROM common_Users
			WHERE user_id = " . $input['user_id'];

			$result = $this->m_common->m_db->query( $sql, __FILE__, __LINE__ );
			$row = $this->m_common->m_db->fetchRow( $result );
			
			if( $row[0] == 0 )
			{
				$this->m_form->m_error = "Error: Invalid User ID.";
			}
		}
		
		//check existing view_id
		if( !$this->m_form->m_error )
		{
			if( !array_key_exists( 'view_id', $input ) || 
				!is_numeric( $input['view_id'] ) ||
				$input['view_id'] == 0 )
			{
				$this->m_form->m_error = "You must choose a view.";
			}
		}
		
		//check valid view_id
		if( !$this->m_form->m_error )
		{
			$sql = "
			SELECT count(*)
			FROM common_Views
			WHERE view_id = " . $input['view_id'];

			$result = $this->m_common->m_db->query( $sql, __FILE__, __LINE__ );
			$row = $this->m_common->m_db->fetchRow( $result );
			
			if( $row[0] == 0 )
			{
				$this->m_form->m_error = "Error: Invalid View ID.";
			}
		}
		
		//check existing section_id
		if( !$this->m_form->m_error )
		{
			if( !array_key_exists( 'section_id', $input ) || 
				!is_numeric( $input['section_id'] ) ||
				$input['section_id'] == 0 )
			{
				$this->m_form->m_error = "You must choose a section.";
			}
		}
		
		//check valid section_id
		if( !$this->m_form->m_error )
		{
			$sql = "
			SELECT count(*)
			FROM common_Sections
			WHERE section_id = " . $input['section_id'];

			$result = $this->m_common->m_db->query( $sql, __FILE__, __LINE__ );
			$row = $this->m_common->m_db->fetchRow( $result );
			
			if( $row[0] == 0 )
			{
				$this->m_form->m_error = "Error: Invalid Section ID.";
			}
		}
		
		return $this->m_form->m_error;
		
	}//checkInput()
	
	public function setLinkedObjects()
	{
		return array( 
			'authentication' => new Authentication( $this->m_user_id ),
			'section' => new Section( $this->m_section_id ),
			'view' => new View( $this->m_view_id )
		);
		
	}//setLinkedObjects()
	
	/**
	 * Adds a view to this article.
	 * Returns TRUE
	 * @since	20100718, hafner
	 * @return	int
	 * @param	int				$view_id			id of the view record
	 */
	public function updateView( $view_id )
	{
		//check valid view id
		$vars = array( 
			'table_name' => "common_Views", 
			'check_values' => array( 'view_id' => $input['view_id'] ) 
		);
		
		$error = $this->m_form->checkKeyExists( TRUE, $vars );

		if( is_string( $error ) )
		{
			throw new Exception( "ERROR: " . $error );
		}
		
		$sql = "
		UPDATE common_Articles
		SET view_id = " . $view_id . "
		WHERE article_id = " . $this->m_article_id;
		$this->m_common->m_db->query( $sql, __FILE__, __LINE__ );
		
		return TRUE;
		
	}//updateView()
	
	/**
	 * Adds a section to this article.
	 * Returns TRUE.
	 * @since	20100718, hafner
	 * @return	int
	 * @param	int				$section_id			id of the section record
	 */
	public function updateSection( $section_id )
	{
		//check valid section id
		$vars = array( 
			'table_name' => "common_Sections", 
			'check_values' => array( 'section_id' => $input['section_id'] ) 
		);
		
		$error = $this->m_form->checkKeyExists( TRUE, $vars );

		if( is_string( $error ) )
		{
			throw new Exception( "ERROR: " . $error );
		}
		
		$sql = "
		UPDATE common_Articles
		SET section_id = " . $section_id . "
		WHERE article_id = " . $this->m_article_id;
		$this->m_common->m_db->query( $sql, __FILE__, __LINE__ );
		
		return TRUE;
		
	}//updateSection()
	
	public function updateFile( $file_id )
	{
		$sql = "
		UPDATE common_ArticleToFile
		SET file_id = " . $file_id . "
		WHERE article_id = " . $this->m_article_id;
		
		$this->m_common->m_db->query( $sql, __FILE__, __LINE__ );
		
		return TRUE;
		
	}//updateFile()
	
		
	/**
	 * Updates article priority for the current section/view
	 * Returns TRUE
	 * @since	20101213, hafner
	 * @return	Mixed
	 * @param	int		$priority		new priority
	 */
	public function updatePriority( $priority )
	{
		$sql = "
		UPDATE common_Articles
		SET priority = " . $priority . "
		WHERE article_id = " . $this->m_articlie_id;
		
		$this->m_common->m_db->query( $sql, __FILE__, __LINE__ );
		
		return TRUE;
		
	}//updatePriority()
	
	/**
	 * Updates file priority for the current article
	 * Returns TRUE
	 * @since	20101213, hafner
	 * @return	Mixed
	 * @param	int		$priority		new priority
	 * @param	int		$file_id		id of the file
	 */
	public function updateFilePriority( $priority, $file_id )
	{
		$sql = "
		UPDATE common_ArticleToFile
		SET priority = " . $priority . "
		WHERE article_id = " . $this->m_articlie_id . " AND
		file_id = " . $file_id;
		
		$this->m_common->m_db->query( $sql, __FILE__, __LINE__ );
		
		return TRUE;
		
	}//updateFilePriority()
	
	/**
	 * Gets an article based on the view title and the section title.
	 * Returns Article Object on success, FALSE otherwise.
	 * @since	20100728, hafner
	 * @return	Mixed
	 * @param	string				$article_title			id of the article to view relationship
	 * @param	string				$section_title			id of the section
	 */
	public function getArticle( $view_title, $section_title )
	{
		$return = array();
		
		$view_id = $this->m_common->m_db->getIdFromTitle( $view_title, array(  
			'pk_name' => "view_id", 
			'table' => "common_Views", 
			'title_field' => "alias" ) 
		);
		
		$section_id = $this->m_common->m_db->getIdFromTitle( $section_title, array(  
			'pk_name' => "section_id", 
			'table' => "common_Sections", 
			'title_field' => "title" ) 
		);
		
		if( $view_id > 0 && $section_id > 0 )
		{
			$sql = "
			SELECT article_id
			FROM common_Articles
			WHERE view_id = " . $view_id . " AND
			section_id = " . $section_id . "
			ORDER BY priority ASC";
			
			$result = $this->m_common->m_db->query( $sql, __FILE__, __LINE__ );
			while( $row = $this->m_common->m_db->fetchRow( $result ) )
			{
				$return[] = new Article( $row[0], FALSE );
			}
		}
		
		return $return;
		
	}//getArticle()
	
	public function getFiles()
	{
		$return = array();
		
		$sql = "
		SELECT file_id
		FROM common_ArticleToFile
		WHERE article_id = " . $this->m_article_id . "
		ORDER BY priority ASC";
		
		$result = $this->m_common->m_db->query( $sql, __FILE__, __LINE__ );
		while( $row = $this->m_common->m_db->fetchRow( $result ) )
		{
			$return[] = $row[0];
		}
		
		return $return;
		
	}//getFiles()
	
	public static function getArticles( $field, $pk )
	{
		$return = array();
		$common = new Common();
		
		$sql = "
		SELECT article_id
		FROM common_Articles
		WHERE article_id > 0 AND
		" . $field . " = " . $pk . "
		ORDER BY post_timestamp DESC";
		
		$result = $common->m_db->query( $sql, __FILE__, __LINE__ );
		
		while( $row = $common->m_db->fetchRow( $result ) )
		{
			$return[] = new Article( $row[0], FALSE );
		}
		
		return $return;
		
	}//getArticles()
	
	/**
	* Returns next priority for given section/view.
	* @author	20101213, Hafner
	* @return	array
	* @param	int			$section_id		determines which HTML snippet to return
	* @param	array		$view_id		array of variables for the html
	*/
	public function getAutoPriority( $section_id, $view_id )
	{
		$sql = "
		SELECT count(*)
		FROM common_Articles
		WHERE view_id = " . $view_id . " AND
		section_id = " . $section_id;
		
		$result = $this->m_common->m_db->query( $sql, __FILE__, __LINE__ );
		$row = $this->m_common->m_db->fetchRow( $result );
		
		return (int)$row[0] + 1;
		
	}//getAutoPriority()
	
	/**
	* Returns HTML
	* @author	20100908, Hafner
	* @return	array
	* @param	string		$cmd		determines which HTML snippet to return
	* @param	array		$vars		array of variables for the html
	*/
	public static function getHtml( $cmd, $vars = array() )
	{
		$common = new Common();
		
		switch( strtolower( $cmd ) )
		{
			case "get-view-form":
			
				$a = $vars['active_record'];
				$body = ( strlen( $a->m_body ) > 199 ) ? substr( $a->m_body, 0, 200 ) . "..." : $a->m_body;
				
				$view_title = $a->m_common->m_db->getTitleFromId( $a->m_view_id, array(  
					'table'	=> "common_Views",
					'pk_name' => "view_id",
					'title_field' => "alias" ) 
				);
				
				$section_title = $a->m_common->m_db->getTitleFromId( $a->m_section_id, array(  
					'table'	=> "common_Sections",
					'pk_name' => "section_id",
					'title_field' => "title" ) 
				);
				
				$poster = $a->m_common->m_db->getTitleFromId( $a->m_user_id, array(  
					'table'	=> "common_Users",
					'pk_name' => "user_id",
					'title_field' => "username" ) 
				);
				
				$num_tags = ( !is_null( $a->m_tag_string ) ) ? count ( explode( " ", $a->m_tag_string ) ) : "0";
				
				$return = array( 'html' => '
					<div class="header color_accent">
						' . $a->m_title . '
					</div>
					
					<p class="normal_font padder_10_bottom">
						' . $body . '
					</p>
						
					<div class="article_meta">
						Posted by ' . $poster . ' on ' . $a->m_post_timestamp . ' on ' . $view_title . ' in ' . $section_title . ' - ' . $num_tags . ' Tags 
					</div>
					'
				);
				break;
			
			case "get-edit-form":
				
				//get auth id
				$a = $vars['active_record'];
				
				if( $a->m_article_id > 0 )
				{
					$user_id = $a->m_user_id;
					$title = $a->m_title;
					$body = $a->m_body;
					$tag_string = $a->m_tag_string;
					$process = "modify";
					$from_add = "0";
					$body_id = "post_body";
				}
				else
				{
					$user_id = Authentication::getLoginUserId();
					$process = "add";
					$title = "";
					$body = "";
					$from_add = "1";
					$tag_string = "";
					$body_id = "post_body_0";
				}
				
				//get view selector
				$options = $common->getListRecords( "common_Views" );
				$view_selector = Common::getHtml( "select-list", array( 
					'options' => $options,
					'selected_option' => $a->m_view_id,
					'default_option' => "Select View",
					'name' => "view_id" ) 
				);
				
				//get section selector
				$selector = self::getHtml( "get-section-selector", array( 'active_record' => $a ) );
				$section_selector = $selector['html'];
				
				$html = '
				<div class="padder_10">
					' . Common::getHtml( "title-bar", array( 
						'title' => ucWords( $process ) . " Post", 
						'classes' => '' ) 
					) . '
					
					<div id="result_' . $process . '_' . $a->m_article_id . '" class="result">
					</div>
	
					<form id="article_form_' . $a->m_article_id . '">
						
						<div class="padder_10">
							<span class="title_span">
								Title:
							</span>
							<input type="text" name="title" class="text_input text_extra_long" value="' . $title  . '" />
						</div>
						
						<div class="padder_10 padder_no_top">
							<span class="title_span">
								Guts:
							</span>
							<textarea name="body" id="' . $body_id . '" class="post_body">' . $body .'</textarea>
						</div>
						
						
						<div class="padder_10">
							' . Common::getHtml( "selector-module", array( 
								'title' => "View", 
								'content' => $view_selector,
								'content_class' => "" ) ) . '
								
							' . Common::getHtml( "selector-module-spacer", array() ) . '
							
							' . Common::getHtml( "selector-module", array( 
								'title' => "Section", 
								'content' => $section_selector,
								'content_class' => "article_section_selector_" . $a->m_article_id ) ) . '
								
							<div class="clear"></div>
							
						</div>
						
						<div class="padder_10 padder_no_top">
							<span class="title_span">
								Tags ( separate with space ):
							</span>
							<input type="text" name="tag_string" class="text_input text_extra_long" value="' . $tag_string  . '" />
						</div>
						
						' . Common::getHtml( "get-form-buttons", array( 
						
							'left' => array( 
								'pk_name' => "article_id",
								'pk_value' => $a->m_article_id,
								'process' => $process,
								'id' => "article",
								'button_value' => ucwords( $process ),
								'extra_style' => 'style="width:41px;"' ),
								
							'right' => array(
								'pk_name' => "item_id",
								'pk_value' => $a->m_article_id,
								'process' => "view",
								'id' => "list_item",
								'button_value' => "Cancel" )							) 
						) . '
													
						<input type="hidden" name="user_id" value="' . $user_id . '"/>
						<input type="hidden" name="from_add" value="' . $from_add . '"/>
						
					</form>
				</div>
				';
				
				$return = array( 'html' => $html );
				break;
				
			case "get-delete-form":
				
				$a = $vars['active_record'];
				
				$html = '
				<div class="padder_10">
					' . Common::getHtml( "title-bar", array( 'title' => "Really Delete Post?", 'classes' => '' ) ) . '
					
					<div class="button_container">
						' . Common::getHtml( "get-form-buttons", array( 
						
							'left' => array( 
								'pk_name' => "article_id",
								'pk_value' => $a->m_article_id,
								'process' => "delete",
								'id' => "article",
								'button_value' => "Delete" ),
								
							'right' => array(
								'pk_name' => "item_id",
								'pk_value' => $a->m_article_id,
								'process' => "view",
								'id' => "list_item",
								'button_value' => "Cancel" ) 
							) 
						) . '
					</div>			
				</div>
				';
				
				$return = array( 'html' => $html );
				break;
							
			case "get-section-selector":
				
				//get section selector
				$a = $vars['active_record'];
				$options = $common->getListRecords( "common_Sections" );
				
				
				//generate section selector
				$html = Common::getHtml( "select-list", array( 
					'options' => $options,
					'selected_option' => $a->m_section_id,
					'default_option' => "Select Section",
					'name' => 'section_id' ) 
				) . '&nbsp; <a href="#" id="article" process="refresh_section_selector" article_id="' . $a->m_article_id . '">
						Refresh
					</a>
				';
				
				$return = array( 'html' => $html );
				break;
				
			case "get-admin-list-item":
				
				$a = $vars['active_record'];
				$view_form = Article::getHtml( "get-view-form", array( 'active_record' => $a ) );
				$mod_form = Article::getHtml( "get-edit-form", array( 'active_record' => $a ) );
				$delete_form = Article::getHtml( "get-delete-form", array( 'active_record' => $a ) );
				$li_classes = Common::getHtml( "get-admin-list-item-classes", array( 'type' => "article" ) );
				$buttons = Common::getHtml( "get-admin-item-buttons", array( 'item_id' => $a->m_article_id ) );
				 
				$html = '
				<div class="' . $li_classes['html'] . '" hover_enabled="1">
				
					<div id="item_view_' . $a->m_article_id . '">						
						' . $view_form['html'] . '
					</div>
										
					<div id="item_mod_' . $a->m_article_id . '" style="display:none;">
						' . $mod_form['html'] . '
					</div>
					
					<div id="item_delete_' . $a->m_article_id . '" style="display:none;">
						' . $delete_form['html'] . '
					</div>
					
					<div class="title_button_container" id="item_control" style="display:none;">
						' . $buttons['html'] . '
					</div>

				</div>
				';
				
				$return = array( 'html' => $html );
				break;
				
			default:
				throw new Exception( "Error: Command '" . $cmd . "' is invalid." );
				break;
		}
		
		return $return;
		
	}//getHtml()
	
	/**
	* Get a member variable's value
	* @author	Version 20100618, hafner
	* @return	mixed
	* @param	string		$var_name		Variable name to get
	*/
	public function __get( $var_name )
	{
		$exclusions = array();

		if( !in_array( $var_name, $exclusions ) )
		{
			return $this->$var_name;
		}
		else
		{
			throw new exception( "Error: Access to member variable '" . $var_name . "' for class '" . get_class( $this ) . "' is denied" );
		}
	}//__get()
	
	/**
	* Set a member variable's value
	* @since	20100618, hafner
	* @return	mixed
	* @param	string		$var_name		Variable name to set
	* @param	string		$var_value		Value to set
	*/
	public function __set( $var_name, $var_value )
	{
		$exclusions = array( 'm_article_id' );

		if( !in_array( $var_name, $exclusions ) )
		{
			$this->$var_name = $var_value;
			return TRUE;
		}
		else
		{
			throw new exception( "Error: Access to member variable '" . $var_name . "' for class '" . get_class( $this ) . "' is denied" );
		}
	}//__set()
	
}//class View
?>