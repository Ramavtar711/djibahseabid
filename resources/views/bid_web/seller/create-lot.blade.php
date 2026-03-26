@include('bid_web.seller.include.header')

@include('bid_web.seller.include.side_menu')

<!-- Page Wrapper -->
         <div class="page-wrapper">
            <div class="content container-fluid">
              <div class="page-header">
    <div class="row align-items-center">
      <div class="col">
        <h1 class="page-title text-white">Create New Lot</h1>
        <p class="text-white">Seller Auction Management &amp; Perform Optimization</p>
      </div>
      <div class="col-auto">
         <a class="btn btn-primary" href="{{ route('seller.lot-list') }}"><i class="bi bi-eye"></i> View all</a>
       
      </div>
    </div>
  </div>
          
   <div class="glass p-4 p-md-5">
            <form method="POST" action="{{ route('seller.store-lot') }}" id="createLotForm" enctype="multipart/form-data" novalidate>
                @csrf

                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="row g-4 mb-5">
                    <div class="col-12">
                        <label class="form-label small ">Lot Title</label>
                        <input type="text" class="glass-input @error('title') is-invalid @enderror" placeholder="Enter lot name" name="title" value="{{ old('title') }}" minlength="3" maxlength="255">
                        @error('title')
                            <div class="invalid-feedback text-danger d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label small ">Product Image</label>
                        <div class="upload-dropzone" onclick="document.getElementById('productImage').click()">
                            <input type="file" id="productImage" hidden name="product_image">
                            <i class="bi bi-cloud-arrow-up fs-2"></i>
                            <p class="mb-0 text-white">Drag and drop or click to upload fish image</p>
                            <small class="opacity-50 text-white">PNG, JPG up to 5MB</small>
                        </div>
                        @error('product_image')
                            <div class="invalid-feedback text-danger d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small ">Species / Common Name</label>
                        <input type="text" class="glass-input @error('species') is-invalid @enderror" placeholder="e.g. Yellowfin Tuna" name="species" value="{{ old('species') }}" maxlength="255">
                        @error('species')
                            <div class="invalid-feedback text-danger d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label class="form-label small ">Quantity (kg)</label>
                        <div class="input-group-glass">
                            <input type="number" class="glass-input @error('quantity') is-invalid @enderror" placeholder="0.00" name="quantity" step="0.01" min="0" value="{{ old('quantity') }}">
                            @error('quantity')
                                <div class="invalid-feedback text-danger d-block">{{ $message }}</div>
                            @enderror
                            <span class="unit-text">KG</span>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label small ">Starting Price ($)</label>
                        <div class="input-group-glass">
                            <span class="unit-text">$</span>
                            <input type="number" class="glass-input @error('starting_price') is-invalid @enderror" placeholder="0.00" name="starting_price" step="0.01" min="0" value="{{ old('starting_price') }}">
                            @error('starting_price')
                                <div class="invalid-feedback text-danger d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <h6 class="fw-bold mb-3 border-bottom text-white border-white-10 pb-2">QC & Storage Details</h6>
                <div class="row g-4 mb-5">
                    <div class="col-md-6">
                        <label class="form-label small ">Harvest Date</label>
                        <input type="date" class="glass-input @error('harvest_date') is-invalid @enderror" name="harvest_date" value="{{ old('harvest_date') }}">
                        @error('harvest_date')
                            <div class="invalid-feedback text-danger d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small ">Storage Temp (°C)</label>
                        <input type="text" class="glass-input @error('storage_temperature') is-invalid @enderror" placeholder="e.g. -18°C" name="storage_temperature" value="{{ old('storage_temperature') }}" maxlength="50">
                        @error('storage_temperature')
                            <div class="invalid-feedback text-danger d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small ">Health Certificate</label>
                        <div class="file-pill">
                            <i class="bi bi-file-earmark-pdf"></i>
                            <input type="file" class="form-control-file @error('health_certificate') is-invalid @enderror" name="health_certificate" accept=".pdf,.jpg,.jpeg,.png">
                            @error('health_certificate')
                                <div class="invalid-feedback text-danger d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small ">Additional Documents</label>
                        <div class="file-pill">
                            <i class="bi bi-file-earmark-zip"></i>
                            <input type="file" class="form-control-file @error('additional_documents') is-invalid @enderror" name="additional_documents" accept=".zip,.pdf,.jpg,.jpeg,.png">
                            @error('additional_documents')
                                <div class="invalid-feedback text-danger d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="col-12 mb-5">
                    <label class="form-label small ">Lot Notes / Descriptions</label>
                    <textarea class="glass-input @error('notes') is-invalid @enderror" rows="4" placeholder="Mention grade details, handling info, etc." name="notes">{{ old('notes') }}</textarea>
                    @error('notes')
                        <div class="invalid-feedback text-danger d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-shield-check text-success fs-4"></i>
                        <span class="small opacity-50">Verified Seller QC Protocol Applied</span>
                    </div>
                    <button type="submit" class="btn btn-primary shadow-lg">Submit for QC Review</button>
                </div>
            </form>
        </div>
    

    
</div>
         



  
          
        </div>

        
   

</div>
</div>

              
              
            </div>
         </div>
         <!-- /Page Wrapper -->

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" data-cfasync="false"></script>

<script>

// Donut Chart
new Chart(document.getElementById('bidChart'),{
    type:'doughnut',
    data:{
        labels:['Used','Remaining'],
        datasets:[{
            data:[12500, 37500],
            backgroundColor:['#3d7eff','#cce0ff']
        }]
    },
    options:{ plugins:{ legend:{ display:false } } }
});

// Bar Chart
new Chart(document.getElementById('barChart'),{
    type:'bar',
    data:{
        labels:['Mon','Tue','Wed','Thu','Fri'],
        datasets:[{
            data:[500,900,700,1000,1200],
            backgroundColor:'#3d7eff'
        }]
    },
    options:{ plugins:{ legend:{ display:false } } }
});

</script>

@include('bid_web.seller.include.footer')
