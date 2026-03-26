@include('bid_admin.admin.include.header')

@include('bid_admin.admin.include.side_menu')

<!-- Page Wrapper -->
      <div class="page-wrapper">
         <div class="content container-fluid">
            <div class="status-header d-flex flex-wrap justify-content-between align-items-center">
               <div class="d-flex align-items-center gap-4">
                  <div class="small">
                     <i class="bi bi-circle-fill text-success me-1"></i> SYSTEM STATUS: <strong>LIVE</strong>
                  </div>
                  <div class="small">
                     <i class="bi bi-circle-fill text-danger me-1"></i> <strong>6</strong> Live Auctions
                  </div>
                  <div class="small">
                     <i class="bi bi-circle-fill text-warning me-1"></i> <strong>3</strong> Upcoming
                  </div>
                  <div class="small">
                     <i class="bi bi-circle-fill text-success me-1"></i> <strong>$12,450</strong> Revenue Today
                  </div>
               </div>
               <div class="fw-bold">
                  48 <span class="text-muted fw-normal">Buyers Online</span>
               </div>
            </div>
            

            <div class="page-header">
    <div class="row align-items-center">
      <div class="col">
        <h1 class="page-title">Lot Management</h1>
        <p class="text-muted">Manage and analyze Auction lot</p>
      </div>
      <div class="col-auto">
         
        <a href="{{ route('admin.create-lot') }}" class="btn btn-primary">
          <i class="fe fe-plus me-2"></i>Create New Lot
        </a>
      </div>
    </div>
  </div>
  
  <div class="card p-3 mb-4">
            <div class="row">
               <div class="col-lg-3">
                <select class="form-select">
                    <option>
                        Species
                    </option>
                    <option>
                        Shrimps
                    </option>
                    <option>
                        Fish
                    </option>
                </select> 
               </div>
               <div class="col-lg-2">
                <select class="form-select">
                    <option>
                        Status
                    </option>
                    <option>
                        Active
                    </option>
                    <option>
                        Ended
                    </option>
                    <option>
                        Draft
                    </option>
                    <option>
                        Suspended
                    </option>
                </select> 
               </div>
               <div class="col-lg-3">
                <select class="form-select">
                    <option>
                        Seller
                    </option>
                    <option>
                        SAGAL CLEM
                    </option>
                    <option>
                        Harnai Market
                    </option>
                </select> 
                </div>
                <div class="col-lg-2">
                <input class="form-control" type="date">
                 </div>
                 <div class="col-lg-2">
                 <button class="btn btn-primary">Filter</button>
                 </div>
            </div>
        </div><!-- Auction Table -->
    
   <!-- ================= TABLE VIEW ================= -->
        <div class="table-glass" id="tableView">
             
            <div class="table-responsive">
                <table class="table table-striped" id="userTable">
                    <thead>
                    <tr>
                        <th>Lot</th>
                        <th>Seller</th>
                        <th>Starting Price</th>
                        <th>Increment</th>
                        <th>End Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                    <tbody>
                        <tr>
                        <td>
                           <div class="d-flex align-items-center gap-3">
                              <img src="https://loremflickr.com/cache/resized/532_32680198402_77f1cd591e_400_300_nofilter.jpg" class="lotfish"/>
                              <span>Frozen Tiger Shrimps</span>
                           </div>
                        </td>
                        <td>SAGAL CLEM</td>
                        <td>$21000</td>
                        <td>$500</td>
                        <td>2026-01-10 18:00</td>
                        <td><span class="badge badge-active text-white">Active</span></td>
                        <td>
                            <div class="action-buttons">
                                             <a href="{{ route('admin.lot-details') }}" class="btn btn-sm btn-primary" title="View"><i class="bi bi-eye"></i></a>
                                             <a href="#" class="btn btn-sm btn-warning" title="Edit"><i class="bi bi-pencil-square"></i></a>
                                             <button class="btn btn-sm btn-danger" title="Delete"><i class="bi bi-trash3"></i></button>
                                          </div>                             
                        </td>
                    </tr>
                    <tr>
                        <td>
                           <div class="d-flex align-items-center gap-3">
                              <img src="https://loremflickr.com/cache/resized/532_32680198402_77f1cd591e_400_300_nofilter.jpg" class="lotfish"/>
                              <span>Dry Fish Premium</span>
                           </div>
                        </td>
                        
                        <td>Harnai Market</td>
                        <td>$8000</td>
                        <td>$200</td>
                        <td>2026-01-05 16:00</td>
                        <td><span class="badge badge-ended text-white">Ended</span></td>
                        <td>
                           <div class="action-buttons">
                                             <a href="{{ route('admin.lot-details') }}" class="btn btn-sm btn-primary" title="View"><i class="bi bi-eye"></i></a>
                                             <a href="#" class="btn btn-sm btn-warning" title="Edit"><i class="bi bi-pencil-square"></i></a>
                                             <button class="btn btn-sm btn-danger" title="Delete"><i class="bi bi-trash3"></i></button>
                                          </div>
                        </td>
                    </tr>
                    <tr>
                        
                        <td>
                           <div class="d-flex align-items-center gap-3">
                              <img src="https://loremflickr.com/cache/resized/532_32680198402_77f1cd591e_400_300_nofilter.jpg" class="lotfish"/>
                              <span>Gold Fish Bulk</span>
                           </div>
                        </td>
                        <td>Seller Kumar</td>
                        <td>$5000</td>
                        <td>$100</td>
                        <td>2026-01-12 12:00</td>
                        <td><span class="badge badge-draft text-white">Draft</span></td>
                        <td>
                           <div class="action-buttons">
                                             <a href="{{ route('admin.lot-details') }}" class="btn btn-sm btn-primary" title="View"><i class="bi bi-eye"></i></a>
                                             <a href="#" class="btn btn-sm btn-warning" title="Edit"><i class="bi bi-pencil-square"></i></a>
                                             <button class="btn btn-sm btn-danger" title="Delete"><i class="bi bi-trash3"></i></button>
                                          </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                           <div class="d-flex align-items-center gap-3">
                              <img src="https://loremflickr.com/cache/resized/532_32680198402_77f1cd591e_400_300_nofilter.jpg" class="lotfish"/>
                              <span>Head On Tiger Shrimps</span>
                           </div>
                        </td>
                        <td>SAGAL CLEM</td>
                        <td>$25000</td>
                        <td>$600</td>
                        <td>2026-01-20 15:00</td>
                        <td><span class="badge badge-active text-white">Active</span></td>
                        <td>
                           <div class="action-buttons">
                                             <a href="{{ route('admin.lot-details') }}" class="btn btn-sm btn-primary" title="View"><i class="bi bi-eye"></i></a>
                                             <a href="#" class="btn btn-sm btn-warning" title="Edit"><i class="bi bi-pencil-square"></i></a>
                                             <button class="btn btn-sm btn-danger" title="Delete"><i class="bi bi-trash3"></i></button>
                                          </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                           <div class="d-flex align-items-center gap-3">
                              <img src="https://loremflickr.com/cache/resized/532_32680198402_77f1cd591e_400_300_nofilter.jpg" class="lotfish"/>
                              <span>Mixed Fish Lot</span>
                           </div>
                        </td>
                        <td>Harnai Market</td>
                        <td>$12000</td>
                        <td>$300</td>
                        <td>2026-01-18 11:00</td>
                        <td><span class="badge badge-suspended text-white">Suspended</span></td>
                        <td>
                           <div class="action-buttons">
                                             <a href="{{ route('admin.lot-details') }}" class="btn btn-sm btn-primary" title="View"><i class="bi bi-eye"></i></a>
                                             <a href="#" class="btn btn-sm btn-warning" title="Edit"><i class="bi bi-pencil-square"></i></a>
                                             <button class="btn btn-sm btn-danger" title="Delete"><i class="bi bi-trash3"></i></button>
                                          </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                           <div class="d-flex align-items-center gap-3">
                              <img src="https://loremflickr.com/cache/resized/532_32680198402_77f1cd591e_400_300_nofilter.jpg" class="lotfish"/>
                              <span>Premium Lobster</span>
                           </div>
                        </td>
                        <td>Ocean Traders</td>
                        <td>$40000</td>
                        <td>$1000</td>
                        <td>2026-01-25 19:00</td>
                        <td><span class="badge badge-active text-white">Active</span></td>
                        <td>
                           <div class="action-buttons">
                                             <a href="{{ route('admin.lot-details') }}" class="btn btn-sm btn-primary" title="View"><i class="bi bi-eye"></i></a>
                                             <a href="#" class="btn btn-sm btn-warning" title="Edit"><i class="bi bi-pencil-square"></i></a>
                                             <button class="btn btn-sm btn-danger" title="Delete"><i class="bi bi-trash3"></i></button>
                                          </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                           <div class="d-flex align-items-center gap-3">
                              <img src="https://loremflickr.com/cache/resized/532_32680198402_77f1cd591e_400_300_nofilter.jpg" class="lotfish"/>
                              <span>Requin Special</span>
                           </div>
                        </td>
                        <td>SAGAL CLEM</td>
                        <td>$30000</td>
                        <td>$700</td>
                        <td>2026-01-30 14:00</td>
                        <td><span class="badge badge-ended text-white">Ended</span></td>
                        <td>
                           <div class="action-buttons">
                                             <a href="{{ route('admin.lot-details') }}" class="btn btn-sm btn-primary" title="View"><i class="bi bi-eye"></i></a>
                                             <a href="#" class="btn btn-sm btn-warning" title="Edit"><i class="bi bi-pencil-square"></i></a>
                                             <button class="btn btn-sm btn-danger" title="Delete"><i class="bi bi-trash3"></i></button>
                                          </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        </div><!-- ================= KANBAN VIEW ================= -->
        
   

         </div>
      </div>
   </div><!-- /Page Wrapper -->

