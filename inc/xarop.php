<?php


defined('ABSPATH') || exit;
add_action('wp_dashboard_setup', 'xarop_dashboard_widgets');

// // PRIVATE SITE if is devellopment site (dev. subdomain)
// add_action('template_redirect', 'xarop_private');
// function xarop_private()
// {
//   $siteUrl = $_SERVER['HTTP_HOST'];
//   // echo $siteUrl;
//   // if (!is_user_logged_in() || strpos($siteUrl, "dev.") === false) { 
//   if (!is_user_logged_in() ) {
//     wp_safe_redirect(wp_login_url(home_url(add_query_arg(array(), $wp->request))), 302);
//     exit();
//   }
// }

// LOGIN LOGO
function xarop_login_logo()
{
?>
  <style type="text/css">
    body.login div#login h1 a {
      background-image: url(//xarop.com/xarop-logo.png);
      background-size: 200px;
      width: 200px;
    }

    body.login.wp-core-ui .button-primary {
      background: #EE2455;
      border-color: #EE2455;
    }
  </style>
<?php
}
add_action('login_enqueue_scripts', 'xarop_login_logo');


// DASHBOARD

function remove_dashboard_meta() {
  remove_meta_box( 'dashboard_incoming_links', 'dashboard', 'normal' );
  remove_meta_box( 'dashboard_plugins', 'dashboard', 'normal' );
  remove_meta_box( 'dashboard_primary', 'dashboard', 'side' );
  remove_meta_box( 'dashboard_secondary', 'dashboard', 'normal' );
  remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );
  remove_meta_box( 'dashboard_recent_drafts', 'dashboard', 'side' );
  remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'normal' );
  remove_meta_box( 'dashboard_right_now', 'dashboard', 'normal' );
  remove_meta_box( 'dashboard_activity', 'dashboard', 'normal');
  }
  add_action( 'admin_init', 'remove_dashboard_meta' );

// xarop widget
function xarop_dashboard_widgets()
{
  global $wp_meta_boxes;
  wp_add_dashboard_widget('xarop_help_widget', 'xarop.com', 'xarop_dashboard_help');
}

function xarop_dashboard_help()
{
  echo '
  <!-- <h2><a href="//xarop.com?site=' . get_bloginfo('name') . '">xarop.com</a></h2> -->
  <div style="position: relative; width: 100%; height: 500px;">
    <iframe src="//xarop.com?site=' . get_bloginfo('name') . '" style="position: absolute;position: absolute; top: 0; left: 0; bottom: 0; right: 0; width: 100%; height: 100%;"></iframe>
  </div>
  ';
}
