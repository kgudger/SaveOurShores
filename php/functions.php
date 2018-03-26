<?php
/*--Basic Config calling---*/
global $renova_pagenow;
require_once(get_template_directory().'/admin/class-tgm-plugin-activation.php');
//Theme Options
require_once get_template_directory() . "/admin/index.php";
// //Metaboxes
require_once get_template_directory() . "/admin/custom-metabox.php";
// //Custom Post types
require_once get_template_directory() . "/admin/custom-post-types.php";
//Theme Stylesadmin
require_once get_template_directory() . "/admin/theme-styles.php";
//Theme scripts
require_once get_template_directory() . "/admin/theme-scripts.php";

require_once get_template_directory() . "/admin/mobile-menu.php";

// Re-define meta box path and URL
define('RWMB_URL', trailingslashit( get_stylesheet_directory_uri() . '/admin/meta-box'));
define('RWMB_DIR', trailingslashit( get_stylesheet_directory() . '/admin/meta-box'));
// Include the meta box script
require_once RWMB_DIR . 'meta-box.php';
include_once 'admin/meta-box-init.php';

/*---------------------------------------
---------Reason Initialiszation---------
-----------------------------------------*/
function renova_setup() 
  {
        //Feed links
		add_theme_support( 'automatic-feed-links' );
		//Nav menu
		register_nav_menu( 'primary', __( 'Primary Menu','renovalang') );
		//Sidebar
		$args = array(
		'name'          => __( 'renova_side', 'renovalang' ),
		'id'            => 'renova01',
		'description'   => '',
	    'class'         => '',
		'before_widget' => '<section id="%1$s"  class="blog-side-panel %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2>',
		'after_title'   => '</h2>' );
		register_sidebar( $args ); 

        //Content width
		if ( ! isset( $content_width ) ) $content_width = 900;
		//Initiate custom post types
        add_action( 'init', 'renova_post_types' );
        add_action( 'init', 'renova_post_gallery' );
        //Load the text domain
		load_theme_textdomain('renovalang', get_template_directory() . '/languages');
		//Post Thumbnails		
		add_theme_support( 'post-thumbnails', array('portfolio_item','gallery_item','post' ) );
        //Post formats
        add_theme_support(
			'post-formats', array(
				'image',
				'audio',
				'link',
				'quote',
				'video'
			)
		);

		set_post_thumbnail_size( 300, 300, true ); // Standard Size Thumbnails
		//Function to crop all thumbnails
		if(false === get_option("thumbnail_crop")) {
		add_option("thumbnail_crop", "1");
		} else {
		update_option("thumbnail_crop", "1");
		}	

  }
   add_action( 'after_setup_theme', 'renova_setup' );


/*---------------------------------------
---------Includes-----------------
-----------------------------------------*/


if (!is_admin() && 'wp-login.php' != $renova_pagenow) 
{ 
    add_action('wp_enqueue_scripts', 'renova_scripts');
    add_action('wp_enqueue_scripts', 'renova_styles' );
}
if(is_admin())
{

    add_action('admin_enqueue_scripts', 'renova_admin_scripts');
}
//Comment scrpt
if (is_singular()): wp_enqueue_script( "comment-reply" ); endif;



/*---------------------------------
Important Plugin Activation Check
-----------------------------------*/
add_action( 'tgmpa_register', 'designova_register_required_plugins' );
/**
 * Register the required plugins for this theme.
 *
 * In this example, we register two plugins - one included with the TGMPA library
 * and one from the .org repo.
 *
 * The variable passed to tgmpa_register_plugins() should be an array of plugin
 * arrays.
 *
 * This function is hooked into tgmpa_init, which is fired within the
 * TGM_Plugin_Activation class constructor.
 */