<!-- jQuery -->
      <!-- Bootstrap Core JS -->
      <!-- Feather Icon JS -->
      <!-- Slimscroll JS -->
      <!-- Theme Settings JS -->
      <!-- Custom JS -->
      <!-- Datatable JS -->
      <script src="https://cdn.datatables.net/2.2.1/js/dataTables.js"  type="text/javascript"></script>
      <script src="https://cdn.datatables.net/2.2.1/js/dataTables.bootstrap5.js"  type="text/javascript"></script>
      <script src="https://cdn.datatables.net/buttons/3.2.0/js/dataTables.buttons.js"  type="text/javascript"></script>
      <script src="https://cdn.datatables.net/buttons/3.2.0/js/buttons.dataTables.js"  type="text/javascript"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"  type="text/javascript"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"  type="text/javascript"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"  type="text/javascript"></script>
      <script src="https://cdn.datatables.net/buttons/3.2.0/js/buttons.html5.min.js"  type="text/javascript"></script>
      <script src="https://cdn.datatables.net/buttons/3.2.0/js/buttons.print.min.js"  type="text/javascript"></script>
      <!-- Feather Icon JS -->
      <script>
         // Initialize DataTable with enhanced features
         $(document).ready(function() {
            $('#userTable').DataTable({
               pageLength: 10,
               order: [[0, 'asc']],
               responsive: true
               
              
            });
         });
      </script>

@include('bid_admin.admin.include.footer')

