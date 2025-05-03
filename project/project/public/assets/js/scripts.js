// Wait for the DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    // Mobile sidebar toggle
    const sidebarToggle = document.getElementById('sidebarToggle');
    const mobileSidebar = document.getElementById('mobileSidebar');
    const closeSidebar = document.getElementById('closeSidebar');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    
    if (sidebarToggle && mobileSidebar) {
        sidebarToggle.addEventListener('click', function() {
            mobileSidebar.classList.remove('hidden');
        });
        
        if (closeSidebar) {
            closeSidebar.addEventListener('click', function() {
                mobileSidebar.classList.add('hidden');
            });
        }
        
        if (sidebarOverlay) {
            sidebarOverlay.addEventListener('click', function() {
                mobileSidebar.classList.add('hidden');
            });
        }
    }
    
    // Initialize charts if they exist
    if (typeof echarts !== 'undefined') {
        initCharts();
    }
    
    // Add event listeners for dashboard functionality
    initDashboardEvents();
});

// Initialize dashboard charts
function initCharts() {
    // Clearance Status Chart
    const clearanceChartEl = document.getElementById('clearanceChart');
    if (clearanceChartEl) {
        const clearanceChart = echarts.init(clearanceChartEl);
        
        const clearanceOption = {
            tooltip: {
                trigger: 'item'
            },
            legend: {
                orient: 'vertical',
                left: 'left',
                textStyle: {
                    fontSize: 12
                }
            },
            series: [
                {
                    name: 'Clearance Status',
                    type: 'pie',
                    radius: ['40%', '70%'],
                    avoidLabelOverlap: false,
                    itemStyle: {
                        borderRadius: 10,
                        borderColor: '#fff',
                        borderWidth: 2
                    },
                    label: {
                        show: false,
                        position: 'center'
                    },
                    emphasis: {
                        label: {
                            show: true,
                            fontSize: '16',
                            fontWeight: 'bold'
                        }
                    },
                    labelLine: {
                        show: false
                    },
                    data: [
                        { value: 87, name: 'Cleared', itemStyle: { color: '#22c55e' } },
                        { value: 18, name: 'Pending Room Check', itemStyle: { color: '#facc15' } },
                        { value: 9, name: 'Unpaid Fees', itemStyle: { color: '#ef4444' } },
                        { value: 5, name: 'Missing Documents', itemStyle: { color: '#f97316' } }
                    ]
                }
            ]
        };
        
        clearanceChart.setOption(clearanceOption);
        
        // Handle resize
        window.addEventListener('resize', function() {
            clearanceChart.resize();
        });
    }
    
    // Payment Trends Chart
    const paymentChartEl = document.getElementById('paymentChart');
    if (paymentChartEl) {
        const paymentChart = echarts.init(paymentChartEl);
        
        const paymentOption = {
            tooltip: {
                trigger: 'axis',
                axisPointer: {
                    type: 'shadow'
                }
            },
            legend: {
                data: ['Fees Collected', 'Unpaid Fees']
            },
            grid: {
                left: '3%',
                right: '4%',
                bottom: '3%',
                containLabel: true
            },
            xAxis: [
                {
                    type: 'category',
                    data: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun']
                }
            ],
            yAxis: [
                {
                    type: 'value',
                    name: 'Amount ($)',
                    axisLabel: {
                        formatter: '${value}'
                    }
                }
            ],
            series: [
                {
                    name: 'Fees Collected',
                    type: 'bar',
                    itemStyle: {
                        color: '#4f46e5'
                    },
                    emphasis: {
                        focus: 'series'
                    },
                    data: [12500, 13200, 14800, 13900, 15600, 17200]
                },
                {
                    name: 'Unpaid Fees',
                    type: 'bar',
                    itemStyle: {
                        color: '#ef4444'
                    },
                    emphasis: {
                        focus: 'series'
                    },
                    data: [2800, 2500, 2100, 2400, 1900, 1500]
                }
            ]
        };
        
        paymentChart.setOption(paymentOption);
        
        // Handle resize
        window.addEventListener('resize', function() {
            paymentChart.resize();
        });
    }
}

// Initialize dashboard event listeners
function initDashboardEvents() {
    // Example: Search functionality for the residents table
    const searchInput = document.querySelector('input[placeholder="Search residents..."]');
    if (searchInput) {
        searchInput.addEventListener('keyup', function(e) {
        });
    }
    
    // Example: Filter button functionality
    const filterButton = document.querySelector('button i.ri-filter-3-line');
    if (filterButton) {
        filterButton.parentElement.addEventListener('click', function() {
            // Add filter functionality here
            console.log('Filter button clicked');
        });
    }
} 