<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
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
                        stepSize: 1
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                },
                x: {
                    ticks: {
                        font: { size: 16 }
                    },
                    grid: {
                        display: false
                    }
                }
            },
            plugins: {
                legend: {
                    labels: {
                        font: { size: 16 }
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
                backgroundColor: ['#28a745', '#ffc107', '#dc3545'],
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
                        padding: 25
                    }
                }
            }
        }
    });
</script>