function designova_register_required_plugins() {

  /**
   * Array of plugin arrays. Required keys are name and slug.
   * If the source is NOT from the .org repo, then source is also required.
   */
  $plugins = array(

    // This is an example of how to include a plugin pre-packaged with a theme
    array(
      'name'            => 'Designova Renova Shortcodes', // The plugin name
      'slug'            => 'renova-shortcodes', // The plugin slug (typically the folder name)
      'source'          => get_stylesheet_directory() . '/plugins/renova-shortcodes.zip', // The plugin source
      'required'        => true, // If false, the plugin is only 'recommended' instead of required
      'version'         => '', // E.g. 1.0.0. If set, the active plugin must be this version or higher, otherwise a notice is presented
      'force_activation'    => true, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
      'force_deactivation'  => true, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
      'external_url'      => '', // If set, overrides default API URL and points to an external URL
    ),



  );

  // Change this to your theme text domain, used for internationalising strings
  $theme_text_domain = 'renovalang';

  /**
   * Array of configuration settings. Amend each line as needed.
   * If you want the default strings to be available under your own theme domain,
   * leave the strings uncommented.
   * Some of the strings are added into a sprintf, so see the comments at the
   * end of each line for what each argument will be.
   */
  $config = array(
    'domain'          => $theme_text_domain,          // Text domain - likely want to be the same as your theme.
    'default_path'    => '',                          // Default absolute path to pre-packaged plugins
    'parent_menu_slug'  => 'themes.php',        // Default parent menu slug
    'parent_url_slug'   => 'themes.php',        // Default parent URL slug
    'menu'            => 'install-required-plugins',  // Menu slug
    'has_notices'       => true,                        // Show admin notices or not
    'is_automatic'      => false,             // Automatically activate plugins after installation or not
    'message'       => '',              // Message to output right before the plugins table
    'strings'         => array(
      'page_title'                            => __( 'Install Required Plugins', $theme_text_domain ),
      'menu_title'                            => __( 'Install Plugins', $theme_text_domain ),
      'installing'                            => __( 'Installing Plugin: %s', $theme_text_domain ), // %1$s = plugin name
      'oops'                                  => __( 'Something went wrong with the plugin API.', $theme_text_domain ),
      'notice_can_install_required'           => _n_noop( 'This theme requires the following plugin: %1$s.', 'This theme requires the following plugins: %1$s.' ), // %1$s = plugin name(s)
      'notice_can_install_recommended'      => _n_noop( 'This theme recommends the following plugin: %1$s.', 'This theme recommends the following plugins: %1$s.' ), // %1$s = plugin name(s)
      'notice_cannot_install'           => _n_noop( 'Sorry, but you do not have the correct permissions to install the %s plugin. Contact the administrator of this site for help on getting the plugin installed.', 'Sorry, but you do not have the correct permissions to install the %s plugins. Contact the administrator of this site for help on getting the plugins installed.' ), // %1$s = plugin name(s)
      'notice_can_activate_required'          => _n_noop( 'The following required plugin is currently inactive: %1$s.', 'The following required plugins are currently inactive: %1$s.' ), // %1$s = plugin name(s)
      'notice_can_activate_recommended'     => _n_noop( 'The following recommended plugin is currently inactive: %1$s.', 'The following recommended plugins are currently inactive: %1$s.' ), // %1$s = plugin name(s)
      'notice_cannot_activate'          => _n_noop( 'Sorry, but you do not have the correct permissions to activate the %s plugin. Contact the administrator of this site for help on getting the plugin activated.', 'Sorry, but you do not have the correct permissions to activate the %s plugins. Contact the administrator of this site for help on getting the plugins activated.' ), // %1$s = plugin name(s)
      'notice_ask_to_update'            => _n_noop( 'The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.', 'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.' ), // %1$s = plugin name(s)
      'notice_cannot_update'            => _n_noop( 'Sorry, but you do not have the correct permissions to update the %s plugin. Contact the administrator of this site for help on getting the plugin updated.', 'Sorry, but you do not have the correct permissions to update the %s plugins. Contact the administrator of this site for help on getting the plugins updated.' ), // %1$s = plugin name(s)
      'install_link'                  => _n_noop( 'Begin installing plugin', 'Begin installing plugins' ),
      'activate_link'                 => _n_noop( 'Activate installed plugin', 'Activate installed plugins' ),
      'return'                                => __( 'Return to Required Plugins Installer', $theme_text_domain ),
      'plugin_activated'                      => __( 'Plugin activated successfully.', $theme_text_domain ),
      'complete'                  => __( 'All plugins installed and activated successfully. %s', $theme_text_domain ), // %1$s = dashboard link
      'nag_type'                  => 'updated' // Determines admin notice type - can only be 'updated' or 'error'
    )
  );

  tgmpa( $plugins, $config );

}


