#!/usr/bin/php
<?php

class Reviewer{

    private $name;
    private $dates = array();
    private $devices = array();
    private $average;
    private $reviews;
    private $iScore = 100;

    public function processReview($review){
        $reviewArray = explode(',', $review);
        
        //check if review is valid -- Guard clause
        if(count($reviewArray) < 6){
            return 'Could not read review summary data';
        }
        // check if account is deactivated
        if($this->iScore < 50){
            return 'Alert:'.  $this->name.' has been de-activated due to a low trusted review score';;
        }
        //set name for first review
        if(empty($this->name)){
            $this->name = $reviewArray[1];
        }
    
        $percentChange = 0;

        $percentChange += $this->checkSolicited($reviewArray[2]);
        $percentChange += $this->checkTimestamp($reviewArray[0]);
        $percentChange += $this->checkWordCount($reviewArray[4]);
        $percentChange += $this->checkDevice($reviewArray[3]);
        $percentChange += $this->checkRating($reviewArray[5]);


        $this->iScore += $percentChange;

        //nothing above 100%
        if($this->iScore > 100){
            $this->iScore = 100;
        }

        
        if($this->iScore >= 70){
            $return = 'Info:'.   $this->name.' has a trusted review score of '.$this->iScore;
        }
        else if ($this->iScore >= 50){
            $return = 'Warning:'.$this->name.' has a trusted review score of '.$this->iScore;
        }
        else{
            $return = 'Alert:'.  $this->name.' has been de-activated due to a low trusted review score';
        }

        return $return;
    }

    private function checkSolicited($solicited){

        $return = 0;

        if($solicited == 'solicited'){
            $return = 3;
        }

        return $return;
    }

    private function checkWordCount($words){

        $return = 0;
        $wordCount = str_replace('words', '', $words);

        if(intval($wordCount) > 100){
            $return = -0.5;
        }

        return $return;
    }

    private function checkTimestamp($dateTime){

        //New Review Timestamp
        $timestamp = date_timestamp_get(DateTime::createFromFormat("dS M H:i",  $dateTime));

        //guard clause. return if no dates as this will be the first review being entered
        if(empty($this->dates)){
            $this->dates[$timestamp] = 1;
            return 0;
        }

        $return = 0;

        if(!empty($this->dates[$timestamp])){

            $this->dates[$timestamp] += 1;

            if($this->dates[$timestamp] == 2){
                $return = -40;
            }

        }else{

            //number of reviews within the same hour. if 2 then retrun -20% if more or less then return 0%
            $numWithinHour=0;
            foreach(array_keys($this->dates) as $date){
                if($date < ($timestamp + 3600) and $date > ($timestamp - 3600)){
                    $numWithinHour += 1;
                }
                if($numWithinHour >= 2){
                    break;
                }
            }

            if($numWithinHour == 1){
                $return = -20;
            }
            $this->dates[$timestamp] = 1;

        }
        return $return;
    }

    private function checkDevice($device){

        //guard clause. return if no dates as this will be the first review being entered
        if(empty($this->devices)){
            $this->devices[$device] = true;
            return 0;
        }

        $return = 0;

        if(!empty($this->devices[$device])){
            $return = -30;    
        }

        $this->devices[$device] = true;

        return $return;
    }

    private function checkRating($rating){

        $return = 0;
        $rating = strlen($rating);
        
        if($rating == 5){
            $return = 2;

            if(!empty($this->average)){
                if($this->average < 3.5){
                    $return = $return * 4;
                }
            }
        }
     
        //set new average/review count
        if(!empty($this->average)){
            $this->average = (($this->average * $this->reviews) + $rating) / ($this->reviews + 1);
            $this->reviews += 1;
        }
        else{ //set first average/count if first review
            $this->average = $rating;
            $this->reviews = 1;
        }

        return -$return;
    }

}

//Test Input
$inputReviews = array(
    '12th July 12:04,Jon,solicited,LB3 TYU,50words,*****',
    '12th July 12:05,Jon,unsolicited,KB3 IKU,20words,**',
    '13th July 15:04,Jon,unsolicited,CY8 IPK,150words,***',
    '15th July 10:04,Jon,solicited,BB4 IPK,40words,*****',
    '15th July 15:09,Jon,monkey',
    '29th August 10:04,Jon,solicited,LX2 IPK,70words,****',
    '2nd September 10:04,Jon,solicited,KB3 IKU,50words,****',
    '2nd September 10:04,Jon,solicited,AN9 IPK,90words,**',
);

$reviewer = new Reviewer;

//itterate through each review
foreach($inputReviews as $review){
    $newScore =  $reviewer->processReview($review);   
    echo $newScore ."\n";
}




?>