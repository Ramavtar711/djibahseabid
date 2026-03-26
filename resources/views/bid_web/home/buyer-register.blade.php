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

	<style>

	

	

	/* PROGRESS BAR */

	.progress-container{
	display:flex;
	justify-content:space-between;
	position:relative;
	margin-bottom:40px;
	}

	.progress-container::before{
	content:"";
	position:absolute;
	top:15px;
	left:0;
	width:100%;
	height:4px;
	background:#334155;
	z-index:1;
	}

	.progress-bar-step{
	position:absolute;
	top:15px;
	left:0;
	height:4px;
	background:#38bdf8;
	z-index:2;
	width:0%;
	transition:.4s;
	}

	.step{
	position:relative;
	z-index:3;
	text-align:center;
	width:25%;
	}

	.step-circle{
	width:34px;
	height:34px;
	background:#334155;
	border-radius:50%;
	display:flex;
	align-items:center;
	justify-content:center;
	margin:auto;
	font-size:14px;
	}

	.step.active .step-circle{
	background:#38bdf8;
	}

	.step.completed .step-circle{
	background:#22c55e;
	}

	.step-label{
	font-size:13px;
	margin-top:6px;
	color:#fff;
	font-weight:bold;
	}

	/* FORM */

	.form-step{
	display:none;
	animation:fade .3s ease;
	}

	.form-step.active{
	display:block;
	}

	@keyframes fade{
	from{opacity:0;transform:translateX(10px)}
	to{opacity:1;transform:translateX(0)}
	}

	.business-card{
	border: 2px solid #ffffff1a !important;
	padding:14px;
	border-radius:8px;
	color:#fff;
	    width: 100%;
	cursor:pointer;
	transition:.2s;
	}

	.business-card:hover{
	border-color:#38bdf8;
	}

	.badge-sec{
	background:#0f172a;
	padding:6px 10px;
	border-radius:6px;
	font-size:12px;
	margin-right:5px;
	}

	</style>
</head>
<body>

	<div class="position-fixed" style="top:20px; right:5%; z-index:9999">
    <select class="input-field" onchange="changeLanguage(this.value)">
        <option value="en">English</option>
        <option value="ar">Arabic</option>
        <option value="fr">French</option>
    </select>
