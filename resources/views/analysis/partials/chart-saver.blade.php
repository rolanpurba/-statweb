{{--
    Plugin Chart.js untuk:
    1) memberi latar putih pada canvas (supaya PNG tidak transparan), dan
    2) mengirim gambar grafik (PNG) ke server SATU kali setelah grafik selesai digambar,
       supaya export PDF dari halaman hasil MAUPUN dari riwayat selalu punya grafik.

    Pakai di view grafik:
      - @include partial ini SETELAH <script src="...chart.js"> dan SEBELUM script grafik
      - di config new Chart(...): plugins: window.statwebChartPlugins || [],
    View yang meng-include harus punya variabel $latestResult (sudah dikirim controller).
--}}
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

        fetch(@json(route('analysis.chart.store', $latestResult->id, false)), {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': @json(csrf_token()),
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
