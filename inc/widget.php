<?php

/**
 * new WordPress Widget format
 * Wordpress 2.8 and above
 * @see http://codex.wordpress.org/Widgets_API#Developing_Widgets
 */

class XCKD_API_Widget extends WP_Widget {

    /**
     * Register widget with WordPress.
     */
    function __construct() {
        parent::__construct(
            'xkcd_widget', // Base ID
            __( 'XKCD Widget', 'xkcd_embedder' ), // Name
            array( 'description' => __( 'A widget to show your favorite webcomic!', 'xkcd_embedder' ), ) // Args
        );
    }

    /**
     * Front-end display of widget.
     *
     * @see WP_Widget::widget()
     *
     * @param array $args     Widget arguments.
     * @param array $instance Saved values from database.
     */
    public function widget( $args, $instance ) {

        $out = '';

        if ( $instance['comic'] == 'specific' ) {
            $id = $instance['comic_id'];
        } else {
            $id = $instance['comic'];
        }

        $xkcd = new XKCD_Comic();
        $content = $xkcd->get($id); 

        echo $args['before_widget'];


        if ( $instance['comic_title'] ) {
            echo $args['before_title'] . apply_filters( 'widget_title', esc_attr($content->safe_title) ). $args['after_title'];
        } else {
             echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
        }

        $out .= '<a href="http://xkcd.com/' . $content->num . '" target="_blank">';
        $out .= '<img class="xkcd-img" src="' . esc_url($content->img) . '" title="'. esc_attr($content->alt) .'" >';
        $out .= '</a>';
        echo $out;


        echo $args['after_widget'];
    }

    /**
     * Sanitize widget form values as they are saved.
     *
     * @see WP_Widget::update()
     *
     * @param array $new_instance Values just sent to be saved.
     * @param array $old_instance Previously saved values from database.
     *
     * @return array Updated safe values to be saved.
     */
    public function update( $new_instance, $old_instance ) {
        $instance = array();

        //set and sanitize user inputs
        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
        $instance['comic_title'] = ( ! empty ( $new_instance['comic_title'] ) ) ? absint( $new_instance['comic_title'] ) : '';
        $instance['comic'] = ( ! empty ( $new_instance['comic'] ) ) ? sanitize_text_field( $new_instance['comic'] ) : '';
        $instance['comic_id'] = ( ! empty ( $new_instance['comic_id'] ) ) ? absint( $new_instance['comic_id'] ) : '';

        return $instance;
    }

    /**
     * Back-end widget form.
     *
     * @see WP_Widget::form()
     *
     * @param array $instance Previously saved values from database.
     */
    public function form( $instance ) {
        // Set up the form
        $title = '';
        if( isset ( $instance['title'] ) ) {
            $title = $instance['title'];
        }

        $comic_title = '';
        if( isset ( $instance['comic_title'] ) ) {
            $comic_title = $instance['comic_title'];
        }

        $comic = '';
        if( isset ( $instance['comic'] ) ) {
            $comic = $instance['comic'];
        }

        $comic_id = '';
        if( isset ( $instance['comic_id'] ) ) {
            $comic_id = $instance['comic_id'];
        }

        ?>
        <p>
        <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
        <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </p>
        <p>
            <input class="checkbox" type="checkbox" value="1" <?php checked( 1, $comic_title ); ?> id="<?php echo $this -> get_field_id( 'comic_title' ); ?>" name="<?php echo $this -> get_field_name( 'comic_title' ); ?>" />
            <label for="<?php echo $this -> get_field_id( 'comic_title' ); ?>"><?php _e( 'Use XKCD comic title for widget title?' ); ?></label>
        </p>
        <p>
            <label for"<?php echo $this -> get_field_id( 'comic' ); ?>"><?php _e('Dynamic Comic?')?></label>
            <br>
            <select name="<?php echo $this -> get_field_name( 'comic' ); ?>">
                <option <?php echo selected( $comic, 'random', FALSE ); ?> value='random'><?php _e( 'Random', 'xkcd_embedder')?></option>
                <option <?php echo selected( $comic, 'latest', FALSE ); ?> value='latest'><?php _e( 'Latest', 'xkcd_embedder' )?></option>
                <option <?php echo selected( $comic, 'specific', FALSE ); ?> value='specific'><?php _e( 'Choose Your Own!', 'xkcd_embedder' )?></option>
            </select>   </p>
        <p>
        <p>
        <label for="<?php echo $this->get_field_id( 'comic_id' ); ?>"><?php _e( 'XKCD Comic ID:' ); ?></label> 
        <input class="widefat" id="<?php echo $this->get_field_id( 'comic_id' ); ?>" name="<?php echo $this->get_field_name( 'comic_id' ); ?>" type="text" value="<?php echo esc_attr( $comic_id ); ?>">
        </p>
        <?php 
    }


} // class XCKD_API_Widget

// register XCKD_API_Widget widget
function register_xkcd_widget() {
    register_widget( 'XCKD_API_Widget' );
}
add_action( 'widgets_init', 'register_xkcd_widget' );

?>