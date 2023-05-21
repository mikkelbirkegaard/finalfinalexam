<?php
// No direct access, please
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * User fields
 */
class BbhUserFields
{

	private $fields = array();
	function __construct()
	{

		$this->register_fields();
		// add fields
		add_action( 'show_user_profile', array($this, 'extra_user_profile_fields') );
		add_action( 'edit_user_profile', array($this, 'extra_user_profile_fields') );

		// save fields
		add_action( 'personal_options_update', array($this, 'save_extra_user_profile_fields') );
		add_action( 'edit_user_profile_update', array($this, 'save_extra_user_profile_fields') );

		// add fields to woocommerce my account - account information - page
		// add_action('woocommerce_edit_account_form', array($this, 'extra_user_profile_fields') );

		// save fields on woocommerce my account - account information - page
		// add_action( 'woocommerce_save_account_details', array($this, 'save_extra_user_profile_fields') );
	}

	private function register_fields(){
		$fields = array(
			array(
				'key' => 'customer_number',
				'placeholder' => __('', 'bbh'),
				'label' => __('Kunde ID.', 'bbh'),
				'type' => 'text',
			),
			array(
				'key' => 'company_id',
				'placeholder' => __('', 'bbh'),
				'label' => __('Virksomhed ID.', 'bbh'),
				'type' => 'text',
			),
			array(
				'key' => 'customer_cvr',
				'placeholder' => __('', 'bbh'),
				'label' => __('CVR', 'bbh'),
				'type' => 'text',
			),
			array(
				'key' => 'customer_ean_location',
				'placeholder' => __('', 'bbh'),
				'label' => __('EAN lokationskode', 'bbh'),
				'type' => 'text',
			),
			array(
				'key' => 'customer_p_nr',
				'placeholder' => __('', 'bbh'),
				'label' => __('P-nr', 'bbh'),
				'type' => 'text',
			),
			array(
				'key' => 'customer_manager_for',
				'placeholder' => __('', 'bbh'),
				'label' => __('Manager for', 'bbh'),
				'type' => 'text',
			),
			array(
				'key' => 'customer_contact_person',
				'placeholder' => __('', 'bbh'),
				'label' => __('Kontaktperson', 'bbh'),
				'type' => 'text',
			),
			array(
				'key' => 'customer_delivery_days',
				'placeholder' => __('', 'bbh'),
				'label' => __('Leveringsdage', 'bbh'),
				'type' => 'text',
			),
			array(
				'key' => 'customer_delivery_hours',
				'placeholder' => __('', 'bbh'),
				'label' => __('Seneste bestillings time', 'bbh'),
				'type' => 'text',
			),
			array(
				'key' => 'customer_warehouse_nr',
				'placeholder' => __('', 'bbh'),
				'label' => __('Lagernummer', 'bbh'),
				'type' => 'text',
			),
			array(
				'key' => 'customer_payment_conditions',
				'placeholder' => __('', 'bbh'),
				'label' => __('Betalingsbetingelser', 'bbh'),
				'type' => 'text',
			),
			array(
				'key' => 'customer_engros_segment',
				'placeholder' => __('', 'bbh'),
				'label' => __('Engros/detail segment', 'bbh'),
				'type' => 'text',
			),
			array(
				'key' => 'customer_debitor_group',
				'placeholder' => __('', 'bbh'),
				'label' => __('Debitorgruppe', 'bbh'),
				'type' => 'text',
			),
			array(
				'key' => 'customer_debitor_price_group',
				'placeholder' => __('', 'bbh'),
				'label' => __('Debitor prisgruppe', 'bbh'),
				'type' => 'text',
			),
			array(
				'key' => 'customer_debitor_discount_group',
				'placeholder' => __('', 'bbh'),
				'label' => __('Debitor rabatgruppe', 'bbh'),
				'type' => 'text',
			),
			array(
				'key' => 'extra_email_address',
				'placeholder' => __('', 'bbh'),
				'label' => __('Ekstra email (Ordrebekræftelse sendes også her til)', 'bbh'),
				'type' => 'text',
			),
			array(
				'key' => 'bbh_remember_shipping',
				'placeholder' => __('', 'bbh'),
				'label' => __('Send altid varerne til denne adresse?', 'bbh'),
				'type' => 'text',
			),
		);

		$this->fields = apply_filters('bbh_customer_fields', $fields);
	}



	private function field_generator($field, $user_id=false){
		$key = esc_attr($field['key']) ?? false;
		// must have a key
		if(!$key){
			return;
		}
		$user_id ? $user_id : get_current_user_id();
		$label = $field['label'] ?? '';
		$placeholder = $field['placeholder'] ?? $label;
		$type = $field['type'] ?? 'text';
		$value = esc_attr( get_the_author_meta( $key, $user_id ) );



		switch ($type) {
			case 'text':
				$input = "<input class='regular-text' name='{$key}' id='{$key}' type='text' value='{$value}' placeholder='{$placeholder}'/>";
				break;
			case 'number':
				$value = floatval(get_the_author_meta( $key, $user_id ));
				$input = "<input class='regular-text' name='{$key}' id='{$key}' type='number' value='{$value}' placeholder='{$placeholder}'/>";
				break;
			default:
				$input = "<input class='regular-text' name='{$key}' id='{$key}' type='text' value='{$value}' placeholder='{$placeholder}'/>";
				break;
		}

		?>
		<tr>
		   <th><label for="<?php echo $key; ?>"><?php echo $label; ?></label></th>
		   <td>
			   <?php echo $input; ?>
		   </td>
	   </tr>
		<?php


	}

	public function extra_user_profile_fields( $user ) {
		$user = $user ?: wp_get_current_user();
		?>
	    <h3><?php _e("Extra profile fields", "bbh"); ?></h3>

	    <table class="form-table">
		<?php if ($this->fields): ?>
			<?php foreach ($this->fields as $field): ?>
			   	<?php $this->field_generator($field, $user->ID) ?>
			<?php endforeach; ?>
		<?php endif; ?>
	    </table>
	<?php }


	public function save_extra_user_profile_fields( $user_id ) {
	    if ( !current_user_can( 'edit_user', $user_id ) ) {
	        return false;
	    }

		if ($this->fields) {
			foreach ($this->fields as $field) {
				$key = $field['key'] ?? false;
				if($key){
					update_user_meta( $user_id, $key, esc_attr($_POST[$key]) );
				}
			}
		}
	}


}


new BbhUserFields();
