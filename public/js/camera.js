document.addEventListener('DOMContentLoaded', function () {
    const openCameraBtn = document.getElementById('openCameraBtn');
    const cameraContainer = document.getElementById('cameraContainer');
    const video = document.getElementById('video');
    const canvas = document.getElementById('canvas');
    const captureBtn = document.getElementById('captureBtn');
    const closeCameraBtn = document.getElementById('closeCameraBtn');
    const fotoInput = document.getElementById('Foto');
    let stream = null;

    if (!openCameraBtn) return;

    openCameraBtn.addEventListener('click', async function () {
        cameraContainer.style.display = 'block';
        openCameraBtn.style.display = 'none';
        try {
            stream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: { exact: "environment" } }
            });
        } catch (e) {
            // fallback ke kamera depan jika kamera belakang tidak tersedia
            stream = await navigator.mediaDevices.getUserMedia({ video: true });
        }
        video.srcObject = stream;
    });

    captureBtn.addEventListener('click', function () {
        // Ambil gambar dari video ke canvas
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);

        // Konversi canvas ke blob lalu ke File dan set ke input file
        canvas.toBlob(function (blob) {
            const file = new File([blob], 'camera-capture.jpg', { type: 'image/jpeg' });
            // Buat DataTransfer untuk set file ke input file
            const dt = new DataTransfer();
            dt.items.add(file);
            fotoInput.files = dt.files;
        }, 'image/jpeg', 0.95);

        // Sembunyikan kamera, tampilkan tombol ambil lagi
        stopCamera();
    });

    closeCameraBtn.addEventListener('click', function () {
        stopCamera();
    });

    function stopCamera() {
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
            stream = null;
        }
        video.srcObject = null;
        cameraContainer.style.display = 'none';
        openCameraBtn.style.display = 'inline-block';
    }
});