<?php

$bulletCount=0;

if($propInfo->theRemarks['xb1']){
   $bulletCount=$bulletCount+1;
}
if($propInfo->theRemarks['xb2']){
   $bulletCount=$bulletCount+1;
}
if($propInfo->theRemarks['xb3']){
   $bulletCount=$bulletCount+1;
}
if($propInfo->theRemarks['xb4']){
   $bulletCount=$bulletCount+1;
}
if($propInfo->theRemarks['xb5']){
   $bulletCount=$bulletCount+1;
}
if($propInfo->theRemarks['xb6']){
   $bulletCount=$bulletCount+1;
}
if($propInfo->theRemarks['xb7']){
   $bulletCount=$bulletCount+1;
}
if($propInfo->theRemarks['xb8']){
   $bulletCount=$bulletCount+1;
}

if($bulletCount<=4){
   $bullets_LH=2.25;
}else{
   $bullets_LH=1.75;
}

if($bulletCount===7){
   $remHeight=164;
}else{
   $remHeight=160;
}
