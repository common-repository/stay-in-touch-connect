<?php
/**
 * Plugin Name:       Stay In Touch Connect
 * Plugin URI:        https://stayintouch.co.in/plugins/wordpress/
 * Description:       Stay In Touch Plugin For Adding Emails To Stayintouch Lists.
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            stayintouch
 * Author URI:        https://stayintouch.co.in/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       stayintouch
 */

 /*
Stay In Touch Connect is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.
 
Stay In Touch Connect is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
 
You should have received a copy of the GNU General Public License
along with Stay In Touch Connect. If not, see https://stayintouch.co.in/plugins/wordpress/.
*/

/**
 * Activate the plugin.
 */

register_activation_hook(__FILE__,'sit_plugin_activation');


/**
 * Deactivation plugin.
 */
register_deactivation_hook(__FILE__,'sit_plugin_deactivation');

/**
    Database SHit
*/


function sit_create_plugin_table(){

    global $wpdb;

    //setup return value
    $return_value = false;

    try {
        //get appropriate Charset of current database
        $charset_collate = $wpdb->get_charset_collate();

        //sql for custom table creation
        $sql = "CREATE TABLE {$wpdb->prefix}sit_newsletter(
            id mediumint(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
            token text NOT NULL,
            list_id varchar(32) NULL,
            created_at TIMESTAMP DEFAULT '2020-01-01 00:00:00',
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
            ) $charset_collate;";

            // include wordpress funstion for wpDelta
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

            // DBdelta will create a new table if none exists or update and existing one
            dbDelta($sql);

            //sql for custom table creation
        $sql = "CREATE TABLE {$wpdb->prefix}sit_newsletter_log(
            id mediumint(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
            email text NOT NULL,
            subscriber_id varchar(32) NULL,
            created_at TIMESTAMP DEFAULT '2020-01-01 00:00:00',
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
            ) $charset_collate;";

            // include wordpress funstion for wpDelta
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

            // DBdelta will create a new table if none exists or update and existing one
            dbDelta($sql);

            //return true
            $return_value = true;
    } catch (Exception $th) {
        //throw $th;
    }

    //return result
    return $return_value;
}

function sit_drop_plugin_table(){
    global $wpdb;

    //setup return value
    $return_value = false;

    try {
         //get appropriate Charset of current database
        $charset_collate = $wpdb->get_charset_collate();

        $table_name = $wpdb->prefix . 'sit_newsletter';
        $sql = "DROP TABLE IF EXISTS $table_name";
        $wpdb->query($sql);

        $table_name = $wpdb->prefix . 'sit_newsletter_log';
        $sql = "DROP TABLE IF EXISTS $table_name";
        $wpdb->query($sql);

    } catch (Exception $th) {
        //throw $th;
    }
}

function sit_plugin_activation(){
    //create/update tables
    sit_create_plugin_table();

}

function sit_plugin_deactivation(){
    //drop tables
    sit_drop_plugin_table();
}


// Add Shortcode
function sit_form_shortcode() {

    echo "<input type='email' id='sit_front_subscriber_email'></input> <button id='sit_front_subscribe'>Subscribe</button>";

}
add_shortcode( 'sit_form', 'sit_form_shortcode' );


/**
    Enqueue Script / Style
 */

add_action( 'admin_enqueue_scripts', 'sit_enqueue' );
add_action( 'wp_enqueue_scripts', 'sit_public_enqueue' );
function sit_enqueue( $hook ) {
    wp_enqueue_script(
        'ajax-script',
        plugins_url( '/js/stayintouch.js', __FILE__ ),
        array( 'jquery' ),
        '1.0.0',
        true
    );
    wp_enqueue_style(
        'sit-style',
        plugins_url( '/css/stayintouch.css', __FILE__ ),
        true
    );
}

function sit_public_enqueue( $hook ) {
    wp_enqueue_script(
        'ajax-script',
        plugins_url( '/js/stayintouch_public.js', __FILE__ ),
        array( 'jquery' ),
        '1.0.0',
        true
    );

}


/**
    Admin Pages
 */