/*---------------------------------------
---------Customised Menu-----------------
-----------------------------------------*/
function renova_custom_menu($image = NULL,$onep = 0)
{
	$locations = get_nav_menu_locations();
	$menu = wp_get_nav_menu_object( $locations['primary']);

	
	_wp_menu_item_classes_by_context( $menu_items );

	$return = '<div class="navigation hidden-phone hidden-tablet">
    <ul id="main-nav">';
if($menu != '')
	{
	$menu_items = wp_get_nav_menu_items($menu->term_id);		

	$menunu = array();
	foreach((array)$menu_items as $key => $menu_item )
	{
		$menunu[ (int) $menu_item->db_id ] = $menu_item;
	}
	unset($menu_items);

	$parent_counter = 0;
	foreach ($menunu as $i => $men )
	{
		if($men->menu_item_parent == '0')
		{
			$parent_counter++;
		}
	}
    $logo_level = round($parent_counter / 2) + 1;
    
    $level_finder = 0;
	foreach ($menunu as $i => $men ){
		if($men->menu_item_parent == '0')
		{
			//Pushing logo
			$level_finder++;

			if($level_finder == $logo_level)
			{
             if($onep == 0) {$logo_link = "#body"; } else{ $logo_link= get_site_url();}
			$return .= '<li class="logo-wrap">
                 <a class="scroll-link logo" href="'.$logo_link.'" data-soffset="100">
                   <img alt="'.get_bloginfo('name').'" title="'.get_bloginfo('name').'" src="'.$image.'"/>
                 </a>
                 </li>';
			}

            //Other menu items
			$return .= '<li>';
			    //Specific additions add custom icons
				if( ( 'page' == $men->object ))
				{
					//print_r($men);
					$post_finder = get_post($men->object_id);
					$page_slug = $post_finder->post_name;

					$newlink   = strtolower(preg_replace('/[^a-zA-Z]/s', '', $page_slug)); 
					$href =  '#'.$newlink;				
					$incl_onepage = get_post_meta($men->object_id,'one_page',true);

                  if($incl_onepage == 'yes' OR $incl_onepage == 'Yes')
                    {
						$href =  '#'.$newlink;
						$identifyClass = "is_onepage";
				    }
				    else
				    {
                       $href = $men->url;
                       $identifyClass = "not_onepage";
				    }	
				} 
				else 
				{
					$href =  $men->url;
					$identifyClass = "not_onepage";

				}
				$return .= '<a class="scroll-link '.$identifyClass.'" href="'.$href.'" id="'.$newlink.'-linker" data-soffset="100">'.$men->title.'</a>';
               //child
               if(renova_detect_child($i, false) ){
					$return .= renova_detect_child($i, true);
				}
                
			$return .= '</li>' . "\n";


		}
	}
}
	else
	{
      $return .= '<ul id="main-nav"><li  class="links">';
 	  $return .= '<a id="defaultam-linker" title="Configure Menu" href="'.site_url().'/wp-admin/nav-menus.php">Configure Menu</a>';       
	  $return .= '</li>' . "\n";  
	}

	unset($menunu);	
	$return .= '</ul> </div>' . "\n";
	echo $return;
}

