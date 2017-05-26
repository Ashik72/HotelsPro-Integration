<?php

if(!defined('WPINC')) // MUST have WordPress.
	exit('Do NOT access this file directly: '.basename(__FILE__));

use \JsonCollectionParser\Parser as Parser;
use Unirest\Request as Request;

/**
 * HP_Static_Data
 */
class HP_Static_Data
{

  private static $dataParser = [];


  private static $instance;

  public static function get_instance() {
  	if ( ! isset( self::$instance ) ) {
  		self::$instance = new self();
  	}

  	return self::$instance;
  }

  function __construct() {

    add_action( 'admin_enqueue_scripts', array($this, 'admin_enqueue_scripts_func') );
    //add_action( 'init', array($this, 'handle_json_file') );

    add_action( 'wp_ajax_uploaded_file_verifier_tag_extract', array($this, 'uploaded_file_verifier_tag_extract_func') );
		add_action( 'wp_ajax_nopriv_uploaded_file_verifier_tag_extract', array($this, 'uploaded_file_verifier_tag_extract_func') );

    add_action( 'wp_ajax_generate_sql', array($this, 'generate_sql_func') );
    add_action( 'wp_ajax_nopriv_generate_sql', array($this, 'generate_sql_func') );

    add_action( 'wp_ajax_import_2_db', array($this, 'import_2_db_func') );
    add_action( 'wp_ajax_nopriv_import_2_db', array($this, 'import_2_db_func') );

    add_action( 'wp_ajax_api_make_request', array($this, 'api_make_request_func') );
    add_action( 'wp_ajax_nopriv_api_make_request', array($this, 'api_make_request_func') );

  }

  public function import_2_db_func() {

    $titan = TitanFramework::getInstance( 'hotels_pro_fw' );
    $the_json_file = $titan->getOption('uploaded_json_file_link');
    $data_opts = $titan->getOption('uploaded_json_file_data_opts');

    if (in_array('large_file', $data_opts) && in_array('extra_tags', $data_opts)) {
      echo json_encode(['error' => 'Can not parse a large json file with extra tags!']);
      wp_die();
    } elseif (!in_array('large_file', $data_opts) && !in_array('extra_tags', $data_opts)) {
      echo json_encode(static::simple_parse($the_json_file, $titan));
      wp_die();
    } elseif (in_array('large_file', $data_opts) && !in_array('extra_tags', $data_opts)) {
      echo json_encode(static::streaming_parse($the_json_file, $titan));
      wp_die();
    } elseif (!in_array('large_file', $data_opts) && in_array('extra_tags', $data_opts)) {
      echo json_encode(static::extra_tags_parse($the_json_file, $titan));
      wp_die();
    }
    //echo json_encode([$the_json_file, $data_opts]);

    wp_die();

  }

  public static function validateSQL($data = "") {

    if (!isset($data))
      return "0";

    if (empty($data))
      return "0";

      $titan = TitanFramework::getInstance( 'hotels_pro_fw' );
      $connection = DB::ConnectTitan($titan);

			if (is_array($data))
				$data = serialize($data);

      $data = ( empty( $connection->real_escape_string($data) ) ? "" : $connection->real_escape_string($data) );
			if (strlen($data) < 1) $data = "0";
      $connection = null;
      $titan = null;
      return $data;

  }

