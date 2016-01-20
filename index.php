<?php
/*
Plugin Name: Kurs BCA
Plugin URI: https://github.com/udibagas/wp_kurs_bca
Description: A simple plugin to add Kurs Bank BCA as a widget to your wordpress Sidebar - Develop by Bagas Udi Sahsangka 081218425750
Version: 1.0.
Author: Bagas Udi Sahsangka
Author URI: http://www.facebook.com/bagas.udi.sahsangka
License: GPL2
*/

class wp_kurs_bca extends WP_Widget {

	// constructor
    function wp_kurs_bca() {
        parent::WP_Widget(false, $name = __('Kurs BCA', 'wp_widget_kurs') );
    }

	// widget form creation
	function form($instance) {

	// Check values
	if( $instance) {
        $title = esc_attr($instance['title']);
	} else {
        $title = '';
        $text = '';
        $textarea = '';
	}
	?>

	<p>
	<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title', 'wp_widget_kurs'); ?></label>
	<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
	</p>

	<?php
	}

	// update widget
	function update($new_instance, $old_instance) {
        $instance = $old_instance;
        // Fields
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['text'] = strip_tags($new_instance['text']);
        $instance['textarea'] = strip_tags($new_instance['textarea']);
        return $instance;
	}

	// display widget
	function widget($args, $instance) {
        extract( $args );
        // these are the widget options
        $title = apply_filters('widget_title', $instance['title']);
        $text = $instance['text'];
        $textarea = $instance['textarea'];
        echo $before_widget;
        // Display the widget
        echo '<div class="widget-kurs">';
        // Check if title is set
        if ( $title ) {
            echo $before_title . $title . $after_title;
        }

	   /* start kurs */
	   include('simple_html_dom.php');
	   $html = file_get_html('http://www.bca.co.id/id/kurs-sukubunga/kurs_counter_bca/kurs_counter_bca_landing.jsp');
	   $tables         = $html->find('table');
	   $tblMataUang    = str_get_html($tables[1]);
	   $tblKurs        = str_get_html($tables[2]);
	   $mataUang       = [];
	   $kurs           = [];

	   // ambil mata uang
	   foreach ($tblMataUang->find('td') as $td) {
	       $mataUang[] = $td->innertext;
	   }

	   unset($mataUang[0]); // "Mata Uang"

	   // ambil kurs
	   $i = 0;
	   foreach ($tblKurs->find('tr') as $tr) {
	       $row = str_get_html($tr);
	       foreach ($row->find('td') as $td) {
	           $kurs[$i][] = $td->innertext;
	       }
	       $i++;
	   }

	   ?>

	   <div style="text-align:center;">e-Rate per <?php echo str_get_html($kurs[0][0])->find('div', 0)->innertext; ?></div><br />
	   <table class="table">
	       <thead>
	           <tr>
	               <th> Mata Uang </th>
	               <th> Jual </th>
	               <th> Beli </th>
	           </tr>
	       </thead>
	       <tbody>
	           <?php foreach ($mataUang as $i => $v) : ?>
	           <tr>
	               <td> <?php echo $v ?> </td>
	               <td> <?php echo $kurs[$i+1][0] ?> </td>
	               <td> <?php echo $kurs[$i+1][1] ?> </td>
	           </tr>
	           <?php endforeach; ?>
	       </tbody>
	   </table>

	   <?php
	}

}

// register widget
add_action('widgets_init', create_function('', 'return register_widget("wp_kurs_bca");'));

?>