add_action( 'admin_menu', 'sit_page' );

function sit_page() {

    $top_leve_menu = 'sit_welcome_page';
    add_menu_page(
        'Stay In Touch',
        'Stay In Touch',
        'manage_options',
        $top_leve_menu,
        $top_leve_menu,
        'dashicons-email',
        20
    );

    add_submenu_page(
        $top_leve_menu,
        'Connection',
        'Connection',
        'manage_options',
        'connection',
        'sit_connection_page'
    );

    add_submenu_page(
        $top_leve_menu,
        'Logs',
        'Logs',
        'manage_options',
        'logs',
        'sit_log_page'
    );

};



 function sit_welcome_page(){
     echo "<h1 class='welcome'>Welcome To Stay In Touch Connect | Wordpress Plugin for Stayintouch Newsletter System</h1><hr>";

     global $wpdb;

    $table_name = $wpdb->prefix . 'sit_newsletter';

    $mylink = $wpdb->get_row( "SELECT * FROM $table_name WHERE id = 1", ARRAY_A );

    if (strlen($mylink['token']) == 60 && !empty($mylink['list_id'])) {
    $response = wp_remote_get( "http://in02.stayintouch.co.in/api/v1/lists/605d6e2c16d8e/subscribers?api_token=".$mylink['token'] );
    $datanew = json_decode($response['body'],true);

    $count_subs =  count($datanew);
    // print_r($datanew);
    echo "<div class='flex-container'><div>Status : <span style='color:green;'>Connected <span class='connec'>&#11044;</span></span></div><div>Total Subscribers : $count_subs</div></div>";

    echo "<div class='fex-container-two'><div> Use Shortcode : <code>[sit_form]</code> on the page where you want <a href='https://www.stayintouch.co.in/'>stayintouch</a> subscription form to be shown.</div></div>";
    }else {
        echo "<div class='flex-container'><div>Your System Dosen't Seems To Be Connected . Please Visit Connection Page</div></div>";
    }

     
 };

 function sit_connection_page(){

    global $wpdb;

    $table_name = $wpdb->prefix . 'sit_newsletter';

    $mylink = $wpdb->get_row( "SELECT * FROM $table_name WHERE id = 1", ARRAY_A );

    //echo strlen($mylink['token']);


    $ajax_page = plugins_url( '/includes/ajax.php', __FILE__ );
    echo "<h1 class='welcome'>Connection</h1><hr>";
    $adminurl = admin_url().'admin-ajax.php?action=sit_ajax_save_login';

    if (strlen($mylink['token']) == 60) {
        echo "<div class='flex-container-token'>";
        // echo "<div>Your API Token Is <b style='background-color:green;padding:5px;color:white;'>".$mylink['token']."</b>&nbsp;<a onclick='' id='delete_token' class=''>Disconnect</a></div>";
        echo "<div>Your API Token Is <b style='background-color:green;padding:5px;color:white;'>Connected</b>&nbsp;<a onclick='' id='delete_token' class=''>Disconnect</a></div>";
        echo "</div>";

        //echo $mylink['list_id'];

        if (empty($mylink['list_id'])) {
            // fetch Available lists for User

            $response = wp_remote_get( "http://in02.stayintouch.co.in/api/v1/lists?api_token=".$mylink['token'] );

            $response = json_decode($response['body'],true);

            echo "<div class='flex-container-token'>";
            echo "<div>";
            // echo "<h2>Available lists for User</h2>";
            echo "<label>Select List : </label>";
            echo "<select id='listid'>";
            foreach ($response as $listname) {
                echo $listname['uid']."<br>";
                echo "<option value='".$listname['uid']."'>".$listname['name']."</option>";
                
            }
            echo "</select>";
            echo "<button class='button' id='list_select'>Submit</button>";
            echo "</div>";
            echo "</div>";
            
        }else {


            $response = wp_remote_get( 'http://in02.stayintouch.co.in/api/v1/lists/'.$mylink['list_id'].'?api_token='.$mylink['token'] );

            $data_j = json_decode($response['body'],true);
            // echo $response;
            echo "<div class='flex-container-token'>";
            echo "<div>Connected List  <b style='background-color:green;padding:5px;color:white;'>".$data_j['list']['name']."</b>&nbsp;<a id='delete_list' class=''>Disconnect</a></br></div>";
            echo "</div>";
            echo "<div class='flex-container-token'>";
            echo "<div>Use Shortcode : <code>[sit_form]</code> on the page where you want <a href='https://www.stayintouch.co.in/'>stayintouch</a> subscription form to be shown.</div>";
            echo "</div>";
        }

    }else {
        echo "<div class='flex-fome'>";
        echo "<div class='login-form'><label>Username / Email:</label>
        <input type='email' id='sit_username' name='sit_username' placeholder='Info@example.com'><br><br>
        <label>Password:</label>
        <input type='password' id='sit_password' name='sit_password' placeholder='*********'><br><br>
        <button type='submit' class='' value='Log in' id='sit_form_submit'>Log in</button>&nbsp;<a target='_blank' href='https://in02.stayintouch.co.in/password/reset'><button>Forgot Password</button></a></div>";

        echo "<div class=''><h1>Don't Have Account ?</h1><hr><p>Stay In Touch (SIT) email offers and integrated, automated and robust feature set built to help the enterprise of any size. A powerfully simple and simply powerful email marketing solution designed to help you create and send compelling campaigns with ease.</p><a  target='_blank' href='https://www.stayintouch.co.in/'><button>Read More.</button></a>&nbsp;<a  target='_blank' href='https://www.stayintouch.co.in/price'><button>Sign Up</button></a></div>";
        echo "</div>";

    }
    
 };

 function sit_log_page(){
    echo "<h1 class='welcome'>Subscribers Logs</h1><hr>";

    global $wpdb;

    $table_name = $wpdb->prefix . 'sit_newsletter_log';

    $mylink = $wpdb->get_results( "SELECT * FROM $table_name", ARRAY_A );
    echo "<div class='table-log-container' >";
    echo "<div>";
    echo "<table class='sit_tabkle' style=''  >";
    echo "<tr class='head'>";
    echo "<th>Id</th>";
    echo "<th>Subscriber</th>";
    echo "<th>subscriber Id</th>"; 
    echo "<th>Created At</th>"; 
    echo "<th>Updated At</th>"; 
    echo "</tr>";
    foreach ($mylink as $data) {
    echo "<tr>";
    echo "<td>".$data['id']."</td>";
    echo "<td>".$data['email']."</td>";
    echo "<td>".$data['subscriber_id']."</td>";
    echo "<td>".$data['created_at']."</td>";
    echo "<td>".$data['updated_at']."</td>";
    echo "</tr>";
    }
    echo "</table>";
    echo "</div>";
    echo "</div>";

    //echo "<script>swal ( 'Oops' ,  'Something went wrong!' ,  'error' )</script>";
    

   //print_r($mylink);

};