//child menu
function renova_detect_child($parent, $echo = false){
		
    $parent = ($parent != "") ? $parent : '0';

    $locations = get_nav_menu_locations();
	$menu = wp_get_nav_menu_object( $locations[ 'primary' ] );
	$menu_items = wp_get_nav_menu_items( $menu->term_id );
	
	_wp_menu_item_classes_by_context( $menu_items );
	
	$menu_next = array();
	foreach( (array) $menu_items as $key => $menu_item ){
		if($menu_item->menu_item_parent == $parent)
			$menu_next[ (int) $menu_item->db_id ] = $menu_item;
	}
	unset ($menu_items);
	
	if( !$echo ){
		if( !empty($menu_next) )
			return true;
		else
			return false;
	} else {
		$child_ul = '<ul>' . "\n";
		$ret = '';
			foreach ( $menu_next as $i => $mnn ){


			    //Specific additions add custom icons
				if( ( 'page' == $mnn->object ))
				{
					$post_finder = get_post($men->object_id);
			        $page_slug = $post_finder->post_name;   
			        
					$newlink   = strtolower(preg_replace('/[^a-zA-Z]/s', '', $page_slug)); 
					$href =  '#'.$newlink;				
					$iconp = get_post_meta($mnn->object_id,'menu_icon',true);
					$incl_onepage = get_post_meta($mnn->object_id,'one_page',true);

                  if($incl_onepage == 'yes' OR $incl_onepage == 'Yes')
                    {
						$href =  '#'.$newlink;
						$identifyClass = "is_onepage";
				    }
				    else
				    {
                       $href = $mnn->url;
                       $identifyClass = "not_onepage";
				    }	

				} 
				else 
				{
					$href =  $mnn->url;
					$identifyClass = "not_onepage";

				}



				$ret .= '<li><a class="scroll-link '.$identifyClass.'" href="'.$href.'" id="'.$newlink.'-linker" data-soffset="100">'.$mnn->title.'</a></li>' . "\n";
			}
			unset ($menu_next);
		$child_ul_close = '</ul>' . "\n";
		
		if( !empty($ret) )
			return $child_ul . $ret . $child_ul_close;
	}    

	}



/*---------------------------------------
---------Format comment Callback-----------
-----------------------------------------*/

function renova_format_comments($comment, $args, $depth) 
{
   $GLOBALS['comment'] = $comment; ?>
	<li <?php comment_class('commentlists'); ?>>
		<div id="comment-<?php comment_ID(); ?>" class=" clearfix cmntparent <?php
                        $authID = get_the_author_meta('ID');
                                                    
                        if($authID == $comment->user_id)
                           echo "cmntbyauthor";
                       else
                           echo "";
                        ?>">
			<div class="comment">


            				<div class="comment-author image-polaroid">
            					<?php 
                                $defimg = get_stylesheet_directory_uri(). "/images/avatar.png";
                                if(get_avatar($comment)):
                                	echo get_avatar($comment,$size='75');
                                else:
                                ?>
                                <img src="<?php echo $defimg; ?>"  alt="avatar" />
            					<?php endif; ?>
            				</div>
            				<div class="comment-body">
 										<div class="comment-meta">
											<span class="meta-name"><?php printf(__('%s','livelang'), get_comment_author_link()) ?></span>
											<span class="meta-date"> on <?php comment_time('F jS, Y'); ?></span>
											<div class="reply">
											<?php comment_reply_link(array_merge( $args, array('depth' => $depth, 'max_depth' => $args['max_depth']))) ?>
											<?php edit_comment_link(__('Edit','personalang'),'<span class="edit-comment">', '</span>'); ?>
										    </div>
									    </div>           					
                                <?php if ($comment->comment_approved == '0') : ?>
                   					<div class="alert-box success">
                      					<?php _e('Your comment is awaiting moderation.','personalang') ?>
                      				</div>
            					<?php endif; ?>
                                
                                <?php comment_text() ?>
                                
                                <!-- removing reply link on each comment since we're not nesting them -->
            					
                            </div>		
		</li>

<?php
}


