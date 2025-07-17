const nonce = Convert2Webp.nonce;
const pluginsUrl = Convert2Webp.pluginsUrl;
const convertButton = document.getElementById('convert-button');
const imagesToBeConvertedInfo = document.querySelector(".imagesToBeConvertedInfo");
const loader = document.querySelector(".loader");

async function fetchPrivateData() {
    const resultsDiv = document.querySelector('.resultsDiv');
    const logDiv = document.querySelector('.logDiv');

    logDiv.innerHTML = "";
    convertButton.style.display = "none";
    imagesToBeConvertedInfo.style.display = "none";

    try {
        resultsDiv.innerHTML = "";
        resultsDiv.classList.add("inProgress");
        resultsDiv.textContent = "Converting...";
        loader.style.display = "block";

        const interval = setInterval(showLogData, 50);

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
        loader.style.display = "none";
        resultsDiv.innerHTML = "";
        resultsDiv.textContent = data.message;
        resultsDiv.classList.add("done");
        clearInterval(interval);
    } catch (error) {
        console.error('Error:', error.message);
    }
}

async function showLogData() {
    const logDiv = document.querySelector('.logDiv');

    const url = pluginsUrl + "/convert2webp/c2w.log";
    try {
        const response = await fetch(url, {cache: "no-store"});
        if (!response.ok) {
            throw new Error(`Response status: ${response.status}`);
        }

        let text = await response.text();
        logDiv.style.display = "block";
        logDiv.innerHTML = "";
        text = text.replace(/&quot;/gi, '')
            .replace(/\\\//gi, '/')
            .replace(/\n/gi, "<br>")
            .replace(
                /(Error.+details.)/,
                `<div class='c2wLogError'>$1</div>
            `);

        logDiv.innerHTML = text;

    } catch (error) {
        console.error(error.message);
    }
}

if (convertButton) {
    convertButton.addEventListener('click', fetchPrivateData);
}
