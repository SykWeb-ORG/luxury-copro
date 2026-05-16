<?php
get_header();

if (is_front_page()) {
    get_template_part('front-page');
} else {
    echo '<section style="padding:7rem 5%;min-height:60vh">';
    if (have_posts()) {
        while (have_posts()) {
            the_post();
            the_title('<h1 class="sec-title">', '</h1>');
            echo '<div class="about-inner">';
            the_content();
            echo '</div>';
        }
    }
    echo '</section>';
}

get_footer();