/*---------------------------------------
-------BG color with alpha converter-----
-----------------------------------------*/
function renova_hex2rgb($hex) {
   $hex = str_replace("#", "", $hex);

   if(strlen($hex) == 3) {
      $r = hexdec(substr($hex,0,1).substr($hex,0,1));
      $g = hexdec(substr($hex,1,1).substr($hex,1,1));
      $b = hexdec(substr($hex,2,1).substr($hex,2,1));
   } else {
      $r = hexdec(substr($hex,0,2));
      $g = hexdec(substr($hex,2,2));
      $b = hexdec(substr($hex,4,2));
   }
   $rgb = array($r, $g, $b);
   return implode(",", $rgb); // returns the rgb values separated by commas
   //return $rgb; // returns an array with the rgb values
}


/*---------------------------------------
-------Columns-----
-----------------------------------------*/
// GET FEATURED IMAGE
function ST4_get_featured_image($post_ID){
 $post_thumbnail_id = get_post_thumbnail_id($post_ID);
 if ($post_thumbnail_id){
  $post_thumbnail_img = wp_get_attachment_image_src($post_thumbnail_id, 'featured_preview');
  return $post_thumbnail_img[0];
 }
}

// ADD NEW COLUMN
function ST4_columns_head($defaults) {
  $new = array();
  foreach($defaults as $key => $title) {
    if ($key=='title') // Put the Thumbnail column before the Author column
      $new['featured_image'] = 'Gallery Item';
    $new[$key] = $title;
  }
  return $new;


}

// SHOW INFO IN THE NEW COLUMN
function ST4_columns_content($column_name, $post_ID) {
 if ($column_name == 'featured_image') {
  $post_featured_image = ST4_get_featured_image($post_ID);
  if ($post_featured_image){
   echo '<img class="thumbnail" src="' . $post_featured_image . '" style="max-width:100px;" />'; 
  }
 }
}

add_filter('manage_gallery_item_posts_columns', 'ST4_columns_head');
add_filter('manage_gallery_item_posts_custom_column', 'ST4_columns_content', 1, 2);

function ST4_columns_content_with_default_image($column_name, $post_ID) {
 // Create a default.jpg image and save it in the images directory of your active theme.

 if ($column_name == 'featured_image') {
  $post_featured_image = ST4_get_featured_image($post_ID);
  if ($post_featured_image){
   // HAS A FEATURED IMAGE
   echo '<img src="' . $post_featured_image . '"  />';
   } else {
    // NO FEATURED IMAGE, USE THE DEFAULT ONE
    echo '<img src="' . get_template_directory_uri(). '/images/default.jpg" />'; 
   }
  }
}

/*Inpage Styles Init*/

// function renova_inpage_styles() 
// {
//     require_once get_template_directory() . "/inpagestyles.php";

// }

/*Inpage Styles Init*/
function renova_addmenu($new_title)
{  
  wp_enqueue_script( "menutrigger", get_stylesheet_directory_uri(). "/javascripts/menutrigger-init.js",array(),false,true);  
  add_action('wp_enqueue_scripts', 'renova_add_menu');
  wp_localize_script('menutrigger', 'pageid', $new_title);
}

add_filter('next_post_link', 'post_link_attributes');
add_filter('previous_post_link', 'post_link_attributes');
 
function post_link_attributes($output) {
    $code = 'class="btn btn-renova-alt"';
    return str_replace('<a href=', '<a '.$code.' href=', $output);
}


