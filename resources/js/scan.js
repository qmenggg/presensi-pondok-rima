import { BrowserMultiFormatReader } from '@zxing/browser';

/**
 * ZXing QR Scanner - Livewire Compatible Version
 * Designed to work with Alpine.js and Livewire components
 */

// Global variables untuk ZXing scanner
window.zxingScanner = null;
window.zxingCameraId = null;
window.zxingScanning = false;
window.zxingRestartTimeout = null;

/**
 * Alpine.js Component untuk ZXing QR Scanner
 * Kompatibel dengan Livewire
 */
window.zxingQrScanner = function () {
    return {
        cameraActive: false,
        cameraStatus: 'Memuat kamera...',
        scanning: false,

        /**
         * Initialize Scanner (dipanggil dari x-init)
         */
        initScanner() {
            console.log("🔧 Initializing ZXing scanner...");

            try {
                // Buat instance BrowserMultiFormatReader
                window.zxingScanner = new BrowserMultiFormatReader();
                console.log("✅ ZXing Scanner instance created");

                // Mulai kamera setelah delay singkat
                setTimeout(() => {
                    this.startCamera();
                }, 500);

            } catch (e) {
                console.error("❌ ZXing Scanner init error:", e);
                this.cameraStatus = 'Gagal memuat kamera';
            }
        },

        /**
         * Start Camera
         */
        async startCamera() {
            if (!window.zxingScanner) {
                console.error("❌ Scanner not initialized");
                return;
            }

            try {
                // Cek apakah kamera sedang berjalan
                if (window.zxingScanning) {
                    console.log("⚠️ Camera already running");
                    this.cameraActive = true;
                    this.cameraStatus = 'Kamera Aktif';
                    return;
                }

                // Get available cameras
                const devices = await BrowserMultiFormatReader.listVideoInputDevices();

                if (!devices || devices.length === 0) {
                    this.cameraStatus = 'Kamera tidak ditemukan';
                    console.error("❌ No cameras found");
                    return;
                }

                // Pilih kamera belakang atau kamera pertama
                const backCamera = devices.find(device =>
                    device.label.toLowerCase().includes('back') ||
                    device.label.toLowerCase().includes('rear') ||
                    device.label.toLowerCase().includes('environment')
                );

                window.zxingCameraId = backCamera ? backCamera.deviceId : devices[0].deviceId;
                console.log("📷 Using camera:", backCamera ? backCamera.label : devices[0].label);

                this.cameraStatus = 'Memulai kamera...';

                // Start decoding
                await window.zxingScanner.decodeFromVideoDevice(
                    window.zxingCameraId,
                    'qr-reader',  // Sesuai dengan ID di Blade
                    (result, error) => {
                        if (result) {
                            this.onScanSuccess(result.getText());
                        }
                        // Silent error handling untuk NotFoundException
                    }
                );

                console.log("✅ ZXing Camera started successfully");
                window.zxingScanning = true;
                this.cameraActive = true;
                this.cameraStatus = 'Kamera Aktif';

                // Sembunyikan placeholder
                const placeholder = document.getElementById('camera-placeholder');
                if (placeholder) placeholder.style.display = 'none';

            } catch (err) {
                console.error("❌ Failed to start camera:", err);
                this.cameraActive = false;
                this.cameraStatus = 'Gagal memulai kamera';

                // Retry setelah 2 detik
                setTimeout(() => this.startCamera(), 2000);
            }
        },

        /**
         * Callback ketika QR berhasil di-scan
         */
        onScanSuccess(code) {
            // Prevent multiple scans
            if (this.scanning) {
                console.log("⚠️ Already processing scan, ignoring...");
                return;
            }

            this.scanning = true;
            console.log("📷 QR Detected:", code);

            // Clear any pending restart
            if (window.zxingRestartTimeout) {
                clearTimeout(window.zxingRestartTimeout);
                window.zxingRestartTimeout = null;
            }

            // Stop camera
            this.stopCamera();

            // Kirim ke Livewire component
            const component = document.querySelector('[wire\\:id]');
            if (!component) {
                console.error("❌ Livewire component not found");
                this.scanning = false;
                this.startCamera();
                return;
            }

            // Call Livewire scan method
            window.Livewire.find(component.getAttribute('wire:id'))
                .call('scan', code)
                .then(result => {
                    console.log("✅ Livewire result:", result);

                    // Play sound feedback
                    const successSound = document.getElementById('success-sound');
                    const errorSound = document.getElementById('error-sound');

                    if (result?.status === 'success') {
                        if (successSound) {
                            successSound.currentTime = 0;
                            successSound.play().catch(() => { });
                        }
                    } else {
                        if (errorSound) {
                            errorSound.currentTime = 0;
                            errorSound.play().catch(() => { });
                        }
                    }
                })
                .catch(err => {
                    console.error("❌ Livewire error:", err);
                    const errorSound = document.getElementById('error-sound');
                    if (errorSound) {
                        errorSound.currentTime = 0;
                        errorSound.play().catch(() => { });
                    }
                })
                .finally(() => {
                    // Restart camera setelah delay
                    window.zxingRestartTimeout = setTimeout(() => {
                        this.scanning = false;
                        console.log("🔄 Restarting camera...");
                        this.startCamera();
                    }, 2000);
                });
        },

        /**
         * Stop Camera
         */
        async stopCamera() {
            if (!window.zxingScanner) return;

            try {
                window.zxingScanner.reset();
                window.zxingScanning = false;
                this.cameraActive = false;
                this.cameraStatus = 'Kamera berhenti';
                console.log("⏸️ Camera stopped");
            } catch (err) {
                console.error("❌ Failed to stop camera:", err);
            }
        },

        /**
         * Switch Camera
         */
        async switchCamera() {
            if (!window.zxingScanner) return;

            try {
                const devices = await BrowserMultiFormatReader.listVideoInputDevices();

                if (devices.length < 2) {
                    alert('Hanya ada 1 kamera tersedia');
                    return;
                }

                // Stop current camera
                await this.stopCamera();

                // Ganti ke kamera berikutnya
                const currentIndex = devices.findIndex(cam => cam.deviceId === window.zxingCameraId);
                const nextIndex = (currentIndex + 1) % devices.length;
                window.zxingCameraId = devices[nextIndex].deviceId;

                console.log("🔄 Switching to camera:", devices[nextIndex].label);

                // Start dengan kamera baru
                setTimeout(() => {
                    this.startCamera();
                }, 500);

            } catch (err) {
                console.error("❌ Switch camera failed:", err);
            }
        }
    }
};

/**
 * Cleanup saat Livewire navigating
 */
document.addEventListener('livewire:navigating', () => {
    console.log("🧹 Livewire navigating, cleaning up ZXing...");

    if (window.zxingScanner) {
        window.zxingScanner.reset();
    }

    if (window.zxingRestartTimeout) {
        clearTimeout(window.zxingRestartTimeout);
    }

    window.zxingScanning = false;
});

/**
 * Cleanup saat page unload
 */
window.addEventListener('beforeunload', () => {
    if (window.zxingScanner) {
        window.zxingScanner.reset();
    }
    if (window.zxingRestartTimeout) {
        clearTimeout(window.zxingRestartTimeout);
    }
});

console.log("✅ ZXing Livewire Scanner Module Loaded");
