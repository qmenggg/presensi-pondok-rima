export const initChartOne = () => {
    const chartElement = document.querySelector('#chartOne');
    if (!chartElement) return;

    // data statis contoh (7 hari)
    const chartData = {
        labels: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
        hadir: [140, 138, 145, 142, 141, 139, 145],
    };

    const chartOneOptions = {
        series: [
            {
                name: "Hadir",
                data: chartData.hadir,
            }
        ],
        colors: ["#22c55e"],

        chart: {
            fontFamily: "Outfit, sans-serif",
            type: "bar",
            height: 320,   // lebih pendek
            width: "100%",
            toolbar: { show: false },
        },

        plotOptions: {
            bar: {
                horizontal: false,
                columnWidth: "40%",
                borderRadius: 6,
                borderRadiusApplication: "end",
            },
        },

        dataLabels: { enabled: false },

        stroke: {
            show: true,
            width: 3,
            colors: ["transparent"],
        },

        xaxis: {
            categories: chartData.labels,
            axisBorder: { show: false },
            axisTicks: { show: false },
            labels: {
                style: {
                    fontSize: "14px",
                }
            }
        },

        legend: { show: false },

        yaxis: {
            labels: {
                style: { fontSize: "14px" }
            }
        },

        grid: {
            padding: {
                left: 10,
                right: 10,
            },
            yaxis: {
                lines: { show: true },
            },
        },

        fill: { opacity: 1 },

        tooltip: {
            x: { show: true },
            y: {
                formatter: (val) => val + " hadir",
            },
        },
    };

    const chart = new ApexCharts(chartElement, chartOneOptions);
    chart.render();
    return chart;
};

export default initChartOne;
