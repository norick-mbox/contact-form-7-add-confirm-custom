<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
** A base module for [confirm]
**/

/* Shortcode handler */

add_action( 'wpcf7_init', 'wpcf7c_add_shortcode_confirm' );

function wpcf7c_add_shortcode_confirm() {
	if(function_exists('wpcf7_add_form_tag')) {
		wpcf7_add_form_tag( 'confirm', 'wpcf7c_confirm_shortcode_handler' );
	} else {
		wpcf7_add_shortcode( 'confirm', 'wpcf7c_confirm_shortcode_handler' );
	}
}

function wpcf7c_confirm_shortcode_handler( $tag ) {
	if ( version_compare(WPCF7_VERSION, "4.6", ">=") ) {
		$tag = new WPCF7_FormTag( $tag );
	} else {
		$tag = new WPCF7_Shortcode( $tag );
	}

	$class = wpcf7_form_controls_class( $tag->type );

	$atts = array();

	$atts['class'] = $tag->get_class_option( $class ) . " wpcf7c-elm-step1 wpcf7c-btn-confirm wpcf7c-force-hide";
	$atts['id'] = $tag->get_option( 'id', 'id', true );
	$atts['tabindex'] = $tag->get_option( 'tabindex', 'int', true );

	$value = isset( $tag->values[0] ) ? $tag->values[0] : '';

	if ( empty( $value ) )
		$value = __( 'Confirm', 'formflow-confirm-for-cf7' );

	$atts['type'] = 'submit';
	$atts['value'] = $value;

	$atts = wpcf7_format_atts( $atts );

	$html = sprintf( '<input %1$s />', $atts );

	return $html;
}


/* Tag generator */

if(WPCF7_VERSION >= "4.2.0") {

	if ( is_admin() ) {
		add_action( 'admin_init', 'wpcf7c_add_tag_generator_confirm', 55 );
	}

	function wpcf7c_add_tag_generator_confirm() {
		if(!class_exists('WPCF7_TagGenerator')) {
			return false;
		}
		$tag_generator = WPCF7_TagGenerator::get_instance();
		$tag_generator->add( 'confirm', __( 'Confirm button', 'formflow-confirm-for-cf7' ),
			'wpcf7c_tg_pane_confirm', array( 'nameless' => 1 ) );
	}


	function wpcf7c_tg_pane_confirm( $contact_form, $args = '' ) {
		$args = wp_parse_args( $args, array() );
    /* translators: %s: Documentation link. */
		$description = __( "Generate a form-tag for a confirm button. For more details, see %s.", 'formflow-confirm-for-cf7' );

		$desc_link = wpcf7_link(
    __( 'https://github.com/norick-mbox/contact-form-7-add-confirm-custom.git', 'formflow-confirm-for-cf7' ),
    __( 'Confirm Button', 'formflow-confirm-for-cf7' )
);

		?>
		<div class="control-box">
			<fieldset>
				<legend><?php echo wp_kses_post( sprintf( $description, $desc_link ) ); ?></legend>

				<table class="form-table">
					<tbody>
					<tr>
						<th scope="row"><label
								for="<?php echo esc_attr( $args['content'] . '-values' ); ?>"><?php echo esc_html( __( 'Label', 'formflow-confirm-for-cf7' ) ); ?></label>
						</th>
						<td><input type="text" name="values" class="oneline"
						           id="<?php echo esc_attr( $args['content'] . '-values' ); ?>"/></td>
					</tr>

					<tr>
						<th scope="row"><label
								for="<?php echo esc_attr( $args['content'] . '-id' ); ?>"><?php echo esc_html( __( 'Id attribute', 'formflow-confirm-for-cf7' ) ); ?></label>
						</th>
						<td><input type="text" name="id" class="idvalue oneline option"
						           id="<?php echo esc_attr( $args['content'] . '-id' ); ?>"/></td>
					</tr>

					<tr>
						<th scope="row"><label
								for="<?php echo esc_attr( $args['content'] . '-class' ); ?>"><?php echo esc_html( __( 'Class attribute', 'formflow-confirm-for-cf7' ) ); ?></label>
						</th>
						<td><input type="text" name="class" class="classvalue oneline option"
						           id="<?php echo esc_attr( $args['content'] . '-class' ); ?>"/></td>
					</tr>

					</tbody>
				</table>
			</fieldset>
		</div>

		<div class="insert-box">
			<input type="text" name="confirm" class="tag code" readonly="readonly" onfocus="this.select()"/>

			<div class="submitbox">
				<input type="button" class="button button-primary insert-tag"
				       value="<?php echo esc_attr( __( 'Insert Tag', 'formflow-confirm-for-cf7' ) ); ?>"/>
			</div>
		</div>
		<?php
	}


} else {
	add_action( 'admin_init', 'wpcf7c_add_tag_generator_confirm', 55 );

	function wpcf7c_add_tag_generator_confirm() {
		if ( ! function_exists( 'wpcf7_add_tag_generator' ) )
			return;
		//eyeta_log("wpcf7c_add_tag_generator_confirm");
		wpcf7_add_tag_generator( 'confirm', __( 'Confirm button', 'formflow-confirm-for-cf7' ),
			'wpcf7-tg-pane-confirm', 'wpcf7c_tg_pane_confirm', array( 'nameless' => 1 ) );
	}

	function wpcf7c_tg_pane_confirm( $contact_form ) {
	?>
	<div id="wpcf7-tg-pane-confirm" class="hidden">
	<form action="">
	<table>
	<tr>
	<td><code>id</code> (<?php echo esc_html( __( 'optional', 'formflow-confirm-for-cf7' ) ); ?>)<br />
	<input type="text" name="id" class="idvalue oneline option" /></td>

	<td><code>class</code> (<?php echo esc_html( __( 'optional', 'formflow-confirm-for-cf7' ) ); ?>)<br />
	<input type="text" name="class" class="classvalue oneline option" /></td>
	</tr>

	<tr>
	<td><?php echo esc_html( __( 'Label', 'formflow-confirm-for-cf7' ) ); ?> (<?php echo esc_html( __( 'optional', 'formflow-confirm-for-cf7' ) ); ?>)<br />
	<input type="text" name="values" class="oneline" /></td>

	<td></td>
	</tr>
	</table>

	<div class="tg-tag"><?php echo esc_html( __( "Copy this code and paste it into the form left.", 'formflow-confirm-for-cf7' ) ); ?><br /><input type="text" name="confirm" class="tag wp-ui-text-highlight code" readonly="readonly" onfocus="this.select()" /></div>
	</form>
	</div>
	<?php
	}

}