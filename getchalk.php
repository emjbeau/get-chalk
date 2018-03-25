<?php

/*
Plugin Name:    Chalk PHP Test Plugin
Description:    WordPress Plugin which displays NFL teams grouped by conference and then by division
Version:        1.0
Author:         Emily Beauchamp
*/


function wp_chalk_shortcode(){

    echo '<style>';
        include 'chalk.css';
    echo '</style>';    
    
    echo '<body>';
    
    echo '<h1 id="title">NFL TEAMS BY CONFERENCE AND DIVISION</h1>';

    echo '<div id="buttonGroup">';
        echo '<button id="btnDiv" onclick="filterTeamsByDiv()">DIVISION</button>';
        echo '<button id="btnConf" onclick="filterTeamsByConf()">CONFERENCE</button>';
    echo '</div>';     

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
    echo '<div id="teamsByDiv">';
    foreach($confArrayUnique as $conference)
    {
        echo '<div class="conference" id="confWithDivs">';
        echo '<h1>' . $conference . '</h1>';
        
        foreach($divArrayUnique as $division)
        {
            echo '<div class="division">';
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
            echo '</div>';
        }
        echo '</div>';
    }
    echo '</div>';
   
    //display data...
    //for each conference, will display conference and then the teams within that conference
    echo '<div id="teamsByConf" hidden=true>';
    foreach($confArrayUnique as $conference)
    {
        echo '<div class="conference" id="confNoDivs">';
        echo '<h1>' . $conference . '</h1>';
        
        echo '<ul>';
        foreach ($teams as $team) 
        {
            if ($team['conference'] === $conference)
            {
                echo '<li>' . $team['name'] . '</li>';
            }
        }
        echo '</ul>';        
        echo '</div>';
    }
    echo '</div>';
    
    echo '<input type="text" id="searchName">';
    
    echo '</body>';
}

add_shortcode('getchalk', 'wp_chalk_shortcode');
    
?>

<script>
function filterTeamsByDiv() {
    var d = document.getElementById("teamsByDiv");
    var c = document.getElementById("teamsByConf");
    
    d.style.display = "block";
    c.style.display = "none";
}

function filterTeamsByConf() {
    var c = document.getElementById("teamsByConf");
    var d = document.getElementById("teamsByDiv");
    
    c.style.display = "block";
    d.style.display = "none";
}
</script>