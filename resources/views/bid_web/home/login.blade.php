<!doctype html>
<html lang="en">


<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="{{ asset('home/assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <!-- Bootstrap Icon CSS -->
    <link href="{{ asset('home/assets/css/bootstrap-icons.css') }}" rel="stylesheet">
  
    <!--Nice Select CSS -->
    <link rel="stylesheet" href="{{ asset('home/assets/css/nice-select.css') }}">

    <link rel="stylesheet" href="{{ asset('home/assets/css/style.css') }}">
    <!-- Title -->
    <title>Djibah SeaBid Pro</title>
    <link rel="icon" href="{{ asset('home/assets/img/logo.png') }}" type="image/png" sizes="20x20">
</head>

<body id="body">

    

   <div class="position-fixed" style="top:20px; right:5%; z-index:9999">
    <select class="input-field" onchange="changeLanguage(this.value)">
        <option value="en">English</option>
        <option value="ar">Arabic</option>
        <option value="fr">French</option>
    </select>
</div>
    
    <!-- End Breadcrumb section -->
    <section class="register-section py-5 mt-5">

         
        <div class="container">
           
            <div class="row gy-4 justify-content-center">
             
                <div class="col-lg-6" style="z-index: 99999;">

                    <div class="right-form-area glass-card" style="z-index: 99999;">
                          <a href="{{ route('home.index') }}" class="text-white"><i class="bi bi-arrow-left"></i> Back To Home</a>
                       <center> <img src="{{ asset('home/assets/img/djibah-logo.png') }}" width="70" class="mb-2"></center>
                        <h3 class="text-white">Login Your Account</h3>
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                {{ $errors->first() }}
                            </div>
                        @endif
                        <form class="w-100" method="POST" action="{{ route('home.login.store') }}">
                            @csrf
                            <div class="mb-2">
                                <label class="text-white">Login As*</label>
                               
                            
                            <select class="input-field" name="login_as" required>
                                <option value="">
                                    Select Type
                                </option>
                               <option value="buyer" @selected(old('login_as') === 'buyer')>
                                    Buyer
                                </option>
                                
                                <option value="seller" @selected(old('login_as') === 'seller')>
                                    Seller
                                </option>
                                
                            </select>
                         
                            </div>
                            <div class="mb-2">
                                <label class="text-white">Email*</label>
                                <input type="email" name="email" placeholder="Email" class="input-field" value="{{ old('email') }}" required>
                            </div>
                            <div class="mb-2">
                                <label class="text-white">Password *</label>
                                <input type="password" name="password" id="password" placeholder="Password" class="input-field" required>
                                
                            </div>
                            <div class="form-inner d-flex justify-content-between flex-wrap">
                                <div class="form-group">
                                    <input type="checkbox" id="html">
                                    <label for="html" class="text-white">I agree to the Terms &amp; Policy</label>
                                </div>
                              
                            
                            </div>
                            <button class="account-btn">LOGIN ACCOUNT</button>
                          <div class="text-center mt-2 text-white"> If you don't have account, Please <a href="{{ route('home.index') }}"> Register Now</a>
                          </div>
                        </form>
                       
                    </div>
                </div>
            </div>
             <p class="pt-5 small text-center opacity-50">Powered by Djibah Seafood</p>
        </div>
    </section>
    
 

    
     <script src="{{ asset('home/assets/js/jquery-3.7.1.min.js') }}"></script>
    <!-- Popper and Bootstrap JS -->
    <script src="{{ asset('home/assets/js/popper.min.js') }}"></script>
    <script src="{{ asset('home/assets/js/bootstrap.min.js') }}"></script>
 
    <!-- Nice Select  JS -->
    <script src="{{ asset('home/assets/js/jquery.nice-select.min.js') }}"></script>
  
   
    
 

    <script src="{{ asset('home/assets/js/main.js') }}"></script>

</body>


</html>
