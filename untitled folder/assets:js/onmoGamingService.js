const OnmoGamingService = (function () {
    const config = { baseUrl: 'https://onmo.penrosesystems.com' };

    const getFromStorage = (key) => localStorage.getItem(key) || sessionStorage.getItem(key);
    const normalizeToken = (token) => (token && token !== 'null' && token !== 'undefined') ? token : null;

    const getUserId = () => {
        const userString = getFromStorage('oauth_user');

        if (!userString) {
            return null;
        }

        try {
            const user = JSON.parse(userString);
            return user && (user.sub || null);
        } catch (e) {
            console.error('Failed to parse user info:', e);
            return null;
        }
    };

    const postActivity = async (userId, timePlayed) => {

        return await fetch(`${config.baseUrl}/activity`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ type: 'game_ended', clientKey: 'unlock3', data: { userId, timePlayed } })
        })
        .then(async (res) => {
            const text = await res.text().catch(() => null);
            let parsed = null;
            
            try {
                parsed = text ? JSON.parse(text) : null;
            } catch (e) {
                parsed = text;
            }

            if (!res.ok) {
                const serverMsg = (parsed && (parsed.description || parsed.message)) || res.statusText || 'Unknown error';
                throw new Error(serverMsg);
            }

            return parsed || text;
        })
        .catch((err) => {
            console.error('Failed to post activity:', err);
            throw new Error('Failed to post activity: ' + (err && err.message ? err.message : err));
        });
    };

    const postGameEnded = (timePlayed) => {
        const userId = getUserId();

        let seconds = null;
        if (typeof timePlayed === 'number' && !isNaN(timePlayed) && timePlayed > 0) {
            seconds = Math.round(timePlayed);
        } else {
            const gameStart = (typeof window !== 'undefined') ? (window.gameStartTime || window.gameStart || window._gameStartTime) : null;
            if (gameStart && !isNaN(gameStart)) {
                seconds = Math.max(0, Math.round((Date.now() - gameStart) / 1000));
            }
        }

        if (seconds === null) {
            seconds = 0;
        }
        
        return postActivity(userId, seconds);
    };

    const isReady = () => {
        const token = normalizeToken(getFromStorage('oauth_access_token'));
        const userId = getUserId();
        const isGamezone = window.location.hostname.indexOf('gamezone') !== -1;
        return !!(token && userId && isGamezone);
    };

    return { 
        postActivity, 
        postGameEnded, 
        getUserId, 
        isReady 
    };
})();
