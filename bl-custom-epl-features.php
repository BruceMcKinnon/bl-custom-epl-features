<?php
/*
Plugin Name: Custom Additional Features for Easy Property Listing
Version: 2023.01
Plugin URI: http://ingeni.net
Author: Bruce McKinnon - ingeni.net
Author URI: http://ingeni.net
Description: Create custom 'additional features' for Easy property Listing listings
*/

/*
Copyright (c) 2017 ingeni.net
Released under the GPL license
http://www.gnu.org/licenses/gpl.txt

Disclaimer: 
	Use at your own risk. No warranty expressed or implied is provided.
	This program is free software; you can redistribute it and/or modify 
	it under the terms of the GNU General Public License as published by 
	the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 	See the GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA


Requires : Wordpress 3.x or newer ,PHP 5 +

v2017.01 - Initial release
v2023.01 - Misc updates for PHP 8 compatibility

*/

class EplCustomSettingsPage
{
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;

    /**
     * Start up
     */
    public function __construct()
    {
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ), 11 );
				add_action( 'admin_init', array( $this, 'page_init' ) );
    }

    /**
     * Add options page
     */
    public function add_plugin_page()
    {
			// Add this under the Easy Property Listings menu
			add_submenu_page( 
				'epl-general', 
				'Custom Additional Options', 
				'Custom Additional Options', 
				'manage_options', 
				'custom_epl_page',
				array( $this, 'custom_epl_options_page' )
			 );
    }

    /**
     * Options page callback
     */
    public function custom_epl_options_page()
    {
        // Set class property
        $this->options = get_option( 'epl_custom_additional_options' );
        ?>
        <div class="wrap">
						<form method="post" action="options.php">
            <?php
                // This prints out all hidden setting fields
                settings_fields( 'epl_custom_additional_options' );
                do_settings_sections( 'custom_epl_page' );
                submit_button();
            ?>
            </form>
        </div>
        <?php
    }

    /**
     * Register and add settings
     */
    public function page_init()
    {        
        register_setting(
            'epl_custom_additional_options', // Option group
            'epl_custom_additional_options', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

        add_settings_section(
            'epl_custom_section_id', // ID
            'Custom Easy Property Listing Additional Options', // Title
            array( $this, 'print_section_info' ), // Callback
            'custom_epl_page' // Page
        );  

        add_settings_field(
            'id_epl_custom_internal', // ID
            'Internal Options', // Title 
            array( $this, 'id_epl_custom_internal_callback' ), // Callback
            'custom_epl_page', // Page
            'epl_custom_section_id' // Section           
		);

        add_settings_field(
            'id_epl_custom_external', // ID
            'External Options', // Title 
            array( $this, 'id_epl_custom_external_callback' ), // Callback
            'custom_epl_page', // Page
            'epl_custom_section_id' // Section           
		);

        add_settings_field(
            'id_epl_custom_extras', // ID
            'Extras', // Title 
            array( $this, 'id_epl_custom_extras_callback' ), // Callback
            'custom_epl_page', // Page
            'epl_custom_section_id' // Section           
        );  
	}

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize( $input )
    {
		$new_input = array();

        if( isset( $input['id_epl_custom_internal'] ) )
            $new_input['id_epl_custom_internal'] =  wp_filter_post_kses( $input['id_epl_custom_internal'] );
				if( isset( $input['id_epl_custom_external'] ) )
            $new_input['id_epl_custom_external'] =  wp_filter_post_kses( $input['id_epl_custom_external'] );
				if( isset( $input['id_epl_custom_extras'] ) )
            $new_input['id_epl_custom_extras'] =  wp_filter_post_kses( $input['id_epl_custom_extras'] );

        return $new_input;
    }

    /** 
     * Print the Section text
     */
    public function print_section_info()
    {
        print 'One additional feature per line:';
    }

    /** 
     * Get the settings option array and print one of its values
     */
    public function id_epl_custom_internal_callback()
    {
        printf( '<textarea id="id_epl_custom_internal" name="epl_custom_additional_options[id_epl_custom_internal]" cols="80" rows="10">%s</textarea>',
            isset( $this->options['id_epl_custom_internal'] ) ? stripslashes( $this->options['id_epl_custom_internal'] ) : ''
        );
		}
    public function id_epl_custom_external_callback()
    {
        printf( '<textarea id="id_epl_custom_external" name="epl_custom_additional_options[id_epl_custom_external]" cols="80" rows="10">%s</textarea>',
            isset( $this->options['id_epl_custom_external'] ) ? stripslashes( $this->options['id_epl_custom_external'] ) : ''
        );
    }
    public function id_epl_custom_extras_callback()
    {
        printf( '<textarea id="id_epl_custom_extras" name="epl_custom_additional_options[id_epl_custom_extras]" cols="80" rows="10">%s</textarea>',
            isset( $this->options['id_epl_custom_extras'] ) ? stripslashes( $this->options['id_epl_custom_extras'] ) : ''
        );
    }
}

