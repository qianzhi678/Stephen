//Stephen
// Fetch data from the API and limit to 248 countries
async function fetchCountries() {
    try {
        const response = await fetch("https://restcountries.com/v3.1/all");
        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
        }
        const data = await response.json();

        // Limit the data to 248 countries
        const limitedData = data.slice(0, 248);
        return limitedData;
    } catch (error) {
        console.error("Error fetching countries:", error);
        return [];
    }
}

// Display countries in the table and update the count
function displayCountries(countries) {
    const tableBody = document.querySelector("#countryTable tbody");
    const summary = document.getElementById("summary");

    // Clear the table body
    tableBody.innerHTML = "";

    // Update the summary with the number of countries displayed
    summary.innerHTML = `<strong>Countries of the World</strong><br>`;
    summary.innerHTML += `${countries.length} countries retrieved.<br><br>`;

    // Populate the table with country data
    countries.forEach(country => {
        const name = country.name.common || "N/A";
        const capital = country.capital ? country.capital[0] : "N/A";
        const population = country.population ? country.population.toLocaleString() : "N/A";
        const area = country.area ? country.area.toLocaleString() : "N/A";
        const currencies = country.currencies ? Object.values(country.currencies).map(currency => currency.name).join(", ") : "N/A";
        const flag = country.cca2 ? `<span class="flag-icon flag-icon-${country.cca2.toLowerCase()}"></span>` : "N/A";
        const region = country.region || "N/A";

        const row = document.createElement("tr");
        row.innerHTML = `
            <td>${flag}</td>
            <td>${name}</td>
            <td>${capital}</td>
            <td>${population}</td>
            <td>${area}</td>
            <td>${currencies}</td>
        `;
        tableBody.appendChild(row);
    });
}

// Apply filters and update the displayed count
function applyFilter() {
    const populationFilter = parseInt(document.getElementById('population').value) || 0;
    const areaFilter = parseInt(document.getElementById('area').value) || 0;
    const regionFilter = document.getElementById('region').value.toLowerCase();

    const filteredCountries = allCountries.filter(country => 
        country.population > populationFilter &&
        country.area > areaFilter &&
        (regionFilter === "" || country.region.toLowerCase().includes(regionFilter))
    );

    displayCountries(filteredCountries);
}

// Initialize
let allCountries = [];

async function initialize() {
    allCountries = await fetchCountries();
    displayCountries(allCountries);
}

initialize();