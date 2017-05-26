<?php

if (!defined('ABSPATH'))
  exit;

require_once( plugin_dir_path( __FILE__ ) . '/inc/class.static_data.php' );


add_action( 'tf_create_options', 'hotels_pro_fw_custom_options', 150 );

function hotels_pro_fw_custom_options() {


	$titan = TitanFramework::getInstance( 'hotels_pro_fw' );

  HP_Static_Data::getAdminOptions($titan);

}


 ?>
