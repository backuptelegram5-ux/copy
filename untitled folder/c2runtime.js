curl 'https://sportstrivia.quizinc.co.za/trivia-service/get-user-questions' \
  -H 'accept: */*' \
  -H 'accept-language: en-US,en-SG;q=0.9,en;q=0.8' \
  -H 'authorization: Basic UGVucm9zZV9HYW1pbmc6Qm83c2pIeVQ4MGdB' \
  -H 'content-type: application/x-www-form-urlencoded' \
  -H 'origin: https://www.unlock3.co.za' \
  -H 'priority: u=1, i' \
  -H 'referer: https://www.unlock3.co.za/' \
  -H 'sec-ch-ua: "Not:A-Brand";v="99", "Google Chrome";v="145", "Chromium";v="145"' \
  -H 'sec-ch-ua-mobile: ?0' \
  -H 'sec-ch-ua-platform: "macOS"' \
  -H 'sec-fetch-dest: empty' \
  -H 'sec-fetch-mode: cors' \
  -H 'sec-fetch-site: cross-site' \
  -H 'user-agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36' \
  --data-raw 'msisdn=27646226167&amountOfQuestions=1&productId=6'



Response

[{"questionCategoryId":47,"wordId":466,"word":"igw","wordLength":3,"answers":[null,null,null,null],"questionsAnsweredCount":[{"QuestionsAnsweredCount":4,"GameId":"112141982"}]}]