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

	.progress-wrapper{
	display:flex;
	justify-content:space-between;
	position:relative;
	margin-bottom:40px;
	}

	.progress-wrapper::before{
	content:"";
	position:absolute;
	top:15px;
	left:0;
	width:100%;
	height:4px;
	background:#334155;
	}

	.progress-bar-step{
	position:absolute;
	top:15px;
	left:0;
	height:4px;
	background:#38bdf8;
	width:0%;
	transition:.4s;
	}

	.step{
	text-align:center;
	width:25%;
	position:relative;
	font-size:13px;
	}


	.circle{
	width:34px;
	height:34px;
	border-radius:50%;
	background:#334155;
	margin:auto;
	display:flex;
	align-items:center;
	justify-content:center;
	font-size:14px;
	}

	.step.active .circle{
	background:#38bdf8;
	}

	.step.completed .circle{
	background:#22c55e;
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

	.option-card{
	border: 2px solid #ffffff1a !important;
	padding:14px;
	    width: 100%;
	border-radius:8px;
	color:#fff;
	cursor:pointer;
	transition:.2s;
	}

	.option-card:hover{
	border-color:#38bdf8;
	}

	.badge-sec{
	background:#0f172a;
	padding:6px 10px;
	border-radius:6px;
	font-size:12px;
	margin-right:5px;
	}

	@media screen and (max-width: 600px) {
  .step{
	
	font-size:10px;
	}
}

	</style>
</head>
<body>
	<div class="position-fixed" style="top:20px;  right:5%; z-index:9999">
    <select class="input-field" onchange="changeLanguage(this.value)" style="color:#fff;">
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
			<h3 class="mb-4 text-white">Create Seller Account</h3><!-- PROGRESS -->
			<div class="progress-wrapper">
				<div class="progress-bar-step" id="progress"></div>
				<div class="step active">
					<div class="circle">
						1
					</div>
					<div class="label">
						Contact
					</div>
				</div>
				<div class="step">
					<div class="circle">
						2
					</div>
					<div class="label">
						Company
					</div>
				</div>
				<div class="step">
					<div class="circle">
						3
					</div>
					<div class="label">
						Capabilities
					</div>
				</div>
				<div class="step">
					<div class="circle">
						4
					</div>
					<div class="label">
						Compliance
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
			<form method="POST" action="{{ route('home.seller-register.store') }}" enctype="multipart/form-data">
				@csrf
				<!-- STEP 1 -->
				<div class="form-step active">
					<h5>Contact Information</h5>
					<div class="row g-3 mt-3">
						<div class="col-md-6">
							<label>Full Name *</label> <input class="input-field" name="name" value="{{ old('name') }}" required>
						</div>
						<div class="col-md-6">
							<label>Phone *</label> <input class="input-field" name="phone" value="{{ old('phone') }}" required>
						</div>
						<div class="col-md-6">
							<label>Email *</label> <input class="input-field" type="email" name="email" value="{{ old('email') }}" required>
						</div>
						<div class="col-md-6">
							<label>Password *</label> <input class="input-field" type="password" name="password" required>
						</div>
					</div>
				</div><!-- STEP 2 -->
				<div class="form-step">
					<h5>Company & Location</h5>
					<div class="row g-3 mt-3">
						<div class="col-md-6">
							<label>Company Name *</label> <input class="input-field" name="company_name" value="{{ old('company_name') }}" required>
						</div>
						<div class="col-md-6">
							<label>Landing Site / Port *</label> <input class="input-field" name="landing_site_port" value="{{ old('landing_site_port') }}" required>
						</div>
						<div class="col-md-12">
							<label>Address *</label> <input class="input-field" name="address" value="{{ old('address') }}" required>
						</div>
						<div class="col-md-6">
							<label>Country *</label> <select class="input-field" name="country" required>
								<option value="">
									Select Country
								</option>
								<option value="France" @selected(old('country') === 'France')>
									France
								</option>
								<option value="Spain" @selected(old('country') === 'Spain')>
									Spain
								</option>
								<option value="India" @selected(old('country') === 'India')>
									India
								</option>
								<option value="Morocco" @selected(old('country') === 'Morocco')>
									Morocco
								</option>
							</select>
						</div>
					</div>
				</div><!-- STEP 3 -->
				<div class="form-step">
					<h5>Capabilities</h5><label class="mt-3">Supply Type</label>
					<div class="row g-3">
						<div class="col-md-4">
							<label class="option-card"><input type="checkbox" name="supply_type[]" value="Fresh" @checked(in_array('Fresh', old('supply_type', [])))> Fresh</label>
						</div>
						<div class="col-md-4">
							<label class="option-card"><input type="checkbox" name="supply_type[]" value="Frozen" @checked(in_array('Frozen', old('supply_type', [])))> Frozen</label>
						</div>
						<div class="col-md-4">
							<label class="option-card"><input type="checkbox" name="supply_type[]" value="Both" @checked(in_array('Both', old('supply_type', [])))> Both</label>
						</div>
					</div><label class="mt-4">Processing Status</label>
					<div class="row g-3">
						<div class="col-md-3">
							<label class="option-card"><input type="checkbox" name="processing_status[]" value="Whole" @checked(in_array('Whole', old('processing_status', [])))> Whole</label>
						</div>
						<div class="col-md-3">
							<label class="option-card"><input type="checkbox" name="processing_status[]" value="Fillet" @checked(in_array('Fillet', old('processing_status', [])))> Fillet</label>
						</div>
						<div class="col-md-3">
							<label class="option-card"><input type="checkbox" name="processing_status[]" value="Packed" @checked(in_array('Packed', old('processing_status', [])))> Packed</label>
						</div>
						<div class="col-md-3">
							<label class="option-card"><input type="checkbox" name="processing_status[]" value="IQF" @checked(in_array('IQF', old('processing_status', [])))> IQF</label>
						</div>
						<div class="col-md-3">
							<label class="option-card"><input type="checkbox" name="processing_status[]" value="Other" @checked(in_array('Other', old('processing_status', [])))> Other</label>
						</div>
					</div>
					<div class="mt-4">
						<label>Estimated Weekly Volume (kg)</label> <input class="input-field" name="estimated_weekly_volume" value="{{ old('estimated_weekly_volume') }}">
					</div>
				</div><!-- STEP 4 -->
				<div class="form-step">
					<h5>Compliance</h5>
					<div class="row g-3 mt-3">
						<div class="col-md-6">
							<label>Upload Trade License *</label> <input class="input-field" type="file" name="trade_license_file" required>
						</div>
						<div class="col-md-6">
							<label>Facility Photos</label> <input class="input-field" type="file" name="facility_photos_file">
						</div>
						<div class="col-md-6">
							<label>Certificates (HACCP / Health)</label> <input class="input-field" type="file" name="certificates_file">
						</div>
					</div>
					<div class="alert alert-warning mt-4">
						After submission your account will be **Pending Approval** until **Admin QC review**.
					</div>
				</div><!-- BUTTONS -->
				<div id="stepError" class="alert alert-danger d-none mt-3 mb-0"></div>
				<div class="d-flex justify-content-between mt-4">
					<button class="btn btn-secondary" id="prevBtn" type="button">Previous</button> <button class="btn btn-primary" id="nextBtn" type="button">Next</button> <button class="btn btn-success d-none py-3" id="submitBtn" type="submit">Submit for QC Approval</button>
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
</section>
	<script>

	let step=0

	const steps=document.querySelectorAll(".form-step")
	const indicators=document.querySelectorAll(".step")
	const progress=document.getElementById("progress")

	const next=document.getElementById("nextBtn")
	const prev=document.getElementById("prevBtn")
	const submit=document.getElementById("submitBtn")
	const stepError=document.getElementById("stepError")

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

	function validateCurrentStep(){
	clearStepError()

	const requiredFields=steps[step].querySelectorAll("[required]")

	for(const field of requiredFields){
	clearFieldError(field)
	if(!field.value || !field.checkValidity()){
	setFieldError(field,"This field is required.")
	showStepError("Please fix the highlighted fields.")
	field.focus()
	return false
	}
	}

	return true
	}

	function updateForm(){

	steps.forEach((s)=>s.classList.remove("active"))
	steps[step].classList.add("active")

	indicators.forEach((icon,i)=>{

	icon.classList.remove("active","completed")

	if(i<step) icon.classList.add("completed")

	if(i===step) icon.classList.add("active")

	})

	progress.style.width=(step/(steps.length-1))*100+"%"

	prev.style.display= step===0 ? "none":"inline-block"

	if(step===steps.length-1){
	next.style.display="none"
	submit.classList.remove("d-none")
	}else{
	next.style.display="inline-block"
	submit.classList.add("d-none")
	}

	}

	next.onclick=()=>{
	if(step<steps.length-1){
	if(!validateCurrentStep()){
	return
	}
	step++
	updateForm()
	}
	}

	prev.onclick=()=>{
	if(step>0){
	clearStepError()
	step--
	updateForm()
	}
	}

	updateForm()

	document.querySelectorAll("input, select").forEach((field)=>{
	field.addEventListener("input",()=>clearFieldError(field))
	field.addEventListener("change",()=>clearFieldError(field))
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