if( is_admin() ) {
	$my_epl_custom_page = new EplCustomSettingsPage();
}


function array_2d( $array ) {
	$result = array();
	if(!empty($array) && is_array($array)){
        foreach($array as $row) {
            $result[] = array ( $row, "" );
        }
	}
	return $result;
}

// Construct a list of standard addtional features, grouped by the custom type required
function custom_epl_get_standard_additional_features ( $type ) {
    global $property;
    $retHtml = '';

    if ( strtolower($type) != 'extras') {
        if ( strtolower( $type ) == 'internal' ) {
            $std_additional_features = array (
                'property_study',
                'property_dishwasher',
                'property_built_in_robes',
                'property_gym',
                'property_rumpus_room',
                'property_floor_boards',
                'property_broadband',
                'property_pay_tv',
                'property_vacuum_system',
                'property_intercom',
                'property_spa',
                'property_open_fire_place',
                'property_ducted_heating',
                'property_ducted_cooling',
                'property_split_system_heating',
                'property_hydronic_heating',
                'property_split_system_aircon',
                'property_gas_heating',
                'property_reverse_cycle_aircon',
                'property_evaporative_cooling'
            );
        } else {
            $std_additional_features = array (
                'property_workshop',
                'property_remote_garage',
                'property_secure_parking',
                'property_tennis_court',
                'property_balcony',
                'property_deck',
                'property_courtyard',
                'property_outdoor_entertaining',
                'property_shed',
                'property_land_fully_fenced'
            );
        }

        foreach($std_additional_features as $metakey) {
            $metavalue = $property->get_property_meta($metakey);
            
            switch ( $metavalue ) {
                case 1 :
                case 'yes':
                case 'YES':
                case 'Y':
                case 'y':
                case 'on':
                    $retHtml .= '<li class="'.$property->get_class_from_metakey($metakey).'">'.apply_filters('epl_get_'.$metakey.'_label',__($property->get_label_from_metakey($metakey), 'easy-property-listings' ) ).'</li>';
                    break;
            }
        }
    }

    return $retHtml;
}


