document.addEventListener('DOMContentLoaded', () => {
    const popupBox = document.getElementById('popupBox');

    const nameDisplay = document.getElementById('nameDisplay');
    const replayedDisplay = document.getElementById('replayedDisplay');
    const genreDisplay = document.getElementById('genreDisplay');
    const releaseDateDisplay = document.getElementById('releaseDateDisplay');
    const developerDisplay = document.getElementById('developerDisplay');
    const platformDisplay = document.getElementById('platformDisplay');
    const engineDisplay = document.getElementById('engineDisplay');
    const ratingDisplay = document.getElementById('ratingDisplay');

    // Päivämäärän muotoilufunktio
    function formatDateToFinnish(dateString) {
        if (!dateString) return "Ei valittu";
        const [year, month, day] = dateString.split("-");
        return `${day}.${month}.${year}`;
    }

    document.getElementById('submitButton').addEventListener('click', function () {
        // Hae kenttien arvot
        const nameValue = document.getElementById('name').value;
        const replayedValue = document.getElementById('replayed').checked ? 'Kyllä' : 'Ei';
        const genreValue = document.querySelector('input[name="genre"]:checked')?.value || "Ei valittu";
        
        // Muunna päivämäärä muotoon pp.kk.vvvv
        const releaseDateValue = formatDateToFinnish(document.getElementById('releaseDate').value);
        
        const developerValue = document.getElementById('developer').value;
        const platformValue = document.getElementById('platform').value;
        const engineValue = document.getElementById('engine').value;
        
        // Lisää Metacritic-arvosanaan "/100"
        const ratingValue = document.getElementById('review').value;
        const formattedRating = ratingValue ? `${ratingValue}/100` : "Ei arvosanaa";

        // Aseta arvot popup-ruutuun
        nameDisplay.textContent = nameValue;
        replayedDisplay.textContent = replayedValue;
        genreDisplay.textContent = genreValue;
        releaseDateDisplay.textContent = releaseDateValue;
        developerDisplay.textContent = developerValue;
        platformDisplay.textContent = platformValue;
        engineDisplay.textContent = engineValue;
        ratingDisplay.textContent = formattedRating;

        // Näytä popup-ruutu
        popupBox.style.display = 'block';
    });
});