  public function generate_sql_func() {


    if (empty($_POST['textarea_val'])) {
			echo json_encode(['err' => 'textarea_val issue']);
			wp_die();
		}

			$titan = TitanFramework::getInstance( 'hotels_pro_fw' );

      //$textarea_val = explode(PHP_EOL, $_POST['textarea_val']);

			$textarea_val = $titan->getOption('link_with_db_columns');
			$_POST['textarea_val'] = $textarea_val;
			$textarea_val = explode(PHP_EOL, $_POST['textarea_val']);
      $textarea_val = array_filter( $textarea_val, 'strlen' );


      $response = DB::ConnectTitan($titan);
      $get_table_name = $titan->getOption('hotels_table_name');

      $fields_array = SetOptions::get_fields($get_table_name, $response);
			//echo json_encode([count($fields_array), count($textarea_val)]);
      if (count($fields_array) !== count($textarea_val)) {
				echo json_encode(['err' => 'fields and textarea_val issue. Count DB fields: '.count($fields_array).' Count textarea fields: '.count($textarea_val)  ]);
				wp_die();
			}


      $statement = SetOptions::insertIntoSQL($get_table_name, $response);

      $values_statment = "";

      $values_statment .= "VALUES (";

      $i_count = 0;
			$i_a_count = 0;
			$data_types = SetOptions::get_fields_and_data_type($get_table_name, $response);
			$count_dt = 0;
      foreach ($textarea_val as $key => $single_textarea_val) {


        $single_textarea_val = explode("|", $single_textarea_val);

        if (!empty($single_textarea_val[1]) && ($single_textarea_val[1] == "a")) {

          $values_statment .= '\'".static::validateSQL(serialize($single_record[\''.$single_textarea_val[0].'\']))."\'';
					$values_statment = preg_replace('/\s+/', '', $values_statment);
					$values_statment = str_replace(array("\\r", "\\n"), '', $values_statment);

					if (count($textarea_val) == ($i_count+1))
            continue;

						$i_count++;

          $values_statment .= ", ";

          continue;
        }


        $single_textarea_val_multi = explode("-", $single_textarea_val[0]);

        $key_val = "";

        foreach ($single_textarea_val_multi as $key => $single_textarea_val_multi_val)
          $key_val .= '[\''.$single_textarea_val_multi_val.'\']';


          //$values_statment .= '\'".$single_record[\''.$single_textarea_val[0].'\']."\'';

					$type = $data_types[$count_dt];
					$type = $type['type'];
					$is_int = strpos($type, 'int');
					$count_dt++;
					if ($is_int !== FALSE)
	          $values_statment .= '".static::validateSQL($single_record'.$key_val.')."';
					else
						$values_statment .= '\'".static::validateSQL($single_record'.$key_val.')."\'';

						$values_statment = preg_replace('/\s+/', '', $values_statment);
						$values_statment = str_replace(array("\\r", "\\n"), '', $values_statment);


          if (count($textarea_val) == ($i_count+1))
            continue;

          $values_statment .= ", ";

          $i_count++;

      }

      $values_statment .= ");\"";


      echo json_encode("\"".$statement." ".$values_statment);

      wp_die();

  }



  public function uploaded_file_verifier_tag_extract_func() {

    if (empty($_POST['file_link']))
      wp_die();

    if ( empty($_POST['extra_tags']) && empty($_POST['large_file']) )
      echo json_encode(static::simple_parse($_POST['file_link']));
    elseif (!empty($_POST['extra_tags']) && empty($_POST['large_file']) )
      echo json_encode(static::extra_tags_parse($_POST['file_link']));
    elseif (empty($_POST['extra_tags']) && !empty($_POST['large_file']) )
      echo json_encode(static::streaming_parse($_POST['file_link']));


    wp_die();

  }

  public static function simple_parse($location = "", $titan = "") {

    if (empty($location)) return;
    if (empty($titan)) return;

    $data = file_get_contents($location);
    $data = json_decode($data, true);

    if (empty($data))
      return "<span style='color: red'>JSON Error!</span>";

    //d($data);
    return static::importSQL($data, $titan);


  }

  public static function importSQL($data = "", $titan = "") {

    if (empty($data))
      return;

    if (empty($titan))
      return;

      $db = DB::ConnectTitan($titan);

      $generate_sql_from_column_values = $titan->getOption('generate_sql_from_column_values');
      $data_count = count($data);
      $data_array = [];
      for ($i=0; $i < $data_count; $i++) {

        $temp_data = $data[$i];
        $single_record =& $temp_data;

        ob_start();
        eval("echo $generate_sql_from_column_values;");
        $output = ob_get_clean();

        $result = $db->query($output);

        $data_array[] = [
          'stat' => $result,
          'sql' => $output
        ];

        $output = null;
        $temp_data = null;
        $result = null;
      }

      $db->close();
      $db = null;
      $generate_sql_from_column_values = null;
      $data = null;

      return $data_array;
  }


  public static function streaming_parse($location = "", $titan = "") {
    if (empty($location)) return;
    $parser = new Parser();
    $file = $location;
    static::$dataParser = [];
    $parser->parse($file, [get_class(), 'importSQLSingle']);
    return static::$dataParser;
  }

    public static function getTitan() {
      $titan = TitanFramework::getInstance( 'hotels_pro_fw' );
      return $titan;
    }

		public static function importSQLSingleDump($data = "") {

			if (empty($data))
				return;

				$titan = static::getTitan();

				$db = DB::ConnectTitan($titan);

				$generate_sql_from_column_values = $titan->getOption('generate_sql_from_column_values');
				$data_array = [];

					$temp_data = $data;
					$single_record =& $temp_data;
					ob_start();
					eval("echo $generate_sql_from_column_values;");
					$output = ob_get_clean();

					//$result = $db->query($output);
					$data_array = [
						'stat' => $result,
						'sql' => $output
					];
					$output = null;
					$temp_data = null;
					$result = null;
					d($data_array);
				$db->close();
				$db = null;
				$generate_sql_from_column_values = null;
				$data = null;
				$titan = null;

				array_push(static::$dataParser, $data_array);
				return $data_array;
		}