// Construct a list of additional features
function custom_epl_get_feature_list ( $type, $title ) {
    $retHtml = '';

    $meta_query_args = array(
        'relation' => 'AND', // Optional, defaults to "AND"
        array(
            'key'     => 'custom_'.$type.'%',
            'value'   => 'yes',
            'compare' => '='
        )
    );

    // Get the selected features for this post
    global $wpdb;
    global $wp_query;
    $post_id = $wp_query->post->ID;
    
    $sql_string = $wpdb->prepare("SELECT meta_id, post_id, meta_key, meta_value FROM $wpdb->postmeta WHERE (post_id = %d) AND (meta_key LIKE %s) AND (meta_value = 'yes')", $post_id, 'custom_'.$type.'_%');

    $selections = $wpdb->get_results( $sql_string );
    $retHtml = '';

    // Now grab the entire list of available options
    $full_options_list = get_option( 'epl_custom_additional_options' );
    if ( is_array($full_options_list) ) {
        $key = 'id_epl_custom_'.$type;
        $selected_list = $full_options_list[$key];
        $selected_items = explode("\n",$selected_list);
        // Take the available options and match them to their abbreviated form
        $selected_items = array_2d( $selected_items );
        foreach($selected_items as &$item) {
            $item[1] = 'custom_'.$type.'_'.custom_abbrev( $item[0] );
        }

        // Create the required <li> list by matching the abbreviations
        foreach( $selections as $selection ) {
            $key = array_search( strtolower($selection->meta_key), array_column($selected_items,1) );

            if ($key !== false) {
                $retHtml .= '<li>'.$selected_items[$key][0].'</li>';
            }
        }

        $retHtml = custom_epl_get_standard_additional_features( $type ) . $retHtml;

        if ( strlen($retHtml) > 0 ) {
            if ($title == "") {
                $title = "<h4>Optional Extras</h4>";
                if ( ( $type == "internal" ) || ( $type == 'external' ) ) {
                    $title = "<h4>" . ucwords($type) . "</h4>";
                }
            }

            $retHtml = $title . '<ul class="custom_additional_features_'.$type.'">' . $retHtml . '</ul>';
        }
    }

    return $retHtml;
}


function custom_epl_get_list_internal( $atts ) {
    $attribs = shortcode_atts( array(
          'title' => '',
      ), $atts );
    return custom_epl_get_feature_list( 'internal', $attribs['title'] );
}
add_shortcode('custom_epl_features_get_internal', 'custom_epl_get_list_internal');

function custom_epl_get_list_external( $atts ) {
    $attribs = shortcode_atts( array(
          'title' => '',
      ), $atts );
    return custom_epl_get_feature_list( 'external', $attribs['title']);
}
add_shortcode('custom_epl_features_get_external', 'custom_epl_get_list_external');

function custom_epl_get_list_extras( $atts ) {
    $attribs = shortcode_atts( array(
          'title' => '',
      ), $atts );
    return custom_epl_get_feature_list('extras', $attribs['title']);
}
add_shortcode('custom_epl_features_get_extras', 'custom_epl_get_list_extras');




function custom_abbrev( $name ) {
    $name = str_replace( array("&",",","/","\\","-","or","and","of", "to"), "", $name );

	$words = explode(" ", $name);
	$abbrev = '';

	foreach($words as $word) {
		$abbrev .= "_" . strtolower( substr($word,0,4) );
	}

    $abbrev = sanitize_title($abbrev);

    return $abbrev;
}



