// Auth Interceptor - Reads authorisationToken from configuration.json
// and applies it to all XMLHttpRequest calls
// Also transforms payload for get-user-via-product-id API

(function() {
    var authToken = null;
    var productId = null;
    var quizincAllAccessProductId = null;

    // Fetch configuration
    var xhr = new XMLHttpRequest();
    xhr.open('GET', 'configuration.json', false);
    xhr.send();

    if (xhr.status === 200) {
        try {
            var config = JSON.parse(xhr.responseText);
            authToken = config.authorisationToken || null;
            productId = config.productId || null;
            quizincAllAccessProductId = config.quizincAllAccessProductId || null;
        } catch (e) {
            // Configuration parsing failed
        }
    }

    // Store original XMLHttpRequest methods
    var originalOpen = XMLHttpRequest.prototype.open;
    var originalSend = XMLHttpRequest.prototype.send;
    var originalSetRequestHeader = XMLHttpRequest.prototype.setRequestHeader;

    // Override open
    XMLHttpRequest.prototype.open = function(method, url, async, user, password) {
        this._url = url;
        this._method = method;
        return originalOpen.apply(this, arguments);
    };

    // Override setRequestHeader - block Authorization and Content-Type for transformed requests
    XMLHttpRequest.prototype.setRequestHeader = function(header, value) {
        var headerLower = header.toLowerCase();

        // Check if this is a request we're transforming
        var isTransformedRequest = this._url && this._url.toLowerCase().indexOf('get-user-via-product-id') !== -1;

        if (isTransformedRequest) {
            // Block both Authorization and Content-Type - we'll set our own
            if (headerLower === 'authorization') {
                return;
            }
            if (headerLower === 'content-type') {
                return;
            }
        } else if (headerLower === 'authorization') {
            // For other requests, still block duplicate auth
            return;
        }

        return originalSetRequestHeader.apply(this, arguments);
    };

    // Helper function to make API call using fetch (to avoid intercepting ourselves)
    function makeApiCall(url, msisdnValue, productIdValue) {
        var msisdnNumber = parseInt(msisdnValue, 10);
        var payload = {
            msisdn: isNaN(msisdnNumber) ? msisdnValue : msisdnNumber,
            productId: productIdValue,
            externalRef: '1233564'
        };

        var headers = {
            'Content-Type': 'application/json'
        };

        if (authToken) {
            headers['Authorization'] = authToken;
        }

        return fetch(url, {
            method: 'POST',
            headers: headers,
            body: JSON.stringify(payload)
        });
    }

    // Helper function to populate XHR object with response and trigger events
    function populateXHRResponse(xhrObj, status, statusText, responseText) {
        // Define read-only properties
        Object.defineProperty(xhrObj, 'status', {
            value: status,
            writable: false,
            configurable: true
        });
        Object.defineProperty(xhrObj, 'statusText', {
            value: statusText,
            writable: false,
            configurable: true
        });
        Object.defineProperty(xhrObj, 'responseText', {
            value: responseText,
            writable: false,
            configurable: true
        });
        Object.defineProperty(xhrObj, 'response', {
            value: responseText,
            writable: false,
            configurable: true
        });
        Object.defineProperty(xhrObj, 'readyState', {
            value: 4,
            writable: false,
            configurable: true
        });

        // Trigger readystatechange event
        if (xhrObj.onreadystatechange) {
            xhrObj.onreadystatechange.call(xhrObj);
        }

        // Trigger load event
        var loadEvent = new Event('load');
        xhrObj.dispatchEvent(loadEvent);
    }

    // Override send
    XMLHttpRequest.prototype.send = function(data) {
        var self = this;

        if (this._url && this._url.toLowerCase().indexOf('get-user-via-product-id') !== -1 && data) {

            try {
                // Parse form-urlencoded data
                var params = {};
                var pairs = data.split('&');
                for (var i = 0; i < pairs.length; i++) {
                    var pair = pairs[i].split('=');
                    if (pair.length === 2) {
                        params[decodeURIComponent(pair[0])] = decodeURIComponent(pair[1]);
                    }
                }

                var msisdnValue = params.msisdn || '';

                // First call with productId 6
                makeApiCall(this._url, msisdnValue, productId)
                    .then(function(response1) {
                        return response1.text().then(function(responseText1) {
                            return { status: response1.status, statusText: response1.statusText, responseText: responseText1 };
                        });
                    })
                    .then(function(result1) {
                        try {
                            var jsonResponse1 = JSON.parse(result1.responseText);
                            var userStatus = (jsonResponse1.status || '').toUpperCase();

                            // Check if user is ACTIVE or SUBSCRIBED
                            if (userStatus === 'ACTIVE' || userStatus === 'SUBSCRIBED') {
                                populateXHRResponse(self, result1.status, result1.statusText, result1.responseText);
                            } else {
                                // User not active, try with quizincAllAccessProductId
                                makeApiCall(self._url, msisdnValue, quizincAllAccessProductId)
                                    .then(function(response2) {
                                        return response2.text().then(function(responseText2) {
                                            return { status: response2.status, statusText: response2.statusText, responseText: responseText2 };
                                        });
                                    })
                                    .then(function(result2) {
                                        try {
                                            var jsonResponse2 = JSON.parse(result2.responseText);
                                            var userStatus2 = (jsonResponse2.status || '').toUpperCase();

                                            if (userStatus2 === 'ACTIVE' || userStatus2 === 'SUBSCRIBED') {
                                                populateXHRResponse(self, result2.status, result2.statusText, result2.responseText);
                                            } else {
                                                populateXHRResponse(self, result1.status, result1.statusText, result1.responseText);
                                            }
                                        } catch (e) {
                                            populateXHRResponse(self, result1.status, result1.statusText, result1.responseText);
                                        }
                                    })
                                    .catch(function(error) {
                                        populateXHRResponse(self, result1.status, result1.statusText, result1.responseText);
                                    });
                            }
                        } catch (e) {
                            populateXHRResponse(self, result1.status, result1.statusText, result1.responseText);
                        }
                    })
                    .catch(function(error) {
                        // Trigger error event
                        var errorEvent = new Event('error');
                        self.dispatchEvent(errorEvent);
                    });

                // Don't call originalSend - we're handling the request ourselves
                return;

            } catch (e) {
                // Failed to transform payload
            }
        }

        // For other requests, just add auth if needed
        if (this._url && authToken) { 
            var url = this._url.toLowerCase(); 
            if (url.indexOf('quizinc.co.za') !== -1 || 
                url.indexOf('yellorush.co.za') !== -1 || 
                url.indexOf('penroseza.com') !== -1 || 
                url.indexOf('vodacomfantasy.com') !== -1) { 
                originalSetRequestHeader.call(this, 'Authorization', authToken); 
            } 
        } 

        return originalSend.apply(this, arguments);
    };
})();
