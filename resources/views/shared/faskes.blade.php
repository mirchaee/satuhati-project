<x-layout>

@php
    $isSuami = Auth::user()->role === 'suami';

    $primaryColor = $isSuami ? 'text-blue-500' : 'text-softPink';
    $primaryBg = $isSuami ? 'bg-blue-500' : 'bg-softPink';
    $primaryBorder = $isSuami ? 'border-blue-200' : 'border-pink-200';
@endphp

<div class="p-6">

    <!-- HEADER -->
    <div class="flex justify-between items-end mb-6">
        <div>
            <h1 class="text-3xl font-black text-deepBlue">
                Faskes Terdekat
            </h1>
            <p class="text-sm text-mutedGray mt-1">
                Temukan fasilitas kesehatan terdekat dari lokasi Anda
            </p>
        </div>

        <div class="text-right">
            <p class="text-[10px] font-bold text-mutedGray uppercase">Live Location</p>
            <p class="text-sm font-bold {{ $primaryColor }}">GPS Active</p>
        </div>
    </div>

    <!-- GRID -->
    <div class="grid grid-cols-12 gap-6">

        <!-- MAP -->
        <div class="col-span-8">
            <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm overflow-hidden relative z-0">
                <div id="map" class="h-[500px] w-full"></div>
            </div>
        </div>

        <!-- SIDE PANEL -->
        <div class="col-span-4 space-y-4">

            <!-- INFO BOX -->
            <div class="bg-white p-5 rounded-[2rem] border border-gray-100 shadow-sm">
                <h3 class="font-bold text-deepBlue mb-3">📍 Status Lokasi</h3>

                <div class="text-sm text-mutedGray">
                    Sistem akan menampilkan faskes berdasarkan posisi Anda saat ini.
                </div>

                <div class="mt-3 text-xs font-bold {{ $primaryColor }}">
                    ✔ GPS Aktif • ✔ Tracking ON
                </div>
            </div>

            <!-- EMERGENCY -->
            <div class="bg-white p-5 rounded-[2rem] border border-gray-100 shadow-sm">
                <h3 class="font-bold text-deepBlue mb-3">🚨 Kontak Darurat</h3>

                <div class="space-y-2 text-sm">

                    <div class="flex justify-between">
                        <span>Ambulans</span>
                        <span class="font-bold">119</span>
                    </div>

                    <div class="flex justify-between">
                        <span>Darurat Umum</span>
                        <span class="font-bold">112</span>
                    </div>

                    <div class="flex justify-between">
                        <span>Puskesmas</span>
                        <span class="font-bold {{ $primaryColor }}">Lokasi terdekat</span>
                    </div>

                </div>
            </div>

        </div>
    </div>

</div>

<!-- LEAFLET -->
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<script>
let faskes = @json($faskes);

/* DISTANCE */
function getDistance(lat1, lon1, lat2, lon2) {
    const R = 6371;
    const dLat = (lat2 - lat1) * Math.PI / 180;
    const dLon = (lon2 - lon1) * Math.PI / 180;

    const a =
        Math.sin(dLat/2) * Math.sin(dLat/2) +
        Math.cos(lat1 * Math.PI / 180) *
        Math.cos(lat2 * Math.PI / 180) *
        Math.sin(dLon/2) * Math.sin(dLon/2);

    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    return R * c;
}

/* MAP */
let map = L.map('map').setView([-7.6, 111.5], 13);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap'
}).addTo(map);

/* GPS */
if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(function(position) {

        let userLat = position.coords.latitude;
        let userLng = position.coords.longitude;

        map.setView([userLat, userLng], 14);

        L.marker([userLat, userLng])
            .addTo(map)
            .bindPopup("📍 Lokasi Anda")
            .openPopup();

        faskes.forEach(f => {

            let distance = getDistance(
                userLat,
                userLng,
                f.latitude,
                f.longitude
            );

            L.marker([f.latitude, f.longitude])
                .addTo(map)
                .bindPopup(`
                    <b>${f.name}</b><br>
                    📞 ${f.phone ?? '-'}<br>
                    📍 ${distance.toFixed(2)} km<br><br>
                    <a href="https://www.google.com/maps?q=${f.latitude},${f.longitude}" target="_blank">
                        Navigasi
                    </a>
                `);
        });

    });
}
</script>

</x-layout>

<style>
#map {
    z-index: 0 !important;
    position: relative;
}
</style>