<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Detect dark mode
    const isDarkMode = document.body.classList.contains('bg-dark');
    const legendColor = isDarkMode ? '#e2e8f0' : '#334155';
    const gridColor = isDarkMode ? '#2d3748' : 'rgba(0, 0, 0, 0.05)';
    const tickColor = isDarkMode ? '#94a3b8' : '#64748b';

    // User Growth Chart
    const userGrowthData = @json($userGrowthData);
    const growthLabels = userGrowthData.map(item => new Date(item.date).toLocaleDateString('en-US', { month: 'short', day: 'numeric' }));
    const growthCounts = userGrowthData.map(item => item.count);

    const userGrowthCtx = document.getElementById('userGrowthChart').getContext('2d');
    new Chart(userGrowthCtx, {
        type: 'bar',
        data: {
            labels: growthLabels,
            datasets: [{
                label: 'New Users',
                data: growthCounts,
                backgroundColor: 'rgba(0, 159, 177, 0.8)',
                borderColor: '#009fb1',
                borderWidth: 2,
                borderRadius: 8,
                barThickness: 40
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            aspectRatio: 1.2,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        font: { size: 16 },
                        stepSize: 1,
                        color: tickColor
                    },
                    grid: {
                        color: gridColor
                    }
                },
                x: {
                    ticks: {
                        font: { size: 16 },
                        color: tickColor
                    },
                    grid: {
                        display: false
                    }
                }
            },
            plugins: {
                legend: {
                    labels: {
                        font: { size: 16 },
                        color: legendColor
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    titleFont: { size: 14 },
                    bodyFont: { size: 14 }
                }
            }
        }
    });
    //test commit
    // Role Distribution Chart
    const roleData = @json($roleDistribution);
    const roleDistributionCtx = document.getElementById('roleDistributionChart').getContext('2d');
    new Chart(roleDistributionCtx, {
        type: 'doughnut',
        data: {
            labels: roleData.map(item => item.role),
            datasets: [{
                data: roleData.map(item => item.count),
                backgroundColor: ['#77dd77', '#D1C700', '#F53838'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            aspectRatio: 0.9,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        font: { size: 16 },
                        padding: 25,
                        color: legendColor
                    }
                }
            }
        }
    });
</script>
