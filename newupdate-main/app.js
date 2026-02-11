const express = require('express');
const CryptoJS = require('crypto-js');
const app = express();
const port = 3000;

// Middleware to parse JSON bodies
app.use(express.json());

// Basic route
app.post('/', (req, res) => {
    res.send('X-CHAVI GENERATOR');
});





// Get an xchavi token
app.post('/token/:player_id/:game_id/:score/:array', (req, res) => {

    const user = {
        'unique_id': req.params.player_id,
        'game_id': req.params.game_id,
        'score': parseInt(req.params.score),
        'scoreArray': req.params.array
    };

    const token = getXChaviToken(req.params.player_id, req.params.game_id, JSON.stringify(user));

    if (token) {
        res.json(token);
    } else {
        res.status(404).send('Invalid parameters mf.');
    }
});


// Start the server
app.listen(process.env.PORT || port, () => {
    console.log(`Server is running on http://localhost:${port}`);
});




// Get the X-CHAVI token
function getXChaviToken(playerID, gameID, playerData) {

	playerID = CryptoJS['enc']['Utf8']['parse'](playerID['replace'](/-/g, ''));
	gameID = CryptoJS['enc']['Utf8']['parse'](gameID['replace'](/-/g, ''));

	var encryptedValue = CryptoJS['AES']['encrypt'](playerData, playerID, {
		'iv': gameID
	});
	const cipheredValue = encryptedValue['ciphertext']['toString'](CryptoJS['enc']['Base64']);
	return cipheredValue;
}
