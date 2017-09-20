<?php
/**
 * Created by PhpStorm.
 * User: vbt101
 * Date: 7/13/15
 * Time: 12:35 PM
 */


$cover_photo = null;

//The types of slides used in the gallery are
// Text
// Media
// Embedded Media

$slideClassName = array(
    'Text' => 'galleryItem--text',
    'Media' => 'galleryItem--media',
    'Embedded Media' => 'galleryItem--embed'
);
$modalID = "tome-gallery-modal-" . get_the_ID();
$galleryCoverPhoto = get_field('gallery_cover_photo');
$galleryCoverPhotoSize = get_field('cover_photo_size');
$galleryTitle = get_the_title();
if ($galleryCoverPhoto) {
    $cover_photo = $galleryCoverPhoto['url'];
}
//        echo sprintf("%s<pre>%s</pre>", $cover_photo, print_r($galleryCoverPhoto, true));
//        echo "Gallery title is ".$galleryTitle;
//        echo "Content is ".print_r($content, true);
?>

<div id="<?php echo $modalID; ?>" class="wrapper tome-gallery-modal">
    <div class="viewer">
        <div class="viewer-inner">

        <!-- Slider goes here -->
            <div class="slide-me">
            <?php

            while (have_rows('gallery_slide')): the_row();

            $galleryItemType = get_sub_field('gallery_item_type');

            // we need to initialize these selectively based on the type. the reason is that if the user re-orders entries, the conditional values that were set at the previous indexes don't seem to get updated.
            // ex: suppose I have a gallery with slide 1 = text, and slide 2 = image. if move slide 2 to slide 1 position, when slide 2 is updated to have the text object, the conditional values will not be set to blank.
            //      that is, the text slide in position 2 will also have the gallery_item_media option set for it.
            $galleryItemCaption = get_sub_field('gallery_item_caption'); // Field present on all items (except Media, but we will take care of that down below in the "if ($galleryItemType == 'Media')" clause)
            $galleryItemMedia = null;
            $galleryItemEmbed = null;
            $galleryItemChapter = null;
            $galleryItemText = null;
            $aspectRatio = 1;
            $tags = array();


            // get title, caption and sub-title
            $title = '';
            $caption = '';
            $subtitle = '';
            $defaultTitle = '<i>No title available</i>';
            $defaultCaption = '<i>No caption available</i>';
            if ($galleryItemType == 'Media') {
                $galleryItemMedia = get_sub_field('gallery_item_media'); //Normal WP Library contents "Media"
                $title =  $galleryItemMedia['title'];
                $caption =  $galleryItemMedia['caption']; //We use the caption & desc stored in the media library
                $subtitle = $galleryItemMedia['description'];
                $galleryItemCaption = null;
                $imageHeight = $galleryItemMedia['sizes']['mega-image-size-height'];
                $aspectRatio = $imageWidth / $imageHeight;
                $tags = get_the_terms($galleryItemMedia['ID'], 'post_tag');
            } elseif ($galleryItemType == 'Embedded Media') {
                $galleryItemEmbed = get_sub_field('gallery_item_embedded_media');
                $title = get_the_title($galleryItemEmbed->ID);
                $caption = get_field('caption', $galleryItemEmbed->ID);
                $subtitle = get_field('description', $galleryItemEmbed->ID);

//                $caption = $galleryItemEmbed->post_title;
//                $subtitle = $galleryItemCaption;
            }


            if (strlen(html2text($caption)) == 0 && strlen(html2text($subtitle)) == 0) {
                // both title and subtitle are empty
                $caption = $defaultCaption;
                $subtitle = '';
            }

            $title = clean_line_breaks($title);
            $caption = clean_line_breaks($caption);
            $subtitle = clean_line_breaks($subtitle);
            if ($aspectRatio >= 1):
                $aspectRatioClass = 'landscape';
            else:
                $aspectRatioClass = 'portrait';
            endif;


                echo sprintf('<div class="tome-slider-slide %s %s" data-slide-type="%s">', $slideClassName[$galleryItemType], $aspectRatioClass, $galleryItemType );
            ?>
            <div class="slide-title hide"><?php echo $title; ?></div>
            <div class="slide-caption hide"><?php echo $caption; ?></div>
            <div class="slide-description hide"><?php echo $subtitle; ?></div>
            <ul class="slide-tags hide">
            <?php
                    foreach ($tags as $tag) {
                        echo sprintf('<li><a href="/media/#%s">%s</a></li>', $tag->slug, $tag->name);
                    }
