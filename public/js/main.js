document.addEventListener('DOMContentLoaded', function() {
    var ctx = document.getElementById('carStatusChart');
    if (ctx) {
        var carStatusChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: car_status_labels,
                datasets: [{
                    label: '# of Cars',
                    data: car_status_data,
                    backgroundColor: [
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(255, 159, 64, 0.2)'
                    ],
                    borderColor: [
                        'rgba(75, 192, 192, 1)',
                        'rgba(255, 159, 64, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Car Availability'
                    }
                }
            }
        });
    }
});