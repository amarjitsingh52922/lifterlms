<?php
if ( ! defined( 'ABSPATH' ) ) exit; 

/**
* Admin base MetaBox Class
*
* sets up base metabox functionality and global save.
*
* @version 1.0
* @author codeBOX
* @project lifterLMS
*/
class LLMS_Admin_Meta_Boxes {

	/**
	* array of collected errors.
	* @access public
	* @var string
	*/
	private static $errors = array();

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'hide_meta_boxes' ), 10 );
		add_action( 'add_meta_boxes', array( $this, 'refresh_meta_boxes' ), 10 );
		add_action( 'add_meta_boxes', array( $this, 'get_meta_boxes' ), 10 );
		add_action( 'save_post', array( $this, 'save_meta_boxes' ), 10, 2 );

		// Save Course Meta Boxes
		add_action( 'lifterlms_process_course_meta', 'LLMS_Meta_Box_Course_Product::save', 10, 2 );
		add_action( 'lifterlms_process_course_meta', 'LLMS_Meta_Box_Video::save', 10, 2 );
		add_action( 'lifterlms_process_course_meta', 'LLMS_Meta_Box_Course_Syllabus::save', 10, 2 );

		add_action( 'lifterlms_process_lesson_meta', 'LLMS_Meta_Box_Video::save', 10, 2 );

		add_action( 'lifterlms_process_llms_email_meta', 'LLMS_Meta_Box_Email_Settings::save', 10, 2 );

		//Error handling
		add_action( 'admin_notices', array( $this, 'display_errors' ) );
		add_action( 'shutdown', array( $this, 'set_errors' ) );
	}

	/**
	 * Get error messages from metaboxes
	 *
	 * @param string $text
	 */
	public static function get_error( $text ) {
		self::$errors[] = $text;
	}

	/**
	 * Save messages to the database
	 *
	 * @param string $text
	 */
	public function set_errors() {
		update_option( 'lifterlms_errors', self::$errors );
	}

	/**
	 * Display the messages in the error dialog box
	 *
	 * @param string $text
	 */
	public function display_errors() {
		$errors = get_option( 'lifterlms_errors' );

		if ( empty( $errors ) ) {
			return;
		}

		$errors = maybe_unserialize( $errors );

		echo '<div id="lifterlms_errors" class="error"><p>';

		foreach ( $errors as $error ) {

			echo esc_html( $error );
		}

		echo '</p></div>';

		delete_option( 'lifterlms_errors' );	

	}

	/**
	* Add Metaboxes
	*
	* @return void
	*/
	public function get_meta_boxes() {
		
		add_meta_box( 'postexcerpt', __( 'Course Short Description', 'lifterlms' ), 'LLMS_Meta_Box_Course_Short_Description::output', 'course', 'normal' );
		add_meta_box( 'lifterlms-course-data', __( 'Course Data', 'lifterlms' ), 'LLMS_Meta_Box_Course_Product::output', 'course', 'normal', 'high' );
		add_meta_box( 'lifterlms-course-video', __( 'Course Video', 'lifterlms' ), 'LLMS_Meta_Box_Video::output', 'course', 'normal');
		add_meta_box( 'lifterlms-course-syllabus', __( 'Course Syllabus', 'lifterlms' ), 'LLMS_Meta_Box_Course_Syllabus::output', 'course', 'normal');

		add_meta_box( 'lifterlms-lesson-video', __( 'Featured Video', 'lifterlms' ), 'LLMS_Meta_Box_Video::output', 'lesson', 'normal', 'high' );

		add_meta_box( 'lifterlms-email-settings', __( 'Email Settings', 'lifterlms' ), 'LLMS_Meta_Box_Email_Settings::output', 'llms_email', 'normal', 'high' );
	}

	/**
	* Remove Metaboxes
	*
	* @return void
	*/
	public function hide_meta_boxes() {
		remove_meta_box( 'postexcerpt', 'course', 'normal' );
		remove_meta_box('tagsdiv-course_difficulty','course','side');
	}

	/**
	* Updates global $post variable
	*
	* @return void
	*/
	public function refresh_meta_boxes() {
		global $post;
	}

	/**
	* Validates post and metabox data before saving.
	*
	* @return bool
	* @param $post, $post_id
	*/
	public function validate_post( $post_id, $post ) {

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return false;
		}
		elseif ( empty( $post_id ) || empty( $post ) ) {
			return false;
		}
		elseif ( ! in_array( $post->post_type, array( 'course', 'section', 'lesson', 'order', 'llms_email' ) ) ) {
			return false;
		}
		elseif ( defined( 'DOING_AUTOSAVE' ) || is_int( wp_is_post_revision( $post ) ) || is_int( wp_is_post_autosave( $post ) ) ) {
			return false;
		}
		elseif ( empty( $_POST['lifterlms_meta_nonce'] ) || ! wp_verify_nonce( $_POST['lifterlms_meta_nonce'], 'lifterlms_save_data' ) ) {
			return false;
		} 
		elseif ( empty( $_POST['post_ID'] ) || $_POST['post_ID'] != $post_id ) {
			return false;
		}

		return true;
	}

	/**
	* Global Metabox Save
	*
	* @return void
	* @param $post, $post_id
	*/
	public function save_meta_boxes( $post_id, $post ) {

		if ( LLMS_Admin_Meta_Boxes::validate_post( $post_id, $post ) ) {
			do_action( 'lifterlms_process_' . $post->post_type . '_meta', $post_id, $post );
		}
	}

}

new LLMS_Admin_Meta_Boxes();
