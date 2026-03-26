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
        <h1 class="page-title">Sellers</h1>
        <p class="text-muted">Manage and analyze Sellers</p>
      </div>
      <div class="col-auto">
         
        <a href="{{ route('admin.add-seller') }}" class="btn btn-primary">
          <i class="fe fe-plus me-2"></i>Add New
        </a>
      </div>
    </div>
  </div>
  
    <div class="row mb-4">
            <div class="col-md-3">
                <div class="stat-card d-flex justify-content-between align-items-center">
                    <div>
                        <h6>Total Sellers</h6>
                        <h4>128</h4>
                    </div>
                    <div class="stat-icon">
                        <i class="bi bi-people"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card d-flex justify-content-between align-items-center">
                    <div>
                        <h6>Active Sellers</h6>
                        <h4>102</h4>
                    </div>
                    <div class="stat-icon">
                        <i class="bi bi-check-circle"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card d-flex justify-content-between align-items-center">
                    <div>
                        <h6>Under Review</h6>
                        <h4>16</h4>
                    </div>
                    <div class="stat-icon">
                        <i class="bi bi-hourglass-split"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card d-flex justify-content-between align-items-center">
                    <div>
                        <h6>Total Sales</h6>
                        <h4>$ 3.8 Cr</h4>
                    </div>
                    <div class="stat-icon">
                        <i class="bi bi-currency-dollar"></i>
                    </div>
                </div>
            </div>
        </div>
   <!-- ================= TABLE VIEW ================= -->
        <div class="table-glass" id="tableView">
             <div class="d-flex justify-content-between mb-3">
               <select class="form-select w-25">
                    <option>
                        Filter by Status
                    </option>
                    <option>
                        Active
                    </option>
                    <option>
                        Under Review
                    </option>
                    <option>
                        Suspended
                    </option>
                </select>
            </div>
            <div class="table-responsive">
                <table class="table table-striped" id="userTable">
                    <thead>
                        <tr>
                            <th>Seller</th>
                            <th>Email</th>
                            <th>Port</th>
                            <th>Auctions</th>
                            <th>Total Sales</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img class="seller-avatar" src="https://i.pravatar.cc/100?img=10">
                                    <div>
                                        <strong>Ocean Fresh Exports</strong><br>
                                        <small class="text-muted">SEL1021</small>
                                    </div>
                                </div>
                            </td>
                            <td>seller@gmail.com</td>
                            <td>Mumbai Dockyard</td>
                            <td>32</td>
                            <td class="fw-semibold text-primary">$ 48,00,000</td>
                            <td><span class="badge status-active">Active</span></td>
                            <td>
                                <div class="action-buttons">
                                             <a href="{{ route('admin.seller-details') }}" class="btn btn-sm btn-primary"><i class="bi bi-eye"></i></a>
                                             <button class="btn btn-sm btn-warning" title="Edit"><i class="bi bi-pencil-square"></i></button>
                                             <button class="btn btn-sm btn-danger" title="Delete"><i class="bi bi-trash3"></i></button>
                                          </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img class="seller-avatar" src="https://i.pravatar.cc/100?img=12">
                                    <div>
                                        <strong>BlueWave Fisheries</strong><br>
                                        <small class="text-muted">SEL1022</small>
                                    </div>
                                </div>
                            </td>
                            <td>bluewave@gmail.com</td>
                            <td>Kochi Harbor</td>
                            <td>25</td>
                            <td class="fw-semibold text-primary">$ 36,75,000</td>
                            <td><span class="badge status-review">Under Review</span></td>
                            <td>
                              <div class="action-buttons">
                                             <a href="{{ route('admin.seller-details') }}" class="btn btn-sm btn-primary"><i class="bi bi-eye"></i></a>
                                             <button class="btn btn-sm btn-warning" title="Edit"><i class="bi bi-pencil-square"></i></button>
                                             <button class="btn btn-sm btn-danger" title="Delete"><i class="bi bi-trash3"></i></button>
                                          </div>
                            </td>
                        </tr>

                        <tr>
         <td>
            <div class="d-flex align-items-center gap-2">
               <img class="seller-avatar" src="https://i.pravatar.cc/100?img=14">
               <div>
                  <strong>SeaKing Traders</strong><br>
                  <small class="text-muted">SEL1023</small>
               </div>
            </div>
         </td>
         <td>seaking@gmail.com</td>
         <td>Chennai Port</td>
         <td>18</td>
         <td class="fw-semibold text-primary">$ 21,40,000</td>
         <td><span class="badge status-active">Active</span></td>
         <td>
            <div class="action-buttons">
                                             <a href="{{ route('admin.seller-details') }}" class="btn btn-sm btn-primary"><i class="bi bi-eye"></i></a>
                                             <button class="btn btn-sm btn-warning" title="Edit"><i class="bi bi-pencil-square"></i></button>
                                             <button class="btn btn-sm btn-danger" title="Delete"><i class="bi bi-trash3"></i></button>
                                          </div>
         </td>
      </tr>
      <tr>
         <td>
            <div class="d-flex align-items-center gap-2">
               <img class="seller-avatar" src="https://i.pravatar.cc/100?img=16">
               <div>
                  <strong>HarborCatch Pvt Ltd</strong><br>
                  <small class="text-muted">SEL1024</small>
               </div>
            </div>
         </td>
         <td>harbor@gmail.com</td>
         <td>Visakhapatnam</td>
         <td>40</td>
         <td class="fw-semibold text-primary">$ 62,30,000</td>
         <td><span class="badge status-active">Active</span></td>
         <td>
            <div class="action-buttons">
                                             <a href="{{ route('admin.seller-details') }}" class="btn btn-sm btn-primary"><i class="bi bi-eye"></i></a>
                                             <button class="btn btn-sm btn-warning" title="Edit"><i class="bi bi-pencil-square"></i></button>
                                             <button class="btn btn-sm btn-danger" title="Delete"><i class="bi bi-trash3"></i></button>
                                          </div>
         </td>
      </tr>
      <tr>
         <td>
            <div class="d-flex align-items-center gap-2">
               <img class="seller-avatar" src="https://i.pravatar.cc/100?img=18">
               <div>
                  <strong>DeepSea Exim</strong><br>
                  <small class="text-muted">SEL1025</small>
               </div>
            </div>
         </td>
         <td>deepsea@gmail.com</td>
         <td>Goa Port</td>
         <td>14</td>
         <td class="fw-semibold text-primary">$ 18,75,000</td>
         <td><span class="badge bg-danger">Suspended</span></td>
         <td>
            <div class="action-buttons">
                                             <a href="{{ route('admin.seller-details') }}" class="btn btn-sm btn-primary"><i class="bi bi-eye"></i></a>
                                             <button class="btn btn-sm btn-warning" title="Edit"><i class="bi bi-pencil-square"></i></button>
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

