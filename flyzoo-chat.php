<?php
        /*
          Plugin Name: Live Chat - Flyzoo
          Plugin URI: http://www.flyzoo.co/
          Description: Flyzoo Chat is a sleek and powerful chat platform with Live Support, Group Chats and Realtime visitors monitoring. Get started in just 5 minutes, engage your customers and increase sales!
          Version: 1.2.1
          Author: Andrea De Santis
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

         function flyzoo_chat_uninstall() {

    if(get_option('FlyzooApplicationID')) {  delete_option( 'FlyzooApplicationID'); }
    if(get_option('FlyzooPoweredBy')) {  delete_option( 'FlyzooPoweredBy'); }
    if(get_option('FlyzooApiEnabled')) {  delete_option( 'FlyzooApiEnabled'); }
}

       function flyzoo_get_wp_userid()
      {
            $user = wp_get_current_user();
            if($user->ID > 0)
            {
            return $user->ID;
            }
            else
            {
            return '';
            }
        }

      function flyzoo_get_wp_loginname()
      {
            $user = wp_get_current_user();
            if($user->ID > 0)
            {
            return $user->user_login;
            }
            else
            {
            return '';
            }
        }
  
   function flyzoo_get_wp_email()
      {
            $user = wp_get_current_user();
            if($user->ID > 0)
            {
            return $user->user_email;
            }
            else
            {
            return '';
            }
        }  
    
    function flyzoo_get_wp_username()
    {
      $user = wp_get_current_user();
    
      if($user->ID <= 0)
      {
        return '';
      }
      if(defined('BP_VERSION'))
      {
        $visiblename = bp_get_loggedin_user_fullname();
      }
      else
      {
        $visiblename = $user->display_name;
      }
   
       return $visiblename;

    }
    
    function flyzoo_get_wp_avatar()
    {
      $user = wp_get_current_user();
      if($user->ID > 0)
      {
        if(defined('BP_VERSION'))
        {
          return bp_get_loggedin_user_avatar('type=full&html=false');
        }
      }
    
      return '';
    }
    
        class FlyzooWidget {
    
            protected $options;
    
            public function __construct() {
    
                add_action('wp_footer',  array(&$this, "embedFlyzoo"));
    
                if (is_admin()) {
                    add_action("admin_menu", array(&$this, "adminMenu"));
                    add_action('admin_init', array(&$this, "setOptions") );
                } 
    
            }
    
             function setOptions(){
                register_setting( 'flyzoo-options', 'FlyzooApplicationID' );
                register_setting( 'flyzoo-options', 'FlyzooPoweredBy' );
                register_setting( 'flyzoo-options', 'FlyzooApiEnabled' );
            
            }
    
            public function adminMenu() {
                add_options_page ("Flyzoo Chat for WP", "Flyzoo Chat for WP", "administrator", "flyzoo-chat", array
                    ( &$this, "createAdminPage" )
                );
    
            }
    
          public function createAdminPage() {
    
            $code = get_option('FlyzooApplicationID');    
    
?>
<style>
    #flyzoo-options ul { margin-left: 10px; }
    #flyzoo-options ul li { margin-left: 15px; list-style-type: disc;}
    #flyzoo-options h1 {margin-top: 5px; margin-bottom:10px; color: #00557f}
    .fz-span { margin-left: 23px;}
</style>
<div id="flyzoo-options" style="width:880px;margin-top:10px;">

    <div style="float: left; width: 300px;">
        <?php 
            echo '<a target="_blank" href="http://www.flyzoo.co?utm_source=wp-plugin">';
            echo '<img style="border-radius:5px;border:0px;" src="' . plugins_url( 'images/logo.jpg' , __FILE__ ) . '" > ';
             echo '</a>';
            ?>
        <?php
           //  if ($code !='') {
                echo '<a target="_blank" href="http://dashboard.flyzoo.co?utm_source=wp-plugin">';
                echo '<img style="border:0px;margin-top:5px;border-radius:5px;" src="' . plugins_url( 'images/dashboard.jpg' , __FILE__ ) . '" > ';
                echo '</a>';
           // }
                echo '<a target="_blank" href="http://www.flyzoo.co/support?utm_source=wp-plugin-help">';
                echo '<img style="border:0px;margin-top:5px; margin-bottom:5px;border-radius:5px;" src="' . plugins_url( 'images/support.jpg' , __FILE__ ) . '" > ';
                echo '</a>';
            
        ?>

        <!-- 
        <div style="float: left; width: 280px; padding: 10px; background-color:#f8f8f8; border-radius: 8px;margin-top: 5px;">
            <h3>The Amazing Chat Platform<br />to rock your website!</h3>

            <h4>Features</h4>
            <ul>
                <li>Get started in less than 5 minutes!</li>
                <li>FAST, Facebook-like message delivery with confirmation</li>
                <li>Eye-catching, responsive layout on Smartphones</li>
                <li>Customizable online, offline and login messages</li>
                <li>Floating windows with auto re-load while browsing the site</li>
                <li>User Profiles</li>
                <li>Single Sign On for WordPress/BuddyPress</li>
                <li>Realtime Insights for Operators <strong><i>(PRO Plan)</i></strong> </li>
                <li>Customize Colors <strong><i>(WOW and PRO Plans)</i></strong></li>               
                <li>...and much more!</li>
            </ul>

            Discover more on <a href="http://www.flyzoo.co?utm_source=wp-plugin">Flyzoo Website</a>!
        </div>
        -->
    </div>
    <div style="float: left; margin-left: 10px; width: 500px; background-color:#f8f8f8; padding: 10px; border-radius: 5px;">
        <h1>Get Started!</h1>
        <h3>1) Create your account on Flyzoo</h3>
        <span class="fz-span">
        Go to <a href="http://www.flyzoo.co/signup?s=go&t=12">Flyzoo Sign up</a> and create your account.
        </span><br /><br />
        <h3>2) Enter your Flyzoo Application ID</h3>

        <form method="post" action="options.php">
            <?php settings_fields( 'flyzoo-options' ); ?>

            <p class="fz-span">
                You'll receive a Flyzoo Application ID right after account creation, or you can get it later
                from the  <a target="_blank" href="http://dashboard.flyzoo.co/?utm_source=wp-plugin">Flyzoo Dashboard</a> under the SETUP > WEBSITE menu<br /><br />
                Enter your Flyzoo Application ID
                <input type="text" name="FlyzooApplicationID" id="FlyzooApplicationID" value="<?php echo(get_option("FlyzooApplicationID")); ?>" maxlength="999" />
               
            </p>
            <p class="fz-span">
           <input type="checkbox" id="FlyzooPoweredBy" name="FlyzooPoweredBy" <?php echo (get_option("FlyzooPoweredBy")==true)?'checked="checked"':''?>>
							    <strong>Required</strong> I acknowledge there is a 'powered by Flyzoo' link on the widget. <br />
            </p>
           
             <input type="submit" value="<?php echo(_e("Save Changes")) ?>" /><br /> <br /> 
                      <h3>3) Verify the script installation</h3>
        <p class="fz-span">Almost done :) Verify your installation from the <a target="_blank" href="http://dashboard.flyzoo.co/?utm_source=wp-plugin">Flyzoo Dashboard</a> under the SETUP > WEBSITE menu.<br /><br />
        </p>
        <h3>4) Done!</h3>
        <div class="fz-span">Go to the <a target="_blank" href="http://dashboard.flyzoo.co/?utm_source=wp-plugin">Flyzoo Dashboard</a> to manage your widget.    </div>
            <br /><br />
       <h1>Options</h1>
 
        <input type="checkbox" id="FlyzooApiEnabled" name="FlyzooApiEnabled" <?php echo (get_option("FlyzooApiEnabled")==true)?'checked="checked"':''?>>
							    <strong>Enable Single Sign On</strong> Check this to allow users log into the chat with their existing WordPress Account.<br />
            </p><br />
                <input type="submit" value="<?php echo(_e("Save Changes")) ?>" /><br /> <br /> 
     
      </form>



    
    </div>
</div>
<?php
        }
    
        public function embedFlyzoo()
        {
           $e = '';
           $code = get_option('FlyzooApplicationID');
    
           //if (get_option('FlyzooPoweredBy')!=true) return;
    

           $e .='<!-- Flyzoo Script V2 -->'
              .'<script type="text/javascript">'
              .'(function () { '
              .'var _fzwid="' . $code . '";'
              .'var fz = document.createElement("script"); fz.type = "text/javascript"; fz.async = true;'
              .'fz.src = "//widget.flyzoo.co/scripts/flyzoo.start.js";'
              .'var s = document.getElementsByTagName("script")[0]; s.parentNode.insertBefore(fz, s)})()'
              .'</script>';            
    
               echo ($e);
    
             
               if (get_option('FlyzooApiEnabled')!=true) return;
      
        $api = '<script type="text/javascript">'
              .'var FlyzooApi = { };'
              .'FlyzooApi.UserId = ' .  json_encode(flyzoo_get_wp_userid()) .';'  
              .'FlyzooApi.UserName = ' .  json_encode(flyzoo_get_wp_username()).';'   
              .'FlyzooApi.Avatar = ' .  json_encode(flyzoo_get_wp_avatar()) .';' 
              .'FlyzooApi.Email = ' .  json_encode(flyzoo_get_wp_email()) .';' 
              .'</script>';
    
              echo ($api);   
    
        }    
    }
    
    new FlyzooWidget();
?>