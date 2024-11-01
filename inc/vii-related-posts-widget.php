<?php
class Vii_Related_Posts_Widget extends WP_Widget {
	function __construct() {
		parent::__construct (
			'vii_related_posts',
			'Vii Related Posts',
			array(
				'classname'                   => 'widget_recent_posts',
				'description'                 => __( 'Display related (same category, same tag, specify tag), lasted, popular (comment count) or random posts.' ),
				'customize_selective_refresh' => true,
				'show_instance_in_rest'       => true,
			),
		);
		$this->alt_option_name = 'widget_recent_entries';
	}

	function form( $instance ) {
		$title = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : 'Random posts';
		$number = isset( $instance['number'] ) ? absint( $instance['number'] ) : 5;
		if ( ! $number ) {
			$number = 5;
		}
		$type = isset( $instance['type'] ) ? esc_attr( $instance['type'] ) : '';
		$tag = isset( $instance['tag'] ) ? esc_attr( $instance['tag'] ) : '';
		$cat = isset( $instance['cat'] ) ? esc_attr( $instance['cat'] ) : '';
		$featured_image = isset( $instance['featured_image'] ) ? (bool) $instance['featured_image'] : false;
		$post_excerpt = isset( $instance['post_excerpt'] ) ? (bool) $instance['post_excerpt'] : false;
		$show_date = isset( $instance['show_date'] ) ? (bool) $instance['show_date'] : false;
		?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e( 'Title:' ); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $title; ?>"/>
		</p>
		<p><label for="<?php echo $this->get_field_id('number'); ?>"><?php _e( 'Number of posts to show:' ); ?></label>
			<input type="number" id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" value="<?php echo $number; ?>" max="30" />
		</p>
		<p><label for="<?php echo $this->get_field_id('type'); ?>"><?php _e( 'Related by:' ); ?></label>
			<select id="<?php echo $this->get_field_id('type'); ?>" name="<?php echo $this->get_field_name('type'); ?>">
				<option value="rand" <?php echo selected( $type, 'rand' ); ?>>Random posts</option>
				<option value="last" <?php echo selected( $type, 'last' ); ?>>Lasted posts</option>
				<option value="hot" <?php echo selected( $type, 'hot' ); ?>>Popular posts</option>
				<option value="term" <?php echo selected( $type, 'term' ); ?>>Posts have same tag</option>
				<option value="tag" <?php echo selected( $type, 'tag' ); ?>>Posts have following tag</option>
			</select>
		</p>
		<p><label for="<?php echo $this->get_field_id( 'tag' ); ?>"><?php _e( 'Tag (optional):' ); ?></label>
			<input type="text" id="<?php echo $this->get_field_id( 'tag' ); ?>" name="<?php echo $this->get_field_name( 'tag' ); ?>" value="<?php echo $tag; ?>"/>
		</p>
		<p><label for="<?php echo $this->get_field_id('cat'); ?>"><?php _e( 'Categories (optional):' ); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'cat' ); ?>" name="<?php echo $this->get_field_name( 'cat' ); ?>" value="<?php echo $cat; ?>"/>
			<span class="components-form-token-field__help"><small>Category IDs, separated by commas or empty for all.</small></span>
		</p>
		<p><label>Display:</label>
			<input class="checkbox" type="checkbox" <?php echo checked( $featured_image ); ?> id="<?php echo $this->get_field_id( 'featured_image' ); ?>" name="<?php echo $this->get_field_name( 'featured_image' ); ?>">
			<label for="<?php echo $this->get_field_id( 'featured_image' ); ?>">Featured image</label>
			<input class="checkbox" type="checkbox" <?php echo checked( $post_excerpt ); ?> id="<?php echo $this->get_field_id( 'post_excerpt' ); ?>" name="<?php echo $this->get_field_name( 'post_excerpt' ); ?>">
			<label for="<?php echo $this->get_field_id( 'post_excerpt' ); ?>">Post excerpt</label>
			<input class="checkbox" type="checkbox" <?php echo checked( $show_date ); ?> id="<?php echo $this->get_field_id( 'show_date' ); ?>" name="<?php echo $this->get_field_name( 'show_date' ); ?>">
			<label for="<?php echo $this->get_field_id( 'show_date' ); ?>">Post date</label>
		</p>
		<?php
	}

	function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        $instance['title'] = sanitize_text_field($new_instance['title']);
        $instance['number'] = (int) $new_instance['number'];
        $instance['type'] = sanitize_text_field($new_instance['type']);
        $instance['tag'] = sanitize_text_field($new_instance['tag']);
        $instance['cat'] = sanitize_text_field($new_instance['cat']);
		$instance['featured_image'] = isset( $new_instance['featured_image'] ) ? (bool) $new_instance['featured_image'] : false;
		$instance['post_excerpt'] = isset( $new_instance['post_excerpt'] ) ? (bool) $new_instance['post_excerpt'] : false;
		$instance['show_date'] = isset( $new_instance['show_date'] ) ? (bool) $new_instance['show_date'] : false;
        return $instance;
	}

	function widget( $args, $instance ) {
		if ( ! isset( $args['widget_id'] ) ) {
			$args['widget_id'] = $this->id;
		}
		$title = apply_filters( 'widget_title', isset( $instance['title'] ) ? $instance['title'] : '', $instance, $this->id_base );
		$number = ( ! empty( $instance['number'] ) ) ? absint( $instance['number'] ) : 5;
		if ( ! $number ) {
			$number = 5;
		}
		$type = isset( $instance['type'] ) ? $instance['type'] : '';
		$tag = isset( $instance['tag'] ) ? $instance['tag'] : '';
		$cat = isset( $instance['cat'] ) ? $instance['cat'] : '';
		$featured_image = isset( $instance['featured_image'] ) ? (bool) $instance['featured_image'] : false;
		$post_excerpt = isset( $instance['post_excerpt'] ) ? (bool) $instance['post_excerpt'] : false;
		$show_date = isset( $instance['show_date'] ) ? (bool) $instance['show_date'] : false;

		$main_post_id = get_queried_object_id();

		if ( ! Vii_Related_Posts::$inused ) {
			Vii_Related_Posts::$inused[] = $main_post_id;
		}

		$opts = array(
			'post_type'           => 'post',
			'post__not_in'		  => Vii_Related_Posts::$inused,
			'posts_per_page'      => $number,
			'no_found_rows'       => true,
			'post_status'         => 'publish',
			'ignore_sticky_posts' => true,
			'orderby'             => 'rand',
		);
		if ( 'last' === $type ) {
			$opts['orderby'] = array( 'date' => 'DESC' );
		} elseif ( 'hot' === $type ) {
			$opts['orderby'] = 'comment_count';
		} elseif ( 'term' === $type ) {
			if ( ! has_tag( '', $main_post_id ) ) {
				return;
			}
			$opts['tag_id'] = get_the_tags( $main_post_id )[0]->term_id;
		} elseif ( 'tag' === $type ) {
			if ( ! $tag ) {
				return;
			}
			$opts['tag'] = $tag;
		}
		if ( $cat ) {
			$opts['cat'] = $cat;
		}
		$r = new WP_Query( $opts );
		if ( ! $r->have_posts() ) {
			return;
		}

        echo $args['before_widget'];
        if ( $title ) {
	        echo $args['before_title'] . $title . $args['after_title'];
        }

		$format = apply_filters( 'navigation_widgets_format', current_theme_supports( 'html5', 'navigation-widgets' ) ? 'html5' : 'xhtml' );
		if ( 'html5' === $format ) {
			$title = trim( strip_tags( $title ) );
			$aria_label = $title ? $title : 'Related posts';
			echo '<nav role="navigation" aria-label="' . esc_attr( $aria_label ) . '">';
		}

		echo '<ul>';
		while( $r->have_posts() ) :
			$r->the_post();
			Vii_Related_Posts::$inused[] = get_the_ID();
			?>
			<li>
				<?php if ( $featured_image && has_post_thumbnail() ) : ?>
					<div class="wp-block-latest-posts__featured-image"><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_post_thumbnail(); ?></a></div>
				<?php endif; ?>
				<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a>
				<?php if ( $show_date ) : ?>
					<span class="wp-block-latest-posts__post-date"><?php the_date(); ?></span>
				<?php endif; ?>
				<?php if ( $post_excerpt ) : ?>
					<span class="wp-block-latest-posts__post-excerpt"><?php the_excerpt(); ?></span>
				<?php endif; ?>
			</li>
			<?php
		endwhile;
		echo '</ul>';
		if ( 'html5' === $format ) {
			echo '</nav>';
		}
		echo $args['after_widget'];

		wp_reset_postdata();
	}
}