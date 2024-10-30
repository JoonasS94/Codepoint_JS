document.addEventListener('DOMContentLoaded', () => {
    const popupBox = document.getElementById('popupBox');

    const nameDisplay = document.getElementById('nameDisplay');
    const replayedDisplay = document.getElementById('replayedDisplay'); // Oikea ID
    const genreDisplay = document.getElementById('genreDisplay');
    const releaseYearDisplay = document.getElementById('releaseYearDisplay');
    const developerDisplay = document.getElementById('developerDisplay');
    const platformDisplay = document.getElementById('platformDisplay');
    const engineDisplay = document.getElementById('engineDisplay');
    const ratingDisplay = document.getElementById('ratingDisplay');

    document.getElementById('submitButton').addEventListener('click', function () {
        // Hae kenttien arvot
        const nameValue = document.getElementById('name').value;
        const replayedValue = document.getElementById('replayed').checked ? 'Kyllä' : 'Ei'; // Muista käyttää oikeaa ID:tä
        const genreValue = document.getElementById('genre').value;
        const releaseYearValue = document.getElementById('releaseYear').value;
        const developerValue = document.getElementById('developer').value;
        const platformValue = document.getElementById('platform').value;
        const engineValue = document.getElementById('engine').value;
        const ratingValue = document.getElementById('review').value;

        // Aseta arvot popup-ruutuun
        nameDisplay.textContent = nameValue;
        replayedDisplay.textContent = replayedValue; // Aseta replayedDisplay-ruutuun
        genreDisplay.textContent = genreValue;
        releaseYearDisplay.textContent = releaseYearValue;
        developerDisplay.textContent = developerValue;
        platformDisplay.textContent = platformValue;
        engineDisplay.textContent = engineValue;
        ratingDisplay.textContent = ratingValue;

        // Näytä popup-ruutu
        popupBox.style.display = 'block';
    });
});
