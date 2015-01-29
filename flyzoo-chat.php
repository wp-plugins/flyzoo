<?php
/*
Plugin Name: Flyzoo Chat - Group & Live Support Chat 
Plugin URI: http://www.flyzoo.co/
Description: Need a chat for your website? Flyzoo is the perfect chat for your blog, community or e-commerce.
Version: 1.4.6
Author: Flyzoo
Author URI: http://www.flyzoo.co/
License: GPL2

Copyright 2014 Andrea De Santis (email : info@flyzoo.co)

This program is free trial software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

register_uninstall_hook(__FILE__, 'flyzoo_chat_uninstall');


function flyzoo_check_regex($path, $patterns) {
  $to_replace = array(
    '/(\r\n?|\n)/',
    '/\\\\\*/',
  );
  $replacements = array('|','.*');
  $patterns_fixed = preg_quote($patterns, '/');

  $regexps[$patterns] = '/^(' . preg_replace($to_replace, $replacements, $patterns_fixed) . ')$/';
  return (bool) preg_match($regexps[$patterns], $path);
}

function flyzoo_show_page() {

  $mode = trim(get_option("FlyzooPageFilterMode"));
  $matched = FALSE;
  $pagelist = trim(get_option("FlyzooPageFilterList"));

  if ($mode == "0" || $mode =="") return TRUE;

  if ( $pagelist != '') {
    if(function_exists('mb_strtolower')) {
      $pagelist = mb_strtolower($pagelist);
      $currentpath = mb_strtolower($_SERVER['REQUEST_URI']);
    }
    else {
      $pagelist = strtolower( $pagelist);
      $currentpath = strtolower($_SERVER['REQUEST_URI']);
    }

    if (flyzoo_check_regex($currentpath,"/wp-admin/*")) {
        return TRUE;
    }

    $matched = flyzoo_check_regex($currentpath, $pagelist);

    $matched = ($mode == '2')?(!$matched):$matched;
  }
  else if($mode == '2'){
    $matched = TRUE;
  }
  return $matched;
}

function flyzoo_chat_uninstall()
{
    
    if (get_option('FlyzooApplicationID')) {
        delete_option('FlyzooApplicationID');
    }

    if (get_option('FlyzooPoweredBy')) {
        delete_option('FlyzooPoweredBy');
    }

    if (get_option('FlyzooApiEnabled')) {
        delete_option('FlyzooApiEnabled');
    }

    if (get_option('FlyzooHideInDashboard')) {
        delete_option('FlyzooHideInDashboard');
    }  

    if (get_option('FlyzooPageFilterList')) {
        delete_option('FlyzooPageFilterList');
    }          
    
        if (get_option('FlyzooPageFilterMode')) {
        delete_option('FlyzooPageFilterMode');
    }  
}

function flyzoo_get_wp_userid()
{
    $user = wp_get_current_user();
    if ($user->ID > 0) {
        return $user->ID;
    } else {
        return '';
    }
}

function flyzoo_get_wp_loginname()
{
    $user = wp_get_current_user();
    if ($user->ID > 0) {
        return $user->user_login;
    } else {
        return '';
    }
}

function flyzoo_get_wp_email()
{
    $user = wp_get_current_user();
    if ($user->ID > 0) {
        return $user->user_email;
    } else {
        return '';
    }
}

function flyzoo_get_wp_username()
{
    $user = wp_get_current_user();
    
    if ($user->ID <= 0) {
        return '';
    }
    if (defined('BP_VERSION')) {
        $visiblename = bp_get_loggedin_user_fullname();
    } else {
        $visiblename = $user->display_name;
    }
    
    return $visiblename;
    
}

function flyzoo_get_avatar_url($get_avatar)
{
    preg_match("/src='(.*?)'/i", $get_avatar, $matches);
    return $matches[1];
}

function flyzoo_get_wp_avatar()
{
    $user = wp_get_current_user();
    if ($user->ID > 0) {
        if (defined('BP_VERSION')) {
            return bp_get_loggedin_user_avatar('type=full&html=false');
        } else {
            
            try {
                return flyzoo_get_avatar_url(get_avatar($user->ID));
            }
            catch (Exception $e) {
                return "";
            }
        }
    }
    
    return '';
}

function flyzoo_logout()
{
    setcookie("flyzoo-force-logout", "true", time() + 3600, "/");
}

function flyzoo_embed_chatroom($atts)
{    
    $embed = "<div id='flyzoo-embedded-chatroom' data-id='" . $atts['id'] . "' style='width:" . $atts['width'] . "; height:" . $atts['height'] . ";'></div>";
    return $embed;    
}

add_shortcode('flyzoo-embed-chatroom', 'flyzoo_embed_chatroom');

class FlyzooWidget
{
    
    public $flyzooroot;
    