/**
 * AJAX SHit
 */
// save login details
 add_action('wp_ajax_sit_ajax_save_login','sit_ajax_save_login'); //admin
 add_action('wp_ajax_nopriv_sit_ajax_save_login','sit_ajax_save_login'); //website

 // add selected list
 add_action('wp_ajax_sit_ajax_save_list','sit_ajax_save_list'); //admin
 add_action('wp_ajax_nopriv_sit_ajax_save_list','sit_ajax_save_list'); //website

 // delete selected list
 add_action('wp_ajax_sit_ajax_delete_list','sit_ajax_delete_list'); //admin
 add_action('wp_ajax_nopriv_sit_ajax_delete_list','sit_ajax_delete_list'); //website

  // delete selected token
  add_action('wp_ajax_sit_ajax_delete_token','sit_ajax_delete_token'); //admin
  add_action('wp_ajax_nopriv_sit_ajax_delete_token','sit_ajax_delete_token'); //website

  // subscribe a subscriber
  add_action('wp_ajax_sit_ajax_subscribe_a_subscriber','sit_ajax_subscribe_a_subscriber'); //admin
  add_action('wp_ajax_nopriv_sit_ajax_subscribe_a_subscriber','sit_ajax_subscribe_a_subscriber'); //website
  


function sit_ajax_save_login(){

    // $result = array(
    //     'status' => 0,
    //     'message' => 'could not save response',
    //     'token' => false
    // );

    $username = sanitize_email($_POST['sit_username']);
    $password = sanitize_text_field($_POST['sit_password']);

    //echo $password;

    $response = wp_remote_get( "http://in02.stayintouch.co.in/user/login/$username/$password" );

    $datanew = json_decode($response['body'],true);
    
    // echo $datanew['api_key'];

    global $wpdb;

    $table_name = $wpdb->prefix . 'sit_newsletter';
	
	$insert = $wpdb->insert( 
		$table_name, 
		array( 
            'id' => 1,
			'token' => $datanew['api_key'], 
            'created_at' => current_time( 'mysql' ), 
            'updated_at' => current_time( 'mysql' ),
		) 
	);

    if ($insert) {
        echo true;
    }else {
        echo false;
    }
    //echo json_encode($result,true);
    wp_die();
    
};