/*-----------------------------------------------------------------
Custom Styles
-------------------------------------------------------------------*/
function renova_my_styles_method() {

global $smof_data;

wp_enqueue_style('custom-style',get_template_directory_uri() . '/stylesheets/custom_css.css');

$custom_css ="";


//Generated Classes
$pages = get_pages( 'sort_order=asc&sort_column=menu_order&depth=1');
//Pages to sections published pages
$custom_css .=  '@media only screen and (min-width: 768px){';
foreach($pages as $pag):
setup_postdata($pag);
$newanchorpoint = strtolower(preg_replace('/[^a-zA-Z]/s', '', $pag->post_name)); 
$new_title      = $newanchorpoint;

// //Background
$ren_sb_position       =  get_post_meta($pag->ID,'sb_position',true); 
$ren_section_bg        =  get_post_meta($pag->ID,'parallax_bg',true);


$custom_css .=  '.'.$new_title.'-bgclass {';
$custom_css .=  'background:url('.$ren_section_bg.') '.$ren_sb_position.'  top no-repeat !important;';
$custom_css .=  '}';
endforeach;
$custom_css .=  '}';


//Other Styles

$custom_css .= '

#scroll
{
        background: url("'.$smof_data["ssd_icon"].'") no-repeat center top !important;
}


.navigation
{
    background: '.$smof_data['highlight_color'].' !important;
}
.postformat
{
  background: '.$smof_data['highlight_color'].' !important;
}
.wide-section-showcase{
    background: '.$smof_data['highlight_color'] .';
}

.da-thumbs li a div {
    background-color: rgba('.renova_hex2rgb($smof_data['highlight_color']).',0.8) !important; 
}

.testimonial-block{
   background: '.$smof_data['highlight_color'] .' !important;
}
/*Mobile nav bg*/
#nav a {

   background: '.$smof_data['highlight_color'] .'; 
}
#nav-toggle 
{
     background: '.$smof_data['highlight_color'] .';
     background:  url("'.get_stylesheet_directory_uri().'/stylesheets/hamburger.gif") no-repeat center center '.$smof_data['highlight_color'] .';
}
/* Time Line */
.cbp_tmtimeline:before {
    background: '.$smof_data['highlight_color'] .'
}
.cbp_tmtimeline > li:nth-child(2n+1) .cbp_tmtime span:last-child {
    color: '.$smof_data['highlight_color'] .';
}
.cbp_tmtimeline > li:nth-child(2n+1) .cbp_tmlabel {
    background: '.$smof_data['highlight_color'] .';
}
.cbp_tmtimeline > li .cbp_tmicon {
    box-shadow: 0 0 0 8px '.$smof_data['highlight_color'] .';

}
.cbp_tmtimeline > li:nth-child(2n+1) .cbp_tmlabel:after {
    border-right-color: '.$smof_data['highlight_color'] .';

}


.team-block-inner{
    background: '.$smof_data['highlight_color'] .' !important;
}

.news-date{
    background: '.$smof_data['highlight_color'] .' !important;
}

.news-specs{
    background: '.$smof_data['highlight_color'] .' !important;   
}

.btn-renova:hover{
    background:'.$smof_data['highlight_color'] .' !important;   
}

.btn-renova-alt:hover{
    background:'.$smof_data['highlight_color'] .' !important;    
}
.tweet_list li a {
    color: '.$smof_data['highlight_color'] .';
}

.shoutout
{
    border-left: solid 10px '.$smof_data['highlight_color'] .';
}
.inner-heading span{
border-bottom: double 4px '.$smof_data['highlight_color'] .';
}

#intro-nav ul li{
    background: '.$smof_data['highlight_color'] .';
}
p.p-price {
    color:'.$smof_data['highlight_color'] .';
}


.tweet_list li a:hover {
                color: '.$smof_data['highlight_color'] .';

            }

#container-folio{
  background: '.$smof_data['highlight_color'] .';
}

@media (max-width: 767px){
    .news-img{
    background: '.$smof_data['highlight_color'] .';
  }
  .promo-text > span:after {
  border-bottom:0 !important;
  } 

  .promo-text > span:after {
  border-bottom:0 !important;
  }
.promo-text-alt.darken > span:after {
   border-bottom:0 !important;
}
.promo-text-inv-yellow.darken > span:after
 {
   border-top:0 !important;
}
.promo-text-inv.darken > span:after
 {
   border-top:0 !important;
}   
}

