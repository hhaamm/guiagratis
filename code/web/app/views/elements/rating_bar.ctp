<?php
/*
 * Guia Gratis, sistema para intercambio de regalos.
 * Copyright (C) 2011  Fabian Fiorotto
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */
?>
<script type="text/javascript">
 function changeRatingBar(goodRatings,badRatings){
  bar_size = 200;
  totalRatings = goodRatings+badRatings;
  if(totalRatings>0){
   $("#no-ratings").hide();
   if(goodRatings==0){
     $("#good-ratings").hide();
     $("#bad-ratings").css("border-radius","7px 7px 7px 7px");
   }else{
       $("#good-ratings").show();
       $("#bad-ratings").css("border-radius","0 7px 7px 0");
   }
   if(badRatings==0){
        $("#bad-ratings").hide();
       $("#good-ratings").css("border-radius","7px 7px 7px 7px");
   }else{
       $("#bad-ratings").show();
       $("#good-ratings").css("border-radius","7px 0 0 7px");
   }
   $("#good-ratings").css("width",Math.floor( goodRatings * bar_size / totalRatings )+"px");
   $("#good-ratings").attr("title",goodRatings);
   $("#good-ratings").html("&nbsp;"+goodRatings);
   $("#bad-ratings").css("width",Math.floor( badRatings * bar_size / totalRatings )+"px");
   $("#bad-ratings").html("&nbsp;"+badRatings);
   $("#good-ratings").attr("title",goodRatings)
  }else{
   $("#good-ratings").hide();
   $("#bad-ratings").hide();
   $("#no-ratings").show();
  }
 }

 function toggleLoader(icon){
   if($("#"+icon+"_img").is(':visible')){
       $("#"+icon+"_img").hide();
       $("#"+icon+"_loader").show();
   }else{
       $("#"+icon+"_img").show();
       $("#"+icon+"_loader").hide();
   }
 }
</script>

<?php
 $icon =  $this->Html->image('/img/icons/thumbs_up.png',array('id'=>'thumb_up_img','style'=>'display:block'));
 $loader = $this->Html->image('/img/ajax-loader.gif',array('id'=>'thumb_up_loader','style'=>'display:none'));
 echo $this->Html->link($icon.$loader,"#",array('class'=>"link-button", 'escape' => false,"style"=>"margin-top: 15px;","id"=>"thumb-up"));
?>
<div style="float: left;margin-top: 23px;">
    <div id="good-ratings" style="background-color:green;float: left;width: 140px;border-radius: 7px 0 0 7px;color:#ffffff;">&nbsp;&nbsp;7 </div>
    <div id="bad-ratings"  style="background-color:red;float: left;width: 60px;border-radius: 0 7px 7px 0;color:#ffffff;">&nbsp;3 </div>
    <div id="no-ratings"  style="background-color:#808080;float: left;width: 200px;border-radius: 7px 7px 7px 7px;color:#ffffff;padding-left: 5px;display:none">0 </div>
</div>
<?php
     $icon =  $this->Html->image('/img/icons/thumbs_down.png',array('id'=>'thumb_down_img','style'=>'display:block'));
     $loader = $this->Html->image('/img/ajax-loader.gif',array('id'=>'thumb_down_loader','style'=>'display:none'));
     echo $this->Html->link($icon.$loader,"#",array('class'=>"link-button", 'escape' => false,"style"=>"margin-top: 15px;","id"=>"thumb-down"));
?>
