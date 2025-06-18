<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faktur Tracking System</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .glass-effect {
            background: rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.18);
        }

        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .status-badge {
            transition: all 0.3s ease;
        }

        .status-badge:hover {
            transform: scale(1.05);
        }
    </style>
</head>

<body class="min-h-screen gradient-bg">
    <!-- Background Pattern -->
    <div class="absolute inset-0 opacity-10">
        <div class="absolute inset-0"
            style="background-image: radial-gradient(circle at 1px 1px, rgba(255,255,255,0.3) 1px, transparent 0); background-size: 20px 20px;">
        </div>
    </div>

    <!-- Header -->
    <header class="relative z-10 pt-8 pb-12">
        <div class="container mx-auto px-6">
            <div class="text-center animate-fade-in">
                <h1 class="text-5xl md:text-6xl font-bold text-white mb-4 tracking-tight">
                    Faktur <span class="text-yellow-300">Tracking</span>
                </h1>
                <p class="text-xl text-white/80 max-w-2xl mx-auto leading-relaxed">
                    Sistem pelacakan faktur yang memudahkan Anda mengelola dan memantau status pembayaran distributor
                </p>
            </div>
        </div>
    </header>

    <!-- Search Section -->
    <section class="relative z-10 pb-12">
        <div class="container mx-auto px-6">
            <div class="max-w-4xl mx-auto animate-slide-up">
                <div class="glass-effect rounded-2xl p-8 shadow-2xl">
                    <div class="flex flex-col md:flex-row gap-4">
                        <div class="flex-1">
                            <input type="text" id="searchInput" placeholder="Cari berdasarkan No. Faktur"
                                class="w-full px-6 py-4 rounded-xl border border-white/20 bg-white/10 text-white placeholder-white/60 focus:outline-none focus:ring-2 focus:ring-yellow-300 focus:border-transparent backdrop-blur-sm transition-all duration-300">
                        </div>
                        <button onclick="performSearch()"
                            class="px-8 py-4 bg-yellow-400 hover:bg-yellow-500 text-gray-900 font-semibold rounded-xl transition-all duration-300 transform hover:scale-105 hover:shadow-lg active:scale-95">
                            <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            Cari Faktur
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Results Section -->
    <section class="relative z-10 pb-12">
        <div class="container mx-auto px-6">
            <div id="searchResults" class="max-w-6xl mx-auto space-y-6"></div>
            <div id="noResults" class="max-w-4xl mx-auto text-center hidden">
                <div class="glass-effect rounded-2xl p-12">
                    <div class="text-white/60 mb-4">
                        <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                    </div>
                    <p class="text-xl text-white/80">Tidak ada faktur yang ditemukan</p>
                    <p class="text-white/60 mt-2">Coba gunakan kata kunci yang berbeda</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Sample Data Display (Initial State) -->
    <section class="relative z-10 pb-12" id="sampleData">
        <div class="container mx-auto px-6">
            <div class="max-w-6xl mx-auto">
                <div class="text-center mb-8">
                    <h2 class="text-3xl font-bold text-white mb-4">Contoh Data Faktur</h2>
                    <p class="text-white/70">Berikut adalah contoh tampilan data faktur dalam sistem</p>
                </div>
                <div class="space-y-6" id="sampleCards"></div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="relative z-10 py-8 mt-12">
        <div class="container mx-auto px-6 text-center">
            <p class="text-white/60">&copy; 2025 Faktur Tracking System. Dibuat dengan ❤️ menggunakan Laravel & Tailwind
                CSS</p>
        </div>
    </footer>

    <script>
        // Sample data based on the Faktur model
        const sampleFakturData = [{
                id: 1,
                no_faktur: 'FK-2025-001',
                tgl_faktur: '2025-06-01',
                tgl_jatuh_tempo: '2025-07-01',
                tgl_tanda_terima: '2025-06-03',
                nominal: 15000000,
                status: '1',
                distributor: {
                    nama: 'PT. Distribusi Nusantara',
                    alamat: 'Jl. Raya Industri No. 45, Surabaya, Jawa Timur',
                    whatsapp: '081234567890'
                }
            },
            {
                id: 2,
                no_faktur: 'FK-2025-002',
                tgl_faktur: '2025-06-05',
                tgl_jatuh_tempo: '2025-07-05',
                tgl_tanda_terima: null,
                nominal: 8500000,
                status: '0',
                distributor: {
                    nama: 'CV. Mitra Sejahtera',
                    alamat: 'Jl. Pemuda No. 123, Malang, Jawa Timur',
                    whatsapp: '081234567891'
                }
            },
            {
                id: 3,
                no_faktur: 'FK-2025-003',
                tgl_faktur: '2025-06-10',
                tgl_jatuh_tempo: '2025-07-10',
                tgl_tanda_terima: '2025-06-12',
                nominal: 22000000,
                status: '3',
                distributor: {
                    nama: 'PT. Global Trade Indonesia',
                    alamat: 'Jl. HR. Muhammad No. 78, Sidoarjo, Jawa Timur',
                    whatsapp: '081234567892'
                }
            },
            {
                id: 4,
                no_faktur: 'FK-2025-004',
                tgl_faktur: '2025-06-15',
                tgl_jatuh_tempo: '2025-07-15',
                tgl_tanda_terima: '2025-06-16',
                nominal: 12750000,
                status: '2',
                distributor: {
                    nama: 'UD. Berkah Mandiri',
                    alamat: 'Jl. Diponegoro No. 234, Gresik, Jawa Timur',
                    whatsapp: '081234567893'
                }
            }
        ];

        const statusLabels = {
            '0': {
                label: 'Belum Terjadwal',
                color: 'bg-gray-500'
            },
            '1': {
                label: 'Terjadwal',
                color: 'bg-blue-500'
            },
            '2': {
                label: 'Jadwal Ulang',
                color: 'bg-yellow-500'
            },
            '3': {
                label: 'Terbayar',
                color: 'bg-green-500'
            }
        };

        function formatCurrency(amount) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR'
            }).format(amount);
        }

        function formatDate(dateString) {
            if (!dateString) return '-';
            return new Date(dateString).toLocaleDateString('id-ID', {
                day: 'numeric',
                month: 'long',
                year: 'numeric'
            });
        }

        function createFakturCard(faktur) {
            const status = statusLabels[faktur.status];
            return `
                <div class="glass-effect rounded-2xl p-6 shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 animate-slide-up">
                    <div class="flex flex-col lg:flex-row gap-6">
                        <!-- Distributor Info -->
                        <div class="lg:w-1/3">
                            <div class="bg-white/10 rounded-xl p-4 h-full">
                                <h3 class="text-lg font-semibold text-white mb-3 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-yellow-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                    Distributor
                                </h3>
                                <div class="space-y-2">
                                    <p class="text-white font-medium">${faktur.distributor.nama}</p>
                                    <p class="text-white/70 text-sm flex items-start">
                                        <svg class="w-4 h-4 mr-2 mt-0.5 text-yellow-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        ${faktur.distributor.alamat}
                                    </p>
                                    <a href="https://wa.me/${faktur.distributor.whatsapp}" target="_blank" class="inline-flex items-center text-green-300 hover:text-green-200 transition-colors">
                                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488"/>
                                        </svg>
                                        ${faktur.distributor.whatsapp}
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Faktur Info -->
                        <div class="lg:w-2/3">
                            <div class="flex justify-between items-start mb-4">
                                <h3 class="text-xl font-bold text-white">Faktur ${faktur.no_faktur}</h3>
                                <span class="status-badge px-3 py-1 rounded-full text-xs font-semibold text-white ${status.color}">
                                    ${status.label}
                                </span>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-3">
                                    <div>
                                        <p class="text-white/60 text-sm">Tanggal Faktur</p>
                                        <p class="text-white font-medium">${formatDate(faktur.tgl_faktur)}</p>
                                    </div>
                                    <div>
                                        <p class="text-white/60 text-sm">Jatuh Tempo</p>
                                        <p class="text-white font-medium">${formatDate(faktur.tgl_jatuh_tempo)}</p>
                                    </div>
                                </div>
                                <div class="space-y-3">
                                    <div>
                                        <p class="text-white/60 text-sm">Tanda Terima</p>
                                        <p class="text-white font-medium">${formatDate(faktur.tgl_tanda_terima)}</p>
                                    </div>
                                    <div>
                                        <p class="text-white/60 text-sm">Nominal</p>
                                        <p class="text-white font-bold text-lg text-yellow-300">${formatCurrency(faktur.nominal)}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }

        function displayResults(data) {
            const resultsContainer = document.getElementById('searchResults');
            const noResultsContainer = document.getElementById('noResults');
            const sampleDataContainer = document.getElementById('sampleData');

            sampleDataContainer.style.display = 'none';

            if (data.length === 0) {
                resultsContainer.innerHTML = '';
                noResultsContainer.classList.remove('hidden');
            } else {
                noResultsContainer.classList.add('hidden');
                resultsContainer.innerHTML = data.map(faktur => createFakturCard(faktur)).join('');
            }
        }

        function performSearch() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase().trim();

            if (searchTerm === '') {
                document.getElementById('sampleData').style.display = 'block';
                document.getElementById('searchResults').innerHTML = '';
                document.getElementById('noResults').classList.add('hidden');
                return;
            }

            const filteredData = sampleFakturData.filter(faktur =>
                faktur.no_faktur.toLowerCase().includes(searchTerm) ||
                faktur.distributor.nama.toLowerCase().includes(searchTerm) ||
                faktur.distributor.alamat.toLowerCase().includes(searchTerm)
            );

            displayResults(filteredData);
        }

        // Initialize sample data display
        document.addEventListener('DOMContentLoaded', function() {
            const sampleCardsContainer = document.getElementById('sampleCards');
            sampleCardsContainer.innerHTML = sampleFakturData.map(faktur => createFakturCard(faktur)).join('');

            // Add enter key listener for search
            document.getElementById('searchInput').addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    performSearch();
                }
            });
        });
    </script>
</body>

</html>
