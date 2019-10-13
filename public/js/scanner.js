/*

import QrScanner from '/js/lib/qr-scanner.min.js';

QrScanner.WORKER_PATH = '/js/lib/qr-scanner-worker.min.js';

const video = document.querySelector('video');
// const qrScanner = new QrScanner(videoElem, result => console.log('decoded qr code:', result));


// ####### Web Cam Scanning #######
QrScanner.hasCamera().then(hasCamera => console.log('camera found: ' + hasCamera));
const scanner = new QrScanner(video, result => setResult(result));
scanner.start();
// document.getElementById('inversion-mode-select').addEventListener('change', event => {
//     scanner.setInversionMode(event.target.value);
// });
*/


const code = jsQR(imageData, width, height, options?);

if (code) {
    console.log("Found QR code", code);
}

function setResult(result) {
    console.log(result);

    takeSnapshot();
}


function takeSnapshot(){

    var hidden_canvas = document.querySelector('canvas'),
        video = document.querySelector('video'),
        image = document.querySelector('#result'),

        // Get the exact size of the video element.
        width = video.videoWidth,
        height = video.videoHeight,

        // Context object for working with the canvas.
        context = hidden_canvas.getContext('2d');

    // Set the canvas to the same dimensions as the video.
    hidden_canvas.width = width;
    hidden_canvas.height = height;

    // Draw a copy of the current frame from the video on the canvas.
    context.drawImage(video, 0, 0, width, height);

    // Get an image dataURL from the canvas.
    var imageDataURL = hidden_canvas.toDataURL('image/png');

    // Set the dataURL as source of an image element, showing the captured photo.
    image.setAttribute('src', imageDataURL);

}
