<?php
//    if($getrep['fixthumbs'] < 1) {  
//        //display nothing
//    } else if($getrep['fixthumbs'] <= 100) {
//        echo 'Beginner Fixer';
//    } else if($getrep['fixthumbs'] > 100 && $getrep['fixthumbs'] <= 500) {
//        echo 'Intermediate Fixer';
//    } else if($getrep['fixthumbs'] > 500) {
//        echo 'Ultimate Fixer';
//    }
    if($getrep['acctStatus'] == 3) {
        echo 'Admin';
    }
    else if($getrep['fixRep'] < 1) {  
        //display nothing
    } else if($getrep['fixRep'] <= 100) {
        echo 'Beginner Fixer';
    } else if($getrep['fixRep'] > 100 && $getrep['fixRep'] <= 500) {
        echo 'Intermediate Fixer';
    } else if($getrep['fixRep'] > 500) {
        echo 'Ultimate Fixer';
    }
?>