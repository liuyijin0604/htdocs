
<!DOCTYPE html>
<html>
<head>
  <style type="text/css">
    ul.vertical-navbar {
      list-style-type: none;
      margin: 0;
      padding: 0;
      width: 100%;
      background-color: #f1f1f1;
    }

    li {
    display: block;
    color: #000;
    padding: 8px 16px;
    text-decoration: none;
    }
   
    /* color change on hover */
    li:hover {
      background-color: #0099ff;
      color: #fff;
    }

    .leftpanel {
      position: absolute;
      left: 0px;
      width: 20%;
      display: inline-block;
    }

    .rightpanel {
      position: absolute;
      right: 0px;
      width: 80%;
      display: inline-block;
    }
    .main{
      width: :100%;
    }
    .menu {
      margin-top: 10%;
      width: 100%;
    }

  </style>
	<meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <!-- jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <!-- reCaptcha  -->
    <!-- <script src="https://www.google.com/recaptcha/api.js"></script> -->
    <!-- Custom CSS -->
    
    <title>New Customer Page</title>
</head>

<body>
<div id="sysloading"></div>
<!-- Message Dialog -->
<div id="appmsg" class="jqmDialog jqmMsg">
  <div class="appmsgwin">
    <div class="msgIcon"></div>
    <div class="msgContent"></div>
    <!-- <div>
      <input type="submit" value="OK" class="msgOk" />
      <input type="submit" value="NO" class="msgNo" />
      <input type="submit" value="YES" class="msgYes" />
    </div> -->
  </div>
</div>

<div class="main">
  <div class="leftpanel" >
    <div class="menu panel">
		<ul class="vertical-navbar">
		  <li id="show-email">Communication Email</li>
		</ul>
    </div>
  </div>
  <div class="rightpanel panel" >
    <div id="tabs"></div>
    <div id="tab-strip-spacer"></div>
    <iframe id="content" style="width:100%;height:100em;">      
    </iframe>
  </div>
</div>

<script>

$('.vertical-navbar li#show-email').on('click', function(){
    // $.get('phpfunc.php', { menu: this.id }, function(data){
    //     $('body').append(data); //do something with whatever data is returned
    // });

    let submit_id = <?php echo $submisson_id; ?>;     
    <?php $url = $this->createUrl('salesfunnelCustomerLogin/checkEmails');?>
    $.ajax({
             url: '<?=$url?>'+"?submit_id="+submit_id,
             type: "post",
             processData: false,
             contentType: false,             
             success: function(r) {
              //$('#content').remove();
              var response = JSON.parse(r);              
              if(!response.done)
              {
                console.log(response);
                alert(response.msg);                
              }
              else
              {
                let id = <?php echo $submisson_id ?>;
                $('#content').attr("src","<?=$this->createUrl('salesfunnelCustomerLogin/showCommunicationEmail')?>"+"?submit_id="+id);                
              }

           },
             error: function(e) {
                 console.log(e);
             }
         });

    });

// $(document).on('click','a.tab_link',function(evt, bg){
//     if(evt.which == 2){
//       fevt.preventDefault();
//       bg = true;
//     }
//     var l = $(this) .attr('href');
//     app.tabs .CreateTab({
//       title: $(this).attr('title'),
//       url: l,
//       bg: bg || false   
//     });
//     return false;
// });

</script>
</body>
</html>

