<?php

if(!defined('WPINC')) // MUST have WordPress.
	exit('Do NOT access this file directly: '.basename(__FILE__));

/**
 * SetOptions
 */
class SetOptions
{


  public static function renderOptions($titan) {

    if (empty($titan))
      return;

      $section = $titan->createAdminPanel( array(
            'name' => __( 'HotelsPro Integration', 'hotels_pro_fw' ),
            'icon'	=> 'dashicons-networking'
        ) );

			$section_mother = $section;

      $tab = $section->createTab( array(
              'name' => 'Database Config'
          ) );

          $tab->createOption( array(
          'name' => 'DB Host',
          'id' => 'db_host',
          'type' => 'text',
          'desc' => 'Static DB Host',
          'default' => 'localhost'
        ) );

        $tab->createOption( array(
        'name' => 'DB Name',
        'id' => 'db_name',
        'type' => 'text',
        'desc' => 'Static DB Name',
        'default' => 'hotelspro-static'
        ) );

        $tab->createOption( array(
        'name' => 'DB User',
        'id' => 'db_user',
        'type' => 'text',
        'desc' => 'Static DB User',
        'default' => 'user'
        ) );

        $tab->createOption( array(
        'name' => 'DB Password',
        'id' => 'db_password',
        'type' => 'text',
        'desc' => 'Static DB Password',
        'default' => 'password',
        'is_password' => 1
        ) );

        $tab->createOption( array(
        'name' => 'DB Charset',
        'id' => 'db_charset',
        'type' => 'text',
        'desc' => 'Static DB Charset',
        'default' => 'utf8'
        ) );

        $tab->createOption( array(
        'name' => 'DB Connection Status',
        'type' => 'custom',
        'custom' => self::checkDBConnection($titan)
        ) );

        $tab = $section->createTab( array(
                'name' => 'API Config'
            ) );

        $tab->createOption( array(
        'name' => 'API URL',
        'id' => 'api_url',
        'type' => 'text',
        'desc' => 'Static API URL',
        'default' => 'http://cosmos.metglobal.tech/api/static/v1'
        ) );


        $tab->createOption( array(
        'name' => 'Enable Live Environment',
        'id' => 'is_api_live',
        'type' => 'enable',
        'default' => false,
        'desc' => 'Enable or disable Live Environment',
        ) );


        $tab->createOption( array(
        'name' => 'API Username',
        'id' => 'api_user',
        'type' => 'text',
        'desc' => 'Static API Username'
        ) );

        $tab->createOption( array(
        'name' => 'API Password',
        'id' => 'api_password',
        'type' => 'text',
        'desc' => 'Static API Password',
        'is_password' => 1
        ) );

        $tab->createOption( array(
        'name' => 'API Connection Status',
        'type' => 'custom',
        'custom' => self::checkApiConnection($titan)
        ) );

          $section->createOption( array(
                'type' => 'save',
          ) );

      $section = $section->createAdminPanel( array(
            'name' => __( 'Static Data Implementation', 'hotels_pro_fw' ),
            'icon'	=> 'dashicons-networking'
        ) );

        $tab = $section->createTab( array(
              'name' => 'Step 1: Tables'
          ) );

          $tab->createOption( array(
          'name' => 'Database Tables',
          'id' => 'db_tables',
          'type' => 'textarea',
          'desc' => 'Write Database Table names each line that needs to be imported.',
          'is_code' => true
          ) );


          $tab->createOption( array(
          'name' => 'Available Tables And Data Types',
          'type' => 'custom',
          'custom' => self::showTables($titan)
          ) );


      $tab = $section->createTab( array(
            'name' => 'Step 2: Set and Check DB'
        ) );



        $tab->createOption( array(
        'name' => 'Select Table',
        'id' => 'hotels_table_name',
        'type' => 'select',
        'default' => '1',
        'desc' => 'Set Hotels Table Name',
        'options' => static::db_tables_array($titan)
        ) );


        $tab->createOption( array(
        'name' => 'API Connection Status',
        'type' => 'custom',
        'custom' => self::checkApiConnection($titan)
        ) );

        $tab->createOption( array(
        'name' => 'Columns in this table',
        'type' => 'custom',
        'custom' => self::hotel_columns($titan)
        ) );


        $tab->createOption( array(
        'name' => 'Insert Statement',
        'type' => 'custom',
        'custom' => self::insertStatOptions($titan)
        ) );

        $tab = $section->createTab( array(
              'name' => 'Step 3: JSON Data Verification'
          ) );

          $tab->createOption( array(
          'name' => 'Upload JSON data file',
          'type' => 'custom',
          'custom' => self::json_data_upload($titan)
          ) );


          $tab->createOption( array(
          'name' => 'Request Using API',
          'type' => 'custom',
          'custom' => self::json_data_api_request($titan)
          ) );

          $tab->createOption( array(
          'name' => 'Uploaded File',
          'id' => 'uploaded_json_file_link',
          'type' => 'text',
          'desc' => ''
          ) );

          $tab->createOption( array(
          'name' => 'JSON Data Options',
          'id' => 'uploaded_json_file_data_opts',
          'type' => 'multicheck',
          'desc' => '',
          'options' => array(
          'large_file' => 'This one is a large JSON file, use JSON Streaming parser.',
          'extra_tags' => 'This file has extra tags (count, next, previous etc)',
          )
          ) );

          $tab->createOption( array(
          'name' => 'Link with database columns',
          'id' => 'link_with_db_columns',
          'type' => 'textarea',
          'desc' => static::link_with_db_columns($titan)
          ) );


          $tab->createOption( array(
          'name' => 'Generate SQL',
          'id' => 'generate_sql_from_column_values',
          'type' => 'textarea',
          'desc' => static::generate_sql_from_column_values($titan)
          ) );

          $tab = $section->createTab( array(
                'name' => 'Step 4: Import to Database'
            ) );

            $tab->createOption( array(
            'name' => 'Import to database',
            'type' => 'custom',
            'custom' => self::import2db($titan)
            ) );

          $tab = $section->createTab( array(
                'name' => __( 'JSON Editor', 'hotels_pro_fw' ),
            ) );

            $tab->createOption( array(
            'type' => 'custom',
            'custom' => static::get_json_editor($titan)
            ) );


        $section->createOption( array(
              'type' => 'save',
        ) );


				$section = $section_mother->createAdminPanel( array(
	            'name' => __( 'Booking Options', 'hotels_pro_fw' ),
	            'icon'	=> 'dashicons-networking'
	        ) );

					BookingOptions::doOptions($section);

			$section->createOption( array(
						'type' => 'save',
			) );


  }

