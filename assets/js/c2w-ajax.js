const nonce = Convert2Webp.nonce;
const pluginDirUrl = Convert2Webp.pluginDirUrl;
const convertButton = document.getElementById('convert-button');
const imagesToBeConvertedInfo = document.querySelector(".imagesToBeConvertedInfo");
const loader = document.querySelector(".loader");
const resultsDiv = document.querySelector('.resultsDiv');
const logDiv = document.querySelector('.logDiv');
let interval;

async function startConversion() {
    logDiv.innerHTML = "";
    convertButton.style.display = "none";
    imagesToBeConvertedInfo.style.display = "none";

    try {
        resultsDiv.innerHTML = "";
        resultsDiv.classList.add("inProgress");
        resultsDiv.textContent = "Converting...";
        loader.style.display = "block";

        interval = setInterval(showLogData, 1000);

        const response = await fetch('/wp-json/c2w/v1/convert', {
            method: 'GET',
            headers: {
                'X-WP-Nonce': nonce,
            },
            credentials: 'same-origin',
        });

        if (!response.ok) {
            throw new Error('Request failed with status ' + response.status);
        }

        const data = await response.json();
        console.log(data);
        showProcessingFinished();
        stopFetchingProgressData();
    } catch (error) {
        console.error('Error:', error.message);
    }
}

async function showLogData() {
    const url = pluginDirUrl + "/c2w.log.json";
    try {
        const response = await fetch(url, { cache: "no-store" });
        if (!response.ok) {
            throw new Error(`Response status: ${response.status}`);
        }

        let res = await response.json();
        logDiv.style.display = "block";
        logDiv.innerHTML = "";

        res = res.reverse();
        res.forEach(item => {
            para = document.createElement("p");
            para.classList.add(item.type);
            para.textContent = item.message;
            logDiv.appendChild(para);
        });

        updateProgressBar(res);

        const finished = res.filter(item => item.message === "Conversion finished.").lenght;

        if (finished) {
            showProcessingFinished();
            stopFetchingProgressData();
        };
    } catch (error) {
        console.error(error.message);
    }
}

if (convertButton) {
    convertButton.addEventListener('click', startConversion);
}

function stopFetchingProgressData() {
    clearInterval(interval);
}

function showProcessingFinished() {
    resultsDiv.innerHTML = "";
    resultsDiv.textContent = "Image processing finished.";
    resultsDiv.classList.add("done");
}

function updateProgressBar(res) {
    const latest = res.find(item =>
        item.currentImageNumber && item.imageCount
    );

    if (latest) {
        const currentImageNumber = latest.currentImageNumber;
        const imageCount = latest.imageCount;
        const percentDone = Math.round((currentImageNumber / imageCount) * 100);
        document.querySelector(".bar").style.width = percentDone + "%";
    }
}
