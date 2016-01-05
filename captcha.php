<?php
//Captcha Copyrighted http://jmhoule314.blogspot.com/
session_start();
$strlength = rand(4,7);

for($i=1;$i<=$strlength;$i++)
{
$textornumber = rand(1,3);
if($textornumber == 1)
{
$captchastr .= chr(rand(49,57));
}
if($textornumber == 2)
{
$captchastr .= chr(rand(65,78));
}
if($textornumber == 3)
{
$captchastr .= chr(rand(80,90));
}
}
$randcolR = rand(100,230);
$randcolG = rand(100,230);
$randcolB = rand(100,230);

//initialize image $captcha is handle dimensions 200,50
$captcha = imageCreate(200,50);
$backcolor = imageColorAllocate($captcha, $randcolR, $randcolG, $randcolB);

$txtcolor = imageColorAllocate($captcha, ($randcolR - 85), ($randcolG - 85), ($randcolB - 85));
for($i=1;$i<=$strlength;$i++)
{

$clockorcounter = rand(1,2);
if ($clockorcounter == 1)
{
$rotangle = rand(0,45);
}
if ($clockorcounter == 2)
{
$rotangle = rand(315,360);
}

//$i*25 spaces the characters 25 pixels apart
imagettftext($captcha,rand(20,25),$rotangle,($i*25),30,$txtcolor,"./captcha/heiranyslight.ttf",substr($captchastr,($i-1),1));
}
for($i=1; $i<=4;$i++)
{
imageellipse($captcha,rand(1,200),rand(1,50),rand(50,100),rand(12,25),$txtcolor);
}
for($i=1; $i<=4;$i++)
{
imageellipse($captcha,rand(1,200),rand(1,50),rand(50,100),rand(12,25),$backcolor);
}
//Send the headers (at last possible time)
header('Content-Type: image/png');

//Output the image as a PNG
imagePNG($captcha);

//Delete the image from memory
imageDestroy($captcha);

$_SESSION[captchastr] = $captchastr;

?>