
<script>
    window.statwebSendChart = function (chart) {
        if (chart.$statwebSent) return;
        chart.$statwebSent = true;

        let dataUrl;
        try {
            dataUrl = chart.canvas.toDataURL('image/png');
        } catch (e) {
            console.warn('Gagal membaca canvas grafik', e);
            return;
        }

        fetch(<?php echo json_encode(route('analysis.chart.store', $latestResult->id, false)) ?>, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': <?php echo json_encode(csrf_token(), 15, 512) ?>,
            },
            body: JSON.stringify({ chart_image: dataUrl }),
        }).catch(function (err) {
            console.warn('Gagal menyimpan gambar grafik', err);
        });
    };

    window.statwebChartPlugins = [
        {
            id: 'statwebWhiteBackground',
            beforeDraw: function (chart) {
                const ctx = chart.ctx;
                ctx.save();
                ctx.globalCompositeOperation = 'destination-over';
                ctx.fillStyle = '#ffffff';
                ctx.fillRect(0, 0, chart.width, chart.height);
                ctx.restore();
            },
        },
        {
            id: 'statwebSaveImage',
            // afterRender dipanggil Chart.js setelah animasi selesai (atau setelah render tanpa animasi).
            afterRender: function (chart) {
                if (chart.$statwebSent) return;
                clearTimeout(chart.$statwebTimer);
                chart.$statwebTimer = setTimeout(function () {
                    window.statwebSendChart(chart);
                }, 500);
            },
        },
    ];
</script>
<?php /**PATH C:\Users\LOQ\Herd\statweb\resources\views/analysis/partials/chart-saver.blade.php ENDPATH**/ ?>