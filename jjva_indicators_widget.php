<?php
/**************************************************************************************
* Plugin Name:Venezuelan Economic Indicators 
* Plugin URI: https://jordicuevas.com.ve/plugins/indicadores
* Description: Display some of the economic indicators from Venezuela in a Widget
* Version: 0.1.1
* Author:Jordi Cuevas | @jordicuevas
* Author URI: http://aljovatechnology.com.ve
* Text Domain: venezuelan-economic-indicators
* Domain Path: /languages/
* License: GPLv3
**************************************************************************************/
//Register and load the widget
function jjva_economic_indicators_load_widget() {
    register_widget( 'jjva_economic_indicators_widget' );
}
add_action( 'widgets_init', 'jjva_economic_indicators_load_widget' );
 
// Creating the widget 
class jjva_economic_indicators_widget extends WP_Widget {
     
    function __construct() {
        
    load_plugin_textdomain( 'venezuelan-economic-indicators', null, basename(dirname(__FILE__)) . '/languages/' ); 
        
        parent::__construct(
                // Base ID of your widget
                'jjva_economic-indicators-widget', 
                // Widget name will appear in UI
                __('Venezuelan Economic Widget', 'venezuelan-economic-indicators'), 
                  // Widget description
                array( 'description' => __( 'Widget that displays some economic indicators from Venezuela', 'venezuelan-economic-indicators' ), ) 
        );
    }
    // Creating widget front-end

    public function widget( $args, $instance ) {
          $jjva_economic_indicators_plugin_url =  plugin_dir_url( __FILE__ ) ;  
         
          wp_enqueue_style( 'jjva_indicators_css',$jjva_economic_indicators_plugin_url.'css/indicators.css'   );
          $title = apply_filters( 'widget_title', $instance['title'] );
          //before and after widget arguments are defined by themes
          echo $args['before_widget'];
            if ( ! empty( $title ) )
                 echo $args['before_title'] . $title . $args['after_title'];
         //This is where you run the code and display the output
                 $jjva_economic_indicators_file = "https://s3.amazonaws.com/dolartoday/data.json";
                 $jjva_economic_indicators_data = wp_remote_get($jjva_economic_indicators_file);
                 $jjva_economic_indicators_rows = wp_remote_retrieve_body( $jjva_economic_indicators_data ) ;
         //WE NOW CLEAN THE JSON OBJECT
             $json  = json_decode( preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $jjva_economic_indicators_rows), true );  
         //EXTRACTING THE JSON INTO PIECES
                 $jjva_economic_indicators_LastUpdate       =  $json['_timestamp']['fecha'];
                 $jjva_economic_indicators_SimadiDollar     =  $json['USD']['sicad2'];
                 $jjva_economic_indicators_OilBarrel        =  $json['MISC']['petroleo'];
                 $jjva_economic_indicators_BuyBSF           =  $json['COL']['compra'];
                 $jjva_economic_indicators_SaleBSF          =  $json['COL']['venta'];
                 $jjva_economic_indicators_TRMDollar        =  $json['USDCOL']['ratetrm'];
                 $jjva_economic_indicators_Euro             =  $json['EUR']['transferencia'];
?>
     <div id="jjva-indicators">
        <ul> 
             <li><img src=<?php echo $jjva_economic_indicators_plugin_url;?>icons/compra.png width="32" height="32"> <?php _e("BSF for Buy on Colombia: ","venezuelan-economic-indicators"); ?><b><?php echo  $jjva_economic_indicators_BuyBSF;?>  </b></li>
             <li><img src=<?php echo $jjva_economic_indicators_plugin_url;?>icons/venta.png width="32" height="32"> <?php _e("BSF for Sale on Colombia: ","venezuelan-economic-indicators"); ?><b><?php echo  $jjva_economic_indicators_SaleBSF;?>  </b></li>
             <li><img src=<?php echo $jjva_economic_indicators_plugin_url;?>icons/trm.png width="32" height="32"> <?php _e("TRM Col.Dollar: ","venezuelan-economic-indicators"); ?><b>$ <?php echo $jjva_economic_indicators_TRMDollar ;?></b></li>
             <li><img src=<?php echo $jjva_economic_indicators_plugin_url;?>icons/simadi.png width="32" height="32"><?php _e("DICOM Dollar: ","venezuelan-economic-indicators");?><b>BSF. <?php echo $jjva_economic_indicators_SimadiDollar;?></b></li >
             <li><img src=<?php echo $jjva_economic_indicators_plugin_url;?>icons/oil.png width="32" height="32"> <?php _e("Oil Barrel: ","venezuelan-economic-indicators"); ?><b>$ <?php echo $jjva_economic_indicators_OilBarrel;?></b></li>
             <li><img src=<?php echo $jjva_economic_indicators_plugin_url;?>icons/euro.png width="32" height="32"> <?php _e("Euro: ","venezuelan-economic-indicators"); ?><b>€ <?php echo $jjva_economic_indicators_Euro;?></b></li>      
      
        </ul> 
            <div id="jjva-last-updated"  ><?php _e("Last updated on: ","venezuelan-economic-indicators"); ?><?php echo $jjva_economic_indicators_LastUpdate; ?></div>
    </div>
     
<?php

echo $args['after_widget'];
}
         
    // Widget Backend 
    public function form( $instance ) {
         if ( isset( $instance[ 'title' ] ) ){
              $title = $instance[ 'title' ];
        }
        else {
              $title = __( 'New title', 'venezuelan-economic-indicators' );
        }
    //Widget admin form
?>
     <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
    </p>
<?php 
}
     
   //Updating widget replacing old instances with new
    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
        return $instance;
    }
} //Class jjva_economic_indicators_widget ends here

?>
