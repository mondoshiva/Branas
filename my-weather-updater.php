<?php
/*
Plugin Name: My Weather Updater
Description: Fetches and displays updated weather data from "https://www.branas.se/skidakning-i-branas/webbkameror/".
Version: 1.0
Author: Lars Duziack
*/

// Function to fetch and parse the weather data
function fetch_weather_data() {
    // The URL of the weather data to be fetched
    $url = 'https://www.branas.se/skidakning-i-branas/webbkameror/';
    $response = wp_remote_get($url);
    $weather_data = [];

    if (!is_wp_error($response) && $response['response']['code'] == 200) {
        $body = wp_remote_retrieve_body($response);
        preg_match_all('/<div class="weather-day__name">(.*?)<\/div>.*?<div class="weather-day__image"><img src="(.*?)"><\/div>.*?<div class="weather-day__temp-min px1">(.*?)<\/div>.*?<div class="weather-day__temp-max px1">(.*?)<\/div>/s', $body, $matches, PREG_SET_ORDER);
        
        foreach ($matches as $match) {
            $weather_data[] = [
                'day_name' => $match[1],
                'image_url' => $match[2], // Added image URL extraction
                'temp_min' => $match[3],
                'temp_max' => $match[4]
            ];
        }
    }

    return $weather_data;
}

// Function to create the shortcode [my_simple_weather]
function weather_shortcode() {
    $weather_data = fetch_weather_data();
    $output = '<div class="my-weather" style="display: flex; justify-content: space-around; flex-wrap: wrap; gap: 20px;">'; // Updated for responsive wrapping

    if (!empty($weather_data)) {
        foreach ($weather_data as $weather) {
            $output .= '<div class="weather-day" style="text-align: center; padding: 10px;">'; // Individual weather card
            $output .= '<div style="font-weight: bold;">' . esc_html($weather['day_name']) . '</div>'; // Day name in bold
            $output .= '<img src="' . esc_url($weather['image_url']) . '" style="max-width:100px;height:auto;">'; // Weather image
            $output .= '<div>' . esc_html($weather['temp_min']) . '</div>'; // Min temperature
            $output .= '<div>' . esc_html($weather['temp_max']) . '</div>'; // Max temperature
            $output .= '</div>';
        }
    } else {
        $output .= 'Weather data is not available right now.';
    }

    $output .= '</div>';
    return $output;
}

// Register the shortcode with WordPress
add_shortcode('my_simple_weather', 'weather_shortcode');