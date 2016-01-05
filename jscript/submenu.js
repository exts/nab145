if(document.getElementById||document.all){
var rawr=document.getElementById? document.getElementById("submenu").style : document.all.flylikeabird.style
}
function argh(){
if(parseInt(rawr.left)<0){
rawr.left=parseInt(rawr.left)+20
}else{
rawr.left=0
rawr.fontStyle="normal"
clearInterval(start)
}}
if(document.getElementById||document.all){
start=setInterval("argh()",50)
}