//
// Props to @justinfm101 - https://wordpress.org/support/topic/features-issues-and-questions-not-in-documentation/
//
function custom_epl_add_meta_box_epl_listings_advanced_callback($meta_fields) {

	$custom_options = get_option('epl_custom_additional_options');

    $internal_fields = array();
    $external_fields = array();
    $extras_fields = array();


    if ( is_array($custom_options) ) {
        if ( is_string($custom_options['id_epl_custom_internal']) ) {
            $custom_internal = explode( "\n", $custom_options['id_epl_custom_internal'] );
        
            foreach( $custom_internal as $internal ) {
                $internal_fields[] = array(
                    'name'		=>	'custom_internal_'.custom_abbrev( $internal ),
                    'label'		=>	__($internal, 'epl'),
                    'type'		=>	'checkbox_single',
                    'opts'		=>	array( 'yes'	=>	__('Yes', 'epl'), ),
                    'value'		=>	'custom_internal_'.custom_abbrev( $internal )
                );
            }
            $internal_fields[] = array(
                'name'		=>	'custom_internal_select_all',
                'type'		=>	'button',
                'value'     =>  'Select All',
                'onclick'   =>  'custom_epl_select_all("internal")',
            );
        
        }

        if ( is_string($custom_options['id_epl_custom_external']) ) {
            $custom_external = explode( "\n", $custom_options['id_epl_custom_external'] );
            foreach( $custom_external as $external ) {
                $external_fields[] = array(
                    'name'		=>	'custom_external_'.custom_abbrev( $external ),
                    'label'		=>	__($external, 'epl'),
                    'type'		=>	'checkbox_single',
                    'opts'		=>	array( 'yes'	=>	__('Yes', 'epl'), ),
                    'value'		=>	'custom_internal_'.custom_abbrev( $external )
                );
            }
            $external_fields[] = array(
                'name'		=>	'custom_external_select_all',
                'type'		=>	'button',
                'value'     =>  'Select All',
                'onclick'   =>  'custom_epl_select_all("external")',
            );
        }

        $extras_fields = array();
        if ( is_string($custom_options['id_epl_custom_extras']) ) {
            $custom_extras = explode( "\n", $custom_options['id_epl_custom_extras'] );
            foreach( $custom_extras as $extras ) {
                $extras_fields[] = array(
                    'name'		=>	'custom_extras_'.custom_abbrev( $extras ),
                    'label'		=>	__($extras, 'epl'),
                    'type'		=>	'checkbox_single',
                    'opts'		=>	array( 'yes'	=>	__('Yes', 'epl'), ),
                    'value'		=>	'custom_internal_'.custom_abbrev( $extras )
                );
            }
            $extras_fields[] = array(
                'name'		=>	'custom_extras_select_all',
                'type'		=>	'button',
                'value'     =>  'Select All',
                'click'   =>  'custom_epl_select_all("extras")',
            );
        }
    }

	$custom_field = array(
		'id'		=>	'epl_property_listing_custom_data_id',
		'label'		=>	__('Custom Additional Options', 'epl'), // Box header
		'post_type'	=>	array('property', 'rural', 'rental', 'land', 'commercial', 'commercial_land', 'business'), // Which listing types these will be attached to
		'context'	=>	'normal',
		'priority'	=>	'default',
		'groups'	=>	array(
			array(
				'id'		=>	'property_custom_data_section_1',
				'columns'	=>	'3', // One or two columns
				'label'		=>	'Custom External Options',
				'fields'	=>	$external_fields
			),
			array(
				'id'		=>	'property_custom_data_section_2',
				'columns'	=>	'3', // One or two columns
				'label'		=>	'Custom Internal Options',
				'fields'	=>	$internal_fields
			),
			array(
				'id'		=>	'property_custom_data_section_3',
				'columns'	=>	'3', // One or two columns
				'label'		=>	'Custom Extras Options',
				'fields'	=>	$extras_fields
			)
			
		)
	);
	$meta_fields[] = $custom_field;


	return $meta_fields;
}
add_filter( 'epl_listing_meta_boxes' , 'custom_epl_add_meta_box_epl_listings_advanced_callback' );



add_action('admin_enqueue_scripts', 'custom_epl_admin_scripts');
function custom_epl_admin_scripts(  ) {
    wp_register_script('custom_epl_admin_js', plugin_dir_url( __FILE__ ) . '/custom_epl_admin.js', array('jquery'));
    wp_enqueue_script( 'custom_epl_admin_js');
}


// Plugin activation/deactivation hooks
function custom_epl_features_activation() {
	flush_rewrite_rules( false );
	
	if ( !is_plugin_active('easy-property-listings/easy-property-listings.php') ) {
		deactivate_plugins( plugin_basename( __FILE__ ) );
		wp_die( __( 'Please activate Easy Property Listings - this is required for this plugin.' ) );
	}

}
register_activation_hook(__FILE__, 'custom_epl_features_activation');

function custom_epl_features_deactivation() {
  flush_rewrite_rules( false );
}
register_deactivation_hook( __FILE__, 'custom_epl_features_deactivation' );

?>