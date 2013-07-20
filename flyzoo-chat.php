<?php
    /*
      Plugin Name: Flyzoo Chat & Social Widget
      Plugin URI: http://www.flyzoo.co/
      Description: Elegant <strong>Chat</strong> and <strong>Social Widget</strong> to engage, keep and grow your visitors!
      Version: 0.1.0
      Author: Andrea De Santis
      Author URI: http://www.flyzoo.co/
      License: GPL2
    
      Copyright 2013 Andrea De Santis (email : info@flyzoo.co)
    
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
        }
    
        public function adminMenu() {
            add_options_page ("Flyzoo for WP", "Flyzoo for WP", "administrator", "flyzoo-chat", array
                ( &$this, "createAdminPage" )
            );
    
        }
    
      public function createAdminPage() {
    
        $code = get_option('FlyzooApplicationID');
    
    //echo '<img src="' . plugins_url( 'images/wordpress.png' , __FILE__ ) . '" > ';
    
?>
<style>
    #flyzoo-options ul { margin-left: 10px; }
    #flyzoo-options ul li { margin-left: 15px; list-style-type: disc;}
      #flyzoo-options h1 {margin-top: 5px; margin-bottom:10px; color: #00557f}
    .fz-span { margin-left: 23px;}
</style>
<div id="flyzoo-options" style="width:880px;margin-top:10px;">

    <div style="float: left; width: 300px;">
        <?php echo '<img style="border-radius:5px;" src="' . plugins_url( 'images/logo.jpg' , __FILE__ ) . '" > ';?>
        <?php
            if ($code !='') {
                echo '<a target="_blank" href="http://dashboard.flyzoo.co?utm_source=wp-plugin">';
                echo '<img style="border:0px;margin-top:5px;border-radius:5px;" src="' . plugins_url( 'images/dashboard.jpg' , __FILE__ ) . '" > ';
                echo '</a>';
            }
                echo '<a target="_blank" href="http://www.flyzoo.co/support?utm_source=wp-plugin-help">';
                echo '<img style="border:0px;margin-top:5px; margin-bottom:5px;border-radius:5px;" src="' . plugins_url( 'images/support.jpg' , __FILE__ ) . '" > ';
                echo '</a>';
           
        ?>


        <div style="float: left; width: 280px; padding: 10px; background-color:#f8f8f8; border-radius: 8px;margin-top: 5px;">
            <h3>Free, Elegant Chat and Social Widget <br />to rock your website!</h3>

            <h4>Features</h4>
            <ul>
                <li>One-to-one private chats: great for your community or Live Support!</li>
                <li>Group Chatrooms</li>
                <li>Realtime Visitor Insights: current page, referrer, campaign tracking...</li>
                <li>Social Commenting</li>
                <li>Social Sharing</li>
                <li>User Profiles</li>
                <li>Realtime Notifications</li>
                <li>Points, Badge and Rewards</li>
                <li>... and much more!</li>
            </ul>

            Discover more on <a href="http://www.flyzoo.co?utm_source=wp-plugin">Flyzoo Website</a>!
        </div>
    </div>
    <div style="float: left; margin-left: 10px; width: 500px; background-color:#f8f8f8; padding: 10px; border-radius: 5px;">
        <h1>Get Started!</h1>
        <h3>1) Create your account on Flyzoo</h3>
        <span class="fz-span">
        Go to <a href="http://www.flyzoo.co/joinbeta?utm_source=wp-plugin">Flyzoo Join Beta </a> and create your account.
        </span><br /><br />
        <h3>2) Set your Flyzoo Application ID</h3>

        <form method="post" action="options.php">
            <?php settings_fields( 'flyzoo-options' ); ?>

            <p class="fz-span">
                You'll receive a Flyzoo Application ID right after account creation, or you can get it later
                from the  <a target="_blank" href="http://dashboard.flyzoo.co/?utm_source=wp-plugin">Flyzoo Dashboard</a> under the SETUP > WEBSITE MENU<br /><br />
                Enter your Flyzoo Application ID
                <input type="text" name="FlyzooApplicationID" id="FlyzooApplicationID" value="<?php echo(get_option("FlyzooApplicationID")); ?>" maxlength="999" />
                <input type="submit" value="<?php echo(_e("Save Changes")) ?>" />
            </p>
        </form>
        <h3>3) Verify the installation</h3>
        <p class="fz-span">Almost done :) You need to verify your installation to confirm that you are the
        website's owner and perform administrative tasks on the widget.<br /> </br>
             You can verify your install right after account creation, or you can do it later
                using the <a target="_blank" href="http://dashboard.flyzoo.co/?utm_source=wp-plugin">Flyzoo Dashboard</a> under the SETUP > WEBSITE MENU<br /><br />
        </p>
        <h3>4) Done!</h3>
        <div class="fz-span">Go to the <a target="_blank" href="http://dashboard.flyzoo.co/?utm_source=wp-plugin">Flyzoo Dashboard</a> to customize the following options:
            <ul>
                <li>Login Welcome message</li>
                <li>We are Offline/Online  message</li>
                <li>Notification (notify new users, new comments...)</li>
                <li>User list (Ban users...)</li>
                <li>Rewarding (Create points, badge and rewords to engage users)</li>
                <li>Track Referral</li>
                <li>... and much more!</li>
            </ul>

        </div>
    </div>
</div>
<?php
        }
    
        public function embedFlyzoo()
        {
           $e = '';
           $code = get_option('FlyzooApplicationID');
    
           if ($code == '') return;
    
           $e .='<!-- Flyzoo script v1 Beta -->'
              .'<script type="text/javascript">'
              .'(function (w, d) {'
              .'var _fzwid="' . $code . '";'
              .'var loader = function () {'
              .'var s = d.createElement("script");'
              .'var tag = d.getElementsByTagName("script")[0];'
              .'s.src = "http://widget.flyzoo.co/scripts/flyzoo.start.js";'
              .'_url = location.href;'
              .'var loaded = false;'    
              .'tag.parentNode.insertBefore(s, tag); };'
              .'if (w.addEventListener) {'
              .'w.addEventListener("load", loader, false);'
              .'} else if (w.attachEvent) { w.attachEvent("onload", loader); } else { w.onload = loader; }'
              .'})(window, document);'
              .'</script>';
    
               echo ($e);
        }    
    }
    
    new FlyzooWidget();
?>