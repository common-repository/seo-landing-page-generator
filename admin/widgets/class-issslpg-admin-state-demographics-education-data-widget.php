<?php
/**
 * Demographics State Education Data Widget
 */



/**
 * Class ISSSLPG_Admin_State_Demographics_Education_Data_Widget
 */
class ISSSLPG_Admin_State_Demographics_Education_Data_Widget extends WP_Widget
{
	/** Basic Widget Settings */
	const WIDGET_NAME = "ISS: State Education Data";
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

		$county_id = ISSSLPG_Landing_Page_Api::get_county_id();
		$county_data = new ISSSLPG_County_Data( $county_id );
		$state = $county_data->get_state_data_object();
		if ( ! $state ) {
			return;
		}
		$education_data = $state->get_education_data();
		if ( empty( $education_data ) ) {
			return;
		}

		// Shuffle
		$education_data = ISSSLPG_Array_Helpers::shuffle_associative_array( $education_data );

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

		<ul>
			<?php
			// Assign titles to data array keys
			$titles = array(
				'no_diploma'   => __( 'No Diploma', 'rvn' ),
				'high_school'  => __( 'High School', 'rvn' ),
				'some_college' => __( 'Some College', 'rvn' ),
				'bachelors'    => __( 'Bachelors', 'rvn' ),
				'graduate'     => __( 'Graduate', 'rvn' ),
			);

			// Output
			foreach ( $education_data as $key => $value ) :
				$value = number_format( $value );
				echo "<li><b>{$titles[$key]}:</b> {$value}%</li>";
			endforeach;
			?>
		</ul>

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