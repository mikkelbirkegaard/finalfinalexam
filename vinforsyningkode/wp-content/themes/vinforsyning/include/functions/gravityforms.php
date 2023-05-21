<?php
/**
* Plugin Name: Gravity Forms - Button Field
* Last Modified: 22/06/2014
*/

class RW_Button_Field {
  function __construct() {
      if( ! property_exists( 'GFCommon', 'version' ) || ! version_compare( GFCommon::$version, '1.8', '>=' ) )
          return;
      add_filter( 'gform_add_field_buttons', array( $this, 'add_field_button' ) );
      add_filter( 'gform_field_type_title', array( $this, 'add_field_title' ) );
      add_action( 'gform_field_standard_settings', array( $this, 'button_type_setting' ), 10, 2 );
      add_action( 'gform_field_advanced_settings', array( $this, 'onclick_setting' ), 10, 2 );
      add_action( 'gform_editor_js', array( $this, 'add_field_settings' ) );
      add_action( 'gform_editor_js_set_default_values', array( $this, 'set_field_defaults' ) );
      add_filter( 'gform_tooltips', array( $this, 'add_tooltip') );
      add_filter( 'gform_field_input', array( $this, 'add_field_input' ), 10, 5 );
      add_filter( 'gform_field_content', array( $this, 'add_field_content' ), 10, 5 );
      add_filter( 'gform_submit_button', array( $this, 'remove_submit_button' ), 10, 2 );
  }
  // add button to fields panel
  function add_field_button( $field_groups ) {
      $field_groups[0]['fields'][] = array(
          'class' => 'button',
          'value' => __( 'Button', 'gravityforms' ),
          'onclick' => "StartAddField( 'button' );"
      );
      return $field_groups;
  }
  // add title to field container
  function add_field_title( $title ) {
      if ( $title == 'button' ) {
          $title = __( 'Button', 'gravityforms' );
      }
      return $title;
  }
  // add button type dropdown to the fields properties tab
  function button_type_setting( $position, $form_id ) {
      if($position == 25){
          ?>
          <li class="button_type_setting field_setting">
              <label for="button_type">
                  <?php _e( 'Button Type', 'gravityforms' ); ?>
                  <?php gform_tooltip( 'form_field_type' ) ?>
              </label>
              <select id="button_type" onchange="SetFieldProperty('buttonType', this.value); jQuery('#field_settings').slideUp(function(){StartChangeInputType(jQuery('#button_type').val());});">
                  <option value="button"><?php _e( 'Button', 'gravityforms' ); ?></option>
                  <option value="submit"><?php _e( 'Submit', 'gravityforms' ); ?></option>
                  <option value="reset"><?php _e( 'Reset', 'gravityforms' ); ?></option>
              </select>
          </li>
          <li class="disable_footer_submit_setting field_setting">
              <input type="checkbox" id="disable_footer_submit" onclick="SetFieldProperty('disableSubmit', this.checked);"/>
              <label for="disable_footer_submit" class="inline">
                  <?php _e( 'Disable Footer Submit Button', 'gravityforms'); ?>
                  <?php gform_tooltip( 'disable_footer_submit' ) ?>
              </label>
          </li>
          <?php
      }
  }
  // add onclick input to the fields advanced tab
  function onclick_setting( $position, $form_id ) {
      if($position == 50){
          ?>
          <li class="button_onclick_setting field_setting">
              <label for="button_onclick">
                  <?php _e( 'Button onclick', 'gravityforms' ); ?>
                  <?php gform_tooltip( 'button_onclick' ) ?>
              </label>
              <textarea id="button_onclick" class="fieldwidth-3 fieldheight-2" onkeyup="SetFieldProperty('onclick', this.value);"></textarea>
          </li>
          <?php
      }
  }
  // add settings to the field tabs
  function add_field_settings() {
      ?>
      <script type='text/javascript'>
          fieldSettings['button'] = '.button_type_setting, .button_onclick_setting, .conditional_logic_field_setting, .label_setting, .css_class_setting';
          fieldSettings['submit'] = '.disable_footer_submit_setting, .button_type_setting, .button_onclick_setting, .conditional_logic_field_setting, .label_setting, .css_class_setting';
          fieldSettings['reset'] = '.button_type_setting, .button_onclick_setting, .conditional_logic_field_setting, .label_setting, .css_class_setting';
          jQuery(document).bind('gform_load_field_settings', function(event, field, form){
              jQuery('#button_onclick').val(field.onclick == undefined ? '' : field.onclick);
              jQuery('#button_type').val(field.inputType);
              jQuery('#disable_footer_submit').prop('checked', field.disableSubmit == true ? true : false);
          });
      </script>
      <?php
  }
  function set_field_defaults() {
      ?>
      case 'button' :
          field.label = 'Button';
          field.inputType = 'button';
          field.displayOnly = true;
          field.disableSubmit = false;
      break;
      <?php
  }
  function add_tooltip( $tooltips ) {
      $tooltips['button_onclick'] = "Enter your custom script for the buttons onclick attribute. Don't forget to add the ending semicolon.";
      $tooltips['disable_footer_submit'] = "When enabled the submit button in the form footer will be removed.";
      return $tooltips;
  }
  // define the form editor apperance
  function add_field_input( $input, $field, $value, $lead_id, $form_id ) {
      if ( $field['type'] != 'button' ) {
          return $input;
      }
      if ( RG_CURRENT_VIEW == 'entry' ) {
          return false;
      }
      $tabindex = GFCommon::get_tabindex();
      $disabled = ( GFForms::is_gravity_page() ) ? "disabled" : "";
      $onclick_attr = ( $field['inputType'] == 'submit' || rgar( $field, 'onclick' ) ) ? "onclick='{SCRIPT}'" : "";
      $onclick_val = isset( $field['onclick'] ) ? esc_attr( $field['onclick'] ) : "";
      if ( $field['inputType'] == 'submit' ) {
          $onclick_val .= "if(window[\"gf_submitting_{$form_id}\"]){return false;}";
          if ( GFFormsModel::is_html5_enabled() ) {
              $onclick_val .= " if( !jQuery(\"#gform_".$form_id."\")[0].checkValidity || jQuery(\"#gform_".$form_id."\")[0].checkValidity()){window[\"gf_submitting_{$form_id}\"]=true;}";
          } else {
              $onclick_val .= " window[\"gf_submitting_{$form_id}\"]=true;";
          }
      }
      $btnId = $field['inputType'] == 'submit' ? "gform_submit_button_{$form_id}" : "gform_button_{$form_id}_{$field['id']}";
      $onKeyPress = '';
      if($field['inputType'] == 'submit'){
          $onKeyPress = esc_attr("if( event.keyCode == 13 ){ if(window[\"gf_submitting_{$form_id}\"]){return false;} window[\"gf_submitting_{$form_id}\"]=true;  jQuery(\"#gform_{$form_id}\").trigger(\"submit\",[true]); }");
      }
      $onclick_attr = str_replace( '{SCRIPT}', esc_attr($onclick_val), $onclick_attr );
      $input = "<div class='ginput_container'><button type='{$field['inputType']}' id='{$btnId}' class='gform_button button' {$tabindex} {$onclick_attr} onkeypress='{$onKeyPress}' {$disabled}><span>" .  $field['label'] . "</span></button></div>";
      return $input;
  }
  // define the front end apperance and remove field from entry view
  function add_field_content( $content, $field, $value, $lead_id, $form_id ) {
      if ( $field['type'] != 'button' ) {
          return $content;
      }
      if ( RG_CURRENT_VIEW == 'entry' ) {
          return false;
      }
      $id = $field["id"];
      $duplicate_field_link = "<a class='field_duplicate_icon' id='gfield_duplicate_$id' title='" . __("click to duplicate this field", "gravityforms") . "' href='#' onclick='StartDuplicateField(this); return false;'><i class='fa fa-files-o fa-lg'></i></a>";
      $duplicate_field_link = apply_filters("gform_duplicate_field_link", $duplicate_field_link);
      $delete_field_link = "<a class='field_delete_icon' id='gfield_delete_$id' title='" . __("click to delete this field", "gravityforms") . "' href='#' onclick='StartDeleteField(this); return false;'><i class='fa fa-times fa-lg'></i></a>";
      $delete_field_link = apply_filters("gform_delete_field_link", $delete_field_link);
      $field_type_title = GFCommon::get_field_type_title($field["type"]);
      $admin_buttons = IS_ADMIN ? "<div class='gfield_admin_icons'><div class='gfield_admin_header_title'>{$field_type_title} : " . __("Field ID", "gravityforms") . " {$field["id"]}</div>" . $delete_field_link . $duplicate_field_link . "<a class='field_edit_icon edit_icon_collapsed' title='" . __("click to expand and edit the options for this field", "gravityforms") . "'><i class='fa fa-caret-down fa-lg'></i></a></div>" : "";
      $content = sprintf( "%s%s", $admin_buttons, apply_filters( 'gform_field_input', '', $field, $value, $lead_id, $form_id ) );
      return $content;
  }
  function remove_submit_button( $button, $form ) {
      foreach( $form['fields'] as $field ) {
          if( $field['type'] == 'button' && $field['disableSubmit'] ) {
              return ;
          }
      }
      return $button;
  }
}
new RW_Button_Field();
// validation messages
add_filter( 'gform_validation_message', function ( $message, $form ) {
    if ( gf_upgrade()->get_submissions_block() ) {
        return $message;
    }

    $message = "<div class='validation_error'><p>Der var et problem med din indsendelse. Se fejl nedenfor</p>";
    $message .= '<ul>';

    foreach ( $form['fields'] as $field ) {
        if ( $field->failed_validation ) {
            $message .= sprintf( '<li>%s</li>', GFCommon::get_label( $field ) );
        }
    }

    $message .= '</ul></div>';

    return $message;
}, 10, 2 );
