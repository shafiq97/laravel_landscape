<?php
if (!function_exists('generate_star_rating')) {
    function generate_star_rating($rating)
    {
        $stars = '';

        // Calculate the number of full stars
        $full_stars = floor($rating);

        // Calculate the number of half stars
        $half_stars = ceil($rating - $full_stars);

        // Generate the full stars
        for ($i = 1; $i <= $full_stars; $i++) {
            $stars .= '<i class="fas fa-star text-warning"></i>';
        }

        // Generate the half stars
        for ($i = 1; $i <= $half_stars; $i++) {
            $stars .= '<i class="fas fa-star-half-alt text-warning"></i>';
        }

        // Generate the empty stars
        for ($i = 1; $i <= 5 - $full_stars - $half_stars; $i++) {
            $stars .= '<i class="far fa-star"></i>';
        }

        return $stars;
    }
}

?>
