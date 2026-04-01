@include('bid_admin.admin.include.header')
@include('bid_admin.admin.include.side_menu')

<div class="page-wrapper admin-create-lot-page">
    <div class="content container-fluid">
        <style>
            .admin-create-lot-page .content.container-fluid { padding-top: 10px !important; }
            .create-lot-shell {
                background: linear-gradient(180deg, rgba(5, 145, 215, 0.12), rgba(23, 120, 191, 0.08));
                border-radius: 24px;
                padding: 18px;
            }
            .status-strip {
                background: linear-gradient(135deg, rgba(13, 95, 149, 0.72), rgba(24, 126, 188, 0.58));
                border: 1px solid rgba(255,255,255,.12);
                border-radius: 18px;
                padding: 14px 22px;
                box-shadow: 0 18px 40px rgba(8, 57, 94, 0.16);
                backdrop-filter: blur(8px);
            }
            .status-pill, .buyers-online-pill {
                color: #f8fbff;
                font-size: 15px;
                font-weight: 600;
                display: inline-flex;
                align-items: center;
                white-space: nowrap;
            }
            .status-pill strong, .buyers-online-pill strong { color: #fff; font-weight: 800; }
            .buyers-online-pill { font-size: 17px; font-weight: 700; }
            .status-dot {
                width: 12px;
                height: 12px;
                border-radius: 999px;
                display: inline-block;
                margin-right: 8px;
                box-shadow: 0 0 0 3px rgba(255,255,255,.08);
            }
            .page-head-card, .form-glass {
                background: rgba(248, 251, 255, 0.90);
                border-radius: 22px;
                box-shadow: 0 16px 35px rgba(15,23,42,.08);
                border: 1px solid rgba(255,255,255,.45);
                backdrop-filter: blur(10px);
            }
            .page-head-card {
                padding: 18px;
                margin-bottom: 18px;
            }
            .form-glass {
                padding: 24px;
            }
            .glass-input, .form-glass .form-select, .form-glass .form-control, .form-glass textarea {
                border-radius: 14px;
                border: 1px solid rgba(148, 163, 184, .35);
                background: rgba(255,255,255,.92);
                color: #0f172a;
                min-height: 48px;
            }
            .upload-dropzone {
                border: 2px dashed rgba(59, 130, 246, .30);
                border-radius: 18px;
                padding: 28px 20px;
                text-align: center;
                background: rgba(59, 130, 246, .06);
                cursor: pointer;
            }
            .file-pill {
                border: 1px solid rgba(148, 163, 184, .35);
                border-radius: 14px;
                min-height: 48px;
                display: flex;
                align-items: center;
                gap: 10px;
                padding: 8px 14px;
                background: rgba(255,255,255,.92);
            }
            .prod-img {
                width: 140px;
                height: 140px;
                object-fit: cover;
                border-radius: 16px;
                border: 1px solid rgba(148, 163, 184, .35);
            }
            .input-group-glass {
                position: relative;
            }
            .unit-text {
                position: absolute;
                right: 14px;
                top: 50%;
                transform: translateY(-50%);
                color: #475569;
                font-weight: 700;
                z-index: 2;
            }
            .input-group-glass.has-prefix .unit-text {
                right: auto;
                left: 14px;
            }
            .input-group-glass.has-prefix input {
                padding-left: 34px;
            }
        </style>

        <div class="create-lot-shell">
            <div class="status-strip d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
                <div class="d-flex flex-wrap align-items-center gap-4">
                    <div class="status-pill"><span class="status-dot {{ $systemStatus === 'LIVE' ? 'bg-success' : 'bg-secondary' }}"></span>SYSTEM STATUS: <strong class="ms-1">{{ $systemStatus }}</strong></div>
                    <div class="status-pill"><span class="status-dot bg-danger"></span><strong>{{ $liveAuctionsCount }}</strong><span class="ms-1">Live Auctions</span></div>
                    <div class="status-pill"><span class="status-dot bg-warning"></span><strong>{{ $upcomingAuctionsCount }}</strong><span class="ms-1">Upcoming</span></div>
                    <div class="status-pill"><span class="status-dot bg-success"></span><strong>${{ number_format($revenueToday, 2) }}</strong><span class="ms-1">Revenue Today</span></div>
                </div>
                <div class="buyers-online-pill"><strong>{{ number_format($registeredBuyersCount) }}</strong> Buyers Online</div>
            </div>

            <div class="page-head-card">
                <div class="row align-items-center">
                    <div class="col">
                        <h1 class="page-title mb-1">{{ $isEditMode ? 'Edit Auction Lot' : 'Create Auction Lot' }}</h1>
                   
                    </div>
                    <div class="col-auto">
                        <a href="{{ route('admin.lot-management') }}" class="btn btn-primary">
                            <i class="fe fe-eye me-2"></i>View All
                        </a>
                    </div>
                </div>
            </div>

            <div class="form-glass">
                <form method="POST" action="{{ $isEditMode ? route('admin.update-lot', ['lot' => $lot->id]) : route('admin.store-lot') }}" enctype="multipart/form-data" novalidate>
                    @csrf
                    @if($isEditMode)
                        @method('PUT')
                    @endif

                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <div class="row g-4 mb-5">
                        <div class="col-md-6">
                            <label class="form-label">Seller</label>
                            <select class="form-select @error('seller_id') is-invalid @enderror" name="seller_id">
                                <option value="">Select Seller</option>
                                @foreach($sellerOptions as $sellerOption)
                                    <option value="{{ $sellerOption->id }}" {{ (string) old('seller_id', $lot->seller_id) === (string) $sellerOption->id ? 'selected' : '' }}>{{ $sellerOption->name }}</option>
                                @endforeach
                            </select>
                            @error('seller_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Lot Title</label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" placeholder="Enter lot name" name="title" value="{{ old('title', $lot->title) }}" minlength="3" maxlength="255">
                            @error('title')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label">Product Image</label>
                            @if($isEditMode && $lot->image_path)
                                <div class="mb-3">
                                    <img src="{{ $lot->image_url }}" alt="{{ $lot->title ?? 'Lot image' }}" style="max-width: 220px; border-radius: 18px;">
                                </div>
                            @endif
                            <div class="upload-dropzone" onclick="document.getElementById('adminProductImage').click()">
                                <input type="file" id="adminProductImage" hidden name="product_image" accept="image/*">
                                <i class="bi bi-cloud-arrow-up fs-2"></i>
                                <p class="mb-0">{{ $isEditMode ? 'Click to replace the current fish image' : 'Drag and drop or click to upload fish image' }}</p>
                                <small class="text-muted">PNG, JPG up to 5MB{{ $isEditMode ? ' - leave empty to keep current image' : '' }}</small>
                            </div>
                            @error('product_image')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Species / Common Name</label>
                            <input type="text" class="form-control @error('species') is-invalid @enderror" placeholder="e.g. Yellowfin Tuna" name="species" value="{{ old('species', $lot->species) }}" maxlength="255">
                            @error('species')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Quantity (kg)</label>
                            <div class="input-group-glass">
                                <input type="number" class="form-control @error('quantity') is-invalid @enderror" placeholder="0.00" name="quantity" step="0.01" min="0" value="{{ old('quantity', $lot->quantity) }}">
                                <span class="unit-text">KG</span>
                            </div>
                            @error('quantity')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Starting Price ($)</label>
                            <div class="input-group-glass has-prefix">
                                <span class="unit-text">$</span>
                                <input type="number" class="form-control @error('starting_price') is-invalid @enderror" placeholder="0.00" name="starting_price" step="0.01" min="0" value="{{ old('starting_price', $lot->starting_price) }}">
                            </div>
                            @error('starting_price')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <h6 class="fw-bold mb-3 border-bottom pb-2">QC & Storage Details</h6>
                    <div class="row g-4 mb-5">
                        <div class="col-md-6">
                            <label class="form-label">Harvest Date</label>
                            <input type="date" class="form-control @error('harvest_date') is-invalid @enderror" name="harvest_date" value="{{ old('harvest_date', optional($lot->harvest_date)->format('Y-m-d')) }}">
                            @error('harvest_date')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Storage Temp (C)</label>
                            <input type="text" class="form-control @error('storage_temperature') is-invalid @enderror" placeholder="e.g. -18C" name="storage_temperature" value="{{ old('storage_temperature', $lot->storage_temperature) }}" maxlength="50">
                            @error('storage_temperature')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Health Certificate</label>
                            <div class="file-pill">
                                <i class="bi bi-file-earmark-pdf"></i>
                                <input type="file" class="form-control @error('health_certificate') is-invalid @enderror" name="health_certificate" accept=".pdf,.jpg,.jpeg,.png">
                            </div>
                            @if($isEditMode && $lot->health_certificate_path)
                                @php
                                    $healthExtension = strtolower(pathinfo($lot->health_certificate_path, PATHINFO_EXTENSION));
                                    $healthIsImage = in_array($healthExtension, ['jpg', 'jpeg', 'png', 'webp', 'gif'], true);
                                @endphp
                                @if($healthIsImage)
                                    <div class="mt-2">
                                        <img src="{{ asset('storage/' . ltrim($lot->health_certificate_path, '/')) }}" alt="Health certificate" class="prod-img">
                                    </div>
                                @else
                                    <small class="d-block mt-2 text-muted">Current file: {{ basename($lot->health_certificate_path) }}</small>
                                @endif
                            @endif
                            @error('health_certificate')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Additional Documents</label>
                            <div class="file-pill">
                                <i class="bi bi-file-earmark-zip"></i>
                                <input type="file" class="form-control @error('additional_documents') is-invalid @enderror" name="additional_documents" accept=".zip,.pdf,.jpg,.jpeg,.png">
                            </div>
                            @if($isEditMode && $lot->documents_path)
                                @php
                                    $documentExtension = strtolower(pathinfo($lot->documents_path, PATHINFO_EXTENSION));
                                    $documentIsImage = in_array($documentExtension, ['jpg', 'jpeg', 'png', 'webp', 'gif'], true);
                                @endphp
                                @if($documentIsImage)
                                    <div class="mt-2">
                                        <img src="{{ asset('storage/' . ltrim($lot->documents_path, '/')) }}" alt="Additional document" class="prod-img">
                                    </div>
                                @else
                                    <small class="d-block mt-2 text-muted">Current file: {{ basename($lot->documents_path) }}</small>
                                @endif
                            @endif
                            @error('additional_documents')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-12 mb-5">
                        <label class="form-label">Lot Notes / Descriptions</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror" rows="4" placeholder="Mention grade details, handling info, etc." name="notes">{{ old('notes', $lot->notes) }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2 text-muted">
                            <i class="bi bi-shield-check text-success fs-4"></i>
                          
                        </div>
                        <button type="submit" class="btn btn-primary shadow-lg">{{ $isEditMode ? 'Update Lot' : 'Create Lot' }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@include('bid_admin.admin.include.footer')