    public static function importSQLSingle($data = "") {

      if (empty($data))
        return;

        $titan = static::getTitan();

        $db = DB::ConnectTitan($titan);

        $generate_sql_from_column_values = $titan->getOption('generate_sql_from_column_values');
        $data_array = [];

          $temp_data = $data;
          $single_record =& $temp_data;

          ob_start();
          eval("echo $generate_sql_from_column_values;");
          $output = ob_get_clean();

          $result = $db->query($output);
					$data_array = [
            'stat' => $result,
            'sql' => $output
          ];
          $output = null;
          $temp_data = null;
          $result = null;
        $db->close();
        $db = null;
        $generate_sql_from_column_values = null;
        $data = null;
        $titan = null;

        array_push(static::$dataParser, $data_array);
        return $data_array;
    }

  public static function extra_tags_parse($location = "", $titan = "") {

    if (empty($location)) return;

    ob_start();


    _e("extra_tags_parse");

    $output = ob_get_clean();

    return $output;



  }

  public function handle_json_file() {

    d($_FILES);

    if (!isset($_POST['submit']))
      return;

    if (empty($_POST['json_data_file']))
      return;

    d($_POST);

    die();

  }

  public static function getAdminOptions($titan) {

    if (empty($titan)) return;

    SetOptions::renderOptions($titan);
  }


	public function admin_enqueue_scripts_func() {

		wp_register_script( 'hotels_pro_fw-admin-script', hotels_pro_fw_PLUGIN_URL.'js/admin.js', array( 'jquery' ), '', true );

		wp_localize_script( 'hotels_pro_fw-admin-script', 'hotels_pro_fw_admin_plugin_data', array(
			'ajax_url' => admin_url('admin-ajax.php'), 'userID' => get_current_user_id(),
			'audio_folder' => hotels_pro_fw_PLUGIN_URL."audio/"
		));

		wp_enqueue_script( 'hotels_pro_fw-admin-script' );

    wp_enqueue_style( 'hotels_pro_fw-jsoneditor-css', hotels_pro_fw_PLUGIN_URL.'node_modules/jsoneditor/dist/jsoneditor.min.css' );

    wp_register_script( 'hotels_pro_fw-jsoneditor-script', hotels_pro_fw_PLUGIN_URL.'node_modules/jsoneditor/dist/jsoneditor.min.js', array( 'jquery' ), '' );

		wp_enqueue_script( 'hotels_pro_fw-jsoneditor-script' );




	}

	public static function api_logger($var = "") {

		if (empty($var))
			return;

			echo json_encode($var);
			wp_die();
	}

  public function api_make_request_func() {

      if (empty($_POST['api_url_to_request']))
        wp_die();

      if (empty($_POST['api_file_name']))
        wp_die();

      $titan = static::getTitan();

      $api_url_to_request = $_POST['api_url_to_request'];
      $api_total_fetch = $_POST['api_total_fetch'];
      $api_url_next_id = $_POST['api_url_next_id'];
      $api_file_name = $_POST['api_file_name'];

      $get_api_url = $titan->getOption('api_url');
      $get_api_url .= $api_url_to_request;
      $username = $titan->getOption('api_user');
      $password = $titan->getOption('api_password');


      $response_initial = Unirest\Request::get($get_api_url,
       null, null, $username, $password);



       $response_initial = $response_initial->body;

       $count_total = $response_initial->count;
       $initial_next = $response_initial->next;

       $next_count = 0;
       $i_count = 0;
       $response_array = [];
       $results_temp = [];


       $count_total = (empty($api_total_fetch) ? $count_total : $api_total_fetch);

       $get_api_url .= ( empty($api_url_next_id) ? "" : "&next=".$api_url_next_id );

       while ($next_count < $count_total) {
         $response = Unirest\Request::get($get_api_url,
          null, null, $username, $password);

        $response = $response->body;
        $results_temp = ( (!empty($response->results) && is_array($response->results)) ? $response->results : [] );

        $response_array = array_merge($response_array, $results_temp);

				if (empty($response->next))
					break;

         $get_api_url = $response->next;
         $i_count++;
         $next_count += count($response->results);
         $results_temp = null;
       }

       $response = json_encode($response_array);



       $file_path = hotels_pro_fw_PLUGIN_DIR."files".DS.$_POST['api_file_name'].".json";
       file_put_contents($file_path, $response);


      _e(json_encode( ['file' => $file_path, 'total_fetched' => count($response_array), 'next_url' => $get_api_url] ));
      wp_die();
  }


}


 ?>
