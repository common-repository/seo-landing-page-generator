<?php
/**
 * Demographics City Climate Data Widget
 */



/**
 * Class ISSSLPG_Admin_City_Demographics_Climate_Data_Widget
 */
class ISSSLPG_Admin_City_Demographics_Climate_Data_Widget extends WP_Widget
{
	/** Basic Widget Settings */
	const WIDGET_NAME = "ISS: City Climate Data";
	const WIDGET_DESCRIPTION = "";

	var $textdomain;
	var $fields;

	/**
	 * Construct the widget
	 */
	function __construct()
	{
		// We're going to use $this->textdomain as both the translation domain and the widget class name and ID
		$this->textdomain = strtolower( get_class( $this ) );

		// Add fields
		$this->add_field( 'title', 'Title', '', 'text' );

		// Translations
		load_plugin_textdomain( $this->textdomain, false, basename( dirname( __FILE__ ) ) . '/languages' );

		// Init the widget
		parent::__construct( $this->textdomain, __( self::WIDGET_NAME, $this->textdomain ), array(
			'description' => __( self::WIDGET_DESCRIPTION, $this->textdomain ),
			'classname'   => $this->textdomain
		) );
	}

	/**
	 * Widget frontend
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance )
	{
		// Only output widget in landing pages
		if ( ! ISSSLPG_Landing_Page::is_landing_page() ) {
			return;
		}

//		$climate_data = ISSSLPG_Landing_Page_Api::get_location_climate_data();
		$location = new ISSSLPG_Public_Location();
		$climate_data = $location->get_climate_data();

		if ( empty( $climate_data ) ) {
			return;
		}

		extract( $instance );

		$title  = apply_filters( 'widget_title', $instance['title'] );
		if ( ! empty( $title ) ) {
			$title = $args['before_title'] . do_shortcode( $title ) . $args['after_title'];
		}
		?>

		<?php
		/**
		 * Widget frontend output
		 */
		?>

		<?php echo $args['before_widget']; ?>

		<?php echo $title; ?>

		<?php
		// Assign titles to data array keys
		$category_titles = array(
			'snowfall'              => __( 'Snowfall', 'rvn' ),
			'max_temperature'       => __( 'Maximum Temperature', 'rvn' ),
			'min_temperature'       => __( 'Minimum Temperature', 'rvn' ),
			'avg_temperature'       => __( 'Average Temperature', 'rvn' ),
			'precipitation_normals' => __( 'Precipitation Normals', 'rvn' ),
		);
		$date_titles = array(
			'jan' => __( 'January', 'rvn' ),
			'feb' => __( 'February', 'rvn' ),
			'mar' => __( 'March', 'rvn' ),
			'apr' => __( 'April', 'rvn' ),
			'may' => __( 'May', 'rvn' ),
			'jun' => __( 'June', 'rvn' ),
			'jul' => __( 'July', 'rvn' ),
			'aug' => __( 'August', 'rvn' ),
			'sep' => __( 'September', 'rvn' ),
			'oct' => __( 'October', 'rvn' ),
			'nov' => __( 'November', 'rvn' ),
			'dec' => __( 'December', 'rvn' ),
			'ann' => __( 'Annual', 'rvn' ),
		);

		// Shuffle
		$climate_data = ISSSLPG_Array_Helpers::shuffle_associative_array( $climate_data );
		list( $climate_data_handle ) = array_keys( $climate_data );
		$random_dataset = reset( $climate_data );

		// Output
		echo '<ul>';
			echo "<li><b>{$category_titles[$climate_data_handle]}</b></li>";
			foreach ( $random_dataset as $date_handle => $value ) :
				echo "<li><b>{$date_titles[$date_handle]}:</b> {$value}</li>";
			endforeach;
		echo '</ul>';
		?>

		<?php echo $args['after_widget']; ?>

		<?php
	}

	/**
	 * Widget backend
	 *
	 * @param array $instance
	 *
	 * @return string|void
	 */
	public function form( $instance )
	{
		/* Generate admin for fields */
		foreach ( $this->fields as $field_name => $field_data ) {
			if ( $field_data['type'] === 'text' ):
				?>
				<p>
					<label for="<?php echo $this->get_field_id( $field_name ); ?>">
						<?php _e( $field_data['description'], $this->textdomain ); ?>
					</label>
					<input class="widefat"
					       id="<?php echo $this->get_field_id( $field_name ); ?>"
					       name="<?php echo $this->get_field_name( $field_name ); ?>"
					       type="text"
					       value="<?php echo esc_attr( isset( $instance[ $field_name ] ) ? $instance[ $field_name ] : $field_data['default_value'] ); ?>"
					/>
				</p>
			<?php
			elseif($field_data['type'] == 'textarea'):
				?>
				<p>
					<label for="<?php echo $this->get_field_id( $field_name ); ?>">
						<?php _e( $field_data['description'], $this->textdomain ); ?>
					</label>
					<textarea class="widefat" rows="10" cols="20"
					          id="<?php echo $this->get_field_id( $field_name ); ?>"
					          name="<?php echo $this->get_field_name( $field_name ); ?>"><?php echo esc_attr( isset( $instance[ $field_name ] ) ? $instance[ $field_name ] : $field_data['default_value'] ); ?></textarea>
				</p>
			<?php
			else:
				echo __( 'Error - Field type not supported', $this->textdomain ) . ': ' . $field_data['type'];
			endif;
		}
	}

	/**
	 * Adds field to the widget
	 *
	 * @param $field_name
	 * @param string $field_description
	 * @param string $field_default_value
	 * @param string $field_type
	 */
	private function add_field( $field_name, $field_description = '', $field_default_value = '', $field_type = 'text' )
	{
		if ( ! is_array( $this->fields ) ) {
			$this->fields = array();
		}

		$this->fields[ $field_name ] = array(
			'name'          => $field_name,
			'description'   => $field_description,
			'default_value' => $field_default_value,
			'type'          => $field_type
		);
	}

	/**
	 * Updating widget by replacing the old instance with new
	 *
	 * @param array $new_instance
	 * @param array $old_instance
	 *
	 * @return array
	 */
	public function update( $new_instance, $old_instance )
	{
		return $new_instance;
	}

}