    public function __construct()
    {
        
        $this->flyzooroot = "http://www.flyzoo.co/";
        add_action('wp_logout', 'flyzoo_logout');
        add_action('wp_footer', array(
            &$this,
            "embedFlyzoo"
        ));
        
        if (is_admin()) {
            add_action("admin_menu", array(
                &$this,
                "adminMenu"
            ));
            
            add_action('admin_init', array(
                &$this,
                "setOptions"
            ));
            
            if (get_option('FlyzooHideInDashboard') != true) {
                $Path = $_SERVER['REQUEST_URI'];
                
                if (strpos($Path, 'wp-admin/plugin-install.php?tab=plugin-information') === false) {
                    add_action('admin_footer', array(
                        $this,
                        'embedFlyzoo'
                    ));
                }
            }
            
            if (get_option('FlyzooApplicationID') == '') {
                $this->addNewWebsite();
            }          
        }   
    }
    
    function setOptions()
    {
        register_setting('flyzoo-options', 'FlyzooApplicationID');
        register_setting('flyzoo-options', 'FlyzooPoweredBy');
        register_setting('flyzoo-options', 'FlyzooApiEnabled');
        register_setting('flyzoo-options', 'FlyzooHideInDashboard');
        register_setting('flyzoo-options', 'FlyzooPageFilterList');
        register_setting('flyzoo-options', 'FlyzooPageFilterMode');    
    }
    
    public function adminMenu()
    {
        
        add_menu_page('Flyzoo Live Chat', 'Flyzoo Live Chat', 'manage_options', 'flyzoo-chat', array(
            $this,
            'createAdminPage'
        ), content_url() . '/plugins/flyzoo/images/flyzoo-icon.png');
      
    }
    
    public function getSignupUrl()
    {
        
        return $this->flyzooroot . 'signup?utm_source=wordpress&utm_medium=admin&s=pro&t=12&fzsiteurl=' . urlencode(site_url()) . '&p=wordpress&e=' . urlencode(get_option('admin_email')) . '&sip=' . $_SERVER['REMOTE_ADDR'] . '&un=' . urlencode(wp_get_current_user()->display_name);
        
    }
    
