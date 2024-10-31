//Once all HTML-content is loaded (DOMContentLoaded) start doing all below.
document.addEventListener('DOMContentLoaded', () =>
    {
        //const = variable is constant -> it's not going to chance it's value.
        //document = JavaScript can access whole HTML file.

        //Gets element for popupBox which will be shown to user
        // after pressing OK-button. See <div id="popupBox"> in uudenPelinSyotto.html.
        const popupBox = document.getElementById('popupBox');
        const nameDisplay = document.getElementById('nameDisplay');
        const genreDisplay = document.getElementById('genreDisplay');
        const releaseDateDisplay = document.getElementById('releaseDateDisplay');
        const developerDisplay = document.getElementById('developerDisplay');
        const platformDisplay = document.getElementById('platformDisplay');
        const engineDisplay = document.getElementById('engineDisplay');
        const ratingDisplay = document.getElementById('ratingDisplay');

        //format's the given date to dd-mm-yyyy model.
        function formatDateToFinnish(dateString)
        {
            //Doens't do formating unless date selected.
            if (!dateString) return "Ei valittu";
            const [year, month, day] = dateString.split("-");
            return `${day}.${month}.${year}`;
        }

        //Updates the replayed checkmark based on if question format's box is ticked or not.
        function updateCheckmark()
        {
            const replayedDisplay = document.getElementById('replayedDisplay');

            //Will show checkmark's outer box no matter if replayed or not.
            replayedDisplay.style.border = '2px solid black';
            replayedDisplay.style.padding = '2px';
            replayedDisplay.style.display = 'inline-block';
            replayedDisplay.style.width = '20px';
            replayedDisplay.style.height = '20px';
            replayedDisplay.style.textAlign = 'center';
            replayedDisplay.style.lineHeight = '20px';
        
            //If replayed, spawn checkmark inside of outer box.
            if (document.getElementById('replayed').checked)
                {
                    replayedDisplay.innerHTML = '&#10003;';
                    replayedDisplay.style.color = 'black';
                }
        }
    
        //Calls the element from HTML.
        document.getElementById('submitButton').addEventListener('click', function ()
        {
            const nameValue = document.getElementById('name').value;
            //If genre not selected from radiobutton options, print instead "Not chosen" text.
            const genreValue = document.querySelector('input[name="genre"]:checked')?.value || "Ei valittu";
            const releaseDateValue = formatDateToFinnish(document.getElementById('releaseDate').value);
            const developerValue = document.getElementById('developer').value;
            const platformValue = document.getElementById('platform').value;
            const engineValue = document.getElementById('engine').value;
            
            //Adds text '/100' for rating field where user only writes the value the game has gotten.
            const ratingValue = document.getElementById('review').value;
            //If rating not given, print instead "No rating" text.
            const formattedRating = ratingValue ? `${ratingValue}/100` : "Ei arvosanaa";

            //Place's all text's to popupBox grid's.
            nameDisplay.textContent = nameValue;
            updateCheckmark();
            genreDisplay.textContent = genreValue;
            releaseDateDisplay.textContent = releaseDateValue;
            developerDisplay.textContent = developerValue;
            platformDisplay.textContent = platformValue;
            engineDisplay.textContent = engineValue;
            ratingDisplay.textContent = formattedRating;

            //Show the popupBox.
            popupBox.style.display = 'block';
        });
    });