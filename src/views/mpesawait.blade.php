
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Proccessing Payment....</title>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
</head>
<body>
<div class="container">
     <div class="row">
      <div class="col s12">
        <center><h4>Please Wait and don't close the browser window or tab</h4></center>
        
        <center>Processing Payment for Checkout ID : <b>{{ $checkoutid }}</b></center>
       <div class="progress">
              <div class="indeterminate"></div>
        </div>
        <h5 class="center-align">Payment Status : <span id="status"></span></h5>
         
      </div>
    </div>
</div>
</body>
<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
<script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
<script>
     var myVar;
     myVar = setInterval(function(){ 
        
        $.ajax({
             
                type : 'GET',
                dataType : 'json',
                url  : '{{ route('verifypay',$checkoutid) }}',
                success : function(data){
                  if(data.resultCode == 1){
                       
                      console.log(data.msg);
                      clearInterval(myVar);
                      $('#status').text(data.msg);
                      setTimeout(function(){ location.href = '/eclassm/public/all/cart'; }, 2500);

                      

                  }
                  else if(data.resultCode == 0){
                      console.log(data);
                      clearInterval(myVar);
                      $('#status').text(data.msg);
                      setTimeout(function(){  location.href = '/eclassm/public/'; }, 2500);
    
                  }else if(data.resultCode == 1032){
                      console.log(data);
                      clearInterval(myVar);
                      $('#status').text(data.msg);
                      setTimeout(function(){  location.href = '/eclassm/public/all/cart'; }, 2500);
                      
                  }else{
                      console.log(data);
                  }
                     
                }
                
            });  
         
     }, 3000);
    

 </script>
</html>