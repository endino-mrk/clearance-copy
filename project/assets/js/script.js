document.addEventListener('DOMContentLoaded', function() {
    // Mobile sidebar toggle
    const sidebarToggle = document.getElementById('sidebarToggle');
    const mobileSidebar = document.getElementById('mobileSidebar');
    const closeSidebar = document.getElementById('closeSidebar');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    
    if (sidebarToggle && mobileSidebar && closeSidebar && sidebarOverlay) {
        sidebarToggle.addEventListener('click', function() {
            mobileSidebar.classList.remove('hidden');
        });
        
        closeSidebar.addEventListener('click', function() {
            mobileSidebar.classList.add('hidden');
        });
        
        sidebarOverlay.addEventListener('click', function() {
            mobileSidebar.classList.add('hidden');
        });
    }
    
    // Tab navigation
    const tabButtons = document.querySelectorAll('.tab-button');
    
    if (tabButtons.length > 0) {
        tabButtons.forEach(button => {
            button.addEventListener('click', function() {
                tabButtons.forEach(btn => {
                    btn.classList.remove('active');
                    btn.classList.add('text-gray-500', 'bg-gray-100');
                });
                
                this.classList.add('active');
                this.classList.remove('text-gray-500', 'bg-gray-100');
            });
        });
    }
    
    // Initialize charts if they exist
    if (typeof echarts !== 'undefined') {
        // Payment Status Chart
        const paymentStatusChart = document.getElementById('paymentStatusChart');
        if (paymentStatusChart) {
            const chart = echarts.init(paymentStatusChart);
            const chartData = JSON.parse(paymentStatusChart.getAttribute('data-chart') || '{}');
            
            const paymentStatusOption = {
                tooltip: {
                    trigger: 'item',
                    formatter: '{a} <br/>{b}: {c} ({d}%)'
                },
                legend: {
                    orient: 'horizontal',
                    bottom: 0,
                    data: chartData.legends || ['Paid', 'Partially Paid', 'Unpaid', 'Overdue']
                },
                series: [
                    {
                        name: 'Payment Status',
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
                                fontSize: '18',
                                fontWeight: 'bold'
                            }
                        },
                        labelLine: {
                            show: false
                        },
                        data: chartData.data || [
                            { value: 0, name: 'Paid', itemStyle: { color: '#10B981' } },
                            { value: 0, name: 'Partially Paid', itemStyle: { color: '#3B82F6' } },
                            { value: 0, name: 'Unpaid', itemStyle: { color: '#FBBF24' } },
                            { value: 0, name: 'Overdue', itemStyle: { color: '#EF4444' } }
                        ]
                    }
                ]
            };
            chart.setOption(paymentStatusOption);
            
            // Handle window resize
            window.addEventListener('resize', function() {
                chart.resize();
            });
        }
        
        // Clearance Chart
        const clearanceChart = document.getElementById('clearanceChart');
        if (clearanceChart) {
            const chart = echarts.init(clearanceChart);
            
            const clearanceOption = {
                animation: false,
                tooltip: {
                    trigger: 'axis',
                    backgroundColor: 'rgba(255, 255, 255, 0.8)',
                    textStyle: {
                        color: '#1f2937'
                    }
                },
                legend: {
                    data: ['Cleared', 'Pending', 'Rejected'],
                    bottom: 0
                },
                grid: {
                    left: '3%',
                    right: '4%',
                    bottom: '15%',
                    top: '3%',
                    containLabel: true
                },
                xAxis: {
                    type: 'category',
                    boundaryGap: false,
                    data: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                    axisLine: {
                        lineStyle: {
                            color: '#d1d5db'
                        }
                    },
                    axisLabel: {
                        color: '#1f2937'
                    }
                },
                yAxis: {
                    type: 'value',
                    axisLine: {
                        lineStyle: {
                            color: '#d1d5db'
                        }
                    },
                    axisLabel: {
                        color: '#1f2937'
                    },
                    splitLine: {
                        lineStyle: {
                            color: '#f3f4f6'
                        }
                    }
                },
                series: [
                    {
                        name: 'Cleared',
                        type: 'line',
                        stack: 'Total',
                        smooth: true,
                        lineStyle: {
                            width: 2,
                            color: 'rgba(87, 181, 231, 1)'
                        },
                        areaStyle: {
                            color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                                {
                                    offset: 0,
                                    color: 'rgba(87, 181, 231, 0.3)'
                                },
                                {
                                    offset: 1,
                                    color: 'rgba(87, 181, 231, 0.1)'
                                }
                            ])
                        },
                        emphasis: {
                            focus: 'series'
                        },
                        data: [45, 52, 58, 65, 72, 87]
                    },
                    {
                        name: 'Pending',
                        type: 'line',
                        stack: 'Total',
                        smooth: true,
                        lineStyle: {
                            width: 2,
                            color: 'rgba(251, 191, 114, 1)'
                        },
                        areaStyle: {
                            color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                                {
                                    offset: 0,
                                    color: 'rgba(251, 191, 114, 0.3)'
                                },
                                {
                                    offset: 1,
                                    color: 'rgba(251, 191, 114, 0.1)'
                                }
                            ])
                        },
                        emphasis: {
                            focus: 'series'
                        },
                        data: [35, 42, 38, 45, 38, 32]
                    },
                    {
                        name: 'Rejected',
                        type: 'line',
                        stack: 'Total',
                        smooth: true,
                        lineStyle: {
                            width: 2,
                            color: 'rgba(252, 141, 98, 1)'
                        },
                        areaStyle: {
                            color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                                {
                                    offset: 0,
                                    color: 'rgba(252, 141, 98, 0.3)'
                                },
                                {
                                    offset: 1,
                                    color: 'rgba(252, 141, 98, 0.1)'
                                }
                            ])
                        },
                        emphasis: {
                            focus: 'series'
                        },
                        data: [10, 8, 12, 9, 7, 5]
                    }
                ]
            };
            chart.setOption(clearanceOption);
            
            // Handle window resize
            window.addEventListener('resize', function() {
                chart.resize();
            });
        }
        
        // Payment Trend Chart
        const paymentChart = document.getElementById('paymentChart');
        if (paymentChart) {
            const chart = echarts.init(paymentChart);
            
            const paymentOption = {
                animation: false,
                tooltip: {
                    trigger: 'axis',
                    backgroundColor: 'rgba(255, 255, 255, 0.8)',
                    textStyle: {
                        color: '#1f2937'
                    }
                },
                legend: {
                    data: ['Rental Fees', 'Fines'],
                    bottom: 0
                },
                grid: {
                    left: '3%',
                    right: '4%',
                    bottom: '15%',
                    top: '3%',
                    containLabel: true
                },
                xAxis: {
                    type: 'category',
                    data: ['Nov', 'Dec', 'Jan', 'Feb', 'Mar', 'Apr'],
                    axisLine: {
                        lineStyle: {
                            color: '#d1d5db'
                        }
                    },
                    axisLabel: {
                        color: '#1f2937'
                    }
                },
                yAxis: {
                    type: 'value',
                    axisLine: {
                        lineStyle: {
                            color: '#d1d5db'
                        }
                    },
                    axisLabel: {
                        color: '#1f2937',
                        formatter: '${value}'
                    },
                    splitLine: {
                        lineStyle: {
                            color: '#f3f4f6'
                        }
                    }
                },
                series: [
                    {
                        name: 'Rental Fees',
                        type: 'bar',
                        barWidth: '40%',
                        itemStyle: {
                            color: 'rgba(87, 181, 231, 1)',
                            borderRadius: [4, 4, 0, 0]
                        },
                        emphasis: {
                            itemStyle: {
                                color: 'rgba(87, 181, 231, 0.8)'
                            }
                        },
                        data: [12500, 13200, 14500, 15200, 15800, 16500]
                    },
                    {
                        name: 'Fines',
                        type: 'bar',
                        barWidth: '40%',
                        itemStyle: {
                            color: 'rgba(252, 141, 98, 1)',
                            borderRadius: [4, 4, 0, 0]
                        },
                        emphasis: {
                            itemStyle: {
                                color: 'rgba(252, 141, 98, 0.8)'
                            }
                        },
                        data: [1200, 950, 1450, 1050, 850, 750]
                    }
                ]
            };
            chart.setOption(paymentOption);
            
            // Handle window resize
            window.addEventListener('resize', function() {
                chart.resize();
            });
        }
    }
});