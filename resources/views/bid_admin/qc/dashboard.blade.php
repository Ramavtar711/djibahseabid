@include('bid_admin.qc.include.header')

@include('bid_admin.qc.include.side_menu')

<!-- Page Wrapper -->
         <div class="page-wrapper">
            <div class="content container-fluid">
               <div class="page-header">
    <div class="row align-items-center">
      <div class="col">
        <h1 class="page-title">Quality Control Dashboard</h1>
        <p class="text-white">Market Integrity & Auction Validation Center</p>
      </div>
      <div class="col-auto">
         <button class="btn btn-primary btn-toggle me-2" onclick="showTable()"><i class="bi bi-box"></i> {{ $kpis['pending']['count'] ?? 0 }} lot pending</button> 
       
      </div>
    </div>
  </div>
              
              <section class="kpi-row">
            @php
                $pendingDelta = $kpis['pending']['delta'] ?? 0;
                $approvedDelta = $kpis['approved']['delta'] ?? 0;
                $rejectedDelta = $kpis['rejected']['delta'] ?? 0;
                $tempDelta = $kpis['temp_alerts']['delta'] ?? 0;
                $missingDelta = $kpis['missing_docs']['delta'] ?? 0;
                $auctionDelta = $kpis['auctions']['delta'] ?? 0;

                $trendText = function ($delta) {
                    if ($delta === 0) {
                        return 'Steady';
                    }

                    $arrow = $delta > 0 ? '↑' : '↓';
                    return $arrow . ' ' . abs($delta) . ' vs yesterday';
                };
            @endphp

            <div class="glass-card-setup kpi-card kpi-pending">
                <span class="label">Pending Lots</span>
                <span class="value">{{ $kpis['pending']['count'] ?? 0 }}</span>
                <span class="trend text-warning">{{ $trendText($pendingDelta) }}</span>
            </div>
            <div class="glass-card-setup kpi-card kpi-approved">
                <span class="label">Approved Today</span>
                <span class="value">{{ $kpis['approved']['count'] ?? 0 }}</span>
                <span class="trend text-success">{{ $trendText($approvedDelta) }}</span>
            </div>
            <div class="glass-card-setup kpi-card kpi-rejected">
                <span class="label">Rejected Today</span>
                <span class="value">{{ $kpis['rejected']['count'] ?? 0 }}</span>
                <span class="trend text-danger">{{ $trendText($rejectedDelta) }}</span>
            </div>
            <div class="glass-card-setup kpi-card kpi-temp">
                <span class="label">Temp Alerts</span>
                <span class="value">{{ $kpis['temp_alerts']['count'] ?? 0 }}</span>
                <span class="trend text-warning">{{ $trendText($tempDelta) }}</span>
            </div>
            <div class="glass-card-setup kpi-card kpi-missing">
                <span class="label">Missing Docs</span>
                <span class="value">{{ $kpis['missing_docs']['count'] ?? 0 }}</span>
                <span class="trend text-info">{{ $trendText($missingDelta) }}</span>
            </div>
            <div class="glass-card-setup kpi-card kpi-scheduled">
                <span class="label">Auctions (7 Days)</span>
                <span class="value">{{ $kpis['auctions']['count'] ?? 0 }}</span>
                <span class="trend text-success">{{ $trendText($auctionDelta) }}</span>
            </div>
        </section>

        <div class="row g-4 mb-5">
            <div class="col-12">
                <div class="glass-card-setup p-0">
                    <div class="p-3 border-bottom border-white-10 d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold text-white">Lot Validation Queue</h6>
                       
                    </div>

                    <div class="table-container p-3 table-responsive">
                        <table class="submitted-table">
                            <thead>
                                 <tr>
                                    <th>Lot</th><th>Seller</th><th>Species</th><th>Condition</th><th>Declared</th><th>Verified</th><th>Temp</th><th>Docs</th><th>Action</th>
                                </tr>
                            </thead>
                                                      <tbody>
                               @forelse($lots ?? [] as $lot)
                                   @php
                                       $tempValue = $lot->qc_temperature ?? $lot->temperature_value;
                                       $tempDisplay = $lot->qc_temperature !== null
                                           ? number_format((float) $lot->qc_temperature, 1) . '°C'
                                           : ($lot->storage_temperature ?? '—');
                                       $tempClass = 't-green';
                                       if ($tempValue !== null && $tempValue > 4) {
                                           $tempClass = 't-red';
                                       } elseif ($tempValue !== null && $tempValue > 0) {
                                           $tempClass = 't-orange';
                                       }
                           
                                       $condition = ($tempValue !== null && $tempValue <= 0) ? 'Frozen' : 'Fresh';
                                       $conditionIcon = $condition === 'Frozen' ? 'bi-snow text-info' : 'bi-check2-circle text-success';
                                       $conditionBadge = $condition === 'Frozen' ? 'badge-primary' : 'badge-success';
                           
                                       $declaredWeight = $lot->quantity;
                                       $verifiedWeight = $lot->qc_actual_weight ?? $lot->qc_verified_boxes;
                           
                                       $docsComplete = $lot->qc_documents_verified
                                           || ! empty($lot->documents_path)
                                           || ! empty($lot->health_certificate_path);
                                   @endphp
                                   <tr>
                                       <td>{{ $lot->id }}</td>
                                       <td>{{ $lot->seller_name ?? 'Unknown Seller' }}</td>
                                       <td>
                                           <span class="status-pill {{ $conditionBadge }}">{{ $lot->species ?? 'Unknown' }}</span>
                                       </td>
                                       <td><i class="bi {{ $conditionIcon }}"></i> {{ $condition }}</td>
                                       <td>{{ $declaredWeight !== null ? number_format((float) $declaredWeight, 0) . ' KG' : '—' }}</td>
                                       <td>{{ $verifiedWeight !== null ? number_format((float) $verifiedWeight, 0) . ' KG' : '—' }}</td>
                                       <td><span class="temp-pill {{ $tempClass }}">{{ $tempDisplay }}</span></td>
                                       <td>{{ $docsComplete ? 'Complete' : 'Missing' }}</td>
                                       <td>
                                           <a href="{{ route('qc.lot-subimitted-details', ['lot' => $lot->id]) }}" class="btn btn-sm btn-primary">
                                               <i class="bi bi-eye text-white"></i>
                                           </a>
                                       </td>
                                   </tr>
                               @empty
                                   <tr>
                                       <td colspan="9" class="text-center text-white-50">No lots available.</td>
                                   </tr>
                               @endforelse
                           </tbody>
                        </table>
                    </div>
                    <div class="px-3 pb-3 d-flex justify-content-end">
                        {{ $lots->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
           </div>
    
 
        </div>

              
              
            </div>
         </div>
         <!-- /Page Wrapper -->

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" data-cfasync="false"></script>

    
      <!-- jQuery -->
      <!-- Bootstrap Core JS -->
      <!-- Feather Icon JS -->
      <!-- Slimscroll JS -->
      <!-- Theme Settings JS -->
      <!-- Custom JS -->
      <script>

// Volume Bar
new Chart(document.getElementById("volumeChart"),{
type:"bar",
data:{
labels:["Mon","Tue","Wed","Thu","Fri","Sat","Sun"],
datasets:[{
data:[1500,1400,1300,1600,1700,2000,2200],
backgroundColor:"#4e73df",
borderRadius:6
}]
},
options:{plugins:{legend:{display:false}}}
});

// Transaction Mixed Chart
new Chart(document.getElementById("transactionChart"),{
data:{
labels:["Mon","Tue","Wed","Thu","Fri","Sat","Sun"],
datasets:[
{
type:"bar",
data:[800,1000,1500,1200,2000,2500,3000],
backgroundColor:"#a0c4ff",
borderRadius:6
},
{
type:"line",
data:[600,900,1300,1500,2200,2700,3200],
borderColor:"#4e73df",
tension:0.4,
fill:false
}
]
},
options:{plugins:{legend:{display:false}}}
});

// Revenue Line
new Chart(document.getElementById("revenueChart"),{
type:"line",
data:{
labels:["Mon","Tue","Wed","Thu","Fri","Sat","Sun"],
datasets:[{
data:[2000,2300,2500,2800,3000,3400,3700],
borderColor:"#4e73df",
backgroundColor:"rgba(78,115,223,0.1)",
fill:true,
tension:0.4
}]
},
options:{
plugins:{legend:{display:false}},
scales:{x:{display:false},y:{display:false}}
}
});

</script>
<script>
    function scrollSlider(amount) {
        const slider = document.getElementById('auctionSlider');
        slider.scrollBy({
            left: amount,
            behavior: 'smooth'
        });
    }
</script>
<script>
    // Initialize Lucide Icons
    lucide.createIcons();

    // 1. Revenue Chart (Line)
    new Chart(document.getElementById('revenueChart'), {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'Revenue (USD)',
                data: [400000, 550000, 480000, 700000, 850000, 1200000],
                borderColor: '#4338ca',
                tension: 0.4,
                fill: true,
                backgroundColor: 'rgba(67, 56, 202, 0.1)'
            }]
        },
        options: { maintainAspectRatio: false, plugins: { legend: { display: false } } }
    });

    // 2. Species Chart (Doughnut)
    new Chart(document.getElementById('speciesChart'), {
        type: 'doughnut',
        data: {
            labels: ['Tuna', 'Salmon', 'Cod', 'Shrimp'],
            datasets: [{
                data: [45, 25, 20, 10],
                backgroundColor: ['#4338ca', '#10b981', '#f59e0b', '#64748b']
            }]
        },
        options: { maintainAspectRatio: false }
    });

    // 3. Fresh vs Frozen Chart (Bar)
    new Chart(document.getElementById('priceEvolutionChart'), {
        type: 'bar',
        data: {
            labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
            datasets: [
                { label: 'Fresh', data: [12, 15, 14, 18], backgroundColor: '#10b981' },
                { label: 'Frozen', data: [8, 9, 7, 10], backgroundColor: '#64748b' }
            ]
        },
        options: { maintainAspectRatio: false }
    });
</script>

@include('bid_admin.qc.include.footer')


