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
        <h1 class="page-title">Buyers</h1>
        <p class="text-muted">Manage and analyze buyers</p>
      </div>
      <div class="col-auto">
         <button class="btn btn-outline-primary btn-toggle me-2" onclick="showTable()"><i class="bi bi-table"></i> Table</button> <button class="btn btn-outline-primary btn-toggle" onclick="showKanban()"> <i class="bi bi-grid-3x3-gap"></i>
 Kanban</button>
        <a href="{{ route('admin.add-buyer') }}" class="btn btn-primary">
          <i class="fe fe-plus me-2"></i>Add New
        </a>
      </div>
    </div>
  </div>

   <!-- ================= TABLE VIEW ================= -->
        <div class="table-glass" id="tableView">
          
            <div class="table-responsive">
                <table class="table table-striped" id="userTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Mobile</th>
                            <th>Status</th>
                            <th>Joined</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>Rahul Sharma</td>
                            <td>rahul@gmail.com</td>
                            <td>+91 9000000001</td>
                            <td><span class="status-active">Active</span></td>
                            <td>20 Feb 2026</td>
                            <td>
                                          <div class="action-buttons">
                                             <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#viewModal" title="View"><i class="bi bi-eye"></i></button>
                                             <button class="btn btn-sm btn-warning" title="Edit"><i class="bi bi-pencil-square"></i></button>
                                             <button class="btn btn-sm btn-danger" title="Delete"><i class="bi bi-trash3"></i></button>
                                          </div>
                                       </td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>Amit Verma</td>
                            <td>amit@gmail.com</td>
                            <td>+91 9000000002</td>
                            <td><span class="status-inactive">Inactive</span></td>
                            <td>20 Feb 2026</td>
                            <td>
                                          <div class="action-buttons">
                                             <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#viewModal" title="View"><i class="bi bi-eye"></i></button>
                                             <button class="btn btn-sm btn-warning" title="Edit"><i class="bi bi-pencil-square"></i></button>
                                             <button class="btn btn-sm btn-danger" title="Delete"><i class="bi bi-trash3"></i></button>
                                          </div>
                                       </td>
                        </tr>
                        <tr>
                            <td>3</td>
                            <td>Sneha Patel</td>
                            <td>sneha@gmail.com</td>
                            <td>+91 9000000003</td>
                            <td><span class="status-active">Active</span></td>
                            <td>20 Feb 2026</td>
                            <td>
                                          <div class="action-buttons">
                                             <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#viewModal" title="View"><i class="bi bi-eye"></i></button>
                                             <button class="btn btn-sm btn-warning" title="Edit"><i class="bi bi-pencil-square"></i></button>
                                             <button class="btn btn-sm btn-danger" title="Delete"><i class="bi bi-trash3"></i></button>
                                          </div>
                                       </td>
                        </tr>
                        <tr>
                            <td>4</td>
                            <td>Riya Singh</td>
                            <td>riya@gmail.com</td>
                            <td>+91 9000000004</td>
                            <td><span class="status-active">Active</span></td>
                            <td>20 Feb 2026</td>
                            <td>
                                          <div class="action-buttons">
                                             <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#viewModal" title="View"><i class="bi bi-eye"></i></button>
                                             <button class="btn btn-sm btn-warning" title="Edit"><i class="bi bi-pencil-square"></i></button>
                                             <button class="btn btn-sm btn-danger" title="Delete"><i class="bi bi-trash3"></i></button>
                                          </div>
                                       </td>
                        </tr>
                        <tr>
                            <td>5</td>
                            <td>Vikram Yadav</td>
                            <td>vikram@gmail.com</td>
                            <td>+91 9000000005</td>
                            <td><span class="status-inactive">Inactive</span></td>
                            <td>20 Feb 2026</td>
                            <td>
                                          <div class="action-buttons">
                                             <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#viewModal" title="View"><i class="bi bi-eye"></i></button>
                                             <button class="btn btn-sm btn-warning" title="Edit"><i class="bi bi-pencil-square"></i></button>
                                             <button class="btn btn-sm btn-danger" title="Delete"><i class="bi bi-trash3"></i></button>
                                          </div>
                                       </td>
                        </tr>
                        <tr>
                            <td>6</td>
                            <td>Priya Kapoor</td>
                            <td>priya@gmail.com</td>
                            <td>+91 9000000006</td>
                            <td><span class="status-active">Active</span></td>
                            <td>20 Feb 2026</td>
                            <td>
                                          <div class="action-buttons">
                                             <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#viewModal" title="View"><i class="bi bi-eye"></i></button>
                                             <button class="btn btn-sm btn-warning" title="Edit"><i class="bi bi-pencil-square"></i></button>
                                             <button class="btn btn-sm btn-danger" title="Delete"><i class="bi bi-trash3"></i></button>
                                          </div>
                                       </td>
                        </tr>
                        <tr>
                            <td>7</td>
                            <td>Karan Malhotra</td>
                            <td>karan@gmail.com</td>
                            <td>+91 9000000007</td>
                            <td><span class="status-active">Active</span></td>
                            <td>20 Feb 2026</td>
                            <td>
                                          <div class="action-buttons">
                                             <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#viewModal" title="View"><i class="bi bi-eye"></i></button>
                                             <button class="btn btn-sm btn-warning" title="Edit"><i class="bi bi-pencil-square"></i></button>
                                             <button class="btn btn-sm btn-danger" title="Delete"><i class="bi bi-trash3"></i></button>
                                          </div>
                                       </td>
                        </tr>
                        <tr>
                            <td>8</td>
                            <td>Anjali Mehta</td>
                            <td>anjali@gmail.com</td>
                            <td>+91 9000000008</td>
                            <td><span class="status-inactive">Inactive</span></td>
                            <td>20 Feb 2026</td>
                            <td>
                                          <div class="action-buttons">
                                             <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#viewModal" title="View"><i class="bi bi-eye"></i></button>
                                             <button class="btn btn-sm btn-warning" title="Edit"><i class="bi bi-pencil-square"></i></button>
                                             <button class="btn btn-sm btn-danger" title="Delete"><i class="bi bi-trash3"></i></button>
                                          </div>
                                       </td>
                        </tr>
                        <tr>
                            <td>9</td>
                            <td>Rohit Jain</td>
                            <td>rohit@gmail.com</td>
                            <td>+91 9000000009</td>
                            <td><span class="status-active">Active</span></td>
                            <td>20 Feb 2026</td>
                            <td>
                                          <div class="action-buttons">
                                             <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#viewModal" title="View"><i class="bi bi-eye"></i></button>
                                             <button class="btn btn-sm btn-warning" title="Edit"><i class="bi bi-pencil-square"></i></button>
                                             <button class="btn btn-sm btn-danger" title="Delete"><i class="bi bi-trash3"></i></button>
                                          </div>
                                       </td>
                        </tr>
                        <tr>
                            <td>10</td>
                            <td>Simran Kaur</td>
                            <td>simran@gmail.com</td>
                            <td>+91 9000000010</td>
                            <td><span class="status-active">Active</span></td>
                            <td>20 Feb 2026</td>
                            <td>
                                          <div class="action-buttons">
                                             <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#viewModal" title="View"><i class="bi bi-eye"></i></button>
                                             <button class="btn btn-sm btn-warning" title="Edit"><i class="bi bi-pencil-square"></i></button>
                                             <button class="btn btn-sm btn-danger" title="Delete"><i class="bi bi-trash3"></i></button>
                                          </div>
                                       </td>
                        </tr>
                        <tr>
                            <td>11</td>
                            <td>Arjun Patel</td>
                            <td>arjun@gmail.com</td>
                            <td>+91 9000000011</td>
                            <td><span class="status-inactive">Inactive</span></td>
                            <td>20 Feb 2026</td>
                            <td>
                                          <div class="action-buttons">
                                             <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#viewModal" title="View"><i class="bi bi-eye"></i></button>
                                             <button class="btn btn-sm btn-warning" title="Edit"><i class="bi bi-pencil-square"></i></button>
                                             <button class="btn btn-sm btn-danger" title="Delete"><i class="bi bi-trash3"></i></button>
                                          </div>
                                       </td>
                        </tr>
                        <tr>
                            <td>12</td>
                            <td>Neha Sharma</td>
                            <td>neha@gmail.com</td>
                            <td>+91 9000000012</td>
                            <td><span class="status-active">Active</span></td>
                            <td>20 Feb 2026</td>
                            <td>
                                          <div class="action-buttons">
                                             <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#viewModal" title="View"><i class="bi bi-eye"></i></button>
                                             <button class="btn btn-sm btn-warning" title="Edit"><i class="bi bi-pencil-square"></i></button>
                                             <button class="btn btn-sm btn-danger" title="Delete"><i class="bi bi-trash3"></i></button>
                                          </div>
                                       </td>
                        </tr>
                        <tr>
                            <td>13</td>
                            <td>Manish Gupta</td>
                            <td>manish@gmail.com</td>
                            <td>+91 9000000013</td>
                            <td><span class="status-active">Active</span></td>
                            <td>20 Feb 2026</td>
                            <td>
                                          <div class="action-buttons">
                                             <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#viewModal" title="View"><i class="bi bi-eye"></i></button>
                                             <button class="btn btn-sm btn-warning" title="Edit"><i class="bi bi-pencil-square"></i></button>
                                             <button class="btn btn-sm btn-danger" title="Delete"><i class="bi bi-trash3"></i></button>
                                          </div>
                                       </td>
                        </tr>
                        <tr>
                            <td>14</td>
                            <td>Pooja Verma</td>
                            <td>pooja@gmail.com</td>
                            <td>+91 9000000014</td>
                            <td><span class="status-inactive">Inactive</span></td>
                            <td>20 Feb 2026</td>
                            <td>
                                          <div class="action-buttons">
                                             <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#viewModal" title="View"><i class="bi bi-eye"></i></button>
                                             <button class="btn btn-sm btn-warning" title="Edit"><i class="bi bi-pencil-square"></i></button>
                                             <button class="btn btn-sm btn-danger" title="Delete"><i class="bi bi-trash3"></i></button>
                                          </div>
                                       </td>
                        </tr>
                        <tr>
                            <td>15</td>
                            <td>Aditya Singh</td>
                            <td>aditya@gmail.com</td>
                            <td>+91 9000000015</td>
                            <td><span class="status-active">Active</span></td>
                            <td>20 Feb 2026</td>
                            <td>
                                          <div class="action-buttons">
                                             <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#viewModal" title="View"><i class="bi bi-eye"></i></button>
                                             <button class="btn btn-sm btn-warning" title="Edit"><i class="bi bi-pencil-square"></i></button>
                                             <button class="btn btn-sm btn-danger" title="Delete"><i class="bi bi-trash3"></i></button>
                                          </div>
                                       </td>
                        </tr>
                        <tr>
                            <td>16</td>
                            <td>Kavita Joshi</td>
                            <td>kavita@gmail.com</td>
                            <td>+91 9000000016</td>
                            <td><span class="status-active">Active</span></td>
                            <td>20 Feb 2026</td>
                            <td>
                                          <div class="action-buttons">
                                             <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#viewModal" title="View"><i class="bi bi-eye"></i></button>
                                             <button class="btn btn-sm btn-warning" title="Edit"><i class="bi bi-pencil-square"></i></button>
                                             <button class="btn btn-sm btn-danger" title="Delete"><i class="bi bi-trash3"></i></button>
                                          </div>
                                       </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        
        </div><!-- ================= KANBAN VIEW ================= -->
         <div id="kanbanView">
            <div class="row g-4">
                <!-- 16 Static Cards -->
                <div class="col-lg-3 col-md-6">
                    <div class="glass-card-new">
                        <center><img class="avatar mb-3" src="https://i.pravatar.cc/150?img=1"></center>
                        <h6>Rahul Sharma</h6>
                        <p>rahul@gmail.com</p><span class="status-active">Active</span>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="glass-card-new">
                        <center><img class="avatar mb-3" src="https://i.pravatar.cc/150?img=2"></center>
                        <h6>Amit Verma</h6>
                        <p>amit@gmail.com</p><span class="status-inactive">Inactive</span>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="glass-card-new">
                        <center><img class="avatar mb-3" src="https://i.pravatar.cc/150?img=3"></center>
                        <h6>Sneha Patel</h6>
                        <p>sneha@gmail.com</p><span class="status-active">Active</span>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="glass-card-new">
                        <center><img class="avatar mb-3" src="https://i.pravatar.cc/150?img=4"></center>
                        <h6>Riya Singh</h6>
                        <p>riya@gmail.com</p><span class="status-active">Active</span>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="glass-card-new">
                        <center><img class="avatar mb-3" src="https://i.pravatar.cc/150?img=5"></center>
                        <h6>Vikram Yadav</h6>
                        <p>vikram@gmail.com</p><span class="status-inactive">Inactive</span>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="glass-card-new">
                        <center><img class="avatar mb-3" src="https://i.pravatar.cc/150?img=6"></center>
                        <h6>Priya Kapoor</h6>
                        <p>priya@gmail.com</p><span class="status-active">Active</span>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="glass-card-new">
                        <center><img class="avatar mb-3" src="https://i.pravatar.cc/150?img=7"></center>
                        <h6>Karan Malhotra</h6>
                        <p>karan@gmail.com</p><span class="status-active">Active</span>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="glass-card-new">
                        <center><img class="avatar mb-3" src="https://i.pravatar.cc/150?img=8"></center>
                        <h6>Anjali Mehta</h6>
                        <p>anjali@gmail.com</p><span class="status-inactive">Inactive</span>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="glass-card-new">
                        <center><img class="avatar mb-3" src="https://i.pravatar.cc/150?img=9"></center>
                        <h6>Rohit Jain</h6>
                        <p>rohit@gmail.com</p><span class="status-active">Active</span>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="glass-card-new">
                        <center><img class="avatar mb-3" src="https://i.pravatar.cc/150?img=10"></center>
                        <h6>Simran Kaur</h6>
                        <p>simran@gmail.com</p><span class="status-active">Active</span>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="glass-card-new">
                        <center><img class="avatar mb-3" src="https://i.pravatar.cc/150?img=11"></center>
                        <h6>Arjun Patel</h6>
                        <p>arjun@gmail.com</p><span class="status-inactive">Inactive</span>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="glass-card-new">
                       <center> <img class="avatar mb-3" src="https://i.pravatar.cc/150?img=12"></center>
                        <h6>Neha Sharma</h6>
                        <p>neha@gmail.com</p><span class="status-active">Active</span>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="glass-card-new">
                        <center><img class="avatar mb-3" src="https://i.pravatar.cc/150?img=13"></center>
                        <h6>Manish Gupta</h6>
                        <p>manish@gmail.com</p><span class="status-active">Active</span>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="glass-card-new">
                       <center>  <img class="avatar mb-3" src="https://i.pravatar.cc/150?img=14"></center>
                        <h6>Pooja Verma</h6>
                        <p>pooja@gmail.com</p><span class="status-inactive">Inactive</span>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="glass-card-new">
                       <center> <img class="avatar mb-3" src="https://i.pravatar.cc/150?img=15"></center>
                        <h6>Aditya Singh</h6>
                        <p>aditya@gmail.com</p><span class="status-active">Active</span>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="glass-card-new">
                       <center> <img class="avatar mb-3" src="https://i.pravatar.cc/150?img=16"></center>
                        <h6>Kavita Joshi</h6>
                        <p>kavita@gmail.com</p><span class="status-active">Active</span>
                    </div>
                </div>
            </div>
            <div class="row g-4 mt-3">
               <div class="text-center col-lg-12">
                  <a class="btn btn-primary"><i class="bi bi-arrow-clockwise"></i> Load More</a>
               </div>
            </div>
        </div>
    <script>
    function showTable(){
       document.getElementById("tableView").style.display="block";
       document.getElementById("kanbanView").style.display="none";
    }
    function showKanban(){
       document.getElementById("tableView").style.display="none";
       document.getElementById("kanbanView").style.display="block";
    }
    </script>

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

