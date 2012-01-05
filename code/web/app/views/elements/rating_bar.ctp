<script type="text/javascript">
 function changeRatingBar(goodRatings,badRatings){
  bar_size = 200;
  totalRatings = goodRatings+badRatings;
  if(totalRatings>0){
   $("#no-ratings").hide();
   if(goodRatings==0){
     $("#bad-ratings").css("border-radius","7px 7px 7px 7px");
   }else{
       $("#bad-ratings").css("border-radius","0 7px 7px 0");
   }
   if(badRatings==0){
       $("#good-ratings").css("border-radius","7px 7px 7px 7px");
   }else{
       $("#good-ratings").css("border-radius","7px 0 0 7px");
   }
   $("#good-ratings").show();
   $("#bad-ratings").show();
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
</script>

<?php
 $icon =  $this->Html->image('/img/icons/thumbs_up.png');
 echo $this->Html->link($icon,"#",array('class'=>"link-button", 'escape' => false,"style"=>"margin-top: 15px;","onclick"=>"return false;"));
?>
<div style="float: left;margin-top: 23px;">
    <div id="good-ratings" style="background-color:green;float: left;width: 140px;border-radius: 7px 0 0 7px;color:#ffffff;">&nbsp;&nbsp;7 </div>
    <div id="bad-ratings"  style="background-color:red;float: left;width: 60px;border-radius: 0 7px 7px 0;color:#ffffff;">&nbsp;3 </div>
    <div id="no-ratings"  style="background-color:#808080;float: left;width: 200px;border-radius: 7px 7px 7px 7px;color:#ffffff;padding-left: 5px;display:none">0 </div>
</div>
<?php
     $icon =  $this->Html->image('/img/icons/thumbs_down.png');
     echo $this->Html->link($icon,"#",array('class'=>"link-button", 'escape' => false,"style"=>"margin-top: 15px;","onclick"=>"return false;"));
?>