function sit_ajax_save_list(){

    $list_uid = sanitize_text_field($_POST['sit_list_uid']);

    //echo $list_uid;

    global $wpdb;

    $table_name = $wpdb->prefix . 'sit_newsletter';

    $update = $wpdb->update( $table_name, array( 'list_id' => $list_uid),array('ID'=> 1));
    

    if ($update) {
        echo true;
    }else {
        echo false;
    }

    wp_die();

};

function sit_ajax_delete_list(){


    global $wpdb;

    $table_name = $wpdb->prefix . 'sit_newsletter';

    $update = $wpdb->update( $table_name, array( 'list_id' => ''),array('ID'=> 1));
    

    if ($update) {
        echo true;
    }else {
        echo false;
    }

    wp_die();

};

function sit_ajax_delete_token(){

    global $wpdb;

    $table_name = $wpdb->prefix . 'sit_newsletter';

    $delete = $wpdb->delete( $table_name, array( 'ID' => 1 ) );
    

    if ($delete) {
        echo true;
    }else {
        echo false;
    }

    wp_die();

};

function sit_ajax_subscribe_a_subscriber(){

    global $wpdb;

    $table_name = $wpdb->prefix . 'sit_newsletter';

    $mylink = $wpdb->get_row( "SELECT * FROM $table_name WHERE id = 1", ARRAY_A );

    $list_uid = $mylink['list_id'];

    $api_token = $mylink['token'];

    //$delete = $wpdb->delete( $table_name, array( 'ID' => 1 ) );
    if (isset($_POST['subscriber']) && !empty($_POST['subscriber'])) {
        
        $email = sanitize_email($_POST['subscriber']);

        $response = wp_remote_post('http://in02.stayintouch.co.in/api/v1/lists/'.$list_uid.'/subscribers/store?api_token='.$api_token.'&EMAIL='.$_POST['subscriber'],array(
            'method'      => 'POST',
            'timeout'     => 45,
            'redirection' => 5,
            'httpversion' => '1.0',
            'blocking'    => true,
            'headers'     => array(),
            'body'        => array(),
            'cookies'     => array()
            ));
        $response = json_decode($response['body'],true);

        if ($response['status'] == 1) {
            $table_name = $wpdb->prefix . 'sit_newsletter_log';
	
            $insert = $wpdb->insert( 
                    $table_name, 
                    array( 
                        'email' => $email,
                        'subscriber_id' =>  $response['subscriber_uid'],
                        'created_at' => current_time( 'mysql' ), 
                        'updated_at' => current_time( 'mysql' ),
                    ) 
                );

                if ($insert) {
                    echo true;
                }else {
                    echo false;
                }
                //echo json_encode($result,true);
                wp_die();
        }else {
            echo "unable to insert data";
        }
        //echo $response['message'];
        
    }else {
        echo false;
    }
    
   
    // if ($delete) {
    //     echo true;
    // }else {
    //     echo false;
    // }

    wp_die();

};