  public static function db_tables_array($titan = "") {

    if (empty($titan)) return;

    $db_tables_opts = $titan->getOption( 'db_tables');

    $db_tables_opts = explode(PHP_EOL, $db_tables_opts);

    $db_tables_opts_temp = [];

    foreach ($db_tables_opts as $key => $value) {
      $value = preg_replace('/\s+/', '', $value);
      $db_tables_opts_temp[$value] = $value;
    }


    return $db_tables_opts_temp;
  }

  public static function checkApiConnection($titan = "") {

    if (empty($titan)) return;

    $api_url = $titan->getOption( 'api_url');
    $api_user = $titan->getOption( 'api_user');
    $api_password = $titan->getOption( 'api_password');

    $response = Unirest\Request::get($api_url, null, null, $api_user, $api_password);
    $response = $response->body;
    $response = (array) $response;
    ob_start();


    if (count($response) > 1)
      _e("Connected");
    else
      _e("Not Connected");


    $output = ob_get_clean();


    return $output;

  }

  public static function checkDBConnection($titan = "") {

    if (empty($titan)) return;

    $response = DB::ConnectTitan($titan);

    ob_start();


      if (is_object($response))
        _e("Connected!");
      else
        d($response);

    $output = ob_get_clean();


    return $output;

  }



  public static function hotel_columns($titan = "") {

    if (empty($titan)) return;

    $response = DB::ConnectTitan($titan);
    $get_table_name = $titan->getOption('hotels_table_name');

    ob_start();
    $fields_array = static::get_fields($get_table_name, $response);

    s($fields_array);

    $output = ob_get_clean();


    return $output;


  }