@media (max-width: 360px){
    .promo-text{
    background:'.$smof_data['dsh_color'] .' !important;
  }
  .promo-text-inv-yellow{
     background: '.$smof_data['renovaheading_dark_bg'] .' !important;
     color: '.$smof_data['renovaheading_dark_text'] .' !important;
  }
.promo-text > span:after {
  border-bottom:0 !important;
  }

.promo-text-alt.darken > span:after {
   border-bottom:0 !important;
}
.promo-text-inv-yellow.darken > span:after
 {
   border-top:0 !important;
}
.promo-text-inv.darken > span:after
 {
   border-top:0 !important;
}

}
@media (max-width: 320px){
    .promo-text{
    background:'.$smof_data['dsh_color'] .' !important;
  }
  .promo-text-inv-yellow
  {
    background: '.$smof_data['renovaheading_dark_bg'] .';
    color: '.$smof_data['renovaheading_dark_text'] .' !important;
  }
  .promo-text > span:after {
  border-bottom:0 !important;
  }
.promo-text-alt.darken > span:after {
   border-bottom:0 !important;
}
.promo-text-inv-yellow.darken > span:after
 {
   border-top:0 !important;
}
.promo-text-inv.darken > span:after
 {
   border-top:0 !important;
}



}


/*Sub heads*/
.promo-text > span{
    background: '.$smof_data['dsh_color'] .';
    color:'.$smof_data['dsh_text_color'] .';
}

.promo-text-alt.darken > span{
  color: '.$smof_data['lsh_text_color'] .';
   background: '.$smof_data['lsh_color'] .';

}

.promo-text > span:after {
  border-bottom-color: '.$smof_data['dsh_color'] .' !important;
}

.promo-text-alt.darken > span:after {
  border-bottom-color: '.$smof_data['lsh_color'] .' !important;
}

/*Renova Heading*/
.promo-text-inv-yellow.darken > span{
    color: '.$smof_data['renovaheading_dark_text'] .';
    background: '.$smof_data['renovaheading_dark_bg'] .';
}
.promo-text-inv-yellow.darken > span:after
 {
  border-top-color: '.$smof_data['renovaheading_dark_bg'] .' !important;
}


.promo-text-inv.darken > span{
    color: '.$smof_data['renovaheading_light_text'] .';
    background: '.$smof_data['renovaheading_light_bg'] .';
}

.promo-text-inv.darken > span:after
 {
  border-top-color: '.$smof_data['renovaheading_light_bg'] .' !important;
}

';

  //Custom CSS
  if(isset($smof_data['custom_css']) AND $smof_data['custom_css'] != ''):
   $custom_css .= $smof_data['custom_css'];
  else:
   $custom_css .= '';
  endif;
 wp_add_inline_style( 'custom-style', $custom_css );
}
add_action( 'wp_enqueue_scripts', 'renova_my_styles_method' );

/*BG Video*/
add_action('wp_enqueue_scripts','background_video'); 
function background_video() {
    wp_enqueue_script("bgvideo", get_template_directory_uri(). "/javascripts/okvideo.js",array(),'1.0.0',true);
    wp_enqueue_script("bgvideo-init", get_template_directory_uri(). "/javascripts/bgvideo-init.js",array(),'1.0.0',true);
}
/**
* Make Header Shrink on Page Scroll
**/
 
add_action ('wp_footer','vr_shrink_head',1);
function vr_shrink_head() {
?>
 
<script>
jQuery(document).ready(function($) {
    $(window).scroll(function () {
        if ($(window).scrollTop() > 100) { 
            $(".logo-wrap").find( "img" ).removeClass('unshrink');
            $(".logo-wrap").find( "img" ).addClass('shrink');
        }
        else{
            $(".logo-wrap").find( "img" ).removeClass('shrink');
            $(".logo-wrap").find( "img" ).addClass('unshrink');
        }
    });
});
</script>
 
<?php 
}