    private function addNewWebsite()
    {
        
        if (!is_callable('curl_init')) {
            return;
        }
        
        $service     = $this->flyzooroot . "service/addwebsite";
        $p['ip']     = $_SERVER['REMOTE_ADDR'];
        $p['url']    = site_url();
        $p['source'] = "wordpress";
        
        $client = curl_init();
        
        curl_setopt($client, CURLOPT_AUTOREFERER, TRUE);
        curl_setopt($client, CURLOPT_HEADER, 0);
        curl_setopt($client, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($client, CURLOPT_URL, $service);
        curl_setopt($client, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)');
        
        if (!empty($p)) {
            curl_setopt($client, CURLOPT_POST, count($p));
            curl_setopt($client, CURLOPT_POSTFIELDS, http_build_query($p));
        }
        
        $data = curl_exec($client);
        curl_close($client);
        
        return $data;
        
    }
    
    
    public function createAdminPage()
    {
        $code = get_option('FlyzooApplicationID');
        
        
?>
<style>
    #flyzoo-options ul { margin-left: 10px; }
    #flyzoo-options ul li { margin-left: 15px; list-style-type: disc;}
    #flyzoo-options h1 {margin-top: 5px; margin-bottom:10px; color: #00557f}
    .fz-span { margin-left: 23px;}
    
    
.flyzoo-signup-button {
  float: left;
  vertical-align: top;
  width: auto;
  height: 30px;
  line-height: 30px;
  padding: 10px;
  font-size: 22px;
  color: white;
  text-align: center;
  text-shadow: 0 1px 2px rgba(0, 0, 0, 0.25);
  background: #c0392b;
  border-radius: 5px;
  border-bottom: 2px solid #b53224;
  cursor: pointer;
  -webkit-box-shadow: inset 0 -2px #b53224;
  box-shadow: inset 0 -2px #b53224;
  text-decoration: none;
  margin-top: 10px;
  margin-bottom: 10px;
  clear: both;
}
    
a.flyzoo-signup-button:hover {
  cursor: pointer;
  color: #f8f8f8;
}
    
    
</style>
<div id="flyzoo-options" style="width:880px;margin-top:10px;">

    <div style="float: left; width: 300px;">
        <?php
        
        echo '<a target="_blank" href="http://www.flyzoo.co?utm_source=wp-plugin">';
        echo '<img style="border-radius:5px;border:0px;" src="' . plugins_url('images/logo.jpg', __FILE__) . '" > ';
        echo '</a>';
?>
        <?php
        if ($code != '') {
            echo '<a target="_blank" href="http://dashboard.flyzoo.co?utm_source=wp-plugin">';
            echo '<img style="border:0px;margin-top:5px;border-radius:5px;" src="' . plugins_url('images/dashboard.jpg', __FILE__) . '" > ';
            echo '</a>';
        }
        echo '<a target="_blank" href="http://www.flyzoo.co/support?utm_source=wp-plugin-help">';
        echo '<img style="border:0px;margin-top:5px; margin-bottom:5px;border-radius:5px;" src="' . plugins_url('images/support.jpg', __FILE__) . '" > ';
        echo '</a>';
        
?>


    </div>
    <div style="float: left; margin-left: 10px; width: 500px; background-color:#f8f8f8; padding: 10px; border-radius: 5px;">    

        <h1>Flyzoo Live Chat</h1>  
  
        <?php
        
        if ($code == '') {
            echo "<a class='flyzoo-signup-button' target='_blank' href='" . $this->getSignupUrl() . "'>Click here to create your account!</a>'";
        }
        
?>
      
<script type="text/javascript">

    jQuery(document).ready(function () {

            function updateFilterPanel() {

        if (jQuery("#FlyzooPageFilterMode").val() != "0") {
            jQuery("#FlyzooPageFilterDiv").show()
        } else {
            jQuery("#FlyzooPageFilterDiv").hide();
        }
    };
        jQuery("#FlyzooPageFilterMode").change(function () {
            updateFilterPanel();
        });

          updateFilterPanel();
    })
</script>

<div style="clear: both"></div>
        <h3>Enter your Flyzoo Application ID</h3>

        <form method="post" action="options.php">
            <?php
        settings_fields('flyzoo-options');
?>

            <p >
            <input type="text" style="width: 400px" name="FlyzooApplicationID" id="FlyzooApplicationID" value="<?php
        echo (get_option("FlyzooApplicationID"));
?>" maxlength="999" />   
               
            </p>
            <p>
                <input type="checkbox" id="FlyzooPoweredBy" name="FlyzooPoweredBy" <?php
        echo (get_option("FlyzooPoweredBy") == true ? 'checked="checked"' : '');
?>>
                <strong>Required</strong> I acknowledge there is a 'powered by Flyzoo' link on the widget. <br />
            </p>

            <input type="submit" value="<?php
        echo (_e("Save Changes"));
?>" /><br /> <br />
            <hr /><br />
            <h1>Options</h1>
            <br />
            <input type="checkbox" id="FlyzooApiEnabled" name="FlyzooApiEnabled" <?php
        echo (get_option("FlyzooApiEnabled") == true ? 'checked="checked"' : '');
?>>
            <strong>Enable Single Sign On</strong> Check this to allow users log into the chat with their existing WordPress Account.<br />
            <br />

              <input type="checkbox" id="FlyzooHideInDashboard" name="FlyzooHideInDashboard" <?php
        echo (get_option("FlyzooHideInDashboard") == true ? 'checked="checked"' : '');
?>>
            <strong>Hide in WordPress Admin</strong> Check this to hide the dock from the WP Admin. <br />
            <br />

            <strong>PAGE FILTERS</strong><br /><br>
            Show Flyzoo
            <select id="FlyzooPageFilterMode" name="FlyzooPageFilterMode">
            <option <?php echo (get_option("FlyzooPageFilterMode") == "0" || get_option("FlyzooPageFilterMode") == "" ? 'selected="selected"' : '');?> value="0">on all pages</option>
            <option <?php echo (get_option("FlyzooPageFilterMode") == "1" ? 'selected="selected"' : '');?> value="1">only on the listed pages</option>
            <option <?php echo (get_option("FlyzooPageFilterMode") == "2" ? 'selected="selected"' : '');?> value="2">on all pages except those listed</option>

            </select>
            <div id="FlyzooPageFilterDiv" style="display:none">
            <ul>
            <li>Enter only one path per line</li>
            <li>Always start the path with a forward slash (/)</li>
            <li>Use '*' for the wildcard (Ex. /2014/posts/* to select all the posts)</li>
            </ul>
           
                                   
            <textarea style="width: 450px; height: 90px" name="FlyzooPageFilterList" id="FlyzooPageFilterList"><?php echo (get_option("FlyzooPageFilterList")); ?></textarea>
                
            </div>   
          <br/><br/>
            <input type="submit" value="<?php
        echo (_e("Save Changes"));
?>" /><br /> <br />

        </form>
    </div>
</div>
<?php
    }
    
    public function embedFlyzoo()
    {
        $e    = '';
        $code = get_option('FlyzooApplicationID');
        
        if ($code == '') return;
        //if (get_option('FlyzooPoweredBy')!=true) return;
     
        if (!flyzoo_show_page()) return;
        
        $e .= '<!-- Flyzoo Script V2 -->' . '<script type="text/javascript">' . '(function () { ' . 'window._FlyzooApplicationId="' . $code . '";' . 'var fz = document.createElement("script"); fz.type = "text/javascript"; fz.async = true;' . 'fz.src = "//widget.flyzoo.co/scripts/flyzoo.start.js";' . 'var s = document.getElementsByTagName("script")[0]; s.parentNode.insertBefore(fz, s)})()' . '</script>';
        
        echo ($e);
                
        if (get_option('FlyzooApiEnabled') != true)
            return;
        
        $api = '<script type="text/javascript">' . 'var FlyzooApi = FlyzooApi || { };' . 'FlyzooApi.UserId = ' . json_encode(flyzoo_get_wp_userid()) . ';' . 'FlyzooApi.UserName = ' . json_encode(flyzoo_get_wp_username()) . ';' . 'FlyzooApi.Avatar = ' . json_encode(flyzoo_get_wp_avatar()) . ';' . 'FlyzooApi.Email = ' . json_encode(flyzoo_get_wp_email()) . ';' . '</script>';
        
        echo ($api);
        
    }

}

new FlyzooWidget();
?>