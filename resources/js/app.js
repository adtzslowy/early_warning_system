import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();

// ApexCharts (~450KB) hanya dipakai di dashboard. Dimuat sebagai chunk terpisah
// & async, sehingga:
//  - halaman lain (login/welcome) tak menanggung bebannya,
//  - main bundle jauh lebih kecil → masuk dashboard terasa jauh lebih ringan.
// Kode dashboard sudah menunggu window.ApexCharts (bootChart) lewat retry, jadi
// aman dimuat menyusul.
if (document.getElementById('telemetryChart') || document.getElementById('predictionChart')) {
    import('apexcharts').then(({ default: ApexCharts }) => {
        window.ApexCharts = ApexCharts;
    });
}
