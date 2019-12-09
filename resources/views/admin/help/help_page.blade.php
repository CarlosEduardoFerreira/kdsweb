@extends('admin.layouts.admin')

@section('title', "FAQ")

@section('content')
    
    

    
 
    <div class='form-group'>
               
<br>
<br>
    <p><a class="create" data-toggle="collapse" href="#footwear" aria-expanded="false" aria-controls="footwear" style="margin-top:40px">How do I create my first store?</a>
    
</p>
<div class="collapse" id="footwear" style="margin-left:20px">
<p style="margin-bottom:20px">Resellers and store groups can create stores; however, it needs to follow this hierarchy. Resellers needs to create a store group, follow by a store.
<br>
<img src="/images/explanation.png" width="500px"></p>
</div>
</div>


<p><a class="create" data-toggle="collapse" href="#fooear" aria-expanded="false" aria-controls="fooear" >How do I change information in my account? Name, address, email and password</a>
</p>

<div class="collapse"  id="fooear" style="margin-left:20px">
<p style="margin-bottom:20px">All this information can be modified from the site <a href="https://kdsgo.com/">KitchenGO</a>. When logged in go to top right of the screen and click the name given to your business and go to profile. In the profile section you can change Name, business name, email, time zone, username and password.  
</div>


<p><a class="create" data-toggle="collapse" href="#foar" aria-expanded="false" aria-controls="foar" >What is in my dashboard?</a>
</p>

<div class="collapse" id="foar" style="margin-left:20px">
<p style="margin-bottom:20px">The dashboard provides a report to show how many orders are received during a given time frame, this time frame can be changed. 
</div>


<p><a class="create" data-toggle="collapse" href="#fot" aria-expanded="false" aria-controls="fot" >What is my server information</a>
</p>

<div class="collapse" id="fot" style="margin-left:20px">
<p style="margin-bottom:20px">Server information is usually what the store uses to share files, this will all depend on POS settings. This is only available to change on KitchenGo Allee. When this information is changed, it will be changed on all Allee stations. 
</div>


<p><a class="create" data-toggle="collapse" href="#fog" aria-expanded="false" aria-controls="fog" >What is Automatic Clear Order?</a>
</p>

<div class="collapse" id="fog" style="margin-left:20px">
<p style="margin-bottom:20px">If the user would like to use the feature, this will only be available for Allee users, it allows the user to clear all orders on the screen, after hours or every other hour. 
</div>


<p><a class="create" data-toggle="collapse" href="#foggy" aria-expanded="false" aria-controls="foggy" >How can I add more devices?</a>
</p>

<div class="collapse" id="foggy" style="margin-left:20px">
<p style="margin-bottom:20px">If you are using Allee or Premium, you will be adding more devices at any time adding more licenses to your account. Log in to your account on <a href="https://kdsgo.com/">KitchenGO</a>, and go to General Settings and then Licenses, finally add licenses as desired.  
</div>

<p><a class="create" data-toggle="collapse" href="#fet" aria-expanded="false" aria-controls="fet" >KDS License Deactivated</a>
</p>

<div class="collapse" id="fet" style="margin-left:20px">
<p style="margin-bottom:20px">If you are working for more than 5 days offline, licenses would be deactivated, check internet connection and make sure you have internet service.  
</div>


<p><a class="create" data-toggle="collapse" href="#fete" aria-expanded="false" aria-controls="fete" >How can I enable my SMS?</a>
</p>

<div class="collapse" id="fete" style="margin-left:20px">
<p style="margin-bottom:20px">If you are using Allee or Premium, you need to create a Twillio account. Twillio is located in the Marketplace if you are logged in <a href="https://kdsgo.com/">KitchenGO</a> , you need to create an account on twillio and fill the following information from Twillio once the account is created; Account SSID, Token, Phone From.  
</div>

<p><a class="create" data-toggle="collapse" href="#map" aria-expanded="false" aria-controls="map" >I removed my device by mistake</a>
</p>

<div class="collapse" id="map" style="margin-left:20px">
<p style="margin-bottom:20px">If you removed or delete your device by accident, your device may log out and you will need to log in again. If you are using  Kitchengo premium you will need to enter KDS station number. If you are using Allee, the iPad will take the first KDS station available.  
</div>




@endsection

@section('styles')
    @parent
    {{ Html::style(mix('assets/admin/css/users/edit.css')) }}
    {{ Html::style(mix('assets/admin/css/bootstrap-select.css')) }}
    <style>
    .create{
      hover {background: #eee;}
      margin-top:900px;
      margin-bottom:80px;
    }
    .create:hover {background: #eee;}

    .collasped{
      margin-top:900px;
      margin-bottom:800px;
    }
        .h3{
          margin-top:30px;
          position: absolute;
  top: 50px;
  padding-left: 460px;
        }
    </style>
@endsection

@section('scripts')
    @parent
    {{ Html::script(mix('assets/admin/js/users/edit.js')) }}
    {{ Html::script(mix('assets/admin/js/location.js')) }}
    {{ Html::script(mix('assets/admin/js/validation.js')) }}
    {{ Html::script(mix('assets/admin/js/bootstrap-select.min.js')) }}
    {{ Html::script(mix('assets/admin/js/jquery.mask.js')) }}
    {{ Html::script(mix('assets/admin/js/firebase-api.js')) }}
    {{ Html::script(mix('assets/admin/js/ModalDelete.js')) }}

    <script>
        

        function goBack() {
            window.history.back();
        }

        
      
    </script>
@endsection