  public static function get_fields($table_name = "", $connection = "") {

    if (empty($connection)) return;
    if (empty($table_name)) return;

    $sql = "SELECT * FROM `{$table_name}`";

		if (!is_object($connection))
			return;

    $result = $connection->query($sql);

    if (empty($result))
      return ("`{$table_name}` table doesn't exists!");

    $fields_dump = $result->fetch_fields();

    $fields_array = [];

    foreach ($fields_dump as $key => $fields_dump_single) {
      $fields_array[] = $fields_dump_single->name;
    }

    return $fields_array;

  }


public static function insertIntoSQL($table_name = "", $connection = "") {

  if (empty($connection)) return;
  if (empty($table_name)) return;

  $get_fields = static::get_fields($table_name, $connection);

  if (!is_array($get_fields))
    return;

  $get_fields = implode(", ", $get_fields);

  $get_fields = "INSERT INTO `{$table_name}` ({$get_fields})";

  return $get_fields;

}



    public static function insertStatOptions($titan = "") {

      if (empty($titan)) return;

      $response = DB::ConnectTitan($titan);

      ob_start();

      $get_table_name = $titan->getOption('hotels_table_name');

      $statement = static::insertIntoSQL($get_table_name, $response);

      _e($statement);

      $output = ob_get_clean();


      return $output;


    }

    public static function get_fields_and_data_type($table_name = "", $connection = "") {

      if (empty($connection)) return;
      if (empty($table_name)) return;

      $sql = "SHOW COLUMNS from ".$table_name."";

			if (!is_object($connection))
				return;

      $result = $connection->query($sql);

      if (empty($result))
        return ("`{$table_name}` table doesn't exists!");

        $fields_array = [];

        if(!empty(mysqli_num_rows($result)))
        {

          while($row2 = mysqli_fetch_row($result))
          {
            $fields_array[] = [ 'title' => $row2[0], 'type' => $row2[1] ];
          }
        }

      return $fields_array;

    }

    public static function showTables($titan = "") {

      if (empty($titan)) return;

      $response = DB::ConnectTitan($titan);
      $db_name = $titan->getOption( 'db_name');

			if (!is_object($response))
				return;

      ob_start();

      $mysqli = &$response;

          //show tables
          $result = $response->query("SHOW TABLES from `{$db_name}`");

              //show tables
              //print_r($result);
              while($tableName = mysqli_fetch_row($result))
              {
                  $table = $tableName[0];
                  echo '<h3>' ,$table, '</h3>';
                  $result2 = $mysqli->query("SHOW COLUMNS from ".$table."");


                  if (empty($result2))
                    continue;

                  if(!empty(mysqli_num_rows($result2)))
                  {
                      echo '<table cellpadding = "0" cellspacing = "0" class "db-table">';
                      echo '<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>';
                      while($row2 = mysqli_fetch_row($result2))
                      {
                          echo '<tr>';
                          foreach ($row2 as $key=>$value)
                          {
                              echo '<td>',$value, '</td>';
                          }
                          echo '</tr>';
                      }
                      echo '</table><br />';
                  }
              }

      $output = ob_get_clean();

      return $output;


    }

    public static function json_data_upload($titan = "") {

      if (empty($titan)) return;

      ob_start();

      $html = '<div><form name="upload_json_file" method="post" action="" enctype="multipart/form-data">

    <input type="file" name="json_data_file" id="json_data_file">
    <input type="submit" value="Upload JSON File" name="submit_json_file">
    </form></div>';
      $html .= static::show_limit();

      if (isset($_POST['submit_json_file']))
        $html .= static::processJSONFile($_POST, $_FILES);

      _e($html);

      $output = ob_get_clean();



      return $output;
    }

