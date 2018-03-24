<?php

/*
Plugin Name: Chalk PHP Test Plugin
Description: WordPress Plugin which displays NFL teams grouped by conference and then by division
Version: 1.0
Author: Emily Beauchamp
*/


function wp_chalk_shortcode(){

    //get data from json file
    $url = 'http://delivery.chalk247.com/team_list/NFL.JSON?api_key=74db8efa2a6db279393b433d97c2bc843f8e32b0'; 
    $data = file_get_contents($url); 
    $json = json_decode($data, true);
    
    //drill down to team level
    $teams = $json['results']['data']['team'];   
    
    //create arrays to hold all conferences and divisions (there will be duplicates)
    $confArrayAll = array();
    $divArrayAll = array();
    
    //add conferences and divisions to their respective arrays
    foreach ($teams as $team)
    {
        array_push($confArrayAll, $team['conference']);
        array_push($divArrayAll, $team['division']);
    }
    
    //eliminate duplicate values from arrays and sort
    $confArrayUnique = array_unique($confArrayAll);
    rsort($confArrayUnique);
    $divArrayUnique = array_unique($divArrayAll);
    sort($divArrayUnique);
    
    //display data...
    //for each conference, will display conference, 
    //then the divisions within that conference and then the teams within that division
    foreach($confArrayUnique as $conference)
    {
        echo '<h1>' . $conference . '</h1>';
        
        foreach($divArrayUnique as $division)
        {
            echo '<h3>' . $division . '</h3>';
            
            echo '<ul>';
            foreach ($teams as $team) 
            {
                if ($team['division'] === $division && $team['conference'] === $conference)
                {
                    echo '<li>' . $team['name'] . '</li>';
                }
            }
            echo '</ul>';
        }
        echo '<br><br>';
    }
}

add_shortcode('getchalk', 'wp_chalk_shortcode');
    
?>