//                    echo print_r($tags, TRUE);
//                    echo print_r($galleryItemMedia, TRUE);
            ?>
            </ul>


                <?php if ($galleryItemMedia): ?>
                <?php


                if (is_null($cover_photo)) {
                    $cover_photo = $galleryItemMedia['sizes']['mega-image-size'];
                }

                ?>
                <img src="<?php echo $galleryItemMedia['sizes']['mega-image-size']; ?>" />
            <?php endif; ?>

            <?php if ($galleryItemEmbed): ?>
                <?php
                $tomeMediaPostCustomFields = get_post_custom($galleryItemEmbed->ID);
                //                                        echo '<div class="flex-video">' . $tomeMediaPostCustomFields["tome_media_embed_script"][0] . '</div>';
                ?>
            <?php endif; ?>

            <?php // Now we build the text body of the slide
            echo '<div class="galleryItem-body">';

            // If the post is not a text post, add any caption we find

            if ($galleryItemType == 'Embedded Media'):
                echo '<div class="galleryItem-body-content flex-video">';
                $custom_fields = get_post_custom($galleryItemEmbed->ID);
                $tome_media_embed_script = $custom_fields['tome_media_embed_script'][0];
                echo $tome_media_embed_script;
                echo(apply_filters('the_content', $galleryItemEmbed->post_content));
                echo '</div>';
            endif;


            echo "</div></div>";


            endwhile; // end iteration of slides in the gallery
            ?>
            </div> <!-- end div.slide-me -->

        </div>
    </div>

    <div class="sidebar">
        <div class="controls">
        </div>
        <h1 class="title"><?php echo get_the_title(); ?></h1>
        <div class="caption"><p>Click on the image to switch between landscape and horizontal. - Augs, 2015</p></div>
        <div class="description">
            <p>Click on this to add. You can also edit the text in here. Vero eius suscipit excepturi sapiente <a href="#">Just a hyperlink</a> asperiores repellat nam deleniti est assumenda, possimus facere doloribus pariatur atque expedita maxime reprehenderit perspiciatis, nulla architecto!</p>
        </div>
        <ul class="tags">
            <!-- dynamically populated -->
        </ul>
    </div>
    <button class="viewer-close">&times;</button>
</div>

<?
$display_title = (!empty($content) ? $content : $galleryTitle); //'View Gallery');
$display_title = (!empty($display_title) ? $display_title : 'View Gallery');
if ( $cover_photo){
//        echo sprintf('<a style="" href="#" data-reveal-id="%s"><div class="stack"><div class="facecard" style="background-image: url(%s); background-size: cover;"><h2>%s</h2></div></div></a>', $modalID, $cover_photo, $display_title);
echo sprintf('<a style="" href="javascript:;" class="modal-trigger" data-modal-id="%s"><div class="gallery-cover %s" style="height:'.$cover_height.'px"><img src="%s" /><h2>%s</h2></div></a>', $modalID, $galleryCoverPhotoSize, $cover_photo, $display_title);
}
else{
echo sprintf('<a style="" href="javascript:;" class="modal-trigger" data-modal-id="%s">%s</a>', $modalID, $display_title);
}

echo '<div class="reveal-modal-bg" style="background-color: rgba(0, 0, 0, 0.75);"></div>';
?>