    public static function processJSONFile($post, $file) {

      global $_FILES;

      ob_start();

      $file = $file['json_data_file'];

      $file_ext = strtolower(end(explode('.' , $file['name'])));
      $file_name = $file['name'];
      $file_tmp = $file['tmp_name'];

      if ((strcmp("json", $file_ext)) !== 0)
        return "<span style='color: red'>Error: Not a JSON File</span>";

        if ((file_exists(hotels_pro_fw_PLUGIN_DIR."files".DS.$file_name)))
            unlink(hotels_pro_fw_PLUGIN_DIR."files".DS.$file_name);

          move_uploaded_file($file_tmp, hotels_pro_fw_PLUGIN_DIR."files".DS.$file_name);


        _e("<span class='tmp_json_file_val' style='display: block'>".hotels_pro_fw_PLUGIN_DIR."files".DS.$file_name."</span>");

        // if (empty($post['large_file']) && empty($post['extra_tags']))
        //   _e(static::simple_parse(hotels_pro_fw_PLUGIN_DIR."files".DS.$file_name));
        // elseif (!empty($post['large_file']) && empty($post['extra_tags']))
        //   _e(static::streaming_parse(hotels_pro_fw_PLUGIN_DIR."files".DS.$file_name));
        // elseif (empty($post['large_file']) && !empty($post['extra_tags']))
        //   _e(static::extra_tags_parse(hotels_pro_fw_PLUGIN_DIR."files".DS.$file_name));



      $output = ob_get_clean();


      return $output;
    }

    public static function show_limit() {

      ob_start();

          $max_upload = ini_get('upload_max_filesize');
          //select post limit
          $max_post = ini_get('post_max_size');
          //select memory limit
          $memory_limit = ini_get('memory_limit');
          // return the smallest of them, this defines the real limit
          $file_upload_stat = ini_get('file_uploads');

          _e("<br>"."Max upload: ".$max_upload."<br>");
          _e("Max post: ".$max_post."<br>");
          _e("Max memory limit: ".$memory_limit."<br>");
          _e("File Upload Status: ".( $file_upload_stat ? "On" : "Off" )."<br><hr>");

      $output = ob_get_clean();

      return $output;

    }

    public static function get_json_editor($titan = "") {

      return '<div id="jsoneditor" style="width: 100%; height: 600px;"></div>';
    }


    public static function link_with_db_columns($titan = "") {

      if (empty($titan)) return;

      ob_start();
      $response = DB::ConnectTitan($titan);
      $get_table_name = $titan->getOption('hotels_table_name');

      $fields_array = static::get_fields_and_data_type($get_table_name, $response);

      _e("Please specify JSON field name's for each corresponding database columns. Each on separate line.");
      _e("Make sure you have <b>".count($fields_array)."</b> field(s) name there and if a field is array indicate like this - <br><b>field_name|a</b><br> ");
      _e("Separate each child column with '-', example-<br><b>field_name-field_index-field_index_2</b>");
      _e("It will serialize the whole array into a single string.<br><br>Also keep in mind the data type.<br>");
      _e("<br>Current table: <strong>".$get_table_name."</strong><br><br>");

      foreach ($fields_array as $key => $fields_array_single)
         _e($fields_array_single['title']." | ".$fields_array_single['type']."<br>");

      _e("<br>");
      _e("<br>");

      _e("<span class='needed_records' data-needed_records='".count($fields_array)."'>".count($fields_array)."</span>");

      _e("<br>");
      _e("<br>");

      $output = ob_get_clean();

      return $output;

    }

    public static function generate_sql_from_column_values($titan) {

      if (empty($titan)) return;


      return "<button class='generate_sql_from_column_values'>Generate SQL</button><br>";

    }


    public static function import2db($titan = '') {

      if (empty($titan)) return;

      return "<button class='import_2_db'>Import</button>";

    }

    public static function json_data_api_request($titan = '') {

      if (empty($titan)) return;

      $get_api_url = $titan->getOption('api_url');

      $html = "";
      $html .= $get_api_url.'<input type="text" id="api_url_to_request" name="api_url_to_request">'."<br>";
      $html .= "Next ID: ".'<input type="text" id="api_url_next_id" name="api_url_next_id">'." (keep empty to do it from first)<br>";
      $html .= "Fetch Total: ".'<input type="text" id="api_total_fetch" name="api_total_fetch">'." (keep empty to fectch all records)<br>";
      $html .= "File Name: ".'<input type="text" id="api_file_name" name="api_file_name">'.".json<br>";
      //$html .= "Append File: ".'<input type="text" id="api_file_name" name="api_file_name">'.".json<br>";

      $html .= "<br><button class='api_make_request'>Request Data and Save to file</button>";

      return $html;
    }


}


 ?>
