/**
 * ZXing QR Scanner - Livewire Compatible (CDN Version)
 * NO BUILD REQUIRED - Uses unpkg CDN for ZXing
 * 
 * Usage in Blade:
 * 1. Add CDN: <script src="https://unpkg.com/@zxing/library@latest"></script>
 * 2. Add this file: <script src="{{ asset('js/zxing-scanner.js') }}"></script>
 * 3. Use Alpine: x-data="zxingQrScanner()"
 */

/**
 * Alpine.js Component untuk ZXing QR Scanner
 * Menggunakan ZXing dari window.ZXing (CDN)
 */
window.zxingQrScanner = function () {
    return {
        cameraActive: false,
        cameraStatus: 'Memuat kamera...',
        scanning: false,
        codeReader: null,
        selectedDeviceId: null,
        restartTimeout: null,

        /**
         * Initialize Scanner
         */
        async initScanner() {
            console.log("🔧 Initializing ZXing scanner (CDN)...");

            try {
                // Check if ZXing library is loaded
                if (typeof window.ZXing === 'undefined') {
                    console.error("❌ ZXing library not loaded!");
                    this.cameraStatus = 'Library tidak ditemukan';
                    return;
                }

                // Create BrowserMultiFormatReader instance
                this.codeReader = new window.ZXing.BrowserMultiFormatReader();
                console.log("✅ ZXing Scanner instance created");

                // Start camera after short delay
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
            if (!this.codeReader) {
                console.error("❌ Scanner not initialized");
                return;
            }

            try {
                // Prevent duplicate starts
                if (this.scanning === true) {
                    console.log("⚠️ Camera already running");
                    return;
                }

                // Reset scanner first to ensure clean state
                try {
                    this.codeReader.reset();
                } catch (e) {
                    // Ignore reset errors
                }

                // Get available video devices
                const devices = await this.codeReader.listVideoInputDevices();

                if (!devices || devices.length === 0) {
                    this.cameraStatus = 'Kamera tidak ditemukan';
                    console.error("❌ No cameras found");
                    return;
                }

                // Select back camera if available
                const backCamera = devices.find(device =>
                    device.label.toLowerCase().includes('back') ||
                    device.label.toLowerCase().includes('rear') ||
                    device.label.toLowerCase().includes('environment')
                );

                this.selectedDeviceId = backCamera ? backCamera.deviceId : devices[0].deviceId;
                console.log("📷 Using camera:", backCamera ? backCamera.label : devices[0].label);

                this.cameraStatus = 'Memulai kamera...';

                // Start decoding
                await this.codeReader.decodeFromVideoDevice(
                    this.selectedDeviceId,
                    'qr-reader',  // Video element ID
                    (result, error) => {
                        if (result) {
                            this.onScanSuccess(result.getText());
                        }
                        // Silent error handling for NotFoundException
                    }
                );

                console.log("✅ ZXing Camera started successfully");
                this.scanning = true;
                this.cameraActive = true;
                this.cameraStatus = 'Kamera Aktif';

                // Hide placeholder
                const placeholder = document.getElementById('camera-placeholder');
                if (placeholder) placeholder.style.display = 'none';

            } catch (err) {
                console.error("❌ Failed to start camera:", err);
                this.cameraActive = false;
                this.cameraStatus = 'Gagal memulai kamera';

                // Retry after 2 seconds
                setTimeout(() => this.startCamera(), 2000);
            }
        },

        /**
         * Handle Scan Success
         */
        onScanSuccess(code) {
            if (this.scanning === 'processing') {
                console.log("⚠️ Already processing scan, ignoring...");
                return;
            }

            this.scanning = 'processing';
            console.log("📷 QR Detected:", code);

            // Clear any pending restart
            if (this.restartTimeout) {
                clearTimeout(this.restartTimeout);
                this.restartTimeout = null;
            }

            // Stop camera
            this.stopCamera();

            // Send to Livewire
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
                    // Restart camera after delay
                    this.restartTimeout = setTimeout(() => {
                        this.scanning = false;
                        console.log("🔄 Restarting camera...");
                        this.startCamera();
                    }, 2000);
                });
        },

        /**
         * Stop Camera
         */
        stopCamera() {
            if (!this.codeReader) return;

            try {
                this.codeReader.reset();
                // Don't reset scanning flag here - let the caller handle it
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
            if (!this.codeReader) return;

            try {
                const devices = await this.codeReader.listVideoInputDevices();

                if (devices.length < 2) {
                    alert('Hanya ada 1 kamera tersedia');
                    return;
                }

                // Stop current camera
                this.stopCamera();

                // Switch to next camera
                const currentIndex = devices.findIndex(cam => cam.deviceId === this.selectedDeviceId);
                const nextIndex = (currentIndex + 1) % devices.length;
                this.selectedDeviceId = devices[nextIndex].deviceId;

                console.log("🔄 Switching to camera:", devices[nextIndex].label);

                // Start with new camera
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
 * Cleanup on Livewire navigation
 */
document.addEventListener('livewire:navigating', () => {
    console.log("🧹 Livewire navigating, cleaning up ZXing...");
    // Cleanup will be handled by Alpine destroy
});

/**
 * Cleanup on page unload
 */
window.addEventListener('beforeunload', () => {
    // Cleanup will be handled automatically
});

console.log("✅ ZXing Livewire Scanner (CDN) Module Loaded");
