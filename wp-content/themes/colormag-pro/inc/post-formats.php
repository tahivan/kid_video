<?php if ( has_post_format( 'gallery' ) ) : ?>
   <div class="gallery-post-format">
      <?php
      $galleries = get_post_gallery_images( $post );
      $output = '<ul class="gallery-images">';
      foreach ($galleries as $gallery) {
         $output .= '<li>' . '<img src="'. $gallery . '">' . '</li>';
      }
      $output .= '</ul>';
      echo $output;
      ?>
   </div>
<?php endif; ?>
<?php if ( has_post_format( 'video' ) ) : ?>
   <?php $video_post_url = get_post_meta($post->ID, 'video_url', true); ?>
   <?php if ( !empty($video_post_url) ) : ?>
      <div class="fitvids-video">
         <?php
            $embed_code = wp_oembed_get( $video_post_url );
            echo $embed_code;
         ?>
      </div>
   <?php endif; ?>
<?php endif; ?>