</div>
<section class="hero-section text-white d-flex align-items-center">

	<div class="container mt-5 mb-5 pb-5" style="z-index: 99999;">
		<div class="row gy-4 justify-content-center">
		  <div class="col-lg-9">
		<div class="right-form-area glass-card">
			 <a href="{{ route('home.index') }}" class="text-white"><i class="bi bi-arrow-left"></i> Back To Home</a>
                       <center> <img src="{{ asset('home/assets/img/djibah-logo.png') }}" width="70" class="mb-2"></center>
			<h3 class="mb-4 text-white">Create Buyer Account</h3><!-- PROGRESS -->
			<div class="progress-container">
				<div class="progress-bar-step" id="progress"></div>
				<div class="step active">
					<div class="step-circle">
						1
					</div>
					<div class="step-label">
						Personal
					</div>
				</div>
				<div class="step">
					<div class="step-circle">
						2
					</div>
					<div class="step-label">
						Company
					</div>
				</div>
				<div class="step">
					<div class="step-circle">
						3
					</div>
					<div class="step-label">
						Business
					</div>
				</div>
				<div class="step">
					<div class="step-circle">
						4
					</div>
					<div class="step-label">
						KYC
					</div>
				</div>
			</div>
			@if (session('success'))
				<div class="alert alert-success">{{ session('success') }}</div>
			@endif
			@if ($errors->any())
				<div class="alert alert-danger">
					{{ $errors->first() }}
				</div>
			@endif
			<form method="POST" action="{{ route('home.buyer-register.store') }}" enctype="multipart/form-data">
				@csrf
				<!-- STEP 1 -->
				<div class="form-step active">
					<h5>Personal Info</h5>
					<div class="row g-3 mt-2">
						<div class="col-md-6">
							<label>Full Name *</label> <input class="input-field" name="name" value="{{ old('name') }}" required>
						</div>
						<div class="col-md-6">
							<label>Job Title</label> <input class="input-field" name="job_title" value="{{ old('job_title') }}">
						</div>
						<div class="col-md-6">
							<label>Email *</label> <input class="input-field" name="email" type="email" value="{{ old('email') }}" required>
						</div>
						<div class="col-md-6">
							<label>Phone (WhatsApp) *</label> <input class="input-field" name="phone" value="{{ old('phone') }}" required>
						</div>
						<div class="col-md-6">
							<label>Password *</label> <input class="input-field" name="password" type="password" required>
						</div>
						<div class="col-md-6">
							<label>Confirm Password *</label> <input class="input-field" name="password_confirmation" type="password" required>
						</div>
					</div>
					
				</div><!-- STEP 2 -->
				<div class="form-step">
					<h5>Company Info</h5>
					<div class="row g-3 mt-3">
						<div class="col-md-6">
							<label>Company Legal Name *</label> <input class="input-field" name="company_legal_name" value="{{ old('company_legal_name') }}" required>
						</div>
						<div class="col-md-6">
							<label>City *</label> <input class="input-field" name="city" value="{{ old('city') }}">
						</div>
						<div class="col-md-12">
							<label>Business Address *</label> <input class="input-field" name="business_address" value="{{ old('business_address') }}">
						</div>
						<div class="col-md-6">
							<label>Country *</label> <select class="input-field" name="country">
								<option value="">
									Select Country
								</option>
								<option value="France" @selected(old('country') === 'France')>
									France
								</option>
								<option value="Spain" @selected(old('country') === 'Spain')>
									Spain
								</option>
								<option value="Italy" @selected(old('country') === 'Italy')>
									Italy
								</option>
								<option value="India" @selected(old('country') === 'India')>
									India
								</option>
							</select>
						</div>
						<div class="col-md-6">
							<label>Website</label> <input class="input-field" name="website" value="{{ old('website') }}">
						</div>
						<div class="col-md-6">
							<label>Company Registration Number</label> <input class="input-field" name="company_registration_number" value="{{ old('company_registration_number') }}">
						</div>
					</div>
				</div><!-- STEP 3 -->
				<div class="form-step">
					<h5>Business Type</h5>
					<div class="row g-3 mt-2">
						<div class="col-md-3">
							<label class="business-card"><input type="checkbox" name="business_type[]" value="Hotels" @checked(in_array('Hotels', old('business_type', [])))> Hotels</label>
						</div>
						<div class="col-md-3">
							<label class="business-card"><input type="checkbox" name="business_type[]" value="Restaurants" @checked(in_array('Restaurants', old('business_type', [])))> Restaurants</label>
						</div>
						<div class="col-md-3">
							<label class="business-card"><input type="checkbox" name="business_type[]" value="Supermarkets" @checked(in_array('Supermarkets', old('business_type', [])))> Supermarkets</label>
						</div>
						<div class="col-md-3">
							<label class="business-card"><input type="checkbox" name="business_type[]" value="Catering" @checked(in_array('Catering', old('business_type', [])))> Catering</label>
						</div>
						<div class="col-md-3">
							<label class="business-card"><input type="checkbox" name="business_type[]" value="Bulk Importer" @checked(in_array('Bulk Importer', old('business_type', [])))> Bulk Importer</label>
						</div>
						<div class="col-md-3">
							<label class="business-card"><input type="checkbox" name="business_type[]" value="Distributor" @checked(in_array('Distributor', old('business_type', [])))> Distributor</label>
						</div>
						<div class="col-md-3">
							<label class="business-card"><input type="checkbox" name="business_type[]" value="Reseller" @checked(in_array('Reseller', old('business_type', [])))> Reseller</label>
						</div>
						<div class="col-md-3">
							<label class="business-card"><input type="checkbox" name="business_type[]" value="Processing Company" @checked(in_array('Processing Company', old('business_type', [])))> Processing Company</label>
						</div>
					</div>
					<div id="businessTypeError" class="text-danger small mt-2 d-none"></div>
					<hr class="my-4">
					<h5>Preferences</h5>
					<div class="row g-3">
						<div class="col-md-4">
							<label>Interested In</label><br>
							<input type="checkbox" name="interested_in[]" value="Fresh" @checked(in_array('Fresh', old('interested_in', [])))> Fresh
							<input class="ms-2" type="checkbox" name="interested_in[]" value="Frozen" @checked(in_array('Frozen', old('interested_in', [])))> Frozen
							<input class="ms-2" type="checkbox" name="interested_in[]" value="Both" @checked(in_array('Both', old('interested_in', [])))> Both
						</div>
						<div class="col-md-4">
							<label>Monthly Volume</label> <select class="input-field" name="monthly_volume">
								<option value="">
									Select Monthly Volume
								</option>
								<option value="100 - 500 kg" @selected(old('monthly_volume') === '100 - 500 kg')>
									100 - 500 kg
								</option>
								<option value="500 - 1000 kg" @selected(old('monthly_volume') === '500 - 1000 kg')>
									500 - 1000 kg
								</option>
								<option value="1000 - 5000 kg" @selected(old('monthly_volume') === '1000 - 5000 kg')>
									1000 - 5000 kg
								</option>
								<option value="5000+ kg" @selected(old('monthly_volume') === '5000+ kg')>
									5000+ kg
								</option>
							</select>
						</div>
						<div class="col-md-4">
							<label>Preferred Delivery</label><br>
							<input type="checkbox" name="preferred_delivery[]" value="Air" @checked(in_array('Air', old('preferred_delivery', [])))> Air
							<input class="ms-2" type="checkbox" name="preferred_delivery[]" value="Sea" @checked(in_array('Sea', old('preferred_delivery', [])))> Sea
							<input class="ms-2" type="checkbox" name="preferred_delivery[]" value="Local Pickup" @checked(in_array('Local Pickup', old('preferred_delivery', [])))> Local Pickup
						</div>
					</div>
				</div><!-- STEP 4 -->
				<div class="form-step">
					<h5>KYC & Payment</h5>
					<div class="row g-3">
						<div class="col-md-12">
							<label>Preferred Payment</label><br>
							<input type="checkbox" name="preferred_payment[]" value="Bank Transfer" @checked(in_array('Bank Transfer', old('preferred_payment', [])))> Bank Transfer
							<input class="ms-2" type="checkbox" name="preferred_payment[]" value="Online Payment" @checked(in_array('Online Payment', old('preferred_payment', [])))> Online Payment
							<input class="ms-2" type="checkbox" name="preferred_payment[]" value="LC" @checked(in_array('LC', old('preferred_payment', [])))> LC
						</div>
						<div class="col-md-6">
							<label>Bank Country</label> <input class="input-field" name="bank_country" value="{{ old('bank_country') }}">
						</div>
						<div class="col-md-6">
							<label>Upload Company Registration *</label> <input class="input-field" type="file" name="company_registration_file">
						</div>
						<div class="col-md-6">
							<label>Upload ID</label> <input class="input-field" type="file" name="id_file">
						</div>
						<div class="col-md-6">
							<label>Import License</label> <input class="input-field" type="file" name="import_license_file">
						</div>
					</div>
					<hr class="my-4">
					<div>
						<input type="checkbox" name="is_registered_business" value="1" @checked(old('is_registered_business'))> I confirm this is a registered business<br>
						<input type="checkbox" name="accepted_terms" value="1" @checked(old('accepted_terms'))> I accept Terms & Auction Rules<br>
						<input type="checkbox" name="bank_transfer_validated" value="1" @checked(old('bank_transfer_validated'))> Bank transfers validated by Admin
					</div>
				</div><!-- BUTTONS -->
				<div id="stepError" class="alert alert-danger d-none mt-3 mb-0"></div>
				<div class="d-flex justify-content-between mt-4">
					<button class="btn btn-secondary" id="prevBtn" type="button">Previous</button> <button class="btn btn-primary" id="nextBtn" type="button">Next</button> <button class="btn btn-success d-none py-3" id="submitBtn" type="submit">Submit for Approval</button>
				</div>
			</form>
		</div>
	</div>
	</div>
	<div class="text-center mt-4 row justify-content-center ">
           <div class="col-md-4"><div class="glass-card py-2 text-white"> Already have an account? <a class="text-info" href="{{ route('home.login') }}">Login</a></div>
          
           </div>

        <p class="mt-3 small opacity-50 mb-5">Powered by Djibah Seafood</p>
    </div>
	</div>
	<script>

	let currentStep=0

	const steps=document.querySelectorAll(".form-step")
	const stepIcons=document.querySelectorAll(".step")
	const progress=document.getElementById("progress")

	const next=document.getElementById("nextBtn")
	const prev=document.getElementById("prevBtn")
	const submit=document.getElementById("submitBtn")
	const stepError=document.getElementById("stepError")
	const businessTypeError=document.getElementById("businessTypeError")

	function showStepError(message){
	stepError.textContent=message
	stepError.classList.remove("d-none")
	}

	function clearStepError(){
	stepError.textContent=""
	stepError.classList.add("d-none")
	}

	function setFieldError(field,message){
	field.classList.add("is-invalid")
	let errorEl=field.parentElement.querySelector(".field-error")
	if(!errorEl){
	errorEl=document.createElement("div")
	errorEl.className="text-danger small mt-1 field-error"
	field.parentElement.appendChild(errorEl)
	}
	errorEl.textContent=message
	}

	function clearFieldError(field){
	field.classList.remove("is-invalid")
	const errorEl=field.parentElement.querySelector(".field-error")
	if(errorEl){
	errorEl.remove()
	}
	}

	function clearBusinessTypeError(){
	businessTypeError.textContent=""
	businessTypeError.classList.add("d-none")
	}

	function showBusinessTypeError(message){
	businessTypeError.textContent=message
	businessTypeError.classList.remove("d-none")
	}

	function validateCurrentStep(){
	clearStepError()
	clearBusinessTypeError()

	const requiredFields=steps[currentStep].querySelectorAll("[required]")

	for(const field of requiredFields){
	clearFieldError(field)
	if(!field.value || !field.checkValidity()){
	setFieldError(field,"This field is required.")
	showStepError("Please fix the highlighted fields.")
	field.focus()
	return false
	}
	}

	if(currentStep===0){
	const password=document.querySelector('input[name="password"]')
	const confirmPassword=document.querySelector('input[name="password_confirmation"]')
	if(password && confirmPassword && password.value!==confirmPassword.value){
	clearFieldError(confirmPassword)
	setFieldError(confirmPassword,"Confirm Password must match Password.")
	showStepError("Password and Confirm Password must match.")
	confirmPassword.focus()
	return false
	}
	}

	if(currentStep===2){
	const businessTypes=document.querySelectorAll('input[name="business_type[]"]:checked')
	if(businessTypes.length===0){
	showBusinessTypeError("Please select at least one Business Type.")
	showStepError("Please select at least one Business Type.")
	return false
	}
	}

	return true
	}

	function updateForm(){

	steps.forEach((step,i)=>{
	step.classList.remove("active")
	})

	steps[currentStep].classList.add("active")

	stepIcons.forEach((icon,i)=>{

	icon.classList.remove("active","completed")

	if(i<currentStep){
	icon.classList.add("completed")
	}

	if(i===currentStep){
	icon.classList.add("active")
	}

	})

	progress.style.width=(currentStep/(steps.length-1))*100+"%"

	prev.style.display=currentStep===0?"none":"inline-block"

	if(currentStep===steps.length-1){

	next.style.display="none"
	submit.classList.remove("d-none")

	}else{

	next.style.display="inline-block"
	submit.classList.add("d-none")

	}

	}

	next.onclick=()=>{
	if(currentStep<steps.length-1){
	if(!validateCurrentStep()){
	return
	}
	currentStep++
	updateForm()
	}
	}

	prev.onclick=()=>{
	if(currentStep>0){
	clearStepError()
	currentStep--
	updateForm()
	}
	}

	updateForm()

	document.querySelectorAll("input, select").forEach((field)=>{
	field.addEventListener("input",()=>clearFieldError(field))
	field.addEventListener("change",()=>clearFieldError(field))
	})

	document.querySelectorAll('input[name="business_type[]"]').forEach((field)=>{
	field.addEventListener("change",clearBusinessTypeError)
	})

	</script>

     <script src="{{ asset('home/assets/js/jquery-3.7.1.min.js') }}"></script>
    <!-- Popper and Bootstrap JS -->
    <script src="{{ asset('home/assets/js/popper.min.js') }}"></script>
    <script src="{{ asset('home/assets/js/bootstrap.min.js') }}"></script>
 
    <!-- Nice Select  JS -->
    <script src="{{ asset('home/assets/js/jquery.nice-select.min.js') }}"></script>
  
   
    
 

    <script src="{{ asset('home/assets/js/main.js') }}"></script>

</body>


</html>
