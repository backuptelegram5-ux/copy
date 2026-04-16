
const url = "https://sportstrivia.quizinc.co.za/trivia-service/save-user-answers";

function sendScore(productId, gameId, msisdn, questionCategoryId, questionId, answer )
{
    var myHeaders = new Headers();
    myHeaders.append("Content-Type", "application/json");

    var raw = JSON.stringify({
        "productId": productId,
        "gameId": gameId,
        "answer": [{
            "msisdn": msisdn,
            "questionCategoryId": questionCategoryId,
            "questionId": questionId,
            "answer": answer
        }]
    });

    var requestOptions = {
        method: 'POST',
        headers: myHeaders,
        body: raw,
        redirect: 'follow'
    };

    
    var response = {
        "status": 0,
        "Correct": false,
        "GameEnded": false,
    };

    fetch(url, requestOptions)
    .then((res) => res.json()).then((data) => {
        
        if(data.hasOwnProperty('recordset')) {
            if(data.recordset.hasOwnProperty(0)){
                response.status = 1;
                response.Correct = data.recordset[0].Correct;
                response.GameEnded = data.recordset[0].GameEnded;
				response.GameCompletionTime = data.recordset[0].GameCompletionTime;
                
                if(data.recordset[0].GameEnded && typeof window.trackGameCompletion === 'function') {
                    window.trackGameCompletion(data.recordset[0].GameCompletionTime);
                }
                
                //return JSON.stringify(response);
                c2_callFunction("get_answer_status",[data.recordset[0].Correct, data.recordset[0].GameEnded, data.recordset[0].GameCompletionTime]);
            }
        }
    }).catch((err) => {
        console.log(err);
    });
}

//productId - Number
//gameId - string
//msisdn - Mobile number string
//questionCategoryId - string 
//questionId  - string
//answer - string

/*var productId = 6;  
var gameId = "7";
var msisdn = "27844696041";
var questionCategoryId = "47";
var questionId = "429";
var answer = "rob";


sendScore(productId, gameId, msisdn, questionCategoryId, questionId, answer);*/