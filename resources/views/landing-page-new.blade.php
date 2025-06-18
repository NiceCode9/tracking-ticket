<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Tracking Faktur - Rumah Sakit</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.29.0/feather.min.js"></script>
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .glass-effect {
            backdrop-filter: blur(16px);
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .card-shadow {
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }
    </style>
</head>

<body class="min-h-screen gradient-bg">
    <!-- Header -->
    <header class="relative z-10 px-6 py-4">
        <div class="max-w-7xl mx-auto">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
                        <i data-feather="activity" class="w-6 h-6 text-white"></i>
                    </div>
                    <div>
                        <h1 class="text-white font-bold text-xl">RS Tracking</h1>
                        <p class="text-white text-opacity-80 text-sm">Sistem Pelacakan Faktur</p>
                    </div>
                </div>
                <div class="hidden md:flex items-center space-x-6 text-white text-opacity-90">
                    <a href="#" class="hover:text-white transition-colors">Beranda</a>
                    <a href="#" class="hover:text-white transition-colors">Bantuan</a>
                    <a href="#" class="hover:text-white transition-colors">Kontak</a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="relative z-10 px-6 py-12">
        <div class="max-w-4xl mx-auto">
            <!-- Hero Section -->
            <div class="text-center mb-12 animate-fade-in">
                <h2 class="text-4xl md:text-5xl font-bold text-white mb-4">
                    Lacak Status Faktur Anda
                </h2>
                <p class="text-xl text-white text-opacity-90 mb-8 max-w-2xl mx-auto">
                    Dapatkan informasi real-time mengenai status pembayaran dan jadwal faktur distributor obat
                </p>
            </div>

            <!-- Search Section -->
            <div class="glass-effect rounded-2xl p-8 mb-8 animate-slide-up">
                <div class="max-w-md mx-auto">
                    <label for="faktur-search" class="block text-white text-lg font-semibold mb-4 text-center">
                        Masukkan Nomor Faktur
                    </label>
                    <div class="relative">
                        <input type="text" id="faktur-search" placeholder="Contoh: FAK-2024-001"
                            class="w-full px-6 py-4 bg-white bg-opacity-20 border border-white border-opacity-30 rounded-xl text-white placeholder-white placeholder-opacity-70 focus:outline-none focus:ring-2 focus:ring-white focus:ring-opacity-50 focus:border-transparent transition-all">
                        <button onclick="searchFaktur()"
                            class="absolute right-2 top-2 bottom-2 px-6 bg-white bg-opacity-20 hover:bg-opacity-30 rounded-lg transition-all flex items-center justify-center">
                            <i data-feather="search" class="w-5 h-5 text-white"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Loading State -->
            <div id="loading" class="hidden text-center py-8">
                <div class="inline-flex items-center space-x-2 text-white">
                    <div
                        class="animate-spin rounded-full h-6 w-6 border-2 border-white border-opacity-30 border-t-white">
                    </div>
                    <span>Mencari data faktur...</span>
                </div>
            </div>

            <!-- Results Section -->
            <div id="results" class="hidden space-y-6">
                <!-- Distributor Info Card -->
                <div class="bg-white rounded-2xl card-shadow p-8 animate-slide-up">
                    <div class="flex items-center mb-6">
                        <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center mr-4">
                            <i data-feather="building" class="w-6 h-6 text-blue-600"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">Informasi Distributor</h3>
                            <p class="text-gray-600">Detail perusahaan distributor</p>
                        </div>
                    </div>

                    <div class="grid md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <div>
                                <label class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Nama
                                    Distributor</label>
                                <p id="distributor-name" class="text-lg font-semibold text-gray-900">PT. Kimia Farma
                                    Distribusi</p>
                            </div>
                            <div>
                                <label
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Alamat</label>
                                <p id="distributor-address" class="text-gray-700 leading-relaxed">Jl. Veteran No. 123,
                                    Surabaya, Jawa Timur 60175</p>
                            </div>
                        </div>
                        <div class="space-y-4">
                            <div>
                                <label
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide">WhatsApp</label>
                                <div class="flex items-center space-x-2">
                                    <p id="distributor-whatsapp" class="text-lg font-semibold text-gray-900">+62
                                        812-3456-7890</p>
                                    <button onclick="contactWhatsApp()"
                                        class="p-2 bg-green-100 hover:bg-green-200 rounded-lg transition-colors">
                                        <i data-feather="message-circle" class="w-4 h-4 text-green-600"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="pt-4">
                                <button onclick="contactWhatsApp()"
                                    class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-6 rounded-xl transition-colors flex items-center justify-center space-x-2">
                                    <i data-feather="phone" class="w-5 h-5"></i>
                                    <span>Hubungi via WhatsApp</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Faktur Details Card -->
                <div class="bg-white rounded-2xl card-shadow p-8 animate-slide-up">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center mr-4">
                                <i data-feather="file-text" class="w-6 h-6 text-purple-600"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-900">Detail Faktur</h3>
                                <p class="text-gray-600">Informasi lengkap faktur</p>
                            </div>
                        </div>
                        <div id="status-badge"
                            class="px-4 py-2 rounded-full text-sm font-semibold bg-blue-100 text-blue-800">
                            Terjadwal
                        </div>
                    </div>

                    <div class="grid md:grid-cols-2 gap-8">
                        <div class="space-y-6">
                            <div>
                                <label class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Nomor
                                    Faktur</label>
                                <p id="faktur-number" class="text-lg font-semibold text-gray-900">FAK-2024-001</p>
                            </div>
                            <div>
                                <label class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Tanggal
                                    Faktur</label>
                                <p id="faktur-date" class="text-gray-700">15 Maret 2024</p>
                            </div>
                            <div>
                                <label class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Tanggal Jatuh
                                    Tempo</label>
                                <p id="due-date" class="text-gray-700 font-semibold">30 Maret 2024</p>
                            </div>
                        </div>
                        <div class="space-y-6">
                            <div>
                                <label
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Nominal</label>
                                <p id="faktur-amount" class="text-2xl font-bold text-gray-900">Rp 25.750.000</p>
                            </div>
                            <div>
                                <label class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Tanggal
                                    Tanda
                                    Terima</label>
                                <p id="receipt-date" class="text-gray-700">16 Maret 2024</p>
                            </div>
                            <div>
                                <label class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Status
                                    Pembayaran</label>
                                <p id="payment-status" class="text-lg font-semibold text-blue-600">Terjadwal</p>
                            </div>
                        </div>
                    </div>

                    <!-- Timeline -->
                    {{-- <div class="mt-8 pt-8 border-t border-gray-200">
                        <h4 class="text-lg font-semibold text-gray-900 mb-6">Timeline Status</h4>
                        <div class="space-y-4">
                            <div class="flex items-center space-x-4">
                                <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                                <div class="flex-1">
                                    <p class="font-semibold text-gray-900">Faktur Diterima</p>
                                    <p class="text-sm text-gray-600">16 Maret 2024 - Faktur telah diterima dan
                                        diverifikasi</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-4">
                                <div class="w-3 h-3 bg-blue-500 rounded-full animate-pulse-soft"></div>
                                <div class="flex-1">
                                    <p class="font-semibold text-gray-900">Pembayaran Terjadwal</p>
                                    <p class="text-sm text-gray-600">Status saat ini - Menunggu jadwal pembayaran</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-4">
                                <div class="w-3 h-3 bg-gray-300 rounded-full"></div>
                                <div class="flex-1">
                                    <p class="font-semibold text-gray-400">Pembayaran Selesai</p>
                                    <p class="text-sm text-gray-400">Menunggu proses pembayaran</p>
                                </div>
                            </div>
                        </div>
                    </div> --}}

                    <div class="mt-8 pt-8 border-t border-gray-200">
                        <h4 class="text-lg font-semibold text-gray-900 mb-6">Timeline Status</h4>
                        <div class="space-y-4" id="timeline">

                        </div>
                    </div>

                </div>
            </div>

            <!-- No Results -->
            <div id="no-results" class="hidden text-center py-12">
                <div class="max-w-md mx-auto glass-effect rounded-2xl p-8">
                    <div
                        class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i data-feather="search" class="w-5 h-5 text-white"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-white mb-2">Faktur Tidak Ditemukan</h3>
                    <p class="text-white text-opacity-80">Periksa kembali nomor faktur yang Anda masukkan</p>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="relative z-10 px-6 py-8 mt-16">
        <div class="max-w-4xl mx-auto text-center">
            <div class="glass-effect rounded-xl p-6">
                <p class="text-white text-opacity-90 mb-2">© 2024 Rumah Sakit Tracking System</p>
                <p class="text-white text-opacity-70 text-sm">Sistem pelacakan faktur untuk distributor obat</p>
            </div>
        </div>
    </footer>

    <script>
        // Initialize Feather icons
        feather.replace();

        // Sample data - in real application, this would come from Laravel backend
        // const sampleData = {
        //     'FAK-2024-001': {
        //         distributor: {
        //             name: 'PT. Kimia Farma Distribusi',
        //             address: 'Jl. Veteran No. 123, Surabaya, Jawa Timur 60175',
        //             whatsapp: '+62 812-3456-7890'
        //         },
        //         faktur: {
        //             no_faktur: 'FAK-2024-001',
        //             tgl_faktur: '2024-03-15',
        //             tgl_jatuh_tempo: '2024-03-30',
        //             tgl_tanda_terima: '2024-03-16',
        //             nominal: 25750000,
        //             status: '1'
        //         }
        //     },
        //     'FAK-2024-002': {
        //         distributor: {
        //             name: 'PT. Pharma Distribusi Indonesia',
        //             address: 'Jl. Raya Darmo No. 45, Surabaya, Jawa Timur 60223',
        //             whatsapp: '+62 811-2345-6789'
        //         },
        //         faktur: {
        //             no_faktur: 'FAK-2024-002',
        //             tgl_faktur: '2024-03-10',
        //             tgl_jatuh_tempo: '2024-03-25',
        //             tgl_tanda_terima: '2024-03-11',
        //             nominal: 18500000,
        //             status: '3'
        //         }
        //     }
        // };

        // const statusLabels = {
        //     '0': {
        //         label: 'Belum Terjadwal',
        //         class: 'bg-gray-100 text-gray-800'
        //     },
        //     '1': {
        //         label: 'Terjadwal',
        //         class: 'bg-blue-100 text-blue-800'
        //     },
        //     '2': {
        //         label: 'Jadwal Ulang',
        //         class: 'bg-yellow-100 text-yellow-800'
        //     },
        //     '3': {
        //         label: 'Terbayar',
        //         class: 'bg-green-100 text-green-800'
        //     }
        // };

        function searchFaktur() {
            const query = document.getElementById('faktur-search').value.trim();
            const loading = document.getElementById('loading');
            const results = document.getElementById('results');
            const noResults = document.getElementById('no-results');

            // Hide all sections
            results.classList.add('hidden');
            noResults.classList.add('hidden');

            if (!query) {
                return;
            }

            // Show loading
            loading.classList.remove('hidden');

            // Make API call
            fetch(`/api/faktur/search?no_faktur=${encodeURIComponent(query)}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Faktur tidak ditemukan');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        displayResults(data.data);
                    } else {
                        throw new Error(data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    loading.classList.add('hidden');
                    noResults.classList.remove('hidden');
                    noResults.querySelector('p').textContent = error.message || 'Terjadi kesalahan saat mencari faktur';
                });
        }

        function displayResults(data) {
            // Update distributor info
            document.getElementById('distributor-name').textContent = data.distributor.name;
            document.getElementById('distributor-address').textContent = data.distributor.address;
            document.getElementById('distributor-whatsapp').textContent = data.distributor.whatsapp;

            // Update faktur info
            document.getElementById('faktur-number').textContent = data.faktur.no_faktur;
            document.getElementById('faktur-date').textContent = formatDate(data.faktur.tgl_faktur);
            document.getElementById('due-date').textContent = formatDate(data.faktur.tgl_jatuh_tempo);
            document.getElementById('receipt-date').textContent = data.faktur.tgl_tanda_terima ? formatDate(data.faktur
                .tgl_tanda_terima) : '-';
            document.getElementById('faktur-amount').textContent = formatCurrency(data.faktur.nominal);

            // Update status
            const statusLabels = {
                '0': {
                    label: 'Belum Terjadwal',
                    class: 'bg-gray-100 text-gray-800'
                },
                '1': {
                    label: 'Terjadwal',
                    class: 'bg-blue-100 text-blue-800'
                },
                '2': {
                    label: 'Jadwal Ulang',
                    class: 'bg-yellow-100 text-yellow-800'
                },
                '3': {
                    label: 'Terbayar',
                    class: 'bg-green-100 text-green-800'
                }
            };

            const status = statusLabels[data.faktur.status] || {
                label: 'Unknown',
                class: 'bg-gray-100 text-gray-800'
            };
            const statusBadge = document.getElementById('status-badge');
            const paymentStatus = document.getElementById('payment-status');

            statusBadge.className = `px-4 py-2 rounded-full text-sm font-semibold ${status.class}`;
            statusBadge.textContent = status.label;
            paymentStatus.textContent = status.label;
            paymentStatus.className = `text-lg font-semibold ${getStatusColor(data.faktur.status)}`;

            // Show results
            document.getElementById('results').classList.remove('hidden');
            document.getElementById('loading').classList.add('hidden');

            // Store current whatsapp for contact function
            window.currentWhatsApp = data.distributor.whatsapp;

            updateTimeline(data.faktur);
        }

        function updateTimeline(fakturData) {
            const timeline = document.getElementById('timeline');
            timeline.innerHTML = '';

            if (!fakturData.logs || fakturData.logs.length === 0) {
                // Fallback jika tidak ada log
                const fallbackElement = document.createElement('div');
                fallbackElement.className = 'text-center py-4 text-gray-500';
                fallbackElement.textContent = 'Belum ada riwayat status';
                timeline.appendChild(fallbackElement);
                return;
            }

            // Urutkan log dari yang terlama ke terbaru untuk timeline
            const sortedLogs = [...fakturData.logs].reverse();

            sortedLogs.forEach((log, index) => {
                const isCurrent = index === sortedLogs.length - 1;
                const isCompleted = index < sortedLogs.length - 1;

                const logElement = document.createElement('div');
                logElement.className = 'flex items-start space-x-4';

                let statusClass = '';
                if (isCurrent) {
                    statusClass = 'bg-blue-500 animate-pulse-soft';
                } else if (isCompleted) {
                    statusClass = 'bg-green-500';
                } else {
                    statusClass = 'bg-gray-300';
                }

                logElement.innerHTML = `
            <div class="flex flex-col items-center">
                <div class="w-3 h-3 ${statusClass} rounded-full mt-1.5"></div>
                ${index < sortedLogs.length - 1 ? '<div class="w-px h-full bg-gray-200 my-1"></div>' : ''}
            </div>
            <div class="flex-1 pb-4">
                <div class="flex justify-between items-start">
                    <p class="font-semibold ${isCurrent ? 'text-gray-900' : 'text-gray-700'}">
                        ${log.status_label}
                    </p>
                    <p class="text-xs text-gray-500">
                        ${formatDateTime(log.created_at)}
                    </p>
                </div>
                <p class="text-sm ${isCurrent ? 'text-gray-600' : 'text-gray-500'} mt-1">
                    ${log.keterangan || 'Tidak ada keterangan'}
                </p>
                ${log.user ? `<p class="text-xs text-gray-400 mt-1">Oleh: ${log.user}</p>` : ''}
            </div>
        `;

                timeline.appendChild(logElement);
            });
        }

        function formatDateTime(dateTimeString) {
            const options = {
                day: 'numeric',
                month: 'short',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            };
            return new Date(dateTimeString).toLocaleDateString('id-ID', options);
        }

        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('id-ID', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        }

        function formatCurrency(amount) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(amount);
        }

        function getStatusColor(status) {
            const colors = {
                '0': 'text-gray-600',
                '1': 'text-blue-600',
                '2': 'text-yellow-600',
                '3': 'text-green-600'
            };
            return colors[status] || 'text-gray-600';
        }

        function contactWhatsApp() {
            if (window.currentWhatsApp) {
                const message = encodeURIComponent('Halo, saya ingin menanyakan tentang status faktur.');
                const whatsappUrl = `https://wa.me/${window.currentWhatsApp.replace(/[^0-9]/g, '')}?text=${message}`;
                window.open(whatsappUrl, '_blank');
            }
        }

        // Allow search on Enter key
        document.getElementById('faktur-search').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                searchFaktur();
            }
        });

        // Demo search on page load
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('faktur-search').value = 'FAK-2024-001';
        });
    </script>
</body>

</html>
