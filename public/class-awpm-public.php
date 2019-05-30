<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://github.com/andrewchuev/
 * @since      1.0.0
 *
 * @package    Awpm
 * @subpackage Awpm/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Awpm
 * @subpackage Awpm/public
 * @author     Andrew A. Chuev <andrew.chuev@gmail.com>
 */
class Awpm_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param string $plugin_name The name of the plugin.
	 * @param string $version The version of this plugin.
	 *
	 * @since    1.0.0
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Awpm_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Awpm_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/awpm-public.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'select2-css', plugin_dir_url( __FILE__ ) . 'css/select2.css', array(), $this->version, 'all' );


	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Awpm_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Awpm_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/awpm-public.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( 'select2-js', plugin_dir_url( __FILE__ ) . 'js/select2.js', array( 'jquery' ), $this->version, false );


	}

	public function awpm_ajaxurl() {
		echo '<script type="text/javascript">
           var ajaxurl = "' . admin_url( 'admin-ajax.php' ) . '";
         </script>';
	}

	public function projects_list() {
	    ?>

        <p><button id="new_project">New project</button></p>



        <div id="project_inputs">

            <div style="text-align: right; text-decoration: underline"><span class="cancel_project"> [x] </div>
            <input type="hidden" id="project_id" value="" />

            <div>
                <input id="project_title" placeholder="Title" />
            </div>

            <div>
                <select id="project_manager" placeholder="Project manager">
					<?php self::project_managers_list(); ?>
                </select>
            </div>
            <div>
                <select id="profile" placeholder="Profile" style="width: 300px;">
					<?php self::profiles_list(); ?>
                </select>
            </div>
            <div>
                <select id="country" placeholder="Country" style="width: 300px;">
					<?php self::country_list(); ?>
                </select>
            </div>
            <div>
                <select id="developers" placeholder="Developers" style="width: 300px;" multiple="multiple">
					<?php self::developers_list(); ?>
                </select>
            </div>
            <div>
                <select id="priority" placeholder="Priority">
					<?php self::priority_list(); ?>
                </select>
            </div>
            <div>
                <input id="deadline" type="date" placeholder="Deadline"/>
            </div>
            <div>
                <select id="project_type" placeholder="Type">
                    <option value="hourly">Hourly</option>
                    <option value="fix">Fix</option>
                </select>
            </div>
            <div>
                <input id="cost" placeholder="Cost"/>
            </div>
            <div>
                <textarea id="communication" placeholder="Communication"></textarea>
            </div>
            <div>
                <textarea id="notes" placeholder="Notes"></textarea>
            </div>
            <div id="add_project_wrap">
                <button id="add_project">Add project</button>
            </div>
            <div id="edit_project_wrap">
                <button id="save_project">Save project</button>
                <button class="cancel_project">Cancel</button>
                <button id="delete_project">Delete</button>
            </div>
        </div>

        <table id="projects_list"></table>

		<?php

	}

	public function get_projects() {
		if (!empty($_POST['project_id'])) {
		    $project_id = $_POST['project_id'];
		    $where = " where project_id = $project_id";
        }

		if (!empty($_POST['order'])) {
			$order = $_POST['order'];
			$orderby = " order by $order";
		}

	    global $wpdb;
		$sql      = "select * from wp_awpm_projects_view $where $orderby";
		$projects = $wpdb->get_results( $sql );

		foreach ($projects as $project) {
			$names = self::get_users_names($project->developers);
			$project->devs_names = $names;
		}

		echo json_encode( $projects );
		die();
	}

	public static function get_users_names( $devs_ids ) {
		$devs     = explode( ',', $devs_ids );
		$devs_names = get_users( array( 'include' => $devs, 'fields' => [ 'display_name' ] ) );
		$names    = '';
		foreach ( $devs_names as $name ) {
			$names .= $name->display_name . ' ';
		}

		return $names;
	}


	public function add_project() {
		$table_data = $_POST['project'];
		$count = 0;
		$fields = '';

        foreach($table_data as $col => $val) {
            if ($count++ != 0) $fields .= ', ';
            $fields .= "$col = '$val'";
        }

		$sql = "INSERT INTO wp_awpm_projects SET $fields;";

		global $wpdb;
        $add_project = $wpdb->query( $sql );
        echo json_encode(array('add_project: ' => $add_project));

		die();
	}

	public function save_project() {
		$table_data = $_POST['project'];
		$count = 0;
		$fields = '';

		foreach($table_data as $col => $val) {
			if ($count++ != 0) $fields .= ', ';
			$fields .= "$col = '$val'";
		}

		$sql = "UPDATE wp_awpm_projects SET $fields WHERE project_id = {$table_data['project_id']}";

		global $wpdb;
		$save_project = $wpdb->query( $sql );
		echo json_encode(array('add_project: ' => $save_project));
//        echo $sql;

		die();
	}

	public function delete_project() {
		$project_id = $_POST['project_id'];
		$sql = "DELETE FROM wp_awpm_projects WHERE project_id = $project_id";
		global $wpdb;
		$delete_project = $wpdb->query( $sql );
		echo json_encode(array('delete_project: ' => $delete_project));
		die();
	}

	public static function project_managers_list() {
		$users = get_users(array('role__in' => 'editor'));
		foreach ( $users as $user ) {
			echo "<option value='$user->ID'>$user->display_name</option>";
		}
	}

	public static function developers_list() {

		$users = get_users(array('role__in' => 'editor'));
		foreach ( $users as $user ) {
			echo "<option value='$user->ID'>$user->display_name</option>";
		}
	}

	public static function profiles_list() {
		global $wpdb;
		$sql      = 'select profile_id, profile_name from wp_awpm_profiles';
		$profiles = $wpdb->get_results( $sql );
		foreach ( $profiles as $profile ) {
			echo "<option value='$profile->profile_id'>$profile->profile_name</option>";
		}
	}

	public static function country_list() {
		$countries = array("AF" => "Afghanistan","AX" => "Åland Islands","AL" => "Albania","DZ" => "Algeria","AS" => "American Samoa","AD" => "Andorra","AO" => "Angola","AI" => "Anguilla","AQ" => "Antarctica","AG" => "Antigua and Barbuda","AR" => "Argentina","AM" => "Armenia","AW" => "Aruba","AU" => "Australia","AT" => "Austria","AZ" => "Azerbaijan","BS" => "Bahamas","BH" => "Bahrain","BD" => "Bangladesh","BB" => "Barbados","BY" => "Belarus","BE" => "Belgium","BZ" => "Belize","BJ" => "Benin","BM" => "Bermuda","BT" => "Bhutan","BO" => "Bolivia","BA" => "Bosnia and Herzegovina","BW" => "Botswana","BV" => "Bouvet Island","BR" => "Brazil","IO" => "British Indian Ocean Territory","BN" => "Brunei Darussalam","BG" => "Bulgaria","BF" => "Burkina Faso","BI" => "Burundi","KH" => "Cambodia","CM" => "Cameroon","CA" => "Canada","CV" => "Cape Verde","KY" => "Cayman Islands","CF" => "Central African Republic","TD" => "Chad","CL" => "Chile","CN" => "China","CX" => "Christmas Island","CC" => "Cocos (Keeling) Islands","CO" => "Colombia","KM" => "Comoros","CG" => "Congo","CD" => "Congo, The Democratic Republic of The","CK" => "Cook Islands","CR" => "Costa Rica","CI" => "Cote D'ivoire","HR" => "Croatia","CU" => "Cuba","CY" => "Cyprus","CZ" => "Czech Republic","DK" => "Denmark","DJ" => "Djibouti","DM" => "Dominica","DO" => "Dominican Republic","EC" => "Ecuador","EG" => "Egypt","SV" => "El Salvador","GQ" => "Equatorial Guinea","ER" => "Eritrea","EE" => "Estonia","ET" => "Ethiopia","FK" => "Falkland Islands (Malvinas)","FO" => "Faroe Islands","FJ" => "Fiji","FI" => "Finland","FR" => "France","GF" => "French Guiana","PF" => "French Polynesia","TF" => "French Southern Territories","GA" => "Gabon","GM" => "Gambia","GE" => "Georgia","DE" => "Germany","GH" => "Ghana","GI" => "Gibraltar","GR" => "Greece","GL" => "Greenland","GD" => "Grenada","GP" => "Guadeloupe","GU" => "Guam","GT" => "Guatemala","GG" => "Guernsey","GN" => "Guinea","GW" => "Guinea-bissau","GY" => "Guyana","HT" => "Haiti","HM" => "Heard Island and Mcdonald Islands","VA" => "Holy See (Vatican City State)","HN" => "Honduras","HK" => "Hong Kong","HU" => "Hungary","IS" => "Iceland","IN" => "India","ID" => "Indonesia","IR" => "Iran, Islamic Republic of","IQ" => "Iraq","IE" => "Ireland","IM" => "Isle of Man","IL" => "Israel","IT" => "Italy","JM" => "Jamaica","JP" => "Japan","JE" => "Jersey","JO" => "Jordan","KZ" => "Kazakhstan","KE" => "Kenya","KI" => "Kiribati","KP" => "Korea, Democratic People's Republic of","KR" => "Korea, Republic of","KW" => "Kuwait","KG" => "Kyrgyzstan","LA" => "Lao People's Democratic Republic","LV" => "Latvia","LB" => "Lebanon","LS" => "Lesotho","LR" => "Liberia","LY" => "Libyan Arab Jamahiriya","LI" => "Liechtenstein","LT" => "Lithuania","LU" => "Luxembourg","MO" => "Macao","MK" => "Macedonia, The Former Yugoslav Republic of","MG" => "Madagascar","MW" => "Malawi","MY" => "Malaysia","MV" => "Maldives","ML" => "Mali","MT" => "Malta","MH" => "Marshall Islands","MQ" => "Martinique","MR" => "Mauritania","MU" => "Mauritius","YT" => "Mayotte","MX" => "Mexico","FM" => "Micronesia, Federated States of","MD" => "Moldova, Republic of","MC" => "Monaco","MN" => "Mongolia","ME" => "Montenegro","MS" => "Montserrat","MA" => "Morocco","MZ" => "Mozambique","MM" => "Myanmar","NA" => "Namibia","NR" => "Nauru","NP" => "Nepal","NL" => "Netherlands","AN" => "Netherlands Antilles","NC" => "New Caledonia","NZ" => "New Zealand","NI" => "Nicaragua","NE" => "Niger","NG" => "Nigeria","NU" => "Niue","NF" => "Norfolk Island","MP" => "Northern Mariana Islands","NO" => "Norway","OM" => "Oman","PK" => "Pakistan","PW" => "Palau","PS" => "Palestinian Territory, Occupied","PA" => "Panama","PG" => "Papua New Guinea","PY" => "Paraguay","PE" => "Peru","PH" => "Philippines","PN" => "Pitcairn","PL" => "Poland","PT" => "Portugal","PR" => "Puerto Rico","QA" => "Qatar","RE" => "Reunion","RO" => "Romania","RU" => "Russian Federation","RW" => "Rwanda","SH" => "Saint Helena","KN" => "Saint Kitts and Nevis","LC" => "Saint Lucia","PM" => "Saint Pierre and Miquelon","VC" => "Saint Vincent and The Grenadines","WS" => "Samoa","SM" => "San Marino","ST" => "Sao Tome and Principe","SA" => "Saudi Arabia","SN" => "Senegal","RS" => "Serbia","SC" => "Seychelles","SL" => "Sierra Leone","SG" => "Singapore","SK" => "Slovakia","SI" => "Slovenia","SB" => "Solomon Islands","SO" => "Somalia","ZA" => "South Africa","GS" => "South Georgia and The South Sandwich Islands","ES" => "Spain","LK" => "Sri Lanka","SD" => "Sudan","SR" => "Suriname","SJ" => "Svalbard and Jan Mayen","SZ" => "Swaziland","SE" => "Sweden","CH" => "Switzerland","SY" => "Syrian Arab Republic","TW" => "Taiwan, Province of China","TJ" => "Tajikistan","TZ" => "Tanzania, United Republic of","TH" => "Thailand","TL" => "Timor-leste","TG" => "Togo","TK" => "Tokelau","TO" => "Tonga","TT" => "Trinidad and Tobago","TN" => "Tunisia","TR" => "Turkey","TM" => "Turkmenistan","TC" => "Turks and Caicos Islands","TV" => "Tuvalu","UG" => "Uganda","UA" => "Ukraine","AE" => "United Arab Emirates","GB" => "United Kingdom","US" => "United States","UM" => "United States Minor Outlying Islands","UY" => "Uruguay","UZ" => "Uzbekistan","VU" => "Vanuatu","VE" => "Venezuela","VN" => "Viet Nam","VG" => "Virgin Islands, British","VI" => "Virgin Islands, U.S.","WF" => "Wallis and Futuna","EH" => "Western Sahara","YE" => "Yemen","ZM" => "Zambia","ZW" => "Zimbabwe");
		foreach ( $countries as $key => $value ) {
			echo "<option value='$value'>$value</option>";
		}
	}

	public static function priority_list() {
		$colors = array( 'red', 'green', 'blue', 'yellow', 'gray' );
		foreach ( $colors as $key => $value ) {
			echo "<option value='$value' style='background-color: $value'>$value</option>";
		}
	}

	public static function get_users_list() {
	    $users = get_users(array('fields' => ['ID', 'display_name']));
	    return json_encode($users);
    }



}
